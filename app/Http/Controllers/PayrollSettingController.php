<?php

namespace App\Http\Controllers;

use App\Models\PayrollSetting;
use Illuminate\Http\Request;

class PayrollSettingController extends Controller
{
    public function index()
    {
        $setting = PayrollSetting::first();
        return view('settings.payroll', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'ss_min_salary' => 'required|numeric|min:0',
            'ss_percent' => 'required|numeric|min:0|max:100',
            'ss_max_deduction' => 'required|numeric|min:0',
            'tax_min_salary' => 'required|numeric|min:0',
            'tax_percent' => 'required|numeric|min:0|max:100',
        ]);

        $setting = PayrollSetting::first();
        
        if (!$setting) {
            $setting = new PayrollSetting();
        }

        $setting->ss_min_salary = $request->ss_min_salary;
        $setting->ss_percent = $request->ss_percent;
        $setting->ss_max_deduction = $request->ss_max_deduction;
        $setting->tax_min_salary = $request->tax_min_salary;
        $setting->tax_percent = $request->tax_percent;
        $setting->save();

        return redirect()->route('settings.payroll')->with('success', 'บันทึกการตั้งค่าเงินเดือนเรียบร้อยแล้ว');
    }
}
