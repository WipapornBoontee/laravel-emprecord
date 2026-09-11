@extends('layouts.app')

@section('title', 'ยื่นใบลา')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-6">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-5">
                <div class="card-header bg-gradient text-white p-4" style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);">
                    <h4 class="mb-0 fw-bold d-flex align-items-center gap-2">
                        <i class="bi bi-calendar2-plus-fill"></i> ยื่นคำขอลาหยุดงาน
                    </h4>
                    <p class="text-white-50 mb-0 small mt-1">กรอกข้อมูลเพื่อส่งคำขอให้ผู้บังคับบัญชาหรือ HR อนุมัติ</p>
                </div>

                <div class="card-body p-4 p-md-5">
                    @if ($errors->any())
                        <div class="alert alert-danger rounded-3 border-0 shadow-sm alert-dismissible fade show">
                            <div class="d-flex align-items-center mb-2 fw-bold">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i> โปรดตรวจสอบข้อมูลอีกครั้ง
                            </div>
                            <ul class="mb-0 small">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('apply-leave.store') }}">
                        @csrf

                        <!-- Leave Type Selection -->
                        <div class="mb-4">
                            <label for="leave_type_id" class="form-label fw-bold text-secondary">
                                <i class="bi bi-bookmark-star me-1"></i> ประเภทการลา <span class="text-danger">*</span>
                            </label>
                            <select class="form-select form-select-lg bg-light border-0 shadow-sm" id="leave_type_id" name="leave_type_id" required>
                                <option value="" disabled selected>-- เลือกประเภทการลา --</option>
                                @foreach ($leaveTypes as $leaveType)
                                    <option value="{{ $leaveType->id }}" {{ old('leave_type_id') == $leaveType->id ? 'selected' : '' }}>
                                        {{ $leaveType->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Date Range Selection -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="start_date" class="form-label fw-bold text-secondary">
                                    <i class="bi bi-calendar-event me-1"></i> วันที่เริ่มลา <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control form-control-lg bg-light border-0 shadow-sm" id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="end_date" class="form-label fw-bold text-secondary">
                                    <i class="bi bi-calendar-check me-1"></i> ถึงวันที่ <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control form-control-lg bg-light border-0 shadow-sm" id="end_date" name="end_date" value="{{ old('end_date') }}" required>
                            </div>
                        </div>

                        <!-- Reason Textarea -->
                        <div class="mb-5">
                            <label for="reason" class="form-label fw-bold text-secondary">
                                <i class="bi bi-chat-text me-1"></i> เหตุผลการลา <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control bg-light border-0 shadow-sm rounded-3 p-3" id="reason" name="reason" rows="4" placeholder="ระบุเหตุผลการลาของคุณให้ชัดเจน..." required>{{ old('reason') }}</textarea>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex align-items-center justify-content-between mt-4 pt-4 border-top">
                            <a href="{{ route('dashboard') }}" class="btn btn-light px-4 py-2 fw-semibold text-muted rounded-pill transition-all">
                                <i class="bi bi-x-circle me-1"></i> ยกเลิก
                            </a>
                            <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow-sm d-flex align-items-center gap-2 rounded-pill transition-all" style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); border: none;">
                                <i class="bi bi-send-fill"></i> ส่งคำขอลา
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .transition-all {
        transition: all 0.3s ease;
    }
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(79, 70, 229, 0.3) !important;
    }
    .form-control:focus, .form-select:focus {
        box-shadow: 0 0 0 0.25rem rgba(124, 58, 237, 0.25);
        border-color: #7c3aed;
        background-color: #fff;
    }
</style>
@endsection