<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * แสดงหน้า Login
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        return view('login');
    }

    /**
     * ประมวลผลการเข้าสู่ระบบ
     * รองรับทั้ง username (emp_code หรือ name หรือ email) และ password
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'username.required' => 'กรุณากรอกชื่อผู้ใช้งาน หรือ รหัสพนักงาน',
            'password.required' => 'กรุณากรอกรหัสผ่าน',
        ]);

        $loginInput = $credentials['username'];
        $password = $credentials['password'];
        $remember = $request->boolean('remember');

        // ค้นหาผู้ใช้งานจาก emp_code, email หรือ name
        $user = User::where('emp_code', $loginInput)
            ->orWhere('email', $loginInput)
            ->orWhere('name', $loginInput)
            ->first();

        // ตรวจสอบรหัสผ่าน
        if ($user && Hash::check($password, $user->password)) {
            // ตรวจสอบสถานะการทำงาน
            if ($user->status !== 'active') {
                return back()
                    ->withInput($request->only('username', 'remember'))
                    ->withErrors([
                        'username' => 'บัญชีผู้ใช้นี้ถูกระงับการใช้งาน หรือสถานะไม่ได้ทำงานแล้ว (Resigned)',
                    ]);
            }

            // บันทึกการล็อกอินด้วย Laravel Auth
            Auth::login($user, $remember);
            $request->session()->regenerate();

            // ส่งตรงไปยังหน้า home เสมอ (ไม่ใช้ intended ป้องกัน 404 จาก URL เก่า)
            return redirect()->route('home')->with('success', 'เข้าสู่ระบบสำเร็จ ยินดีต้อนรับ ' . $user->name);
        }

        return back()
            ->withInput($request->only('username', 'remember'))
            ->withErrors([
                'username' => 'ชื่อผู้ใช้งาน / รหัสพนักงาน หรือรหัสผ่านไม่ถูกต้อง',
            ]);
    }

    /**
     * ออกจากระบบ
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'ออกจากระบบเรียบร้อยแล้ว');
    }
}
