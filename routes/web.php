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
    
    // Leave Management Routes
    Route::get('/apply-leave/create', [\App\Http\Controllers\LeaveController::class, 'create'])->name('apply-leave.create');
    Route::post('/apply-leave', [\App\Http\Controllers\LeaveController::class, 'store'])->name('apply-leave.store');
    // Route::get('/apply-leave', [\App\Http\Controllers\LeaveController::class, 'index'])->name('apply-leave.index');
    // Route::get('/apply-leave/{id}/edit', [\App\Http\Controllers\LeaveController::class, 'edit'])->name('apply-leave.edit');
    // Route::put('/apply-leave/{id}', [\App\Http\Controllers\LeaveController::class, 'update'])->name('apply-leave.update');
    // Route::delete('/apply-leave/{id}', [\App\Http\Controllers\LeaveController::class, 'destroy'])->name('apply-leave.destroy');


});