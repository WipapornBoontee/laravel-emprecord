<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// หน้าแรก - หากล็อกอินอยู่แล้วให้ไปที่ home หากยังไม่ได้ล็อกอินให้ไปที่หน้า login เสมอ
Route::get('/', function () {
    return redirect()->route('home');
})->name('index');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes (ต้องเข้าสู่ระบบก่อนเสมอ)
Route::middleware(['auth'])->group(function () {
    Route::get('/home', function () {
        return view('home');
    })->name('home');
});