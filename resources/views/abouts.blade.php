@extends('layout')

@section('title')
เกี่ยวกับเรา
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-lg mb-4" style="background: rgba(30, 41, 59, 0.4); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 20px; backdrop-filter: blur(10px);">
            <div class="card-body p-5">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="p-3 rounded-circle d-flex align-items-center justify-content-center" style="background: rgba(99, 102, 241, 0.15); width: 60px; height: 60px;">
                        <i class="bi bi-info-circle-fill text-primary fs-3" style="color: #818cf8 !important;"></i>
                    </div>
                    <div>
                        <h2 class="fw-bold text-white mb-0">เกี่ยวกับเราจร้าหหหหหหหหหหหหหหหหหห</h2>
                        <p class="text-muted mb-0">ข้อมูลเกี่ยวกับระบบและผู้พัฒนาโปรเจกต์นี้</p>
                    </div>
                </div>
                
                <hr style="border-color: rgba(255, 255, 255, 0.1);">

                <div class="row g-4 my-2">
                    <div class="col-sm-6">
                        <div class="p-4 rounded-4" style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.05);">
                            <span class="text-muted d-block small mb-1">ผู้พัฒนาระบบ</span>
                            <span class="fs-5 fw-bold text-white"><i class="bi bi-person-fill me-2 text-indigo" style="color: #818cf8;"></i>{{ $name }}</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-4 rounded-4" style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.05);">
                            <span class="text-muted d-block small mb-1">วันที่อัปเดต</span>
                            <span class="fs-5 fw-bold text-white"><i class="bi bi-calendar3 me-2 text-success" style="color: #34d399;"></i>{{ $date }}</span>
                        </div>
                    </div>
                </div>

                <hr style="border-color: rgba(255, 255, 255, 0.1);">

                <p class="lead text-muted-custom mt-4" style="color: var(--text-muted); line-height: 1.8;">
                    Lorem ipsum, dolor sit amet consectetur adipisicing elit. Voluptatem eveniet, quis odit architecto illum
                    dicta earum totam aliquam id, corrupti consectetur delectus corporis sapiente minus. Amet optio inventore ipsa ut!
                </p>
            </div>
        </div>
    </div>
</div>
@endsection