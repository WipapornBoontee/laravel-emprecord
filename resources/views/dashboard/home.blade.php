@extends('layouts.app')

@section('title', 'แดชบอร์ดภาพรวม')

@section('content')
{{-- อันนี้ตรงมี ห้ามลบ --}}
<div class="row g-4 justify-content-center">
    <!-- 1. Hero Welcome Banner -->
    <div class="col-12">
        <div class="hero-welcome-card p-4 p-md-5">
            <div class="row align-items-center justify-content-between g-4">
                <div class="col-lg-7 text-start">
                    <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-3 status-pill">
                        <span class="pulse-dot"></span>
                        <span class="small fw-semibold">ระบบพร้อมใช้งาน (System Online)</span>
                    </div>
                    <h1 class="display-6 fw-bold mb-2">
                        ยินดีต้อนรับ, <span class="gradient-text">{{ Auth::user()->name }}</span> 👋
                    </h1>
                    <p class="text-muted fs-6 mb-0">
                        เข้าสู่ระบบในบทบาท 
                        @if(Auth::user()->role === 'admin')
                            <strong class="text-danger">System Administrator (ผู้ดูแลระบบสูงสุด)</strong>
                        @elseif(Auth::user()->role === 'hr')
                            <strong class="text-warning">Human Resources Manager (ฝ่ายบุคคล)</strong>
                        @else
                            <strong class="text-success">Employee (พนักงานประจำ)</strong>
                        @endif
                        • รหัสพนักงาน: <code class="fw-bold">{{ Auth::user()->emp_code ?? '-' }}</code>
                    </p>
                </div>
                <div class="col-lg-5 text-lg-end">
                    <div class="quick-stat-badge d-inline-flex flex-column text-lg-end p-3 px-4 rounded-4">
                        <span class="text-muted small">เวลาปัจจุบันของระบบ</span>
                        <span class="fs-4 fw-bold font-monospace" id="liveClock">{{ date('H:i:s') }}</span>
                        <span class="small text-muted">{{ date('d F Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Interactive KPI Stats Cards -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card p-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="stat-label">สถานะการทำงาน</span>
                <div class="stat-icon-wrapper icon-green">
                    <i class="bi bi-person-check-fill"></i>
                </div>
            </div>
            <h3 class="stat-value text-success mb-1">Active</h3>
            <p class="stat-desc mb-0"><i class="bi bi-shield-check me-1"></i> ปฏิบัติงานปกติ</p>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card p-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="stat-label">สิทธิ์วันลาคงเหลือ</span>
                <div class="stat-icon-wrapper icon-indigo">
                    <i class="bi bi-calendar-heart-fill"></i>
                </div>
            </div>
            <h3 class="stat-value text-primary mb-1">30 <span class="fs-6 fw-normal text-muted">วัน/ปี</span></h3>
            <p class="stat-desc mb-0"><i class="bi bi-arrow-up-right me-1"></i> โควตาประจำปี {{ date('Y') }}</p>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card p-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="stat-label">บันทึกเวลาวันนี้</span>
                <div class="stat-icon-wrapper icon-amber">
                    <i class="bi bi-clock-history"></i>
                </div>
            </div>
            <h3 class="stat-value text-warning mb-1">ตรงเวลา</h3>
            <p class="stat-desc mb-0"><i class="bi bi-geo-alt-fill me-1"></i> Check-in เรียบร้อย</p>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card p-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="stat-label">ระดับสิทธิ์ (Role)</span>
                <div class="stat-icon-wrapper icon-purple">
                    <i class="bi bi-shield-shaded"></i>
                </div>
            </div>
            <h3 class="stat-value mb-1 text-uppercase">{{ Auth::user()->role }}</h3>
            <p class="stat-desc mb-0"><i class="bi bi-key-fill me-1"></i> ได้รับการยืนยันสิทธิ์</p>
        </div>
    </div>

    <!-- 3. Quick Actions Menu -->
    <div class="col-12">
        <div class="action-card p-4">
            <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-lightning-charge-fill text-warning"></i> เมนูดำเนินการด่วน (Quick Actions)
            </h5>
            <div class="row g-3">
                <div class="col-md-4">
                    <a href="#" class="quick-action-btn p-3 d-flex align-items-center gap-3 text-decoration-none">
                        <div class="action-icon icon-indigo">
                            <i class="bi bi-calendar2-plus-fill"></i>
                        </div>
                        <div>
                            <span class="d-block fw-bold action-title">ยื่นขอลาหยุดงาน</span>
                            <span class="text-muted small">ลาป่วย, ลากิจ, ลาพักร้อน</span>
                        </div>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="#" class="quick-action-btn p-3 d-flex align-items-center gap-3 text-decoration-none">
                        <div class="action-icon icon-green">
                            <i class="bi bi-fingerprint"></i>
                        </div>
                        <div>
                            <span class="d-block fw-bold action-title">บันทึกเวลาเข้า-ออก</span>
                            <span class="text-muted small">ลงเวลาทำงานประจำวัน</span>
                        </div>
                    </a>
                </div>
                @if(Auth::user()->isAdmin() || Auth::user()->isHr())
                    <div class="col-md-4">
                        <a href="#" class="quick-action-btn p-3 d-flex align-items-center gap-3 text-decoration-none">
                            <div class="action-icon icon-purple">
                                <i class="bi bi-people-fill"></i>
                            </div>
                            <div>
                                <span class="d-block fw-bold action-title">จัดการข้อมูลพนักงาน</span>
                                <span class="text-muted small">ระบบสำหรับ HR & Admin</span>
                            </div>
                        </a>
                    </div>
                @else
                    <div class="col-md-4">
                        <a href="#" class="quick-action-btn p-3 d-flex align-items-center gap-3 text-decoration-none">
                            <div class="action-icon icon-amber">
                                <i class="bi bi-person-lines-fill"></i>
                            </div>
                            <div>
                                <span class="d-block fw-bold action-title">ประวัติส่วนตัว</span>
                                <span class="text-muted small">ตรวจสอบข้อมูลของฉัน</span>
                            </div>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    // อัปเดตนาฬิกาแบบ Real-time
    function updateLiveClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        const clockElem = document.getElementById('liveClock');
        if (clockElem) {
            clockElem.textContent = `${hours}:${minutes}:${seconds}`;
        }
    }
    setInterval(updateLiveClock, 1000);
</script>
@endsection
