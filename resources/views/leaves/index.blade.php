@extends('layouts.app')

@section('title', 'ประวัติการลาของฉัน')

@push('styles')
<style>
    .leave-card {
        background: var(--surface-bg);
        border: 1px solid var(--surface-border);
        border-radius: 20px;
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        box-shadow: 0 15px 35px -10px rgba(0, 0, 0, 0.08);
    }
    .filter-input {
        background: var(--badge-bg);
        border: 1px solid var(--surface-border);
        color: var(--text-main);
        border-radius: 12px;
        padding: 0.65rem 1rem;
        font-size: 0.9rem;
    }
    .filter-input:focus {
        background: var(--badge-bg);
        border-color: var(--accent-color);
        color: var(--text-main);
        outline: none;
    }
    .btn-apply-leave {
        background: var(--primary-gradient);
        color: white;
        border: none;
        border-radius: 14px;
        padding: 10px 22px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 8px 18px -6px rgba(99, 102, 241, 0.6);
        text-decoration: none;
    }
    .btn-apply-leave:hover {
        transform: translateY(-2px);
        color: white;
        box-shadow: 0 12px 22px -6px rgba(99, 102, 241, 0.8);
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
                    <div class="action-icon icon-indigo">
                        <i class="bi bi-calendar2-check-fill"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-1 gradient-text">ประวัติการลาของฉัน</h3>
                        <p class="text-muted mb-0 small">ติดตามสถานะคำขอลาหยุดงาน และตรวจสอบประวัติการอนุมัติ</p>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('leaves.balances', [], false) }}" class="btn btn-outline-secondary rounded-3 px-3 py-2 text-decoration-none">
                        <i class="bi bi-pie-chart-fill me-1 text-primary"></i> สิทธิ์วันลาคงเหลือ
                    </a>
                    <a href="{{ route('leaves.create', [], false) }}" class="btn btn-apply-leave d-flex align-items-center gap-2">
                        <i class="bi bi-plus-lg"></i>
                        <span>ยื่นใบลาใหม่</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Mini Stat Cards -->
    <div class="col-sm-4">
        <div class="stat-card p-3 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small">รอการอนุมัติ</span>
                <h4 class="fw-bold mb-0 text-warning">{{ number_format($pendingCount) }} <span class="fs-6 text-muted fw-normal">รายการ</span></h4>
            </div>
            <div class="stat-icon-wrapper icon-amber" style="width: 44px; height: 44px;">
                <i class="bi bi-clock-history fs-5"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="stat-card p-3 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small">อนุมัติแล้ว</span>
                <h4 class="fw-bold mb-0 text-success">{{ number_format($approvedCount) }} <span class="fs-6 text-muted fw-normal">รายการ</span></h4>
            </div>
            <div class="stat-icon-wrapper icon-green" style="width: 44px; height: 44px;">
                <i class="bi bi-check-circle-fill fs-5"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="stat-card p-3 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small">ไม่อนุมัติ / ปฏิเสธ</span>
                <h4 class="fw-bold mb-0 text-danger">{{ number_format($rejectedCount) }} <span class="fs-6 text-muted fw-normal">รายการ</span></h4>
            </div>
            <div class="stat-icon-wrapper icon-purple" style="width: 44px; height: 44px;">
                <i class="bi bi-x-circle-fill fs-5"></i>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="col-12">
        <div class="leave-card p-4">
            <form method="GET" action="{{ route('leaves.index', [], false) }}" class="row g-3">
                <div class="col-md-5">
                    <select name="status" class="form-select filter-input">
                        <option value="">-- ทุกสถานะ --</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>รออนุมัติ (Pending)</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>อนุมัติแล้ว (Approved)</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>ปฏิเสธ (Rejected)</option>
                    </select>
                </div>
                <div class="col-md-5">
                    <select name="year" class="form-select filter-input">
                        @for($y = date('Y'); $y >= date('Y') - 3; $y--)
                            <option value="{{ $y }}" {{ request('year', date('Y')) == $y ? 'selected' : '' }}>ประจำปี {{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 rounded-3 d-flex align-items-center justify-content-center gap-1">
                        <i class="bi bi-funnel-fill"></i> ค้นหา
                    </button>
                    @if(request()->hasAny(['status', 'year']))
                        <a href="{{ route('leaves.index', [], false) }}" class="btn btn-outline-secondary rounded-3 d-flex align-items-center justify-content-center" title="ล้างตัวกรอง">
                            <i class="bi bi-arrow-clockwise"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Leave Requests Table -->
    <div class="col-12">
        <div class="leave-card overflow-hidden">
            <div class="table-responsive">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th style="width: 80px;">#</th>
                            <th>ประเภทการลา</th>
                            <th>ช่วงวันที่ลา</th>
                            <th>จำนวนวัน</th>
                            <th>เหตุผลการลา</th>
                            <th>สถานะคำขอ</th>
                            <th>การพิจารณา</th>
                            <th class="text-end" style="width: 100px;">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leaveRequests as $index => $leave)
                            <tr>
                                <td class="text-muted">{{ $leaveRequests->firstItem() + $index }}</td>
                                <td class="fw-bold text-theme">
                                    <i class="bi bi-bookmark-fill text-primary me-2"></i>{{ $leave->leaveType->name ?? '-' }}
                                </td>
                                <td>
                                    <div class="fw-semibold">
                                        {{ \Carbon\Carbon::parse($leave->start_date)->format('d/m/Y') }}
                                        <span class="text-muted mx-1">ถึง</span>
                                        {{ \Carbon\Carbon::parse($leave->end_date)->format('d/m/Y') }}
                                    </div>
                                    <span class="text-muted small">ยื่นเมื่อ {{ $leave->created_at->format('d/m/Y H:i') }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary fw-bold px-2 py-1 fs-6">
                                        {{ $leave->days_count }} วัน
                                    </span>
                                </td>
                                <td style="max-width: 250px;">
                                    <div class="text-truncate" title="{{ $leave->reason }}">{{ $leave->reason }}</div>
                                </td>
                                <td>
                                    @if($leave->status === 'approved')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                            <i class="bi bi-check-circle-fill me-1"></i>อนุมัติแล้ว
                                        </span>
                                    @elseif($leave->status === 'rejected')
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                            <i class="bi bi-x-circle-fill me-1"></i>ไม่อนุมัติ
                                        </span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">
                                            <i class="bi bi-hourglass-split me-1"></i>รออนุมัติ
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($leave->approver)
                                        <div class="small fw-semibold">{{ $leave->approver->name }}</div>
                                        <span class="text-muted small">{{ $leave->approved_at ? \Carbon\Carbon::parse($leave->approved_at)->format('d/m/Y H:i') : '' }}</span>
                                        @if($leave->remark)
                                            <div class="text-muted small fst-italic">"{{ $leave->remark }}"</div>
                                        @endif
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if($leave->status === 'pending')
                                        <form action="{{ route('leaves.cancel', $leave, false) }}" method="POST" class="d-inline"
                                            onsubmit="return confirm('คุณต้องการยกเลิกคำขอนี้ใช่หรือไม่?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-3 py-1 px-2" title="ยกเลิกคำขอ">
                                                <i class="bi bi-x-lg"></i> ยกเลิก
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-calendar-x fs-1 d-block mb-2 opacity-50"></i>
                                    <span>ยังไม่มีประวัติการยื่นคำขอลา</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($leaveRequests->hasPages())
                <div class="p-3 border-top border-theme d-flex justify-content-center">
                    {{ $leaveRequests->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
