<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClaimController extends Controller
{
    public function form()
    {
        return view('claim_form');
    }

    public function insert(Request $request)
    {
        $request->validate([
            'serial_number' => 'required|alpha_num|min:5|max:20',
            'email' => 'required|email',
            'symptom' => 'required|min:10',
            'urgency' => 'required|in:low,medium,high',
        ], [
            'serial_number.required' => 'กรุณากรอกรหัสสินค้า (Serial Number)',
            'serial_number.alpha_num' => 'รหัสสินค้าต้องเป็นตัวอักษรหรือตัวเลขเท่านั้น',
            'serial_number.min' => 'รหัสสินค้าต้องมีความยาวอย่างน้อย 5 ตัวอักษร',
            'serial_number.max' => 'รหัสสินค้าต้องมีความยาวไม่เกิน 20 ตัวอักษร',
            'email.required' => 'กรุณากรอกอีเมลผู้ติดต่อ',
            'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
            'symptom.required' => 'กรุณากรอกอาการชำรุด',
            'symptom.min' => 'กรุณาระบุอาการชำรุดโดยละเอียดอย่างน้อย 10 ตัวอักษร',
            'urgency.required' => 'กรุณาเลือกระดับความเร่งด่วน',
            'urgency.in' => 'ระดับความเร่งด่วนไม่ถูกต้อง',
        ]);

        // Process claim (e.g. save to DB, send email, etc.)
        return redirect()->back()->with('success', 'ส่งข้อมูลแจ้งเคลมสินค้าสำเร็จ!');
    }
}
