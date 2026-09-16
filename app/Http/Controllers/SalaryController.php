<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalaryEmp;

class SalaryController extends Controller
{
    public function show()
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        
        $baseSalary = (float) ($user->salary ?? 0);
        $daysInMonth = \Carbon\Carbon::now()->daysInMonth;
        
        $dailyWage = 0;
        if ($daysInMonth > 0 && $baseSalary > 0) {
            $dailyWage = $baseSalary / $daysInMonth;
        }

        return view('salary.salary_show', compact('baseSalary', 'daysInMonth', 'dailyWage'));
    }

    public function overtime(\Illuminate\Http\Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
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
                    'ot_pay' => round($otPay, 2)
                ];
            }
        }

        return view('salary.salary_overtime', compact(
            'month', 'year', 'otDetails', 'totalOtHours', 'totalOtPay', 'hourlyRate'
        ));
    }
}
