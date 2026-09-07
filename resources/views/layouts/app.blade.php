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
        href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;700&family=Outfit:wght@300;400;600;700&display=swap"
        rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            --accent-color: #4f46e5;
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
            flex-direction: column;
            background-image:
                radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(79, 70, 229, 0.15) 0px, transparent 50%);
            background-attachment: fixed;
        }

        /* Glassmorphism Navbar */
        .custom-navbar {
            background: rgba(15, 23, 42, 0.8) !important;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--card-border);
            padding: 15px 0;
            transition: all 0.3s ease;
        }

        .navbar-brand-custom {
            font-weight: 700;
            font-size: 1.3rem;
            letter-spacing: -0.5px;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .main-container {
            flex: 1;
            padding: 50px 0;
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

        footer {
            border-top: 1px solid var(--card-border);
            padding: 20px 0;
            background: rgba(15, 23, 42, 0.9);
            color: var(--text-muted);
            font-size: 0.9rem;
        }
    </style>
    @stack('styles')
</head>

<body>
    <!-- Main Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark custom-navbar sticky-top">
        <div class="container">
            <a class="navbar-brand navbar-brand-custom" href="{{ route('dashboard', [], false) }}">
                <i class="bi bi-rocket-takeoff-fill me-2"></i>WB-SYSTEM
            </a>

            <!-- User Profile & Logout -->
            <div class="d-flex align-items-center gap-3 ms-auto">
                @auth
                    <div class="d-flex align-items-center gap-2 text-white small">
                        <i class="bi bi-person-circle fs-5" style="color: #818cf8;"></i>
                        <div class="d-flex flex-column text-start">
                            <span class="fw-semibold">{{ Auth::user()->name }}</span>
                            <span class="text-muted" style="font-size: 0.75rem;">
                                รหัส: <strong class="text-info">{{ Auth::user()->emp_code ?? '-' }}</strong> |
                                @if(Auth::user()->role === 'admin')
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-1">Admin</span>
                                @elseif(Auth::user()->role === 'hr')
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-1">HR</span>
                                @else
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-1">Employee</span>
                                @endif
                            </span>
                        </div>
                    </div>
                    <a href="{{ route('logout', [], false) }}" class="btn btn-outline-danger btn-sm px-3"
                        style="border-radius: 8px"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="bi bi-box-arrow-right me-1"></i> ออกจากระบบ
                    </a>
                    <form id="logout-form" action="{{ route('logout', [], false) }}" method="POST" class="d-none">
                        @csrf
                    </form>
                @else
                    <a href="{{ route('login', [], false) }}" class="btn btn-primary btn-sm px-3"
                        style="background: var(--primary-gradient); border: none; border-radius: 8px;">
                        <i class="bi bi-box-arrow-in-right me-1"></i> เข้าสู่ระบบ
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Main Content Area -->
    <main class="main-container">
        <div class="container">
            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="text-center">
        <div class="container">
            <p class="mb-0">© 2026 WB-PROJECT. Created with ❤️ for premium experience.</p>
        </div>
    </footer>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>
