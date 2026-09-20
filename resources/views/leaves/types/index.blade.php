@extends('layouts.app')

@section('title', 'จัดการประเภทการลา')

@push('styles')
<style>
    .type-card {
        background: var(--surface-bg);
        border: 1px solid var(--surface-border);
        border-radius: 20px;
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        box-shadow: 0 15px 35px -10px rgba(0, 0, 0, 0.08);
    }
    .form-control-custom {
        background: var(--badge-bg);
        border: 1px solid var(--surface-border);
        color: var(--text-main);
        border-radius: 14px;
        padding: 0.75rem 1.1rem;
        font-size: 0.95rem;
    }
    .form-control-custom:focus {
        background: var(--badge-bg);
        border-color: var(--accent-color);
        color: var(--text-main);
        outline: none;
    }
    .btn-submit-custom {
        background: var(--primary-gradient);
        color: white;
        border: none;
        border-radius: 14px;
        padding: 10px 24px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .btn-submit-custom:hover {
        transform: translateY(-2px);
        color: white;
    }
</style>
@endpush

@section('content')
<div class="row g-4">
    <!-- Header Banner -->
    <div class="col-12">
        <div class="hero-welcome-card p-4">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="action-icon icon-purple">
                        <i class="bi bi-gear-fill"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-1 gradient-text">จัดการประเภทการลาและโควตาเริ่มต้น</h3>
                        <p class="text-muted mb-0 small">กำหนดประเภทวันลาและโควตาวันลาเริ่มต้นประจำปีสำหรับพนักงานใหม่</p>
                    </div>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <a href="{{ route('settings.holidays.index', [], false) }}" class="btn btn-outline-secondary rounded-3 px-3 py-2 text-decoration-none">
                        <i class="bi bi-calendar-heart-fill me-1 text-danger"></i> ปฏิทินวันหยุดบริษัท
                    </a>
                    <a href="{{ route('leaves.index', [], false) }}" class="btn btn-outline-secondary rounded-3 px-3 py-2 text-decoration-none">
                        <i class="bi bi-calendar2-check-fill me-1 text-primary"></i> ประวัติการลาของฉัน
                    </a>
                    <a href="{{ route('leaves.approvals', [], false) }}" class="btn btn-outline-secondary rounded-3 px-3 py-2 text-decoration-none">
                        <i class="bi bi-check2-square me-1 text-success"></i> หน้ารายการอนุมัติคำขอลา
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Alerts -->
    @if(session('success'))
        <div class="col-12">
            <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 d-flex align-items-center gap-2 p-3 shadow-sm mb-0" 
                style="background: rgba(16, 185, 129, 0.12); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2) !important;">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <div class="fw-semibold">{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="col-12">
            <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 d-flex align-items-center gap-2 p-3 shadow-sm mb-0"
                style="background: rgba(239, 68, 68, 0.12); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2) !important;">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                <div class="fw-semibold">{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    <!-- Add Leave Type Form -->
    <div class="col-lg-4">
        <div class="type-card p-4">
            <h5 class="fw-bold mb-3 text-primary d-flex align-items-center gap-2">
                <i class="bi bi-plus-circle-fill"></i> เพิ่มประเภทการลาใหม่
            </h5>

            @if ($errors->any())
                <div class="alert alert-danger rounded-3 border-0 small mb-3">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('leaves.types.store', [], false) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold small text-theme">ชื่อประเภทการลา <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control form-control-custom" 
                        placeholder="เช่น ลาคลอด, ลาฝึกอบรม" required value="{{ old('name') }}">
                </div>
                <div class="mb-4">
                    <label for="default_days" class="form-label fw-semibold small text-theme">โควตาเริ่มต้นต่อปี (วัน) <span class="text-danger">*</span></label>
                    <input type="number" name="default_days" id="default_days" class="form-control form-control-custom" 
                        placeholder="เช่น 30 หรือ 6" required min="0" max="365" value="{{ old('default_days', 6) }}">
                </div>
                <button type="submit" class="btn btn-submit-custom w-100 d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-check-lg"></i> บันทึกประเภทการลา
                </button>
            </form>
        </div>
    </div>

    <!-- Leave Types List Table -->
    <div class="col-lg-8">
        <div class="type-card overflow-hidden">
            <div class="p-3 px-4 border-bottom border-theme d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="fw-bold text-theme">
                    <i class="bi bi-table me-1 text-primary"></i> ประเภทการลาทั้งหมด ({{ $leaveTypes->count() }} ประเภท)
                </div>
                <div>
                    <span class="badge bg-primary-subtle text-primary px-3 py-1 fw-bold">
                        {{ $leaveTypes->count() }} รายการ
                    </span>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th style="width: 70px;">#</th>
                            <th>ประเภทการลา</th>
                            <th>โควตาเริ่มต้น / ปี</th>
                            <th>จำนวนคำขอลาที่ใช้</th>
                            <th class="text-end" style="width: 140px;">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leaveTypes as $index => $type)
                            <tr>
                                <td class="text-muted">{{ $index + 1 }}</td>
                                <td class="fw-bold text-theme">
                                    <i class="bi bi-bookmark-fill text-primary me-2"></i>{{ $type->name }}
                                </td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary fs-6 px-2 py-1">
                                        {{ $type->default_days }} วัน
                                    </span>
                                </td>
                                <td>
                                    <span class="text-muted small">
                                        {{ $type->leave_requests_count }} รายการ
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex align-items-center gap-1">
                                        <!-- Edit Modal Trigger -->
                                        <button type="button" class="btn btn-outline-warning btn-sm rounded-3 py-1 px-2"
                                            data-bs-toggle="modal" data-bs-target="#editTypeModal{{ $type->id }}" title="แก้ไข">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>

                                        <!-- Delete Form -->
                                        @if($type->leave_requests_count === 0)
                                            <form action="{{ route('leaves.types.destroy', $type, false) }}" method="POST" class="d-inline"
                                                onsubmit="return confirm('ยืนยันลบประเภทการลา {{ $type->name }}?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm rounded-3 py-1 px-2" title="ลบ">
                                                    <i class="bi bi-trash3"></i>
                                                </button>
                                            </form>
                                        @else
                                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-3 py-1 px-2 disabled opacity-50" 
                                                title="ไม่สามารถลบได้เนื่องจากมีคำขอลาผูกอยู่">
                                                <i class="bi bi-lock-fill"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">ยังไม่มีประเภทการลาในระบบ</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modals Section (ย้ายออกมานอกตาราง เพื่อแก้ปัญหา Backdrop และ Form Submission บัค) -->
@foreach($leaveTypes as $type)
    <div class="modal fade text-start" id="editTypeModal{{ $type->id }}" tabindex="-1" aria-labelledby="editTypeModalLabel{{ $type->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content form-card border-0 shadow-lg p-3">
                <div class="modal-header border-bottom border-theme pb-3">
                    <h5 class="modal-title fw-bold text-theme d-flex align-items-center gap-2" id="editTypeModalLabel{{ $type->id }}">
                        <i class="bi bi-pencil-square text-warning"></i>
                        <span>แก้ไขประเภทการลา: {{ $type->name }}</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('leaves.types.update', $type, false) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-theme">ชื่อประเภทการลา <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control form-control-custom" 
                                value="{{ old('name', $type->name) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-theme">โควตาเริ่มต้นต่อปี (วัน) <span class="text-danger">*</span></label>
                            <input type="number" name="default_days" class="form-control form-control-custom" 
                                value="{{ old('default_days', $type->default_days) }}" min="0" max="365" required>
                        </div>
                    </div>
                    <div class="modal-footer border-top border-theme pt-2">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">ยกเลิก</button>
                        <button type="submit" class="btn btn-warning rounded-pill px-4 fw-semibold">บันทึกการแก้ไข</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

@endsection
