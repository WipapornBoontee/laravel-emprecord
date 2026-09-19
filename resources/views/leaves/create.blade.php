@extends('layouts.app')

@section('title', 'ยื่นคำขอลาหยุดงาน')

@push('styles')
<style>
    .form-card {
        background: var(--surface-bg);
        border: 1px solid var(--surface-border);
        border-radius: 24px;
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.08);
    }
    .quota-mini-card {
        background: var(--badge-bg);
        border: 1px solid var(--surface-border);
        border-radius: 16px;
        padding: 1rem 1.2rem;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .quota-mini-card:hover {
        border-color: var(--accent-color);
        transform: translateY(-2px);
    }
    .form-control-custom {
        background: var(--badge-bg);
        border: 1px solid var(--surface-border);
        color: var(--text-main);
        border-radius: 14px;
        padding: 0.8rem 1.1rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }
    .form-control-custom:focus {
        background: var(--badge-bg);
        border-color: var(--accent-color);
        box-shadow: 0 0 0 4px var(--accent-glow);
        color: var(--text-main);
        outline: none;
    }
    .form-control-custom option {
        background: var(--dropdown-bg);
        color: var(--text-main);
    }
    .btn-submit-custom {
        background: var(--primary-gradient);
        color: white;
        border: none;
        border-radius: 14px;
        padding: 12px 32px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 10px 20px -10px rgba(99, 102, 241, 0.6);
    }
    .btn-submit-custom:hover {
        transform: translateY(-2px);
        color: white;
        box-shadow: 0 14px 24px -10px rgba(99, 102, 241, 0.8);
    }
    .btn-cancel-custom {
        background: var(--badge-bg);
        border: 1px solid var(--surface-border);
        color: var(--text-muted);
        border-radius: 14px;
        padding: 12px 28px;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
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
    <div class="col-lg-10 col-xl-9">
        
        <!-- Header Banner -->
        <div class="hero-welcome-card p-4 mb-4">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="action-icon icon-indigo">
                        <i class="bi bi-calendar2-plus-fill"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-1 gradient-text">ยื่นคำขอลาหยุดงาน</h3>
                        <p class="text-muted mb-0 small">กรอกรายละเอียดเพื่อส่งคำขอลาให้ฝ่ายบุคคลและหัวหน้างานพิจารณา</p>
                    </div>
                </div>
                <div>
                    <a href="{{ route('leaves.index', [], false) }}" class="btn btn-outline-secondary btn-sm rounded-3">
                        <i class="bi bi-clock-history me-1"></i> ดูประวัติคำขอลา
                    </a>
                </div>
            </div>
        </div>

        <!-- Real-Time Leave Quota Overview -->
        <div class="row g-3 mb-4">
            @forelse($leaveBalances as $balance)
                <div class="col-sm-4">
                    <div class="quota-mini-card">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="fw-bold small text-theme">{{ $balance->leaveType->name ?? 'การลา' }}</span>
                            <span class="badge bg-primary-subtle text-primary fw-bold">
                                เหลือ {{ $balance->remaining_days }} วัน
                            </span>
                        </div>
                        <div class="progress my-2" style="height: 6px;">
                            @php
                                $percent = $balance->total_days > 0 ? ($balance->used_days / $balance->total_days) * 100 : 0;
                            @endphp
                            <div class="progress-bar bg-primary" style="width: {{ $percent }}%"></div>
                        </div>
                        <div class="d-flex justify-content-between text-muted" style="font-size: 0.75rem;">
                            <span>ใช้ไป: {{ $balance->used_days }} วัน</span>
                            <span>เต็ม: {{ $balance->total_days }} วัน</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info rounded-4 border-0 small mb-0">
                        <i class="bi bi-info-circle me-1"></i> ยังไม่มีการจัดสรรโควตาวันลาประจำปี ระบบจะอิงตามโควตาเริ่มต้นของประเภทการลา
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Form Card -->
        <div class="form-card p-4 p-md-5 mb-5">
            @if ($errors->any())
                <div class="alert alert-danger rounded-4 border-0 mb-4" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                    <div class="d-flex align-items-center mb-2 fw-bold">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> ไม่สามารถส่งคำขอลาได้ โปรดตรวจสอบข้อผิดพลาด
                    </div>
                    <ul class="mb-0 small">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('leaves.store', [], false) }}" id="leaveForm" enctype="multipart/form-data">
                @csrf

                <div class="mb-4">
                    <label for="leave_type_id" class="form-label fw-semibold small text-theme">
                        <i class="bi bi-bookmark-star-fill text-primary me-1"></i> ประเภทการลา <span class="text-danger">*</span>
                    </label>
                    <select class="form-select form-control-custom" id="leave_type_id" name="leave_type_id" required>
                        <option value="" disabled selected>-- กรุณาเลือกประเภทการลา --</option>
                        @foreach($leaveTypes as $type)
                            @php
                                $userBalance = $leaveBalances->firstWhere('leave_type_id', $type->id);
                                $remaining = $userBalance ? $userBalance->remaining_days : $type->default_days;
                            @endphp
                            <option value="{{ $type->id }}" {{ old('leave_type_id') == $type->id ? 'selected' : '' }} data-remaining="{{ $remaining }}">
                                {{ $type->name }} (คงเหลือ {{ $remaining }} วัน)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-5">
                        <label for="start_date" class="form-label fw-semibold small text-theme">
                            <i class="bi bi-calendar-event text-success me-1"></i> วันที่เริ่มต้น <span class="text-danger">*</span>
                        </label>
                        <input type="date" class="form-control form-control-custom" id="start_date" name="start_date" 
                            value="{{ old('start_date', date('Y-m-d')) }}" required>
                    </div>

                    <div class="col-md-5">
                        <label for="end_date" class="form-label fw-semibold small text-theme">
                            <i class="bi bi-calendar-check text-warning me-1"></i> ถึงวันที่ <span class="text-danger">*</span>
                        </label>
                        <input type="date" class="form-control form-control-custom" id="end_date" name="end_date" 
                            value="{{ old('end_date', date('Y-m-d')) }}" required>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fw-semibold small text-theme">
                            <i class="bi bi-calculator me-1 text-info"></i> รวม
                        </label>
                        <div class="form-control form-control-custom d-flex align-items-center justify-content-center fw-bold text-primary" id="calculatedDays">
                            1 วัน
                        </div>
                    </div>
                </div>

                <div class="mb-5">
                    <label for="reason" class="form-label fw-semibold small text-theme">
                        <i class="bi bi-chat-left-text-fill text-info me-1"></i> เหตุผลการขอลา <span class="text-danger">*</span>
                    </label>
                    <textarea class="form-control form-control-custom" id="reason" name="reason" rows="4" 
                        placeholder="ระบุเหตุผลความจำเป็น เช่น ป่วยเป็นไข้หวัดพบแพทย์, ติดต่อธุระราชการ..." required>{{ old('reason') }}</textarea>
                </div>

                <div class="mb-5">
                    <label for="attachment" class="form-label fw-semibold small text-theme">
                        <i class="bi bi-paperclip text-secondary me-1"></i> แนบเอกสาร/ใบรับรองแพทย์ (ถ้ามี)
                    </label>
                    <input type="file" class="form-control form-control-custom" id="attachment" name="attachment" accept=".jpg,.jpeg,.png,.pdf" onchange="checkFileSize(this)">
                    <div class="form-text text-muted small mt-2">
                        <i class="bi bi-info-circle me-1"></i>รองรับไฟล์รูปภาพ (JPG, PNG) หรือ PDF ขนาดไม่เกิน 5MB
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex align-items-center justify-content-between pt-4" style="border-top: 1px dashed var(--surface-border);">
                    <a href="{{ route('leaves.index', [], false) }}" class="btn btn-cancel-custom d-flex align-items-center gap-2">
                        <i class="bi bi-x-lg"></i> ยกเลิก
                    </a>
                    <button type="submit" class="btn btn-submit-custom d-flex align-items-center gap-2">
                        <i class="bi bi-send-fill"></i> ยื่นส่งคำขอลา
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

<script>
    function checkFileSize(input) {
        if (input.files && input.files[0]) {
            const fileSize = input.files[0].size / 1024 / 1024; // MB
            if (fileSize > 5) {
                alert('ขนาดไฟล์เกิน 5MB กรุณาเลือกไฟล์ใหม่');
                input.value = ''; // เคลียร์ค่า
            }
        }
    }

    // คำนวณจำนวนวันลาอัตโนมัติ
    function calculateDays() {
        const startInput = document.getElementById('start_date').value;
        const endInput = document.getElementById('end_date').value;
        const daysDisplay = document.getElementById('calculatedDays');

        if (startInput && endInput) {
            const start = new Date(startInput);
            const end = new Date(endInput);
            const diffTime = end - start;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;

            if (diffDays > 0) {
                daysDisplay.textContent = diffDays + ' วัน';
            } else {
                daysDisplay.textContent = 'ระบุผิด';
            }
        }
    }

    document.getElementById('start_date').addEventListener('change', calculateDays);
    document.getElementById('end_date').addEventListener('change', calculateDays);
    document.addEventListener('DOMContentLoaded', calculateDays);
</script>
@endsection
