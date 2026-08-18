<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบจัดการข้อมูล - รายชื่อสมาชิก | WB-PROJECT</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts: Outfit & Noto Sans Thai -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;700&family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
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
            margin-top: 40px;
            margin-bottom: 40px;
            flex: 1;
        }

        .card-custom {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 20px;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .card-header-custom {
            background: rgba(15, 23, 42, 0.4);
            padding: 24px;
            border-bottom: 1px solid var(--card-border);
        }

        .search-box-custom {
            background: rgba(15, 23, 42, 0.5) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            color: var(--text-main) !important;
            border-radius: 12px;
            padding: 10px 16px;
            max-width: 300px;
            width: 100%;
        }

        .search-box-custom::placeholder {
            color: rgba(255, 255, 255, 0.3);
        }

        .search-box-custom:focus {
            border-color: #6366f1 !important;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.25) !important;
        }

        .table {
            margin: 0;
            color: var(--text-main);
        }

        .table thead {
            background: rgba(15, 23, 42, 0.6);
        }

        .table thead th {
            padding: 18px 20px;
            font-weight: 600;
            color: var(--text-muted);
            border-bottom: 1px solid var(--card-border);
            white-space: nowrap;
        }

        .table tbody td {
            padding: 18px 20px;
            vertical-align: middle;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .table-hover tbody tr {
            transition: background 0.2s ease;
        }

        .table-hover tbody tr:hover {
            background: rgba(255, 255, 255, 0.03);
        }

        .table img {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            object-fit: cover;
            border: 2px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.25);
        }

        .number-circle {
            width: 32px;
            height: 32px;
            background: rgba(99, 102, 241, 0.15);
            color: #818cf8;
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .badge-status {
            padding: 6px 12px;
            border-radius: 30px;
            font-size: 0.8rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .badge-active {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .badge-inactive {
            background: rgba(239, 68, 68, 0.15);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .badge-pending {
            background: rgba(245, 158, 11, 0.15);
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.2);
        }

        .btn-custom-add {
            background: var(--primary-gradient);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .btn-custom-add:hover {
            opacity: 0.95;
            transform: translateY(-1px);
            color: white;
        }

        .btn-action {
            border-radius: 8px;
            padding: 6px 12px;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .btn-action-edit {
            background: rgba(245, 158, 11, 0.15);
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.2);
        }

        .btn-action-edit:hover {
            background: #fbbf24;
            color: #0f172a;
        }

        .btn-action-delete {
            background: rgba(239, 68, 68, 0.15);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .btn-action-delete:hover {
            background: #ef4444;
            color: white;
        }

        footer {
            border-top: 1px solid var(--card-border);
            padding: 20px 0;
            background: rgba(15, 23, 42, 0.9);
            color: var(--text-muted);
            font-size: 0.9rem;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark custom-navbar sticky-top">
        <div class="container">
            <a class="navbar-brand navbar-brand-custom" href="{{ route('home') }}">
                <i class="bi bi-rocket-takeoff-fill me-2"></i>WB-DASHBOARD
            </a>
            <button class="navbar-toggler border-0 text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="bi bi-list fs-2"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <div class="d-flex align-items-center gap-3 mt-3 mt-lg-0">
                    <span class="text-white small">
                        <i class="bi bi-person-circle me-1 text-muted"></i> สวัสดี, {{ session('user_logged_in') }}
                    </span>
                    <a href="{{ route('logout') }}" class="btn btn-outline-danger btn-sm px-3" style="border-radius: 8px;">
                        ออกจากระบบ
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container main-container">

        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
            <div>
                <h2 class="fw-bold text-white mb-1">ตารางรายชื่อสมาชิก</h2>
                <p class="text-muted mb-0 small">จัดการและตรวจสอบรายชื่อผู้ใช้งานทั้งหมดในระบบ</p>
            </div>
            <a href="{{ route('add') }}" class="btn btn-custom-add d-inline-flex align-items-center gap-2 align-self-start">
                <i class="bi bi-person-plus-fill"></i> เพิ่มรายชื่อใหม่
            </a>
        </div>

        <div class="card card-custom">

            <div class="card-header-custom d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                <h5 class="mb-0 fw-bold text-white d-flex align-items-center gap-2">
                    <i class="bi bi-people-fill text-primary" style="color: #818cf8 !important;"></i> รายชื่อสมาชิกในระบบ
                </h5>
                <div class="position-relative d-flex align-items-center w-100 w-sm-auto">
                    <i class="bi bi-search position-absolute ms-3 text-muted"></i>
                    <input type="text" class="form-control search-box-custom ps-5" placeholder="ค้นหาสมาชิก...">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th style="width: 80px;">ลำดับ</th>
                            <th style="width: 100px;">รูป</th>
                            <th>ชื่อ-นามสกุล</th>
                            <th>อีเมล</th>
                            <th>เบอร์โทร</th>
                            <th>สถานะ</th>
                            <th class="text-center" style="width: 180px;">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><div class="number-circle">1</div></td>
                            <td><img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100" alt="Avatar"></td>
                            <td class="fw-semibold text-white">สมชาย ใจดี</td>
                            <td>somchai@example.com</td>
                            <td>081-234-5678</td>
                            <td>
                                <span class="badge-status badge-active">
                                    <i class="bi bi-check-circle-fill"></i> ใช้งานอยู่
                                </span>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="#" class="btn btn-action btn-action-edit">
                                        <i class="bi bi-pencil-square"></i> แก้ไข
                                    </a>
                                    <a href="#" class="btn btn-action btn-action-delete" onclick="return confirm('ยืนยันที่จะลบรายชื่อนี้หรือไม่?')">
                                        <i class="bi bi-trash3-fill"></i> ลบ
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><div class="number-circle">2</div></td>
                            <td><img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100" alt="Avatar"></td>
                            <td class="fw-semibold text-white">สมหญิง รักเรียน</td>
                            <td>somying@example.com</td>
                            <td>089-876-5432</td>
                            <td>
                                <span class="badge-status badge-inactive">
                                    <i class="bi bi-x-circle-fill"></i> ระงับการใช้งาน
                                </span>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="#" class="btn btn-action btn-action-edit">
                                        <i class="bi bi-pencil-square"></i> แก้ไข
                                    </a>
                                    <a href="#" class="btn btn-action btn-action-delete" onclick="return confirm('ยืนยันที่จะลบรายชื่อนี้หรือไม่?')">
                                        <i class="bi bi-trash3-fill"></i> ลบ
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><div class="number-circle">3</div></td>
                            <td><img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100" alt="Avatar"></td>
                            <td class="fw-semibold text-white">นภา สงบใจ</td>
                            <td>napa@example.com</td>
                            <td>082-999-1122</td>
                            <td>
                                <span class="badge-status badge-active">
                                    <i class="bi bi-check-circle-fill"></i> ใช้งานอยู่
                                </span>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="#" class="btn btn-action btn-action-edit">
                                        <i class="bi bi-pencil-square"></i> แก้ไข
                                    </a>
                                    <a href="#" class="btn btn-action btn-action-delete" onclick="return confirm('ยืนยันที่จะลบรายชื่อนี้หรือไม่?')">
                                        <i class="bi bi-trash3-fill"></i> ลบ
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><div class="number-circle">4</div></td>
                            <td><img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=100" alt="Avatar"></td>
                            <td class="fw-semibold text-white">วิชัย กล้าหาญ</td>
                            <td>wichai@example.com</td>
                            <td>085-444-5566</td>
                            <td>
                                <span class="badge-status badge-pending">
                                    <i class="bi bi-hourglass-split"></i> รอการตรวจสอบ
                                </span>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="#" class="btn btn-action btn-action-edit">
                                        <i class="bi bi-pencil-square"></i> แก้ไข
                                    </a>
                                    <a href="#" class="btn btn-action btn-action-delete" onclick="return confirm('ยืนยันที่จะลบรายชื่อนี้หรือไม่?')">
                                        <i class="bi bi-trash3-fill"></i> ลบ
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><div class="number-circle">5</div></td>
                            <td><img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=100" alt="Avatar"></td>
                            <td class="fw-semibold text-white">อรอนงค์ งดงาม</td>
                            <td>onanong@example.com</td>
                            <td>087-111-2233</td>
                            <td>
                                <span class="badge-status badge-inactive">
                                    <i class="bi bi-x-circle-fill"></i> ระงับการใช้งาน
                                </span>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="#" class="btn btn-action btn-action-edit">
                                        <i class="bi bi-pencil-square"></i> แก้ไข
                                    </a>
                                    <a href="#" class="btn btn-action btn-action-delete" onclick="return confirm('ยืนยันที่จะลบรายชื่อนี้หรือไม่?')">
                                        <i class="bi bi-trash3-fill"></i> ลบ
                                    </a>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>

    </div>

    <!-- Footer -->
    <footer class="text-center">
        <div class="container">
            <p class="mb-0">© 2026 WB-PROJECT. Created with ❤️ for premium experience.</p>
        </div>
    </footer>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
