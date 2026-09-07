<!DOCTYPE html>
<html lang="th" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'ระบบจัดการข้อมูลพนักงาน') | LV-PROJECT</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts: Outfit & Noto Sans Thai -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&family=Outfit:wght@300;400;600;700&display=swap"
        rel="stylesheet">
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
        /* === Color Palette & CSS Variables === */
        :root[data-bs-theme="dark"] {
            --primary-gradient: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            --accent-color: #6366f1;
            --background-color: #0b0f19;
            --navbar-bg: rgba(11, 15, 25, 0.85);
            --card-bg: rgba(30, 41, 59, 0.65);
            --card-border: rgba(255, 255, 255, 0.08);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --bg-radial-1: rgba(99, 102, 241, 0.12);
            --bg-radial-2: rgba(79, 70, 229, 0.12);
            --user-badge-bg: rgba(255, 255, 255, 0.05);
            --dropdown-bg: rgba(30, 41, 59, 0.95);
            --footer-bg: rgba(11, 15, 25, 0.92);
        }

        :root[data-bs-theme="light"] {
            --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
            --accent-color: #4f46e5;
            --background-color: #f8fafc;
            --navbar-bg: rgba(255, 255, 255, 0.9);
            --card-bg: rgba(255, 255, 255, 0.85);
            --card-border: rgba(0, 0, 0, 0.08);
            --text-main: #0f172a;
            --text-muted: #64748b;
            --bg-radial-1: rgba(99, 102, 241, 0.08);
            --bg-radial-2: rgba(79, 70, 229, 0.06);
            --user-badge-bg: rgba(0, 0, 0, 0.04);
            --dropdown-bg: rgba(255, 255, 255, 0.98);
            --footer-bg: rgba(255, 255, 255, 0.95);
        }

        body {
            font-family: 'Outfit', 'Noto Sans Thai', sans-serif;
            background-color: var(--background-color);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-image:
                radial-gradient(at 0% 0%, var(--bg-radial-1) 0px, transparent 50%),
                radial-gradient(at 100% 100%, var(--bg-radial-2) 0px, transparent 50%);
            background-attachment: fixed;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* Glassmorphism Navbar */
        .custom-navbar {
            background: var(--navbar-bg) !important;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--card-border);
            padding: 14px 0;
            transition: all 0.3s ease;
        }

        .navbar-brand-custom {
            font-weight: 700;
            font-size: 1.35rem;
            letter-spacing: -0.5px;
            color: var(--text-main) !important;
        }

        .brand-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: var(--primary-gradient);
            color: #ffffff;
            font-size: 1.1rem;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.35);
        }

        .nav-link-custom {
            color: var(--text-muted) !important;
            font-weight: 500;
            padding: 8px 14px !important;
            border-radius: 10px;
            transition: all 0.2s ease;
            font-size: 0.95rem;
        }

        .nav-link-custom:hover,
        .nav-link-custom.active {
            color: var(--text-main) !important;
            background: rgba(99, 102, 241, 0.12);
        }

        /* Theme Toggle Button */
        .btn-theme-toggle {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: var(--user-badge-bg);
            border: 1px solid var(--card-border);
            color: var(--text-main);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-theme-toggle:hover {
            background: rgba(99, 102, 241, 0.15);
            color: #818cf8;
            transform: rotate(15deg);
        }

        [data-bs-theme="dark"] .theme-icon-dark { display: inline-block; }
        [data-bs-theme="dark"] .theme-icon-light { display: none; }
        [data-bs-theme="light"] .theme-icon-dark { display: none; }
        [data-bs-theme="light"] .theme-icon-light { display: inline-block; color: #f59e0b; }

        .user-profile-badge {
            background: var(--user-badge-bg) !important;
            border: 1px solid var(--card-border) !important;
        }

        .user-name-text {
            color: var(--text-main);
        }

        .avatar-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: rgba(99, 102, 241, 0.2);
            color: #818cf8;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        .custom-dropdown-menu {
            background: var(--dropdown-bg) !important;
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border: 1px solid var(--card-border) !important;
            border-radius: 14px;
            padding: 8px;
        }

        .dropdown-item {
            color: var(--text-main);
            border-radius: 8px;
            transition: all 0.2s ease;
            font-size: 0.9rem;
        }

        .dropdown-item:hover {
            color: var(--text-main);
            background: rgba(99, 102, 241, 0.15);
            transform: translateX(3px);
        }

        .main-container {
            flex: 1;
            padding: 40px 0;
            display: flex;
            align-items: center;
        }

        .card-custom {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 24px;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            transition: background 0.3s ease, border-color 0.3s ease;
        }

        /* Footer */
        .custom-footer {
            border-top: 1px solid var(--card-border);
            background: var(--footer-bg);
            color: var(--text-muted);
            transition: background 0.3s ease;
        }

        .footer-brand-text {
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .text-theme {
            color: var(--text-main);
        }
    </style>
    @stack('styles')
</head>

<body>
    <!-- 1. Header (Navbar) -->
    @include('layouts.partials.header')

    <!-- 2. Main Content Area -->
    <main class="main-container">
        <div class="container">
            @yield('content')
        </div>
    </main>

    <!-- 3. Footer -->
    @include('layouts.partials.footer')

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Theme Switcher JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const themeToggleBtn = document.getElementById('themeToggleBtn');
            const htmlElement = document.documentElement;

            // ตรวจสอบ Theme ปัจจุบัน
            function getPreferredTheme() {
                return localStorage.getItem('wb_theme') || 'dark';
            }

            function setTheme(theme) {
                htmlElement.setAttribute('data-bs-theme', theme);
                localStorage.setItem('wb_theme', theme);
            }

            // จัดการ Event กดปุ่มสลับโหมด
            if (themeToggleBtn) {
                themeToggleBtn.addEventListener('click', function () {
                    const currentTheme = htmlElement.getAttribute('data-bs-theme') || getPreferredTheme();
                    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                    setTheme(newTheme);
                });
            }
        });
    </script>
    @stack('scripts')
</body>

</html>
