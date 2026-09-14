<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// หน้าแรก - หากล็อกอินแล้วจะไป dashboard หากยังไม่ล็อกอินจะถูกส่งไปที่ login อัตโนมัติ
Route::get('/', function () {
    return redirect()->to(route('dashboard', [], false));
})->name('index');

// Authentication Routes (auth/login)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes (ต้องเข้าสู่ระบบก่อนเสมอ)
Route::middleware(['auth'])->group(function () {
    // Dashboard & Home (resources/views/dashboard/home.blade.php)
    Route::get('/dashboard', function () {
        return view('dashboard.home');
    })->name('dashboard');

    // Route alias สำหรับ /home ให้เชื่อมโยงกับ dashboard
    Route::get('/home', function () {
        return redirect()->to(route('dashboard', [], false));
    })->name('home');
    
    // Leave Management Routes (ระบบจัดการการลาสำหรับพนักงานทุกคน)
    Route::get('/leaves', [\App\Http\Controllers\LeaveController::class, 'index'])->name('leaves.index');
    Route::get('/leaves/create', [\App\Http\Controllers\LeaveController::class, 'create'])->name('leaves.create');
    Route::post('/leaves', [\App\Http\Controllers\LeaveController::class, 'store'])->name('leaves.store');
    Route::delete('/leaves/{leaveRequest}/cancel', [\App\Http\Controllers\LeaveController::class, 'cancel'])->name('leaves.cancel');
    Route::get('/leaves/balances', [\App\Http\Controllers\LeaveController::class, 'balances'])->name('leaves.balances');

    // Route alias สำหรับ /apply-leave
    Route::get('/apply-leave/create', [\App\Http\Controllers\LeaveController::class, 'create'])->name('apply-leave.create');
    Route::post('/apply-leave', [\App\Http\Controllers\LeaveController::class, 'store'])->name('apply-leave.store');

    // Time Attendance Routes (ระบบบันทึกเวลาทำงาน)
    Route::get('/attendance', function () {
        return redirect()->route('attendances.checkin', [], false);
    });
    Route::get('/attendance/checkin', [\App\Http\Controllers\AttendanceController::class, 'checkinView'])->name('attendances.checkin');
    Route::post('/attendance/checkin', [\App\Http\Controllers\AttendanceController::class, 'checkIn'])->name('attendances.checkin.process');
    Route::post('/attendance/checkout', [\App\Http\Controllers\AttendanceController::class, 'checkOut'])->name('attendances.checkout.process');
    Route::get('/attendance/my-history', [\App\Http\Controllers\AttendanceController::class, 'myHistory'])->name('attendances.my-history');

    // Profile (Read-only สำหรับพนักงานทั่วไป, Admin/HR ดูของตนเองได้)
    Route::get('/profile', [\App\Http\Controllers\EmployeeController::class, 'profile'])->name('profile');

    // Employee & Organization Management (เฉพาะ Admin และ HR เท่านั้น)
    Route::middleware(['role:admin,hr'])->group(function () {
        // จัดการข้อมูลพนักงาน (Admin จัดการได้ทุกคนรวมทั้ง HR, HR จัดการได้เฉพาะ employee)
        Route::get('/employees', [\App\Http\Controllers\EmployeeController::class, 'index'])->name('employees.index');
        Route::get('/employees/create', [\App\Http\Controllers\EmployeeController::class, 'create'])->name('employees.create');
        Route::post('/employees', [\App\Http\Controllers\EmployeeController::class, 'store'])->name('employees.store');
        Route::get('/employees/{employee}/edit', [\App\Http\Controllers\EmployeeController::class, 'edit'])->where('employee', '[0-9]+')->name('employees.edit');
        Route::put('/employees/{employee}', [\App\Http\Controllers\EmployeeController::class, 'update'])->where('employee', '[0-9]+')->name('employees.update');
        Route::delete('/employees/{employee}', [\App\Http\Controllers\EmployeeController::class, 'destroy'])->where('employee', '[0-9]+')->name('employees.destroy');

        // จัดการโครงสร้างองค์กร (แผนก และ ตำแหน่งงาน)
        Route::get('/departments', [\App\Http\Controllers\DepartmentController::class, 'index'])->name('departments.index');
        Route::post('/departments', [\App\Http\Controllers\DepartmentController::class, 'store'])->name('departments.store');
        Route::delete('/departments/{department}', [\App\Http\Controllers\DepartmentController::class, 'destroy'])->name('departments.destroy');
        Route::get('/departments/positions', [\App\Http\Controllers\DepartmentController::class, 'positions'])->name('departments.positions');
        Route::post('/departments/positions', [\App\Http\Controllers\DepartmentController::class, 'storePosition'])->name('departments.positions.store');
        Route::delete('/departments/positions/{position}', [\App\Http\Controllers\DepartmentController::class, 'destroyPosition'])->name('departments.positions.destroy');

        // ศูนย์อนุมัติคำขอลา (Leave Approvals & Quota Management)
        Route::get('/leaves-approvals', [\App\Http\Controllers\LeaveApprovalController::class, 'index'])->name('leaves.approvals');
        Route::post('/leaves-approvals/{leaveRequest}/approve', [\App\Http\Controllers\LeaveApprovalController::class, 'approve'])->name('leaves.approvals.approve');
        Route::post('/leaves-approvals/{leaveRequest}/reject', [\App\Http\Controllers\LeaveApprovalController::class, 'reject'])->name('leaves.approvals.reject');

        // จัดการประเภทการลาและโควตาเริ่มต้น
        Route::get('/leaves-types', [\App\Http\Controllers\LeaveTypeController::class, 'index'])->name('leaves.types.index');
        Route::post('/leaves-types', [\App\Http\Controllers\LeaveTypeController::class, 'store'])->name('leaves.types.store');
        Route::put('/leaves-types/{type}', [\App\Http\Controllers\LeaveTypeController::class, 'update'])->name('leaves.types.update');
        Route::delete('/leaves-types/{type}', [\App\Http\Controllers\LeaveTypeController::class, 'destroy'])->name('leaves.types.destroy');

        // สรุปรายงานเวลาทำงานองค์กร (Organization Attendance Report)
        Route::get('/attendance/report', [\App\Http\Controllers\AttendanceController::class, 'report'])->name('attendances.report');
    });

    // หน้ารายละเอียดโปรไฟล์พนักงาน (เข้าถึงได้ตามสิทธิ์ที่ Controller ตรวจสอบ)
    Route::get('/employees/{employee}', [\App\Http\Controllers\EmployeeController::class, 'show'])
        ->where('employee', '[0-9]+')
        ->name('employees.show');
});