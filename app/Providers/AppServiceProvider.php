<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $forwardedProto = request()->header('x-forwarded-proto') ?? request()->server('HTTP_X_FORWARDED_PROTO');
        if ($forwardedProto === 'https' || str_contains(request()->header('cf-visitor', ''), 'https')) {
            URL::forceScheme('https');
        }

        // รองรับ Cloudflare Worker หรือ Reverse Proxy ให้ใช้โดเมนเดียวกับหน้าเว็บที่ผู้ใช้เปิดอยู่
        $forwardedHost = request()->header('x-forwarded-host') ?? request()->server('HTTP_X_FORWARDED_HOST');
        if (!empty($forwardedHost)) {
            $scheme = ($forwardedProto === 'https' || str_contains(request()->header('cf-visitor', ''), 'https')) ? 'https' : request()->getScheme();
            URL::forceRootUrl("{$scheme}://{$forwardedHost}");
        }
    }
}
