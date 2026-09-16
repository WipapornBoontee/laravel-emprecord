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

    public function slip()
    {
        return view('salary.salary_slip');
    }

    public function downloadSlip(\Illuminate\Http\Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        
        // ดึงข้อมูล
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));
        
        $baseSalary = (float) ($user->salary ?? 0);

        // คำนวณวันในเดือนและค่าแรงรายวัน
        $daysInMonth = \Carbon\Carbon::create($year, $month)->daysInMonth;
        $dailyWage = $daysInMonth > 0 ? $baseSalary / $daysInMonth : 0;

        // ดึงข้อมูลการเข้า-ออกงานเพื่อหา OT
        $attendances = \App\Models\Attendance::where('user_id', $user->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->whereNotNull('check_out')
            ->get();

        $standardCheckOut = \Carbon\Carbon::createFromTimeString('17:00:00');
        $hourlyRate = 40;
        $totalOtPay = 0;

        foreach ($attendances as $att) {
            $checkOut = \Carbon\Carbon::createFromTimeString($att->check_out);
            if ($checkOut->greaterThan($standardCheckOut)) {
                $diffInMinutes = $standardCheckOut->diffInMinutes($checkOut);
                $otHours = $diffInMinutes / 60;
                $totalOtPay += ($otHours * $hourlyRate);
            }
        }

        // ดึงข้อมูลการลาที่ถูกปฏิเสธ (Rejected Leave) เพื่อนำมาหักเงิน
        $rejectedLeaves = \App\Models\LeaveRequest::where('user_id', $user->id)
            ->whereYear('start_date', $year)
            ->whereMonth('start_date', $month)
            ->where('status', 'rejected')
            ->get();
        
        $rejectedLeaveDays = $rejectedLeaves->sum('days_count');
        $leaveDeduction = $rejectedLeaveDays * $dailyWage;

        $netSalary = $baseSalary + $totalOtPay - $leaveDeduction;

        $data = [
            'user' => $user,
            'month' => $month,
            'year' => $year,
            'baseSalary' => $baseSalary,
            'totalOtPay' => $totalOtPay,
            'rejectedLeaveDays' => $rejectedLeaveDays,
            'leaveDeduction' => $leaveDeduction,
            'netSalary' => $netSalary,
            'datePrinted' => date('d/m/Y H:i:s')
        ];

        $html = view('salary.salary_slip_pdf', $data)->render();

        $mpdf = new \Mpdf\Mpdf([
            'default_font' => 'garuda',
            'format' => 'A4',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 15,
            'margin_bottom' => 15,
        ]);

        // ถ้าพนักงานมีเลขบัตรประชาชน ให้ตั้งเป็นรหัสผ่าน
        if (!empty($user->id_card)) {
            $mpdf->SetProtection(['print', 'copy'], $user->id_card, $user->id_card);
        }

        $mpdf->WriteHTML($html);
        $pdfContent = $mpdf->Output('', 'S');

        return response($pdfContent)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="slip_'.$user->emp_code.'_'.$year.'_'.$month.'.pdf"');
    }
}
