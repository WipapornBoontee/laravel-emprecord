<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Overtime;

class OverTimeController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 10);
        if (!in_array($perPage, [5, 10, 25, 50])) {
            $perPage = 10;
        }

        $pendingRequests = Overtime::with(['user.department', 'user.position'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        // แนบข้อมูล Attendance และชั่วโมงสะสมในสัปดาห์สำหรับแต่ละคำขอ
        foreach ($pendingRequests as $otReq) {
            $otReq->attendance = Attendance::where('user_id', $otReq->user_id)
                ->where('date', $otReq->date)
                ->first();

            $startOfWeek = \Carbon\Carbon::parse($otReq->date)->startOfWeek()->format('Y-m-d');
            $endOfWeek = \Carbon\Carbon::parse($otReq->date)->endOfWeek()->format('Y-m-d');
            $otReq->weekly_approved_hours = Overtime::where('user_id', $otReq->user_id)
                ->whereBetween('date', [$startOfWeek, $endOfWeek])
                ->where('status', 'approved')
                ->sum('hours');
        }
            
        $handledRequests = Overtime::with(['user.department', 'user.position', 'hr'])
            ->whereIn('status', ['approved', 'rejected'])
            ->orderBy('updated_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        foreach ($handledRequests as $otReq) {
            $otReq->attendance = Attendance::where('user_id', $otReq->user_id)
                ->where('date', $otReq->date)
                ->first();
        }

        return view('overtime.overtime', compact('pendingRequests', 'handledRequests', 'perPage'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $todayDateStr = date('Y-m-d');
        $minDateStr = \Carbon\Carbon::today()->subDays(7)->toDateString();
        $maxDateStr = \Carbon\Carbon::today()->addDays(7)->toDateString();
        
        // ดึงประวัติการลงเวลาของพนักงานย้อนหลัง 7 วัน เพื่อใช้อ้างอิงการทำงานจริง
        $recentAttendances = \App\Models\Attendance::where('user_id', $user->id)
            ->where('date', '<=', $todayDateStr)
            ->where('date', '>=', $minDateStr)
            ->orderBy('date', 'desc')
            ->get()
            ->keyBy(fn($item) => \Carbon\Carbon::parse($item->date)->format('Y-m-d'));

        // วันหยุดบริษัทสำหรับแนะนำประเภท OT
        $companyHolidays = \App\Models\CompanyHoliday::all(['name', 'holiday_date', 'is_recurring']);

        $selectedDate = $request->input('date', $todayDateStr);

        $startOfWeek = \Carbon\Carbon::parse($selectedDate)->startOfWeek()->format('Y-m-d');
        $endOfWeek = \Carbon\Carbon::parse($selectedDate)->endOfWeek()->format('Y-m-d');
        $weeklyApprovedHours = (float) \App\Models\Overtime::where('user_id', $user->id)
            ->whereBetween('date', [$startOfWeek, $endOfWeek])
            ->where('status', '!=', 'rejected')
            ->sum('hours');

        return view('overtime.overtime_create', compact(
            'recentAttendances',
            'companyHolidays',
            'selectedDate',
            'minDateStr',
            'maxDateStr',
            'weeklyApprovedHours'
        ));
    }

    public function overtimeRequest(Request $request)
    {
        $user = Auth::user();

        $minDate = \Carbon\Carbon::today()->subDays(7)->toDateString();
        $maxDate = \Carbon\Carbon::today()->addDays(7)->toDateString();

        $request->validate([
            'date' => ['required', 'date', 'after_or_equal:' . $minDate, 'before_or_equal:' . $maxDate],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i'],
            'break_minutes' => ['nullable', 'integer', 'min:0', 'max:240'],
            'ot_type' => ['required', 'string', 'in:normal,holiday,holiday_ot'],
            'description' => ['required', 'string', 'max:1000'],
        ], [
            'date.required' => 'กรุณาเลือกวันที่ปฏิบัติงาน OT',
            'date.after_or_equal' => 'สามารถยื่นขอ OT ย้อนหลังได้ไม่เกิน 7 วัน',
            'date.before_or_equal' => 'สามารถยื่นขอ OT ล่วงหน้าได้ไม่เกิน 7 วัน',
            'start_time.required' => 'กรุณาระบุเวลาเริ่มต้นทำ OT',
            'end_time.required' => 'กรุณาระบุเวลาสิ้นสุดทำ OT',
            'ot_type.required' => 'กรุณาเลือกประเภท OT',
            'description.required' => 'กรุณาระบุรายละเอียดงานที่ปฏิบัติงานล่วงเวลา',
        ]);

        $dateStr = $request->date;
        $isPastOrToday = \Carbon\Carbon::parse($dateStr)->lte(\Carbon\Carbon::today());

        // ถ้าเป็นวันที่ในอดีตหรือวันนี้ ต้องตรวจสอบว่ามีประวัติการเข้างานจริงหรือไม่ (Attendance Verification)
        if ($isPastOrToday) {
            $attendance = \App\Models\Attendance::where('user_id', $user->id)
                ->where('date', $dateStr)
                ->whereNotNull('check_in')
                ->first();

            if (!$attendance) {
                return back()->withInput()->with('error', "ไม่พบประวัติการลงเวลาเข้างานในวันที่ " . \Carbon\Carbon::parse($dateStr)->format('d/m/Y') . " กรุณาตรวจสอบการลงเวลาทำงานก่อนยื่นขอ OT");
            }
        }

        // ตรวจสอบว่าเคยขอ OT วันนี้ไปแล้วหรือไม่ (เฉพาะที่ยังรออนุมัติหรืออนุมัติแล้ว)
        $existingOT = \App\Models\Overtime::where('user_id', $user->id)
            ->where('date', $dateStr)
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($existingOT) {
            return back()->withInput()->with('error', 'คุณได้ยื่นคำขอ OT สำหรับวันที่ ' . \Carbon\Carbon::parse($dateStr)->format('d/m/Y') . ' ไปแล้ว ไม่สามารถขอซ้ำได้');
        }

        // คำนวณชั่วโมง OT
        $startTime = \Carbon\Carbon::parse($dateStr . ' ' . $request->start_time);
        $endTime = \Carbon\Carbon::parse($dateStr . ' ' . $request->end_time);

        // หากเวลาเลิกงานข้ามวัน เช่น เริ่ม 22:00 เลิก 02:00
        if ($endTime->lte($startTime)) {
            $endTime->addDay();
        }

        $diffMinutes = $startTime->diffInMinutes($endTime);
        $breakMinutes = (int) $request->input('break_minutes', 0);
        $netMinutes = max(0, $diffMinutes - $breakMinutes);
        $calculatedHours = round($netMinutes / 60, 1);

        if ($calculatedHours <= 0) {
            return back()->withInput()->with('error', 'ช่วงเวลาที่ระบุคำนวณแล้วไม่ถึง 30 นาที กรุณาตรวจสอบเวลาเริ่มต้นและสิ้นสุด');
        }

        // ตรวจสอบลิมิตกฎหมายแรงงาน 36 ชั่วโมงต่อสัปดาห์
        $startOfWeek = \Carbon\Carbon::parse($dateStr)->startOfWeek()->format('Y-m-d');
        $endOfWeek = \Carbon\Carbon::parse($dateStr)->endOfWeek()->format('Y-m-d');

        $existingOTHours = \App\Models\Overtime::where('user_id', $user->id)
            ->whereBetween('date', [$startOfWeek, $endOfWeek])
            ->where('status', '!=', 'rejected')
            ->sum('hours');

        if (($existingOTHours + $calculatedHours) > 36) {
            return back()->withInput()->with('error', "ไม่สามารถขอทำงานล่วงเวลาได้ เนื่องจากชั่วโมง OT สะสมในสัปดาห์นี้จะเกิน 36 ชม. (ปัจจุบันสะสม: {$existingOTHours} ชม. + ขอเพิ่ม: {$calculatedHours} ชม.)");
        }

        Overtime::create([
            'user_id' => $user->id,
            'date' => $dateStr,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'break_minutes' => $breakMinutes,
            'ot_type' => $request->ot_type,
            'hours' => $calculatedHours,
            'description' => $request->description,
            'status' => 'pending',
        ]);

        return redirect()->route('overtime.show')->with('success', "ส่งคำขอทำงานล่วงเวลา (OT) วันที่ " . \Carbon\Carbon::parse($dateStr)->format('d/m/Y') . " จำนวน {$calculatedHours} ชั่วโมง เรียบร้อยแล้ว");
    }

    public function overtime(\Illuminate\Http\Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));

        // ดึงข้อมูลคำขอ OT ทั้งหมดของ user ในเดือนนี้
        $overtimes = \App\Models\Overtime::where('user_id', $user->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderBy('date', 'asc')
            ->get();

        // ดึงข้อมูลการเข้า-ออกงานทั้งหมดในเดือนนี้
        $attendances = \App\Models\Attendance::where('user_id', $user->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get()
            ->keyBy(function($item) {
                return \Carbon\Carbon::parse($item->date)->format('Y-m-d');
            });

        $otStartTime = \Carbon\Carbon::createFromTimeString('17:30:00');
        
        $hasOTToday = false;
        $todayOtHours = 0;
        $todayDateStr = date('Y-m-d');
        
        $todayOt = $overtimes->where('date', $todayDateStr)->where('status', 'approved')->first();
        if ($todayOt) {
            $hasOTToday = true;
            $todayOtHours = $todayOt->hours;
        }
        
        $hasAnyOTToday = $overtimes->where('date', $todayDateStr)->isNotEmpty();

        $hasCheckedInToday = \App\Models\Attendance::where('user_id', $user->id)
            ->where('date', $todayDateStr)
            ->whereNotNull('check_in')
            ->exists();

        $totalOtHours = 0;
        $otDetails = [];

        foreach ($overtimes as $ot) {
            $dateKey = \Carbon\Carbon::parse($ot->date)->format('Y-m-d');
            
            $detail = [
                'date' => \Carbon\Carbon::parse($ot->date)->format('d/m/Y'),
                'overtime_id' => $ot->id,
                'status' => $ot->status,
                'requested_hours' => $ot->hours,
                'hr_reject_reason' => $ot->hr_reject_reason,
                'check_out' => null,
                'ot_hours' => 0,
                'actual_minutes' => 0,
                'early_checkout_reason' => $ot->early_checkout_reason
            ];
            
            if ($ot->status == 'approved' && isset($attendances[$dateKey]) && $attendances[$dateKey]->check_out) {
                $checkOutStr = \Carbon\Carbon::parse($attendances[$dateKey]->check_out)->format('H:i:s');
                if ($checkOutStr > $otStartTime->format('H:i:s')) {
                    $checkOutTime = \Carbon\Carbon::createFromTimeString($checkOutStr);
                    $diffInMinutes = $otStartTime->diffInMinutes($checkOutTime);
                    
                    $actualHours = floor($diffInMinutes / 60);
                    $otHours = min($actualHours, $ot->hours);
                    
                    if ($otHours >= 0) {
                        $totalOtHours += $otHours;
                        $detail['check_out'] = $attendances[$dateKey]->check_out;
                        $detail['ot_hours'] = $otHours;
                        $detail['actual_minutes'] = $diffInMinutes;
                    }
                }
            }
            $otDetails[] = $detail;
        }

        return view('overtime.overtime_show', compact(
            'month', 'year', 'otDetails', 'totalOtHours', 'hasOTToday', 'todayOtHours', 'hasCheckedInToday', 'hasAnyOTToday'
        ));
    }

    public function submitEarlyCheckoutReason(Request $request)
    {
        $request->validate([
            'overtime_id' => 'required|integer',
            'early_checkout_reason' => 'required|string'
        ]);

        $overtime = \App\Models\Overtime::where('id', $request->overtime_id)
                        ->where('user_id', \Illuminate\Support\Facades\Auth::id())
                        ->firstOrFail();
                        
        $overtime->early_checkout_reason = $request->early_checkout_reason;
        $overtime->save();

        return redirect()->back()->with('success', 'บันทึกเหตุผลกลับก่อนเวลาส่งให้ HR เรียบร้อยแล้ว');
    }

    public function approve(Request $request, $id)
    {
        $overtime = Overtime::findOrFail($id);
        $overtime->status = 'approved';
        $overtime->hr_id = Auth::id();
        $overtime->hr_approved_at = now();
        $overtime->save();

        return redirect()->back()->with('success', 'อนุมัติคำขอทำล่วงเวลาเรียบร้อยแล้ว');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'hr_reject_reason' => 'required|string|max:1000'
        ]);

        $overtime = Overtime::findOrFail($id);
        $overtime->status = 'rejected';
        $overtime->hr_reject_reason = $request->hr_reject_reason;
        $overtime->hr_id = Auth::id();
        $overtime->hr_approved_at = now();
        $overtime->save();

        return redirect()->back()->with('success', 'ปฏิเสธคำขอทำล่วงเวลาเรียบร้อยแล้ว');
    }

    public function bulkApprove(Request $request)
    {
        $request->validate([
            'overtime_ids' => 'required|array',
            'overtime_ids.*' => 'exists:overtimes,id'
        ]);

        Overtime::whereIn('id', $request->overtime_ids)->update([
            'status' => 'approved',
            'hr_id' => Auth::id(),
            'hr_approved_at' => now()
        ]);

        return redirect()->back()->with('success', 'อนุมัติคำขอที่เลือกเรียบร้อยแล้ว');
    }
}