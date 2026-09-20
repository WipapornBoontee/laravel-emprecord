<?php

namespace App\Http\Controllers;

use App\Models\LeaveType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LeaveTypeController extends Controller
{
    /**
     * แสดงรายการประเภทการลาทั้งหมด (Leave Types Management)
     */
    public function index()
    {
        $leaveTypes = LeaveType::withCount(['leaveRequests', 'leaveBalances'])
            ->orderBy('id')
            ->get();

        return view('leaves.types.index', compact('leaveTypes'));
    }

    /**
     * บันทึกประเภทการลาใหม่
     */
    public function store(Request $request)
    {
        // ตัดช่องว่างด้านหน้า/ด้านหลัง เพื่อป้องกันกรณีเคาะ spacebar ซ้ำ
        $request->merge([
            'name' => is_string($request->input('name')) ? trim($request->input('name')) : $request->input('name'),
        ]);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('leave_types', 'name'),
            ],
            'default_days' => ['required', 'integer', 'min:0', 'max:365'],
        ], [
            'name.required' => 'กรุณากรอกชื่อประเภทการลา',
            'name.unique' => 'ประเภทการลานี้มีในระบบแล้ว (กรุณาใช้ชื่ออื่น)',
            'name.max' => 'ชื่อประเภทการลาต้องไม่เกิน 50 ตัวอักษร',
            'default_days.required' => 'กรุณาระบุโควตาวันลาเริ่มต้นต่อปี',
            'default_days.integer' => 'จำนวนวันต้องเป็นตัวเลขจำนวนเต็ม',
            'default_days.min' => 'จำนวนวันต้องไม่น้อยกว่า 0',
            'default_days.max' => 'จำนวนวันต้องไม่เกิน 365 วัน',
        ]);

        LeaveType::create($validated);

        return redirect()->to(route('leaves.types.index', [], false))->with('success', 'เพิ่มประเภทการลาใหม่สำเร็จ');
    }

    /**
     * อัปเดตประเภทการลา
     */
    public function update(Request $request, LeaveType $type)
    {
        // ตัดช่องว่างด้านหน้า/ด้านหลัง เพื่อป้องกันกรณีเคาะ spacebar ซ้ำ
        $request->merge([
            'name' => is_string($request->input('name')) ? trim($request->input('name')) : $request->input('name'),
        ]);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('leave_types', 'name')->ignore($type->id),
            ],
            'default_days' => ['required', 'integer', 'min:0', 'max:365'],
        ], [
            'name.required' => 'กรุณากรอกชื่อประเภทการลา',
            'name.unique' => 'ประเภทการลานี้มีในระบบแล้ว (กรุณาใช้ชื่ออื่น)',
            'name.max' => 'ชื่อประเภทการลาต้องไม่เกิน 50 ตัวอักษร',
            'default_days.required' => 'กรุณาระบุโควตาวันลาเริ่มต้นต่อปี',
            'default_days.integer' => 'จำนวนวันต้องเป็นตัวเลขจำนวนเต็ม',
            'default_days.min' => 'จำนวนวันต้องไม่น้อยกว่า 0',
            'default_days.max' => 'จำนวนวันต้องไม่เกิน 365 วัน',
        ]);

        $type->update($validated);

        return redirect()->to(route('leaves.types.index', [], false))->with('success', 'อัปเดตประเภทการลาสำเร็จ');
    }

    /**
     * ลบประเภทการลา
     */
    public function destroy(LeaveType $type)
    {
        if ($type->leaveRequests()->count() > 0) {
            return redirect()->to(route('leaves.types.index', [], false))->with('error', 'ไม่สามารถลบประเภทการลานี้ได้ เนื่องจากมีประวัติคำขอลาอ้างอิงอยู่');
        }

        $type->delete();

        return redirect()->to(route('leaves.types.index', [], false))->with('success', 'ลบประเภทการลาสำเร็จ');
    }
}
