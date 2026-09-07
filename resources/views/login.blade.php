<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ | WB-PROJECT</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts: Outfit & Noto Sans Thai -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;700&family=Outfit:wght@300;400;600;700&display=swap"
        rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            --background-color: #0f172a;
            --card-bg: rgba(30, 41, 59, 0.65);
            --card-border: rgba(255, 255, 255, 0.08);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
        }

        body {
            font-family: 'Outfit', 'Noto Sans Thai', sans-serif;
            background-color: var(--background-color);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-image:
                radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.18) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(79, 70, 229, 0.18) 0px, transparent 50%);
            background-attachment: fixed;
            padding: 20px;
        }

        .login-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 24px;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
            max-width: 480px;
            width: 100%;
            overflow: hidden;
        }

        .form-control-custom {
            background: rgba(15, 23, 42, 0.5) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            color: var(--text-main) !important;
            border-radius: 12px;
            padding: 12px 16px;
            transition: all 0.2s ease;
        }

        .form-control-custom::placeholder {
            color: rgba(255, 255, 255, 0.3);
        }

        .form-control-custom:focus {
            border-color: #6366f1 !important;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.25) !important;
        }

        .btn-submit {
            background: var(--primary-gradient);
            border: none;
            border-radius: 12px;
            padding: 12px;
            color: #ffffff;
            font-weight: 600;
            transition: transform 0.2s, opacity 0.2s;
        }

        .btn-submit:hover {
            opacity: 0.95;
            transform: translateY(-1px);
        }

        .login-logo {
            font-size: 2.2rem;
            font-weight: 700;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>

<body>

    <div class="login-card p-4 p-md-5">
        <div class="text-center mb-4">
            <div class="mb-2">
                <span class="login-logo"><i class="bi bi-rocket-takeoff-fill me-2"></i>WB</span>
            </div>
            <h3 class="fw-bold text-white mb-1">เข้าสู่ระบบ</h3>
            <p class="text-muted small">ยินดีต้อนรับกลับมา โปรดกรอกข้อมูลเพื่อลงชื่อเข้าใช้งาน</p>
        </div>

        @if (session('error'))
            <div class="alert alert-danger border-0 text-center shadow-sm py-2 mb-4"
                style="background: rgba(239, 68, 68, 0.15); color: #f87171; border-radius: 10px;" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success border-0 text-center shadow-sm py-2 mb-4"
                style="background: rgba(16, 185, 129, 0.15); color: #34d399; border-radius: 10px;" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger border-0 text-center shadow-sm py-2 mb-4"
                style="background: rgba(239, 68, 68, 0.15); color: #f87171; border-radius: 10px;" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login.process') }}" method="POST">

            @csrf

            <div class="mb-3">
                <label for="username" class="form-label text-muted small fw-semibold">ชื่อผู้ใช้ หรือ รหัสพนักงาน</label>
                <div class="input-group">
                    <span class="input-group-text border-0"
                        style="background: rgba(15, 23, 42, 0.5); color: var(--text-muted); border-radius: 12px 0 0 12px;"><i
                            class="bi bi-person"></i></span>
                    <input type="text" class="form-control form-control-custom @error('username') is-invalid @enderror"
                        style="border-radius: 0 12px 12px 0 !important;" id="username" name="username"
                        value="{{ old('username') }}" placeholder="admin, hr, หรือ employee" required autocomplete="username">
                </div>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label text-muted small fw-semibold">รหัสผ่าน</label>
                <div class="input-group">
                    <span class="input-group-text border-0"
                        style="background: rgba(15, 23, 42, 0.5); color: var(--text-muted); border-radius: 12px 0 0 12px;"><i
                            class="bi bi-shield-lock"></i></span>
                    <input type="password" class="form-control form-control-custom @error('password') is-invalid @enderror"
                        style="border-radius: 0 12px 12px 0 !important;" id="password" name="password"
                        placeholder="กรอกรหัสผ่านของคุณ" required autocomplete="current-password">
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}
                        style="background-color: rgba(15, 23, 42, 0.5); border-color: rgba(255,255,255,0.1);">
                    <label class="form-check-label text-muted small" for="remember">จดจำฉันไว้</label>
                </div>
                <a href="#" class="text-decoration-none small" style="color: #818cf8;">ลืมรหัสผ่าน?</a>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" name="login_btn"
                    class="btn btn-submit d-flex align-items-center justify-content-center gap-2">
                    เข้าสู่ระบบ <i class="bi bi-arrow-right-short fs-4"></i>
                </button>
            </div>

        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
