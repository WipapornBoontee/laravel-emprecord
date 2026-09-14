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
    public function index()
    {
        $departments = Department::withCount('users')->orderBy('name')->get();
        return view('departments.index', compact('departments'));
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

        return redirect()->route('departments.index')->with('success', 'เพิ่มแผนกใหม่เรียบร้อยแล้ว');
    }

    /**
     * Delete a department
     */
    public function destroy(Department $department)
    {
        if ($department->users()->count() > 0) {
            return redirect()->route('departments.index')->with('error', 'ไม่สามารถลบแผนกนี้ได้ เนื่องจากมีพนักงานสังกัดอยู่ในแผนกนี้');
        }

        $department->delete();

        return redirect()->route('departments.index')->with('success', 'ลบแผนกเรียบร้อยแล้ว');
    }

    /**
     * Display positions listing and manage them
     */
    public function positions()
    {
        $positions = Position::withCount('users')->orderBy('name')->get();
        return view('departments.positions', compact('positions'));
    }

    /**
     * Store a new position
     */
    public function storePosition(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:positions,name'],
        ], [
            'name.required' => 'กรุณากรอกชื่อตำแหน่งงาน',
            'name.unique' => 'ชื่อตำแหน่งนี้มีในระบบแล้ว',
        ]);

        Position::create($validated);

        return redirect()->route('departments.positions')->with('success', 'เพิ่มตำแหน่งงานใหม่เรียบร้อยแล้ว');
    }

    /**
     * Delete a position
     */
    public function destroyPosition(Position $position)
    {
        if ($position->users()->count() > 0) {
            return redirect()->route('departments.positions')->with('error', 'ไม่สามารถลบตำแหน่งนี้ได้ เนื่องจากมีพนักงานดำรงตำแหน่งนี้อยู่');
        }

        $position->delete();

        return redirect()->route('departments.positions')->with('success', 'ลบตำแหน่งงานเรียบร้อยแล้ว');
    }
}
