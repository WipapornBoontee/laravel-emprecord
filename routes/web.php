<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WebhookController;

// *** GitHub Auto-Deploy Webhook *** //
Route::match(['get', 'post'], '/api/github-webhook', [WebhookController::class, 'handle'])->name('github.webhook');
// ********************************* */

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
});