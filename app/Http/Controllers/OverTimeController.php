<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Overtime;

class OverTimeController extends Controller
{
    public function index()
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        
        $baseOvertime = (float) ($user->overtime ?? 0);
        $daysInMonth = \Carbon\Carbon::now()->daysInMonth;
        
        $dailyWage = 0;
        if ($daysInMonth > 0 && $baseOvertime > 0) {
            $dailyWage = $baseOvertime / $daysInMonth;
        }

        return view('overtime.index', compact('baseOvertime', 'daysInMonth', 'dailyWage'));
    }

    public function create()
    {
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

        // ดึงข้อมูลคำขอ OT ที่ได้รับการอนุมัติแล้วในเดือนนี้
        $approvedOvertimes = \App\Models\Overtime::where('user_id', $user->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->where('status', 'approved')
            ->get()
            ->keyBy(function($item) {
                return \Carbon\Carbon::parse($item->date)->format('Y-m-d');
            });

        // ดึงข้อมูลการเข้า-ออกงานเฉพาะวันที่มีการบันทึกเลิกงาน
        $attendances = \App\Models\Attendance::where('user_id', $user->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->whereNotNull('check_out')
            ->orderBy('date', 'asc')
            ->get();

        $otStartTime = \Carbon\Carbon::createFromTimeString('17:30:00');
        
        $hasOTToday = false;
        $todayDateStr = date('Y-m-d');
        if (isset($approvedOvertimes[$todayDateStr])) {
            $hasOTToday = true;
        }

        $totalOtHours = 0;
        $otDetails = [];

        foreach ($attendances as $att) {
            $dateKey = $att->date->format('Y-m-d');
            
            // ตรวจสอบว่ามีคำขอ OT ที่ได้รับอนุมัติในวันนี้หรือไม่
            if (isset($approvedOvertimes[$dateKey])) {
                $requestedHours = (int) $approvedOvertimes[$dateKey]->hours;
                $checkOutStr = \Carbon\Carbon::parse($att->check_out)->format('H:i:s');
                
                // ถ้าสแกนออกหลัง 17:30 น.
                if ($checkOutStr > $otStartTime->format('H:i:s')) {
                    $checkOutTime = \Carbon\Carbon::createFromTimeString($checkOutStr);
                    $diffInMinutes = $otStartTime->diffInMinutes($checkOutTime);
                    
                    $actualHours = floor($diffInMinutes / 60); // ปัดเศษลงอย่างเข้มงวดตามที่พนักงานควรทราบเวลาออก
                    
                    // ให้ชั่วโมง OT ไม่เกินจำนวนที่ขอไว้
                    $otHours = min($actualHours, $requestedHours);
                    
                    if ($otHours >= 0) {
                        $totalOtHours += $otHours;
                        
                        $otDetails[] = [
                            'date' => $att->date->format('d/m/Y'),
                            'overtime_id' => $approvedOvertimes[$dateKey]->id,
                            'check_out' => $att->check_out,
                            'ot_hours' => $otHours,
                            'actual_minutes' => $diffInMinutes,
                            'requested_hours' => $requestedHours,
                            'early_checkout_reason' => $approvedOvertimes[$dateKey]->early_checkout_reason
                        ];
                    }
                }
            }
        }

        return view('overtime.overtime_show', compact(
            'month', 'year', 'otDetails', 'totalOtHours', 'hasOTToday'
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
}