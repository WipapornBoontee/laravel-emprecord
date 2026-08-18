<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มรายชื่อใหม่ | WB-PROJECT</title>
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
            align-items: center;
            justify-content: center;
            background-image: 
                radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(79, 70, 229, 0.15) 0px, transparent 50%);
            background-attachment: fixed;
            padding: 40px 20px;
        }

        .main-container {
            max-width: 600px;
            width: 100%;
        }

        .card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 20px;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        }

        .form-control-custom, .form-select-custom {
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

        .form-control-custom:focus, .form-select-custom:focus {
            border-color: #6366f1 !important;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.25) !important;
        }

        .btn-custom-primary {
            background: var(--primary-gradient);
            border: none;
            border-radius: 12px;
            padding: 12px 24px;
            color: #ffffff;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .btn-custom-primary:hover {
            opacity: 0.95;
            transform: translateY(-1px);
        }

        .btn-custom-light {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 12px 24px;
            color: var(--text-main);
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .btn-custom-light:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
        }
    </style>
</head>
<body>

<div class="main-container">
    
    <div class="alert border-0 shadow-sm mb-4 d-flex align-items-center" role="alert" 
         style="background: rgba(245, 158, 11, 0.15); color: #fbbf24; border-radius: 14px; border: 1px solid rgba(245, 158, 11, 0.2);">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
            <span><strong>หมายเหตุ:</strong> หน้านี้อยู่ระหว่างการพัฒนา (ระบบฟอร์มจำลอง ยังไม่มีการบันทึกข้อมูลลงฐานข้อมูลจริง)</span>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-white mb-0"><i class="bi bi-person-plus-fill me-2" style="color: #818cf8;"></i>เพิ่มสมาชิกใหม่</h3>
        <a href="{{ route('home') }}" class="btn btn-custom-light btn-sm px-3 py-2 fw-semibold">
            <i class="bi bi-arrow-left me-1"></i> กลับแดชบอร์ด
        </a>
    </div>

    <div class="card p-4 p-md-5">
        <form action="#" method="GET" onsubmit="alert('จำลองการส่งข้อมูลสำเร็จ (ยังไม่ลง Database)'); return false;">
            
            <div class="mb-4">
                <label class="form-label text-muted small fw-semibold">ชื่อ-นามสกุล</label>
                <input type="text" class="form-control form-control-custom" placeholder="เช่น สมชาย ใจดี" required>
            </div>

            <div class="mb-4">
                <label class="form-label text-muted small fw-semibold">อีเมล</label>
                <input type="email" class="form-control form-control-custom" placeholder="เช่น somchai@example.com" required>
            </div>

            <div class="mb-4">
                <label class="form-label text-muted small fw-semibold">เบอร์โทรศัพท์</label>
                <input type="tel" class="form-control form-control-custom" placeholder="เช่น 081-234-5678" required>
            </div>

            <div class="mb-4">
                <label class="form-label text-muted small fw-semibold">สถานะการใช้งาน</label>
                <select class="form-select form-select-custom">
                    <option value="active" selected>ใช้งานอยู่ (Active)</option>
                    <option value="inactive">ระงับการใช้งาน (Inactive)</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label text-muted small fw-semibold">อัปโหลดรูปโปรไฟล์</label>
                <input type="file" class="form-control form-control-custom" accept="image/*">
            </div>

            <hr class="my-4" style="border-color: rgba(255, 255, 255, 0.08);">

            <div class="d-flex justify-content-end gap-3">
                <a href="{{ route('home') }}" class="btn btn-custom-light px-4">ยกเลิก</a>
                <button type="submit" class="btn btn-custom-primary px-4 d-flex align-items-center gap-2">
                    <i class="bi bi-floppy-fill"></i> บันทึกข้อมูล
                </button>
            </div>

        </form>
    </div>
</div>

</body>
</html>