@extends('layouts.app')

@section('title', 'เพิ่มสมาชิกใหม่')

@section('content')
<style>
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

<div class="row justify-content-center">
    <div class="col-lg-8">
        
        <div class="alert border-0 shadow-sm mb-4 d-flex align-items-center" role="alert" 
             style="background: rgba(245, 158, 11, 0.15); color: #fbbf24; border-radius: 14px; border: 1px solid rgba(245, 158, 11, 0.2);">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                <span><strong>หมายเหตุ:</strong> หน้านี้สำหรับ Admin และ HR เท่านั้น</span>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-white mb-0"><i class="bi bi-person-plus-fill me-2" style="color: #818cf8;"></i>เพิ่มสมาชิกใหม่</h3>
            <a href="{{ route('home') }}" class="btn btn-custom-light btn-sm px-3 py-2 fw-semibold">
                <i class="bi bi-arrow-left me-1"></i> กลับแดชบอร์ด
            </a>
        </div>

        <div class="card card-custom p-4 p-md-5">
            <form action="#" method="GET" onsubmit="alert('จำลองการส่งข้อมูลสำเร็จ'); return false;">
                
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
</div>
@endsection