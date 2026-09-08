<!DOCTYPE html>
<html lang="th" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>เข้าสู่ระบบ | LV-PROJECT</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts: Plus Jakarta Sans & Prompt -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Anti-flicker Theme Script -->
    <script>
        (function () {
            const savedTheme = localStorage.getItem('wb_theme') || 'dark';
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
        })();
    </script>

    <style>
        :root[data-bs-theme="dark"] {
            --primary-gradient: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);
            --accent-color: #6366f1;
            --accent-glow: rgba(99, 102, 241, 0.25);
            --bg-color: #090d16;
            --surface-bg: rgba(22, 30, 49, 0.75);
            --surface-border: rgba(255, 255, 255, 0.08);
            --input-bg: rgba(13, 19, 33, 0.7);
            --input-border: rgba(255, 255, 255, 0.12);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --btn-text: #ffffff;
        }

        :root[data-bs-theme="light"] {
            --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
            --accent-color: #4f46e5;
            --accent-glow: rgba(79, 70, 229, 0.2);
            --bg-color: #f1f5f9;
            --surface-bg: rgba(255, 255, 255, 0.9);
            --surface-border: rgba(0, 0, 0, 0.08);
            --input-bg: #ffffff;
            --input-border: #cbd5e1;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --btn-text: #ffffff;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', 'Prompt', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-image: 
                radial-gradient(at 15% 15%, rgba(99, 102, 241, 0.15) 0px, transparent 45%),
                radial-gradient(at 85% 85%, rgba(129, 140, 248, 0.12) 0px, transparent 45%);
            background-attachment: fixed;
            padding: 24px;
            transition: background-color 0.3s ease, color 0.3s ease;
            position: relative;
        }

        .auth-container {
            width: 100%;
            max-width: 440px;
            animation: fadeIn 0.4s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .auth-card {
            background: var(--surface-bg);
            border: 1px solid var(--surface-border);
            border-radius: 28px;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            padding: 40px 36px;
        }

        .brand-logo-badge {
            width: 54px;
            height: 54px;
            border-radius: 16px;
            background: var(--primary-gradient);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            box-shadow: 0 10px 20px -5px rgba(99, 102, 241, 0.5);
            margin: 0 auto 20px auto;
        }

        .auth-title {
            font-weight: 700;
            font-size: 1.65rem;
            color: var(--text-main);
            letter-spacing: -0.5px;
        }

        .auth-subtitle {
            color: var(--text-muted);
            font-size: 0.88rem;
        }

        .form-label-custom {
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 6px;
            letter-spacing: 0.2px;
        }

        .input-group-custom {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            color: var(--text-muted);
            font-size: 1.05rem;
            pointer-events: none;
            z-index: 5;
            transition: color 0.2s ease;
        }

        .form-control-pro {
            width: 100%;
            background-color: var(--input-bg);
            border: 1px solid var(--input-border);
            color: var(--text-main);
            border-radius: 14px;
            padding: 13px 16px 13px 44px;
            font-size: 0.92rem;
            transition: all 0.2s ease;
        }

        .form-control-pro:focus {
            outline: none;
            border-color: var(--accent-color);
            background-color: var(--input-bg);
            color: var(--text-main);
            box-shadow: 0 0 0 4px var(--accent-glow);
        }

        .form-control-pro:focus + .input-icon,
        .input-group-custom:focus-within .input-icon {
            color: var(--accent-color);
        }

        .form-control-pro::placeholder {
            color: var(--text-muted);
            opacity: 0.65;
        }

        .btn-auth-submit {
            background: var(--primary-gradient);
            border: none;
            border-radius: 14px;
            padding: 14px 20px;
            color: var(--btn-text);
            font-weight: 600;
            font-size: 0.98rem;
            transition: all 0.2s ease;
            box-shadow: 0 8px 20px -4px rgba(99, 102, 241, 0.4);
            cursor: pointer;
        }

        .btn-auth-submit:hover {
            opacity: 0.95;
            transform: translateY(-2px);
            box-shadow: 0 12px 24px -4px rgba(99, 102, 241, 0.5);
            color: var(--btn-text);
        }

        .btn-auth-submit:active {
            transform: translateY(0);
        }

        .theme-toggle-fixed {
            position: fixed;
            top: 24px;
            right: 24px;
            width: 44px;
            height: 44px;
            border-radius: 14px;
            background: var(--surface-bg);
            border: 1px solid var(--surface-border);
            color: var(--text-main);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(12px);
            transition: all 0.2s ease;
        }

        .theme-toggle-fixed:hover {
            color: var(--accent-color);
            transform: rotate(15deg) scale(1.05);
        }

        [data-bs-theme="dark"] .theme-icon-dark { display: inline-block; }
        [data-bs-theme="dark"] .theme-icon-light { display: none; }
        [data-bs-theme="light"] .theme-icon-dark { display: none; }
        [data-bs-theme="light"] .theme-icon-light { display: inline-block; color: #f59e0b; }
    </style>
</head>

<body>
    <!-- Theme Toggle Fixed Button -->
    <button type="button" class="theme-toggle-fixed" id="themeToggleBtn" title="สลับโหมดสว่าง/มืด" aria-label="Toggle Theme">
        <i class="bi bi-sun-fill theme-icon-light"></i>
        <i class="bi bi-moon-stars-fill theme-icon-dark"></i>
    </button>

    <div class="auth-container">
        <div class="auth-card">
            <!-- Header & Brand Logo -->
            <div class="text-center mb-4">
                <div class="brand-logo-badge">
                    <i class="bi bi-shield-lock-fill"></i>
                </div>
                <h1 class="auth-title mb-1">เข้าสู่ระบบ 000000000000</h1>
                <p class="auth-subtitle mb-0">ระบบบริหารจัดการข้อมูลพนักงาน (LV-PROJECT)</p>
            </div>

            <!-- Alerts -->
            @if (session('error'))
                <div class="alert alert-danger border-0 d-flex align-items-center gap-2 py-2 px-3 mb-4"
                    style="background: rgba(239, 68, 68, 0.15); color: #ef4444; border-radius: 12px; font-size: 0.88rem;" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success border-0 d-flex align-items-center gap-2 py-2 px-3 mb-4"
                    style="background: rgba(16, 185, 129, 0.15); color: #10b981; border-radius: 12px; font-size: 0.88rem;" role="alert">
                    <i class="bi bi-check-circle-fill fs-5"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger border-0 d-flex align-items-center gap-2 py-2 px-3 mb-4"
                    style="background: rgba(239, 68, 68, 0.15); color: #ef4444; border-radius: 12px; font-size: 0.88rem;" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <!-- Form -->
            <form action="{{ route('login.process', [], false) }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="username" class="form-label-custom">ชื่อผู้ใช้ หรือ รหัสพนักงาน</label>
                    <div class="input-group-custom">
                        <i class="bi bi-person input-icon"></i>
                        <input type="text" class="form-control-pro @error('username') is-invalid @enderror"
                            id="username" name="username" value="{{ old('username') }}" 
                            placeholder="เช่น admin, hr, employee" required autofocus autocomplete="username">
                    </div>
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label for="password" class="form-label-custom mb-0">รหัสผ่าน</label>
                        <a href="#" class="text-decoration-none small" style="color: var(--accent-color); font-size: 0.78rem;">ลืมรหัสผ่าน?</a>
                    </div>
                    <div class="input-group-custom">
                        <i class="bi bi-key input-icon"></i>
                        <input type="password" class="form-control-pro @error('password') is-invalid @enderror"
                            id="password" name="password" placeholder="กรอกรหัสผ่านของคุณ" required autocomplete="current-password">
                    </div>
                </div>

                <div class="form-check mb-4">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}
                        style="background-color: var(--input-bg); border-color: var(--input-border);">
                    <label class="form-check-label small" for="remember" style="color: var(--text-muted); cursor: pointer;">
                        จดจำการเข้าสู่ระบบ
                    </label>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn-auth-submit d-flex align-items-center justify-content-center gap-2">
                        <span>ลงชื่อเข้าสู่ระบบ</span>
                        <i class="bi bi-arrow-right-short fs-4"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Theme Switcher JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const themeToggleBtn = document.getElementById('themeToggleBtn');
            const htmlElement = document.documentElement;

            function getPreferredTheme() {
                return localStorage.getItem('wb_theme') || 'dark';
            }

            function setTheme(theme) {
                htmlElement.setAttribute('data-bs-theme', theme);
                localStorage.setItem('wb_theme', theme);
            }

            if (themeToggleBtn) {
                themeToggleBtn.addEventListener('click', function () {
                    const currentTheme = htmlElement.getAttribute('data-bs-theme') || getPreferredTheme();
                    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                    setTheme(newTheme);
                });
            }
        });
    </script>
</body>

</html>
