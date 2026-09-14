<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// หน้าแรก - หากล็อกอินแล้วจะไป dashboard หากยังไม่ล็อกอินจะถูกส่งไปที่ login อัตโนมัติ
Route::get('/', function () {
    return redirect()->route('dashboard');
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
        return redirect()->route('dashboard');
    })->name('home');
    
    // Leave Management Routes การยื่นใบลา
    Route::get('/apply-leave/create', [\App\Http\Controllers\LeaveController::class, 'create'])->name('apply-leave.create');
    Route::post('/apply-leave', [\App\Http\Controllers\LeaveController::class, 'store'])->name('apply-leave.store');

    // Profile (Read-only สำหรับพนักงานทั่วไป, Admin/HR ดูของตนเองได้)
    Route::get('/profile', [\App\Http\Controllers\EmployeeController::class, 'profile'])->name('profile');
    Route::get('/employees/{employee}', [\App\Http\Controllers\EmployeeController::class, 'show'])->name('employees.show');

    // Employee & Organization Management (เฉพาะ Admin และ HR เท่านั้น)
    Route::middleware(['role:admin,hr'])->group(function () {
        // จัดการข้อมูลพนักงาน (Admin จัดการได้ทุกคนรวมทั้ง HR, HR จัดการได้เฉพาะ employee)
        Route::get('/employees', [\App\Http\Controllers\EmployeeController::class, 'index'])->name('employees.index');
        Route::get('/employees/create', [\App\Http\Controllers\EmployeeController::class, 'create'])->name('employees.create');
        Route::post('/employees', [\App\Http\Controllers\EmployeeController::class, 'store'])->name('employees.store');
        Route::get('/employees/{employee}/edit', [\App\Http\Controllers\EmployeeController::class, 'edit'])->name('employees.edit');
        Route::put('/employees/{employee}', [\App\Http\Controllers\EmployeeController::class, 'update'])->name('employees.update');
        Route::delete('/employees/{employee}', [\App\Http\Controllers\EmployeeController::class, 'destroy'])->name('employees.destroy');

        // จัดการโครงสร้างองค์กร (แผนก และ ตำแหน่งงาน)
        Route::get('/departments', [\App\Http\Controllers\DepartmentController::class, 'index'])->name('departments.index');
        Route::post('/departments', [\App\Http\Controllers\DepartmentController::class, 'store'])->name('departments.store');
        Route::delete('/departments/{department}', [\App\Http\Controllers\DepartmentController::class, 'destroy'])->name('departments.destroy');
        Route::get('/departments/positions', [\App\Http\Controllers\DepartmentController::class, 'positions'])->name('departments.positions');
        Route::post('/departments/positions', [\App\Http\Controllers\DepartmentController::class, 'storePosition'])->name('departments.positions.store');
        Route::delete('/departments/positions/{position}', [\App\Http\Controllers\DepartmentController::class, 'destroyPosition'])->name('departments.positions.destroy');
    });
});