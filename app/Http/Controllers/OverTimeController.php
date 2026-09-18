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

    public function overtimeRequest(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'date' => 'required',
            'hours' => 'required',
            'description' => 'required',
        ]);

        $overtime = Overtime::create($request->all());
        return redirect()->route('overtime.index')->with('success', 'ส่งคำขอ OT เรียบร้อยแล้ว');
    }

    public function overtime(\Illuminate\Http\Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $baseSalary = (float) ($user->salary ?? 0); // ดึงฐานเงินเดือน
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));

        // ดึงข้อมูลการเข้า-ออกงานเฉพาะวันที่มีการบันทึกเลิกงาน
        $attendances = \App\Models\Attendance::where('user_id', $user->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->whereNotNull('check_out')
            ->orderBy('date', 'asc')
            ->get();

        $standardCheckOut = \Carbon\Carbon::createFromTimeString('17:00:00');
        $hourlyRate = 40;
        
        $totalOtHours = 0;
        $totalOtPay = 0;
        $otDetails = [];

        foreach ($attendances as $att) {
            $checkOut = \Carbon\Carbon::createFromTimeString($att->check_out);
            
            // ถ้าเลิกงานหลังเวลา 17:00
            if ($checkOut->greaterThan($standardCheckOut)) {
                $diffInMinutes = $standardCheckOut->diffInMinutes($checkOut);
                $otHours = $diffInMinutes / 60; // คำนวณตามจริงเป็นทศนิยม
                $otPay = $otHours * $hourlyRate;
                
                $totalOtHours += $otHours;
                $totalOtPay += $otPay;
                
                $otDetails[] = [
                    'date' => $att->date->format('d/m/Y'),
                    'check_in' => $att->check_in,
                    'check_out' => $att->check_out,
                    'ot_hours' => round($otHours, 2),
                    'ot_minutes' => $diffInMinutes,
                    'ot_pay' => round($otPay, 2)
                ];
            }
        }

        return view('overtime.overtime_show', compact(
            'month', 'year', 'otDetails', 'totalOtHours', 'totalOtPay', 'hourlyRate', 'baseSalary'
        ));
    }
}