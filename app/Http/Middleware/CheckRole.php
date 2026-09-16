<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->to(route('login', [], false))->with('error', 'กรุณาเข้าสู่ระบบก่อนใช้งาน');
        }

        $user = Auth::user();

        // ตรวจสอบว่าผู้ใช้ยังอยู่ในสถานะ active หรือไม่
        if ($user->status !== 'active') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->to(route('login', [], false))->with('error', 'บัญชีผู้ใช้นี้ถูกระงับการใช้งาน หรือลาออกแล้ว');
        }

        // หากไม่มีการระบุ role หรือ user มี role ตรงกับที่กำหนด
        if (empty($roles) || in_array($user->role, $roles, true)) {
            return $next($request);
        }

        abort(403, 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้ (Unauthorized access for role: ' . $user->role . ')');
    }
}
