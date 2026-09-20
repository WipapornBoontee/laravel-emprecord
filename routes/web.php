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
        return redirect()->to(route('attendances.checkin', [], false));
    });
    Route::get('/attendance/checkin', [\App\Http\Controllers\AttendanceController::class, 'checkinView'])->name('attendances.checkin');
    Route::post('/attendance/checkin', [\App\Http\Controllers\AttendanceController::class, 'checkIn'])->name('attendances.checkin.process');
    Route::post('/attendance/checkout', [\App\Http\Controllers\AttendanceController::class, 'checkOut'])->name('attendances.checkout.process');
    Route::get('/attendance/my-history', [\App\Http\Controllers\AttendanceController::class, 'myHistory'])->name('attendances.my-history');
    Route::post('/attendance/adjustments', [\App\Http\Controllers\AttendanceAdjustmentController::class, 'store'])->name('attendances.adjustments.store');

    // Overtime Routes (ระบบล่วงเวลา สำหรับพนักงาน)
    Route::get('/overtime/show', [\App\Http\Controllers\OverTimeController::class, 'overtime'])->name('overtime.show');
    Route::get('/overtime/create', [\App\Http\Controllers\OverTimeController::class, 'create'])->name('overtime.create');
    Route::post('/overtime/request', [\App\Http\Controllers\OverTimeController::class, 'overtimeRequest'])->name('overtime.request'); // กรณีทำฟอร์มขอ OT
    Route::post('/overtime/reason', [\App\Http\Controllers\OverTimeController::class, 'submitEarlyCheckoutReason'])->name('overtime.reason');
    
    // Profile (Read-only สำหรับพนักงานทั่วไป, Admin/HR ดูของตนเองได้)
    Route::get('/profile', [\App\Http\Controllers\EmployeeController::class, 'profile'])->name('profile');
    

    // Employee & Organization Management (เฉพาะ Admin และ HR เท่านั้น)
    Route::middleware(['role:admin,hr'])->group(function () {
        
        // จัดการคำขอ OT โดย HR
        Route::get('/overtime', [\App\Http\Controllers\OverTimeController::class, 'index'])->name('overtime.index');
        Route::post('/overtime/approve/{id}', [\App\Http\Controllers\OverTimeController::class, 'approve'])->name('overtime.approve');
        Route::post('/overtime/reject/{id}', [\App\Http\Controllers\OverTimeController::class, 'reject'])->name('overtime.reject');
        Route::post('/overtime/bulk-approve', [\App\Http\Controllers\OverTimeController::class, 'bulkApprove'])->name('overtime.bulkApprove');

        // ตั้งค่าเงินเดือน
        Route::get('/settings/payroll', [\App\Http\Controllers\PayrollSettingController::class, 'index'])->name('settings.payroll');
        Route::post('/settings/payroll', [\App\Http\Controllers\PayrollSettingController::class, 'update'])->name('settings.payroll.update');
        
        // จัดการข้อมูลพนักงาน (Admin จัดการได้ทุกคนรวมทั้ง HR, HR จัดการได้เฉพาะ employee)
        Route::get('/employees', [\App\Http\Controllers\EmployeeController::class, 'index'])->name('employees.index');
        Route::get('/employees/create', [\App\Http\Controllers\EmployeeController::class, 'create'])->name('employees.create');
        Route::post('/employees', [\App\Http\Controllers\EmployeeController::class, 'store'])->name('employees.store');
        Route::get('/employees/{employee}/edit', [\App\Http\Controllers\EmployeeController::class, 'edit'])->where('employee', '[0-9]+')->name('employees.edit');
        Route::put('/employees/{employee}', [\App\Http\Controllers\EmployeeController::class, 'update'])->where('employee', '[0-9]+')->name('employees.update');
        Route::delete('/employees/{employee}', [\App\Http\Controllers\EmployeeController::class, 'destroy'])->where('employee', '[0-9]+')->name('employees.destroy');

        // จัดการแผนกและตำแหน่งงานสำหรับบันทึกข้อมูลพนักงาน (Admin และ HR จัดการได้)
        Route::get('/departments', [\App\Http\Controllers\DepartmentController::class, 'index'])->name('departments.index');
        Route::post('/departments', [\App\Http\Controllers\DepartmentController::class, 'store'])->name('departments.store');
        Route::put('/departments/{department}', [\App\Http\Controllers\DepartmentController::class, 'update'])->name('departments.update');
        Route::patch('/departments/{department}/toggle-status', [\App\Http\Controllers\DepartmentController::class, 'toggleStatus'])->name('departments.toggleStatus');
        Route::delete('/departments/{department}', [\App\Http\Controllers\DepartmentController::class, 'destroy'])->name('departments.destroy');

        Route::get('/departments/positions', [\App\Http\Controllers\DepartmentController::class, 'positions'])->name('departments.positions');
        Route::post('/departments/positions', [\App\Http\Controllers\DepartmentController::class, 'storePosition'])->name('departments.positions.store');
        Route::put('/departments/positions/{position}', [\App\Http\Controllers\DepartmentController::class, 'updatePosition'])->name('departments.positions.update');
        Route::patch('/departments/positions/{position}/toggle-status', [\App\Http\Controllers\DepartmentController::class, 'togglePositionStatus'])->name('departments.positions.toggleStatus');
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

        // จัดการวันหยุดบริษัทและวันหยุดนักขัตฤกษ์ (Company Holidays)
        Route::get('/settings/holidays', [\App\Http\Controllers\CompanyHolidayController::class, 'index'])->name('settings.holidays.index');
        Route::post('/settings/holidays', [\App\Http\Controllers\CompanyHolidayController::class, 'store'])->name('settings.holidays.store');
        Route::put('/settings/holidays/{holiday}', [\App\Http\Controllers\CompanyHolidayController::class, 'update'])->name('settings.holidays.update');
        Route::delete('/settings/holidays/{holiday}', [\App\Http\Controllers\CompanyHolidayController::class, 'destroy'])->name('settings.holidays.destroy');

        // สรุปรายงานเวลาทำงานองค์กร (Organization Attendance Report & Monthly Payroll Export)
        Route::get('/attendance/report', [\App\Http\Controllers\AttendanceController::class, 'report'])->name('attendances.report');
        Route::get('/attendance/report/print', [\App\Http\Controllers\AttendanceController::class, 'printReport'])->name('attendances.report.print');
        Route::get('/attendance/report/export-monthly', [\App\Http\Controllers\AttendanceController::class, 'exportMonthlySummaryCsv'])->name('attendances.report.export-monthly');

        // ศูนย์พิจารณาอนุมัติคำขอปรับเวลาทำงานย้อนหลัง (Attendance Adjustments)
        Route::get('/attendance/adjustments', [\App\Http\Controllers\AttendanceAdjustmentController::class, 'index'])->name('attendances.adjustments.index');
        Route::post('/attendance/adjustments/{adjustment}/approve', [\App\Http\Controllers\AttendanceAdjustmentController::class, 'approve'])->name('attendances.adjustments.approve');
        Route::post('/attendance/adjustments/{adjustment}/reject', [\App\Http\Controllers\AttendanceAdjustmentController::class, 'reject'])->name('attendances.adjustments.reject');
        // เครื่องมือรันคำสั่ง Migrate & Seed สำหรับ Admin บน Production/Server
        Route::get('/system/run-migrate', function () {
            try {
                \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
                $migrateOutput = \Illuminate\Support\Facades\Artisan::output();

                // ถ้ามีการส่ง ?seed=1 ให้รัน seeder ด้วย
                $seedOutput = '';
                if (request()->has('seed')) {
                    \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'SystemEnhancementSeeder', '--force' => true]);
                    $seedOutput = "\n" . \Illuminate\Support\Facades\Artisan::output();
                }

                $totalOutput = trim($migrateOutput . $seedOutput) ?: "No migration output (Database might be already up to date).";

                return response()->make("<div style='font-family: sans-serif; padding: 24px; background: #0f172a; color: #f8fafc; border-radius: 8px; margin: 20px auto; max-width: 800px; box-shadow: 0 10px 25px rgba(0,0,0,0.3);'><h2 style='color: #38bdf8; margin-top: 0;'>🚀 Database Migration & Seed Result</h2><pre style='background: #1e293b; padding: 16px; border-radius: 6px; color: #a5f3fc; border: 1px solid #334155; white-space: pre-wrap; word-break: break-all;'>" . htmlspecialchars($totalOutput) . "</pre><div style='margin-top: 15px;'><a href='" . url('/system/run-migrate?seed=1') . "' style='display: inline-block; margin-right: 10px; padding: 8px 16px; background: #059669; color: #fff; text-decoration: none; border-radius: 6px; font-weight: 500;'>🌱 Run Seeder (วันหยุด + ตัวอย่างคำขอปรับเวลา)</a><a href='" . url('/') . "' style='display: inline-block; padding: 8px 16px; background: #2563eb; color: #fff; text-decoration: none; border-radius: 6px; font-weight: 500;'>← กลับหน้าหลัก</a></div></div>", 200, ['Content-Type' => 'text/html; charset=UTF-8']);
            } catch (\Throwable $e) {
                return response()->make("<div style='font-family: sans-serif; padding: 24px; background: #450a0a; color: #fecaca; border-radius: 8px; margin: 20px auto; max-width: 800px;'><h2 style='color: #f87171; margin-top: 0;'>❌ Migration Error</h2><pre style='background: #1c1917; padding: 16px; border-radius: 6px; color: #fca5a5; white-space: pre-wrap;'>" . htmlspecialchars($e->getMessage()) . "</pre></div>", 500, ['Content-Type' => 'text/html; charset=UTF-8']);
            }
        })->name('system.run-migrate');
    });

    // หน้ารายละเอียดโปรไฟล์พนักงาน (เข้าถึงได้ตามสิทธิ์ที่ Controller ตรวจสอบ)
    Route::get('/employees/{employee}', [\App\Http\Controllers\EmployeeController::class, 'show'])
        ->where('employee', '[0-9]+')
        ->name('employees.show');

    
    
});