<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ClaimController;

// connect database
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    return view('index');
})->name('index');

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::post('/login-process', function (Request $request) {
    $username = $request->input('username');  
    return redirect()->route('home')->with('user_logged_in', $username);
});

Route::get('/home', function () {
    return view('home');
})->name('home');

Route::get('/logout', function () {
    return redirect()->route('login');
})->name('logout');

Route::get('/add', function () {
    return view('add'); 
})->name('add');


Route::get('/abouts', [AdminController::class, 'abouts'])->name('abouts');

Route::get('/blogs', [AdminController::class, 'blogs'])->name('blogs');


Route::get('/form', [AdminController::class, 'form'])->name('form');

Route::post('/insert', [AdminController::class , 'insert']);

Route::get('/claim_form', [ClaimController::class, 'form'])->name('claim_form');
Route::post('/claim_store', [ClaimController::class, 'insert'])->name('claim_store');


// connect database route
Route::get('/test-db', function () {
    try {
        DB::connection()->getPdo();
        return "เชื่อมต่อฐานข้อมูลสำเร็จ! Database name: " . DB::connection()->getDatabaseName();
    } catch (\Exception $e) {
        return "ไม่สามารถเชื่อมต่อฐานข้อมูลได้: " . $e->getMessage();
    }
});