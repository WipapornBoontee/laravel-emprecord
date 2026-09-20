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

        $pendingRequests = Overtime::with('user')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();
            
        $handledRequests = Overtime::with(['user', 'hr'])
            ->whereIn('status', ['approved', 'rejected'])
            ->orderBy('updated_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        return view('overtime.overtime', compact('pendingRequests', 'handledRequests', 'perPage'));
    }

    public function create()
    {
        $user = Auth::user();
        $todayDateStr = date('Y-m-d');
        
        $hasCheckedInToday = \App\Models\Attendance::where('user_id', $user->id)
            ->where('date', $todayDateStr)
            ->whereNotNull('check_in')
            ->exists();

        if (!$hasCheckedInToday) {
            return redirect()->route('overtime.show', $user->id)->with('error', 'คุณต้องสแกนเข้างานก่อนจึงจะสามารถขอทำ OT ได้');
        }

        return view('overtime.overtime_create');
    }

    public function overtimeRequest(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'date' => 'required',
            'hours' => 'required|integer|min:1',
            'description' => 'required',
        ]);

        $todayDateStr = date('Y-m-d');
        $hasCheckedInToday = \App\Models\Attendance::where('user_id', $request->user_id)
            ->where('date', $todayDateStr)
            ->whereNotNull('check_in')
            ->exists();

        if (!$hasCheckedInToday) {
            return back()->withInput()->with('error', 'คุณต้องสแกนเข้างานก่อนจึงจะสามารถขอทำ OT ได้');
        }

        $existingOTToday = \App\Models\Overtime::where('user_id', $request->user_id)
            ->where('date', $todayDateStr)
            ->exists();

        if ($existingOTToday) {
            return back()->withInput()->with('error', 'คุณได้ขอ OT สำหรับวันนี้ไปแล้ว ไม่สามารถขอซ้ำได้');
        }

        // หาจุดเริ่มต้นและสิ้นสุดของสัปดาห์ (วันจันทร์ ถึง วันอาทิตย์)
        $startOfWeek = \Carbon\Carbon::parse($request->date)->startOfWeek()->format('Y-m-d');
        $endOfWeek = \Carbon\Carbon::parse($request->date)->endOfWeek()->format('Y-m-d');

        // คำนวณชั่วโมง OT ที่มีอยู่ในสัปดาห์นี้ (เฉพาะที่ยังไม่ถูก reject)
        $existingOTHours = \App\Models\Overtime::where('user_id', $request->user_id)
            ->whereBetween('date', [$startOfWeek, $endOfWeek])
            ->where('status', '!=', 'rejected')
            ->sum('hours');

        $requestedHours = (int) $request->hours;

        if (($existingOTHours + $requestedHours) > 36) {
            return back()->withInput()->with('error', 'ไม่สามารถขอทำงานล่วงเวลาได้ เนื่องจากเกิน 36 ชม. OT ต่อสัปดาห์');
        }

        $overtime = Overtime::create($request->all());
        return redirect()->route('overtime.show')->with('success', 'ส่งคำขอ OT เรียบร้อยแล้ว');
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