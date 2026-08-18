@extends('layout')

@section('title')
    หน้าแรก
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Hero Section -->
            <div class="card p-5 text-center mb-5 border-0 shadow-lg"
                style="background: rgba(30, 41, 59, 0.4); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 24px; backdrop-filter: blur(10px);">
                <div class="mb-4">
                    <span class="badge px-3 py-2 text-uppercase fw-semibold"
                        style="background: rgba(99, 102, 241, 0.15); color: #818cf8; border: 1px solid rgba(99, 102, 241, 0.25);">Welcome
                        to our Space</span>
                </div>
                <h1 class="display-4 fw-bold mb-3"
                    style="background: linear-gradient(135deg, #a5b4fc 0%, #6366f1 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                    ยินดีต้อนรับเข้าสู่เว็บไซต์ของฉัน
                </h1>
                <p class="lead text-muted mx-auto mb-4" style="max-width: 700px; font-size: 1.15rem; line-height: 1.8;">
                    เราสร้างสรรค์ประสบการณ์เว็บไซต์ยุคใหม่ที่รวดเร็ว สวยงาม และตอบโจทย์ทุกการใช้งานของคุณอย่างเป็นเลิศ
                    ค้นพบมุมมองใหม่ๆ ไปกับเราได้แล้ววันนี้
                </p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="{{ route('blogs') }}" class="btn btn-primary px-4 py-3 fw-semibold shadow-sm"
                        style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); border: none; border-radius: 12px; transition: transform 0.2s;">
                        <i class="bi bi-book me-2"></i> อ่านบทความของเรา
                    </a>
                    <a href="{{ route('abouts') }}" class="btn btn-outline-light px-4 py-3 fw-semibold"
                        style="border-radius: 12px; border-color: rgba(255,255,255,0.15); background: rgba(255,255,255,0.03);">
                        <i class="bi bi-info-circle me-2"></i> เรียนรู้เกี่ยวกับเรา
                    </a>


                </div>
            </div>

            <!-- Features Grid -->
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 p-4 border-0"
                        style="background: rgba(30, 41, 59, 0.3); border: 1px solid rgba(255,255,255,0.05); border-radius: 18px; transition: all 0.3s ease;">
                        <div class="fs-1 text-primary mb-3">
                            <i class="bi bi-lightning-charge-fill" style="color: #6366f1;"></i>
                        </div>
                        <h5 class="fw-bold text-white mb-2">รวดเร็ว & ทรงพลัง</h5>
                        <p class="text-muted mb-0 text">ระบบทำงานอย่างมีประสิทธิภาพด้วยสถาปัตยกรรมที่ทันสมัย
                            โหลดข้อมูลได้ทันใจ
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 p-4 border-0"
                        style="background: rgba(30, 41, 59, 0.3); border: 1px solid rgba(255,255,255,0.05); border-radius: 18px; transition: all 0.3s ease;">
                        <div class="fs-1 text-success mb-3">
                            <i class="bi bi-palette-fill" style="color: #10b981;"></i>
                        </div>
                        <h5 class="fw-bold text-white mb-2">ดีไซน์พรีเมียม</h5>
                        <p class="text-muted mb-0">ตกแต่งหน้าตาผู้ใช้งานด้วยความพิถีพิถัน สบายตา รองรับทุกขนาดหน้าจอ</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 p-4 border-0"
                        style="background: rgba(30, 41, 59, 0.3); border: 1px solid rgba(255,255,255,0.05); border-radius: 18px; transition: all 0.3s ease;">
                        <div class="fs-1 text-warning mb-3">
                            <i class="bi bi-shield-check" style="color: #f59e0b;"></i>
                        </div>
                        <h5 class="fw-bold text-white mb-2">ปลอดภัย & มั่นใจ</h5>
                        <p class="text-muted mb-0">ข้อมูลของคุณได้รับการปกป้องด้วยมาตรฐานความปลอดภัยระดับสูง</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
