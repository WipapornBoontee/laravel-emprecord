@extends('layouts.app')

@section('title', 'หน้าหลัก')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8 text-center">
        <!-- Welcome Card -->
        <div class="card card-custom p-5 shadow-lg border-0">
            <div class="mb-4">
                <span class="badge px-3 py-2 text-uppercase fw-semibold"
                    style="background: rgba(99, 102, 241, 0.15); color: #818cf8; border: 1px solid rgba(99, 102, 241, 0.25);">
                    <i class="bi bi-shield-check me-1"></i> เข้าสู่ระบบสำเร็จ
                </span>
            </div>

            <h1 class="display-5 fw-bold text-white mb-3">
                Hello, <span style="background: linear-gradient(135deg, #a5b4fc 0%, #6366f1 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">{{ Auth::user()->name ?? Auth::user()->emp_code }}</span> 👋
            </h1>

            <p class="text-muted fs-5 mb-4">
                ยินดีต้อนรับเข้าสู่ระบบจัดการข้อมูลพนักงาน (Employee Management System)
            </p>

            <div class="d-flex justify-content-center gap-3">
                <div class="p-3 px-4 rounded-4 text-start" style="background: rgba(15, 23, 42, 0.5); border: 1px solid rgba(255, 255, 255, 0.08);">
                    <div class="text-muted small">รหัสพนักงาน:</div>
                    <div class="text-info fw-semibold">{{ Auth::user()->emp_code ?? '-' }}</div>
                </div>

                <div class="p-3 px-4 rounded-4 text-start" style="background: rgba(15, 23, 42, 0.5); border: 1px solid rgba(255, 255, 255, 0.08);">
                    <div class="text-muted small">บทบาท (Role):</div>
                    <div>
                        @if(Auth::user()?->role === 'admin')
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Admin</span>
                        @elseif(Auth::user()?->role === 'hr')
                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle">HR</span>
                        @else
                            <span class="badge bg-success-subtle text-success border border-success-subtle">Employee</span>
                        @endif
                    </div>
                </div>

                <div class="p-3 px-4 rounded-4 text-start" style="background: rgba(15, 23, 42, 0.5); border: 1px solid rgba(255, 255, 255, 0.08);">
                    <div class="text-muted small">สถานะ:</div>
                    <div class="text-success fw-semibold"><i class="bi bi-circle-fill fs-6 me-1" style="font-size: 0.6rem !important;"></i> Active</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
