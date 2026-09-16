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
                    'ot_minutes' => $diffInMinutes,
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

    public function verifySlip(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'id_card' => 'required|string|size:13',
            'month' => 'required',
            'year' => 'required'
        ], [
            'id_card.required' => 'กรุณากรอกเลขบัตรประชาชน',
            'id_card.size' => 'เลขบัตรประชาชนต้องมี 13 หลัก',
        ]);

        $user = \Illuminate\Support\Facades\Auth::user();

        if ($user->id_card !== $request->id_card) {
            return back()->with('error', 'เลขบัตรประชาชนไม่ถูกต้อง รหัสไม่ตรงกับข้อมูลในระบบ');
        }

        // เก็บ session อนุมัติไว้สั้นๆ 5 นาที (300 วินาที)
        $request->session()->put('slip_verified_user_' . $user->id, true);
        $request->session()->put('slip_verified_time_' . $user->id, time());
        
        return redirect()->route('salary_slip.download', [
            'month' => $request->month,
            'year' => $request->year
        ]);
    }

    public function downloadSlip(\Illuminate\Http\Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        
        // เช็ค Session สิทธิ์
        $isVerified = $request->session()->get('slip_verified_user_' . $user->id);
        $verifiedTime = $request->session()->get('slip_verified_time_' . $user->id);
        
        if (!$isVerified || (time() - $verifiedTime) > 300) {
            // ลบ session ทิ้งและเตะกลับ
            $request->session()->forget('slip_verified_user_' . $user->id);
            $request->session()->forget('slip_verified_time_' . $user->id);
            return redirect()->route('salary_slip')->with('error', 'เซสชันหมดอายุ กรุณากรอกเลขบัตรประชาชนใหม่');
        }

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

        // ดึงการตั้งค่าเงินเดือน (ถ้าไม่มีให้จำลองค่า default)
        $setting = \App\Models\PayrollSetting::first() ?? new \App\Models\PayrollSetting([
            'ss_min_salary' => 1,
            'ss_percent' => 5,
            'ss_max_deduction' => 750,
            'tax_min_salary' => 26000,
            'tax_percent' => 3
        ]);

        // คำนวณประกันสังคม (คิดจากฐานเงินเดือน)
        $socialSecurity = 0;
        if ($baseSalary >= $setting->ss_min_salary) {
            $socialSecurity = ($baseSalary * $setting->ss_percent) / 100;
            if ($socialSecurity > $setting->ss_max_deduction) {
                $socialSecurity = $setting->ss_max_deduction;
            }
        }

        // คำนวณหักภาษี ณ ที่จ่าย (คิดจากฐานเงินเดือน หรืออาจรวมรายรับอื่นด้วย)
        // ในที่นี้คิดจากเงินรับรวม (เงินเดือน + OT)
        $tax = 0;
        $totalIncome = $baseSalary + $totalOtPay;
        if ($totalIncome >= $setting->tax_min_salary) {
            $tax = ($totalIncome * $setting->tax_percent) / 100;
        }

        $leaveDeduction = $rejectedLeaveDays * $dailyWage;

        $netSalary = $baseSalary + $totalOtPay - $leaveDeduction - $socialSecurity - $tax;
        $netSalaryText = $this->bahtText($netSalary);

        $data = [
            'user' => $user,
            'month' => $month,
            'year' => $year,
            'baseSalary' => $baseSalary,
            'totalOtPay' => $totalOtPay,
            'rejectedLeaveDays' => $rejectedLeaveDays,
            'leaveDeduction' => $leaveDeduction,
            'socialSecurity' => $socialSecurity,
            'tax' => $tax,
            'netSalary' => $netSalary,
            'netSalaryText' => $netSalaryText,
            'datePrinted' => date('d/m/Y H:i:s')
        ];

        // เคลียร์ session ทันทีเพื่อความปลอดภัย (ดูได้ครั้งเดียว)
        $request->session()->forget('slip_verified_user_' . $user->id);
        $request->session()->forget('slip_verified_time_' . $user->id);

        // โหลด View สลิปในรูปแบบ HTML สำหรับการปริ้นเป็น PDF ผ่านเบราว์เซอร์
        return view('salary.salary_slip_pdf', $data);
    }

    private function bahtText($number)
    {
        $number = number_format($number, 2, '.', '');
        list($integer, $fraction) = explode('.', $number);
        
        $txtNum = ['ศูนย์', 'หนึ่ง', 'สอง', 'สาม', 'สี่', 'ห้า', 'หก', 'เจ็ด', 'แปด', 'เก้า', 'สิบ'];
        $txtUnit = ['', 'สิบ', 'ร้อย', 'พัน', 'หมื่น', 'แสน', 'ล้าน'];
        
        $convert = function($num) use ($txtNum, $txtUnit) {
            $val = '';
            $len = strlen($num);
            for ($i = 0; $i < $len; $i++) {
                $n = substr($num, $i, 1);
                if ($n != 0) {
                    if ($i == ($len - 1) && $n == 1 && $len > 1 && substr($num, $i - 1, 1) != 0) {
                        $val .= 'เอ็ด';
                    } elseif ($i == ($len - 2) && $n == 2) {
                        $val .= 'ยี่';
                    } elseif ($i == ($len - 2) && $n == 1) {
                        $val .= '';
                    } else {
                        $val .= $txtNum[$n];
                    }
                    $val .= $txtUnit[$len - $i - 1];
                }
            }
            return $val;
        };

        $baht = $integer > 0 ? $convert($integer) . 'บาท' : 'ศูนย์บาท';
        $satang = $fraction > 0 ? $convert($fraction) . 'สตางค์' : 'ถ้วน';

        return $baht . $satang;
    }
}
