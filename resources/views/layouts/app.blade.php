<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'ระบบจัดการข้อมูลพนักงาน') | WB-PROJECT</title>
    
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

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            --accent-color: #6366f1;
            --background-color: #0b0f19;
            --card-bg: rgba(30, 41, 59, 0.6);
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
            flex-direction: column;
            background-image:
                radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.12) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(79, 70, 229, 0.12) 0px, transparent 50%);
            background-attachment: fixed;
        }

        /* Glassmorphism Navbar */
        .custom-navbar {
            background: rgba(11, 15, 25, 0.85) !important;
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
            color: #fff !important;
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
            color: #ffffff !important;
            background: rgba(255, 255, 255, 0.08);
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

        .dropdown-item {
            color: var(--text-muted);
            border-radius: 8px;
            transition: all 0.2s ease;
            font-size: 0.9rem;
        }

        .dropdown-item:hover {
            color: #ffffff;
            background: rgba(99, 102, 241, 0.2);
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
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
            overflow: hidden;
        }

        /* Footer */
        .custom-footer {
            border-top: 1px solid var(--card-border);
            background: rgba(11, 15, 25, 0.92);
            color: var(--text-muted);
        }

        .footer-brand-text {
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
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
    @stack('scripts')
</body>

</html>
