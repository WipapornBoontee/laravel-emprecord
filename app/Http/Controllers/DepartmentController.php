<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Position;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Display departments listing and manage them
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 10);
        if (!in_array($perPage, [5, 10, 25, 50])) {
            $perPage = 10;
        }

        $departments = Department::withCount('users')
            ->with(['users.position'])
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();

        return view('departments.index', compact('departments', 'perPage'));
    }

    /**
     * Store a new department
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:departments,name'],
        ], [
            'name.required' => 'กรุณากรอกชื่อแผนก',
            'name.unique' => 'ชื่อแผนกนี้มีในระบบแล้ว',
        ]);

        Department::create($validated);

        return redirect()->to(route('departments.index', [], false))->with('success', 'เพิ่มแผนกใหม่เรียบร้อยแล้ว');
    }

    /**
     * Update department details
     */
     public function update(Request $request, Department $department)
     {
         $validated = $request->validate([
             'name' => ['required', 'string', 'max:100', 'unique:departments,name,' . $department->id],
             'is_active' => ['nullable', 'boolean'],
         ], [
             'name.required' => 'กรุณากรอกชื่อแผนก',
             'name.unique' => 'ชื่อแผนกนี้มีในระบบแล้ว',
         ]);

         $validated['is_active'] = $request->has('is_active') ? true : false;
         $department->update($validated);

         return redirect()->to(route('departments.index', [], false))->with('success', "แก้ไขข้อมูลแผนก {$department->name} เรียบร้อยแล้ว");
     }

     /**
      * Toggle department active status
      */
     public function toggleStatus(Department $department)
     {
         $department->is_active = !$department->is_active;
         $department->save();

         $statusText = $department->is_active ? 'เปิดใช้งาน' : 'ปิดการใช้งาน';
         return redirect()->to(route('departments.index', [], false))->with('success', "เปลี่ยนสถานะแผนก {$department->name} เป็น {$statusText} เรียบร้อยแล้ว");
     }

    /**
     * Delete a department
     */
    public function destroy(Department $department)
    {
        if ($department->users()->count() > 0) {
            return redirect()->to(route('departments.index', [], false))->with('error', 'ไม่สามารถลบแผนกนี้ได้ เนื่องจากมีพนักงานสังกัดอยู่ในแผนกนี้');
        }

        $department->delete();

        return redirect()->to(route('departments.index', [], false))->with('success', 'ลบแผนกเรียบร้อยแล้ว');
    }

    /**
     * Display positions listing and manage them
     */
    public function positions(Request $request)
    {
        $perPage = (int) $request->input('per_page', 10);
        if (!in_array($perPage, [5, 10, 25, 50])) {
            $perPage = 10;
        }

        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $positions = Position::with(['department'])
            ->withCount('users')
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();

        return view('departments.positions', compact('positions', 'departments', 'perPage'));
    }

    /**
     * Store a new position
     */
    public function storePosition(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'department_id' => ['nullable', 'exists:departments,id'],
        ], [
            'name.required' => 'กรุณากรอกชื่อตำแหน่งงาน',
            'department_id.exists' => 'แผนกงานที่เลือกไม่ถูกต้อง',
        ]);

        Position::create($validated);

        return redirect()->to(route('departments.positions', [], false))->with('success', 'เพิ่มตำแหน่งงานใหม่เรียบร้อยแล้ว');
    }

    /**
     * Update position details
     */
    public function updatePosition(Request $request, Position $position)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'กรุณากรอกชื่อตำแหน่งงาน',
            'department_id.exists' => 'แผนกงานที่เลือกไม่ถูกต้อง',
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : false;
        $position->update($validated);

        return redirect()->to(route('departments.positions', [], false))->with('success', "แก้ไขข้อมูลตำแหน่งงาน {$position->name} เรียบร้อยแล้ว");
    }

    /**
     * Toggle position active status
     */
    public function togglePositionStatus(Position $position)
    {
        $position->is_active = !$position->is_active;
        $position->save();

        $statusText = $position->is_active ? 'เปิดใช้งาน' : 'ปิดการใช้งาน';
        return redirect()->to(route('departments.positions', [], false))->with('success', "เปลี่ยนสถานะตำแหน่งงาน {$position->name} เป็น {$statusText} เรียบร้อยแล้ว");
    }

    /**
     * Delete a position
     */
    public function destroyPosition(Position $position)
    {
        if ($position->users()->count() > 0) {
            return redirect()->to(route('departments.positions', [], false))->with('error', 'ไม่สามารถลบตำแหน่งนี้ได้ เนื่องจากมีพนักงานดำรงตำแหน่งนี้อยู่');
        }

        $position->delete();

        return redirect()->to(route('departments.positions', [], false))->with('success', 'ลบตำแหน่งงานเรียบร้อยแล้ว');
    }
}
