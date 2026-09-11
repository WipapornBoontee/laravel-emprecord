@extends('layouts.app')

@section('title', 'ยื่นใบลา')

@push('styles')
<style>
    /* Custom styles to match the dashboard theme */
    .form-card {
        background: var(--surface-bg);
        border: 1px solid var(--surface-border);
        border-radius: 24px;
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.1);
    }
    
    .form-control-custom {
        background: var(--badge-bg);
        border: 1px solid var(--surface-border);
        color: var(--text-main);
        border-radius: 16px;
        padding: 0.85rem 1.2rem;
        transition: all 0.3s ease;
        font-size: 0.95rem;
    }
    
    .form-control-custom:focus {
        background: var(--badge-bg);
        border-color: var(--accent-color);
        box-shadow: 0 0 0 4px var(--accent-glow);
        color: var(--text-main);
        outline: none;
    }
    
    /* For select dropdown options in dark/light mode */
    .form-control-custom option {
        background: var(--dropdown-bg);
        color: var(--text-main);
    }
    
    .form-label-custom {
        color: var(--text-main);
        font-weight: 600;
        font-size: 0.95rem;
        margin-bottom: 0.6rem;
    }
    
    .btn-submit-custom {
        background: var(--primary-gradient);
        color: white;
        border: none;
        border-radius: 16px;
        padding: 12px 32px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 10px 20px -10px rgba(99, 102, 241, 0.6);
    }
    
    .btn-submit-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 25px -10px rgba(99, 102, 241, 0.8);
        color: white;
    }
    
    .btn-cancel-custom {
        background: var(--badge-bg);
        border: 1px solid var(--surface-border);
        color: var(--text-muted);
        border-radius: 16px;
        padding: 12px 28px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-cancel-custom:hover {
        background: rgba(239, 68, 68, 0.1);
        border-color: rgba(239, 68, 68, 0.3);
        color: #ef4444;
    }
</style>
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8 col-xl-7">
        
        <!-- Header Section matching dashboard style -->
        <div class="hero-welcome-card p-4 mb-4">
            <div class="d-flex align-items-center gap-4">
                <div class="action-icon icon-indigo">
                    <i class="bi bi-calendar2-plus-fill"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-1 gradient-text">ยื่นคำขอลาหยุดงาน</h3>
                    <p class="text-muted mb-0 small">กรอกข้อมูลการลาเพื่อส่งคำขอให้ผู้บังคับบัญชาหรือฝ่ายบุคคลพิจารณาอนุมัติ</p>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="form-card p-4 p-md-5 mb-5">
            @if ($errors->any())
                <div class="alert alert-danger rounded-4 border-0 mb-4" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                    <div class="d-flex align-items-center mb-2 fw-bold">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> โปรดตรวจสอบข้อมูลอีกครั้ง
                    </div>
                    <ul class="mb-0 small">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('apply-leave.store') }}">
                @csrf

                <div class="mb-4">
                    <label for="leave_type_id" class="form-label form-label-custom">
                        <i class="bi bi-bookmark-star me-1 text-primary"></i> ประเภทการลา <span class="text-danger">*</span>
                    </label>
                    <select class="form-select form-control-custom w-100" id="leave_type_id" name="leave_type_id" required>
                        <option value="" disabled selected>-- กรุณาเลือกประเภทการลา --</option>
                        @foreach ($leaveTypes as $leaveType)
                            <option value="{{ $leaveType->id }}" {{ old('leave_type_id') == $leaveType->id ? 'selected' : '' }}>
                                {{ $leaveType->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <label for="start_date" class="form-label form-label-custom">
                            <i class="bi bi-calendar-event me-1 text-success"></i> วันที่เริ่มต้น <span class="text-danger">*</span>
                        </label>
                        <input type="date" class="form-control form-control-custom" id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="end_date" class="form-label form-label-custom">
                            <i class="bi bi-calendar-check me-1 text-warning"></i> ถึงวันที่ <span class="text-danger">*</span>
                        </label>
                        <input type="date" class="form-control form-control-custom" id="end_date" name="end_date" value="{{ old('end_date') }}" required>
                    </div>
                </div>

                <div class="mb-5">
                    <label for="reason" class="form-label form-label-custom">
                        <i class="bi bi-chat-text me-1 text-info"></i> เหตุผลการลา <span class="text-danger">*</span>
                    </label>
                    <textarea class="form-control form-control-custom" id="reason" name="reason" rows="4" placeholder="โปรดระบุเหตุผลที่ชัดเจนเพื่อประกอบการพิจารณา..." required>{{ old('reason') }}</textarea>
                </div>

                <div class="d-flex align-items-center justify-content-between pt-4 mt-2" style="border-top: 1px dashed var(--surface-border);">
                    <a href="{{ route('dashboard') }}" class="btn btn-cancel-custom d-flex align-items-center gap-2 text-decoration-none">
                        <i class="bi bi-x-lg"></i> ยกเลิก
                    </a>
                    <button type="submit" class="btn btn-submit-custom d-flex align-items-center gap-2">
                        <i class="bi bi-send-fill"></i> ส่งคำขอลา
                    </button>
                </div>
            </form>
        </div>
        
    </div>
</div>
@endsection