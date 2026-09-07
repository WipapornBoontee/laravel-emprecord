<!DOCTYPE html>
<html lang="th" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'ระบบจัดการข้อมูลพนักงาน') | LV-PROJECT</title>

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
        /* === Color Palette & CSS Variables === */
        :root[data-bs-theme="dark"] {
            --primary-gradient: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);
            --accent-color: #6366f1;
            --accent-glow: rgba(99, 102, 241, 0.25);
            --bg-color: #090d16;
            --navbar-bg: rgba(13, 19, 33, 0.85);
            --surface-bg: rgba(22, 30, 49, 0.7);
            --surface-border: rgba(255, 255, 255, 0.08);
            --card-hover-border: rgba(99, 102, 241, 0.4);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --badge-bg: rgba(255, 255, 255, 0.05);
            --dropdown-bg: rgba(22, 30, 49, 0.98);
            --footer-bg: rgba(13, 19, 33, 0.95);
            --icon-indigo: rgba(99, 102, 241, 0.2);
            --icon-green: rgba(16, 185, 129, 0.2);
            --icon-amber: rgba(245, 158, 11, 0.2);
            --icon-purple: rgba(168, 85, 247, 0.2);
        }

        :root[data-bs-theme="light"] {
            --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
            --accent-color: #4f46e5;
            --accent-glow: rgba(79, 70, 229, 0.2);
            --bg-color: #f1f5f9;
            --navbar-bg: rgba(255, 255, 255, 0.9);
            --surface-bg: rgba(255, 255, 255, 0.9);
            --surface-border: rgba(0, 0, 0, 0.08);
            --card-hover-border: rgba(79, 70, 229, 0.4);
            --text-main: #0f172a;
            --text-muted: #64748b;
            --badge-bg: rgba(0, 0, 0, 0.04);
            --dropdown-bg: rgba(255, 255, 255, 0.98);
            --footer-bg: rgba(255, 255, 255, 0.95);
            --icon-indigo: rgba(79, 70, 229, 0.12);
            --icon-green: rgba(16, 185, 129, 0.12);
            --icon-amber: rgba(245, 158, 11, 0.12);
            --icon-purple: rgba(168, 85, 247, 0.12);
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', 'Prompt', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-image: 
                radial-gradient(at 10% 10%, rgba(99, 102, 241, 0.1) 0px, transparent 40%),
                radial-gradient(at 90% 90%, rgba(129, 140, 248, 0.08) 0px, transparent 40%);
            background-attachment: fixed;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* Navbar Styling */
        .custom-navbar {
            background: var(--navbar-bg) !important;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--surface-border);
            padding: 12px 0;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .navbar-brand-custom {
            font-weight: 800;
            font-size: 1.3rem;
            letter-spacing: -0.5px;
            color: var(--text-main) !important;
        }

        .brand-icon {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            background: var(--primary-gradient);
            color: #ffffff;
            font-size: 1.15rem;
            box-shadow: 0 8px 16px -4px rgba(99, 102, 241, 0.4);
        }

        .nav-link-custom {
            color: var(--text-muted) !important;
            font-weight: 600;
            padding: 8px 14px !important;
            border-radius: 12px;
            transition: all 0.2s ease;
            font-size: 0.92rem;
        }

        .nav-link-custom:hover,
        .nav-link-custom.active {
            color: var(--text-main) !important;
            background: rgba(99, 102, 241, 0.12);
        }

        .btn-theme-toggle {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: var(--badge-bg);
            border: 1px solid var(--surface-border);
            color: var(--text-main);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.25s ease;
        }

        .btn-theme-toggle:hover {
            color: var(--accent-color);
            background: rgba(99, 102, 241, 0.15);
            transform: rotate(15deg);
        }

        [data-bs-theme="dark"] .theme-icon-dark { display: inline-block; }
        [data-bs-theme="dark"] .theme-icon-light { display: none; }
        [data-bs-theme="light"] .theme-icon-dark { display: none; }
        [data-bs-theme="light"] .theme-icon-light { display: inline-block; color: #f59e0b; }

        .user-profile-badge {
            background: var(--badge-bg) !important;
            border: 1px solid var(--surface-border) !important;
            transition: all 0.2s ease;
        }

        .user-name-text {
            color: var(--text-main);
        }

        .avatar-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--primary-gradient);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.95rem;
        }

        .custom-dropdown-menu {
            background: var(--dropdown-bg) !important;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--surface-border) !important;
            border-radius: 16px;
            padding: 8px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2) !important;
        }

        .dropdown-item {
            color: var(--text-main);
            border-radius: 10px;
            padding: 8px 14px;
            font-size: 0.88rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .dropdown-item:hover {
            color: var(--text-main);
            background: rgba(99, 102, 241, 0.12);
            transform: translateX(4px);
        }

        .main-container {
            flex: 1;
            padding: 40px 0;
        }

        /* Pro Component Styles (Hero, Stats, Action Cards) */
        .hero-welcome-card {
            background: var(--surface-bg);
            border: 1px solid var(--surface-border);
            border-radius: 24px;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.1);
        }

        .gradient-text {
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .status-pill {
            background: rgba(16, 185, 129, 0.15);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.25);
        }

        .pulse-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #10b981;
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            animation: pulseAnimation 1.6s infinite;
        }

        @keyframes pulseAnimation {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        .quick-stat-badge {
            background: var(--badge-bg);
            border: 1px solid var(--surface-border);
        }

        .stat-card {
            background: var(--surface-bg);
            border: 1px solid var(--surface-border);
            border-radius: 20px;
            backdrop-filter: blur(16px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            height: 100%;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            border-color: var(--card-hover-border);
            box-shadow: 0 20px 30px -10px rgba(0, 0, 0, 0.15);
        }

        .stat-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-muted);
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .stat-desc {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        .stat-icon-wrapper {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .action-card {
            background: var(--surface-bg);
            border: 1px solid var(--surface-border);
            border-radius: 24px;
            backdrop-filter: blur(16px);
        }

        .quick-action-btn {
            background: var(--badge-bg);
            border: 1px solid var(--surface-border);
            border-radius: 16px;
            transition: all 0.25s ease;
            height: 100%;
        }

        .quick-action-btn:hover {
            background: rgba(99, 102, 241, 0.1);
            border-color: var(--card-hover-border);
            transform: translateY(-2px);
        }

        .action-icon {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            flex-shrink: 0;
        }

        .action-title {
            color: var(--text-main);
            font-size: 0.95rem;
        }

        .icon-indigo { background: var(--icon-indigo); color: #818cf8; }
        .icon-green { background: var(--icon-green); color: #34d399; }
        .icon-amber { background: var(--icon-amber); color: #fbbf24; }
        .icon-purple { background: var(--icon-purple); color: #c084fc; }

        /* Footer */
        .custom-footer {
            border-top: 1px solid var(--surface-border);
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
    @stack('scripts')
</body>

</html>
