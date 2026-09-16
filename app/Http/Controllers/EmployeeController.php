<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\LeaveBalance;
use App\Models\LeaveType;
use App\Models\Position;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    /**
     * Display a listing of employees (Admin & HR only)
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $departmentId = $request->input('department_id');
        $role = $request->input('role');
        $status = $request->input('status');

        $query = User::with(['department', 'position']);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('emp_code', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if (!empty($departmentId)) {
            $query->where('department_id', $departmentId);
        }

        if (!empty($role)) {
            $query->where('role', $role);
        }

        if (!empty($status)) {
            $query->where('status', $status);
        }

        $employees = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();
        $departments = Department::orderBy('name')->get();
        $positions = Position::orderBy('name')->get();

        // สรุปสถิติเบื้องต้น
        $totalEmployees = User::count();
        $activeEmployees = User::where('status', 'active')->count();
        $resignedEmployees = User::where('status', 'resigned')->count();

        return view('employees.index', compact(
            'employees',
            'departments',
            'positions',
            'search',
            'departmentId',
            'role',
            'status',
            'totalEmployees',
            'activeEmployees',
            'resignedEmployees'
        ));
    }

    /**
     * Show the form for creating a new employee
     */
    public function create()
    {
        $departments = Department::orderBy('name')->get();
        $positions = Position::orderBy('name')->get();

        // หากผู้ใช้คือ HR จะจำกัดให้เลือกได้แค่ role: employee
        $allowedRoles = Auth::user()->isAdmin() 
            ? ['admin' => 'ผู้ดูแลระบบ (Admin)', 'hr' => 'ฝ่ายบุคคล (HR)', 'employee' => 'พนักงานทั่วไป (Employee)']
            : ['employee' => 'พนักงานทั่วไป (Employee)'];

        return view('employees.create', compact('departments', 'positions', 'allowedRoles'));
    }

    /**
     * Store a newly created employee in storage
     */
    public function store(Request $request)
    {
        $currentUser = Auth::user();

        // ตรวจสอบสิทธิ์การกำหนด Role (HR กำหนดได้เฉพาะ employee)
        $roleRules = ['required'];
        if ($currentUser->isAdmin()) {
            $roleRules[] = Rule::in(['admin', 'hr', 'employee']);
        } else {
            $roleRules[] = Rule::in(['employee']);
        }

        $validated = $request->validate([
            'emp_code' => ['required', 'string', 'max:20', 'unique:users,emp_code'],
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:100', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'role' => $roleRules,
            'department_id' => ['nullable', 'exists:departments,id'],
            'position_id' => ['nullable', 'exists:positions,id'],
            'start_date' => ['nullable', 'date'],
            'status' => ['required', Rule::in(['active', 'resigned'])],
            'salary' => ['required', 'numeric', 'min:0'],
        ], [
            'emp_code.required' => 'กรุณาระบุรหัสพนักงาน',
            'emp_code.unique' => 'รหัสพนักงานนี้มีในระบบแล้ว',
            'name.required' => 'กรุณากรอกชื่อ-นามสกุล',
            'email.required' => 'กรุณากรอกอีเมล',
            'email.unique' => 'อีเมลนี้มีในระบบแล้ว',
            'password.required' => 'กรุณากำหนดรหัสผ่าน',
            'password.min' => 'รหัสผ่านต้องมีความยาวอย่างน้อย 6 ตัวอักษร',
            'role.required' => 'กรุณาเลือกระดับสิทธิ์',
            'role.in' => 'ระดับสิทธิ์ที่เลือกไม่ถูกต้อง หรือคุณไม่มีสิทธิ์กำหนดบทบาทนี้',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['department_id'] = !empty($validated['department_id']) ? $validated['department_id'] : null;
        $validated['position_id'] = !empty($validated['position_id']) ? $validated['position_id'] : null;
        $validated['start_date'] = !empty($validated['start_date']) ? $validated['start_date'] : null;
        $validated['phone'] = !empty($validated['phone']) ? $validated['phone'] : null;
        $validated['address'] = !empty($validated['address']) ? $validated['address'] : null;

        // สร้างข้อมูลพนักงาน
        $employee = User::create($validated);

        // จัดสรรโควตาวันลาเริ่มต้นประจำปีปัจจุบันให้พนักงานใหม่โดยอัตโนมัติ
        $currentYear = (int) date('Y');
        $leaveTypes = LeaveType::all();
        foreach ($leaveTypes as $type) {
            LeaveBalance::firstOrCreate(
                [
                    'user_id' => $employee->id,
                    'leave_type_id' => $type->id,
                    'year' => $currentYear,
                ],
                [
                    'total_days' => $type->default_days,
                    'used_days' => 0.0,
                    'remaining_days' => $type->default_days,
                ]
            );
        }

        // บันทึกเงินเดือนเริ่มต้นลงในตาราง salary_emps
        \App\Models\SalaryEmp::create([
            'user_id' => $employee->id,
            'salary' => $request->input('salary'),
        ]);

        return redirect()->to(route('employees.index', [], false))->with('success', "เพิ่มพนักงาน {$employee->name} สำเร็จเรียบร้อยแล้ว");
    }

    /**
     * Display the specified employee profile (Read-only for regular employee)
     */
    public function show(User $employee)
    {
        $currentUser = Auth::user();

        // พนักงานทั่วไปสามารถดูได้เฉพาะโปรไฟล์ของตัวเองเท่านั้น
        if ($currentUser->isEmployee() && $currentUser->id !== $employee->id) {
            abort(403, 'คุณสามารถดูได้เฉพาะข้อมูลโปรไฟล์ของตนเองเท่านั้น');
        }

        $employee->load(['department', 'position', 'leaveBalances.leaveType']);

        // สิทธิ์วันลาของปีปัจจุบัน
        $currentYear = (int) date('Y');
        $leaveBalances = LeaveBalance::with('leaveType')
            ->where('user_id', $employee->id)
            ->where('year', $currentYear)
            ->get();

        // ประวัติการลาล่าสุด 5 รายการ
        $recentLeaves = $employee->leaveRequests()
            ->with('leaveType')
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get();

        // ประวัติการลงเวลาล่าสุด 5 รายการ
        $recentAttendances = $employee->attendances()
            ->orderBy('date', 'desc')
            ->limit(5)
            ->get();

        return view('employees.show', compact('employee', 'leaveBalances', 'recentLeaves', 'recentAttendances'));
    }

    /**
     * Show the profile of currently logged-in user (Helper for /profile)
     */
    public function profile()
    {
        return $this->show(Auth::user());
    }

    /**
     * Show the form for editing the specified employee
     */
    public function edit(User $employee)
    {
        $currentUser = Auth::user();

        // พนักงานทั่วไปไม่มีสิทธิ์เข้าถึงฟอร์มแก้ไขเด็ดขาด
        if ($currentUser->isEmployee()) {
            abort(403, 'พนักงานไม่ได้รับอนุญาตให้แก้ไขข้อมูลส่วนตัวด้วยตนเอง กรุณาติดต่อ HR หรือ Admin');
        }

        // ข้อกำหนดสำคัญ: HR ไม่สามารถแก้ไขข้อมูลของ Admin ได้
        if ($currentUser->isHr() && $employee->isAdmin()) {
            abort(403, 'ฝ่ายบุคคล (HR) ไม่มีสิทธิ์แก้ไขข้อมูลของผู้ดูแลระบบ (Admin)');
        }

        $departments = Department::orderBy('name')->get();
        $positions = Position::orderBy('name')->get();

        // หากผู้ใช้คือ HR จะไม่อนุญาตให้เปลี่ยนบทบาทเป้าหมายเป็น Admin
        $allowedRoles = $currentUser->isAdmin()
            ? ['admin' => 'ผู้ดูแลระบบ (Admin)', 'hr' => 'ฝ่ายบุคคล (HR)', 'employee' => 'พนักงานทั่วไป (Employee)']
            : ['hr' => 'ฝ่ายบุคคล (HR)', 'employee' => 'พนักงานทั่วไป (Employee)'];

        return view('employees.edit', compact('employee', 'departments', 'positions', 'allowedRoles'));
    }

    /**
     * Update the specified employee in storage
     */
    public function update(Request $request, User $employee)
    {
        $currentUser = Auth::user();

        // พนักงานทั่วไปไม่มีสิทธิ์แก้ไขเด็ดขาด
        if ($currentUser->isEmployee()) {
            abort(403, 'พนักงานไม่ได้รับอนุญาตให้แก้ไขข้อมูลส่วนตัวด้วยตนเอง');
        }

        // ข้อกำหนดสำคัญ: HR ไม่สามารถแก้ไขข้อมูลของ Admin ได้
        if ($currentUser->isHr() && $employee->isAdmin()) {
            abort(403, 'ฝ่ายบุคคล (HR) ไม่มีสิทธิ์แก้ไขข้อมูลของผู้ดูแลระบบ (Admin)');
        }

        // กฎการตรวจสอบ Role
        $roleRules = ['required'];
        if ($currentUser->isAdmin()) {
            $roleRules[] = Rule::in(['admin', 'hr', 'employee']);
        } else {
            // HR ห้ามเปลี่ยนสิทธิ์ใครเป็น admin
            $roleRules[] = Rule::in(['hr', 'employee']);
        }

        $validated = $request->validate([
            'emp_code' => ['required', 'string', 'max:20', Rule::unique('users', 'emp_code')->ignore($employee->id)],
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:100', Rule::unique('users', 'email')->ignore($employee->id)],
            'password' => ['nullable', 'string', 'min:6'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'role' => $roleRules,
            'department_id' => ['nullable', 'exists:departments,id'],
            'position_id' => ['nullable', 'exists:positions,id'],
            'start_date' => ['nullable', 'date'],
            'status' => ['required', Rule::in(['active', 'resigned'])],
        ], [
            'emp_code.required' => 'กรุณาระบุรหัสพนักงาน',
            'emp_code.unique' => 'รหัสพนักงานนี้มีผู้ใช้งานแล้ว',
            'name.required' => 'กรุณากรอกชื่อ-นามสกุล',
            'email.required' => 'กรุณากรอกอีเมล',
            'email.unique' => 'อีเมลนี้มีผู้ใช้งานแล้ว',
            'password.min' => 'รหัสผ่านใหม่ต้องมีความยาวอย่างน้อย 6 ตัวอักษร',
            'role.required' => 'กรุณาเลือกระดับสิทธิ์',
            'role.in' => 'ระดับสิทธิ์ที่เลือกไม่ถูกต้อง หรือคุณไม่มีสิทธิ์กำหนดบทบาทนี้',
        ]);

        $validated['department_id'] = !empty($validated['department_id']) ? $validated['department_id'] : null;
        $validated['position_id'] = !empty($validated['position_id']) ? $validated['position_id'] : null;
        $validated['start_date'] = !empty($validated['start_date']) ? $validated['start_date'] : null;
        $validated['phone'] = !empty($validated['phone']) ? $validated['phone'] : null;
        $validated['address'] = !empty($validated['address']) ? $validated['address'] : null;

        // อัปเดตรหัสผ่านเฉพาะเมื่อมีการกรอกค่าใหม่
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $employee->update($validated);

        return redirect()->to(route('employees.index', [], false))->with('success', "อัปเดตข้อมูลพนักงาน {$employee->name} สำเร็จเรียบร้อยแล้ว");
    }

    /**
     * Remove the specified employee from storage
     */
    public function destroy(User $employee)
    {
        $currentUser = Auth::user();

        // พนักงานทั่วไปไม่มีสิทธิ์ลบ
        if ($currentUser->isEmployee()) {
            abort(403, 'คุณไม่มีสิทธิ์ลบข้อมูลพนักงาน');
        }

        // ป้องกันไม่ให้ลบบัญชีตัวเอง
        if ($currentUser->id === $employee->id) {
            return redirect()->to(route('employees.index', [], false))->with('error', 'คุณไม่สามารถลบบัญชีของตนเองได้');
        }

        // ข้อกำหนดสำคัญ: HR ไม่สามารถลบ Admin ได้
        if ($currentUser->isHr() && $employee->isAdmin()) {
            abort(403, 'ฝ่ายบุคคล (HR) ไม่มีสิทธิ์ลบข้อมูลของผู้ดูแลระบบ (Admin)');
        }

        $name = $employee->name;
        $employee->delete();

        return redirect()->to(route('employees.index', [], false))->with('success', "ลบข้อมูลพนักงาน {$name} ออกจากระบบเรียบร้อยแล้ว");
    }
}
