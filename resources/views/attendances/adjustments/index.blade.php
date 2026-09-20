@extends('layouts.app')

@section('title', 'พิจารณาอนุมัติคำขอปรับเวลาทำงาน')

@push('styles')
<style>
    .adj-card {
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
    .filter-input option {
        background: var(--dropdown-bg);
        color: var(--text-main);
    }
    .btn-approve {
        background: #10b981;
        color: white;
        border: none;
        border-radius: 10px;
        padding: 6px 14px;
        font-weight: 600;
        font-size: 0.85rem;
        transition: all 0.2s ease;
    }
    .btn-approve:hover {
        background: #059669;
        color: white;
        transform: translateY(-1px);
    }
    .btn-reject {
        background: #ef4444;
        color: white;
        border: none;
        border-radius: 10px;
        padding: 6px 14px;
        font-weight: 600;
        font-size: 0.85rem;
        transition: all 0.2s ease;
    }
    .btn-reject:hover {
        background: #dc2626;
        color: white;
        transform: translateY(-1px);
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
                    <div class="action-icon icon-amber">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-1 gradient-text">พิจารณาอนุมัติคำขอปรับเวลาทำงาน</h3>
                        <p class="text-muted mb-0 small">
                            ตรวจสอบและพิจารณาอนุมัติคำขอปรับปรุงเวลาเข้า-ออกงานย้อนหลังของพนักงาน (เมื่ออนุมัติ ระบบจะซิงค์เวลาทำงานให้อัตโนมัติ)
                        </p>
                    </div>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <a href="{{ route('attendances.report', [], false) }}" class="btn btn-outline-secondary rounded-3 px-3 py-2 text-decoration-none">
                        <i class="bi bi-file-earmark-bar-graph-fill me-1 text-success"></i> รายงานเวลาทำงาน
                    </a>
                    <a href="{{ route('attendances.my-history', [], false) }}" class="btn btn-outline-secondary rounded-3 px-3 py-2 text-decoration-none">
                        <i class="bi bi-calendar2-check me-1 text-primary"></i> ประวัติของฉัน
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
        <div class="adj-card p-4">
            <form method="GET" action="{{ route('attendances.adjustments.index', [], false) }}" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control filter-input" 
                        placeholder="ค้นหาชื่อ หรือรหัสพนักงาน..." value="{{ $search }}">
                </div>
                <div class="col-md-3">
                    <select name="department_id" class="form-select filter-input">
                        <option value="">-- ทุกแผนก --</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ $departmentId == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select filter-input">
                        <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>รออนุมัติ (Pending)</option>
                        <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>อนุมัติแล้ว (Approved)</option>
                        <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>ปฏิเสธ (Rejected)</option>
                        <option value="all" {{ $status === 'all' ? 'selected' : '' }}>-- ทั้งหมด --</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 rounded-3 d-flex align-items-center justify-content-center gap-1">
                        <i class="bi bi-funnel-fill"></i> ค้นหา
                    </button>
                    @if(request()->hasAny(['search', 'department_id']) || (request('status') && request('status') !== 'pending'))
                        <a href="{{ route('attendances.adjustments.index', [], false) }}" class="btn btn-outline-secondary rounded-3 d-flex align-items-center justify-content-center" title="ล้างตัวกรอง">
                            <i class="bi bi-arrow-clockwise"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Adjustments Table -->
    <div class="col-12">
        <div class="adj-card overflow-hidden">
            <div class="p-3 px-4 border-bottom border-theme d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="fw-bold text-theme">
                    <i class="bi bi-table me-1 text-primary"></i> รายการคำขอปรับเวลา ({{ $adjustments->total() }} รายการ)
                </div>
                <!-- Rows Per Page Selector -->
                <form action="{{ route('attendances.adjustments.index', [], false) }}" method="GET" class="d-flex align-items-center gap-2 m-0">
                    <input type="hidden" name="status" value="{{ $status }}">
                    <input type="hidden" name="department_id" value="{{ $departmentId }}">
                    <input type="hidden" name="search" value="{{ $search }}">
                    <label for="per_page_select_adj" class="small text-muted mb-0 text-nowrap">แสดงต่อหน้า:</label>
                    <select name="per_page" id="per_page_select_adj" class="form-select form-select-sm filter-input py-1" style="width: 85px;" onchange="this.form.submit()">
                        <option value="5" {{ ($perPage ?? 10) == 5 ? 'selected' : '' }}>5</option>
                        <option value="10" {{ ($perPage ?? 10) == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ ($perPage ?? 10) == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ ($perPage ?? 10) == 50 ? 'selected' : '' }}>50</option>
                    </select>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th>พนักงาน</th>
                            <th>วันที่ขอปรับ</th>
                            <th>เวลาที่ขอปรับ (เข้า - ออก)</th>
                            <th>เหตุผลความจำเป็น</th>
                            <th>หลักฐาน</th>
                            <th>สถานะ</th>
                            <th class="text-end" style="width: 170px;">ดำเนินการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($adjustments as $adj)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-circle" style="width: 36px; height: 36px; font-size: 0.9rem;">
                                            {{ mb_substr($adj->user->name ?? '?', 0, 1) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('employees.show', $adj->user_id, false) }}" class="fw-bold text-theme text-decoration-none d-block">
                                                {{ $adj->user->name ?? '-' }}
                                            </a>
                                            <span class="text-muted small">
                                                <code>{{ $adj->user->emp_code ?? '-' }}</code> • {{ $adj->user->department->name ?? 'ไม่ระบุแผนก' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-semibold text-theme">{{ \Carbon\Carbon::parse($adj->target_date)->format('d/m/Y') }}</span>
                                    <div class="small text-muted">{{ \Carbon\Carbon::parse($adj->target_date)->format('l') }}</div>
                                </td>
                                <td>
                                    <div class="font-monospace">
                                        <span class="text-success fw-semibold">{{ $adj->requested_check_in ? substr($adj->requested_check_in, 0, 5) . ' น.' : '-' }}</span>
                                        <span class="text-muted mx-1">ถึง</span>
                                        <span class="text-warning fw-semibold">{{ $adj->requested_check_out ? substr($adj->requested_check_out, 0, 5) . ' น.' : '-' }}</span>
                                    </div>
                                    @if($adj->attendance)
                                        <div class="small text-muted" style="font-size: 0.75rem;">
                                            เวลาเดิม: {{ $adj->attendance->check_in ? substr($adj->attendance->check_in, 0, 5) : '-' }} / {{ $adj->attendance->check_out ? substr($adj->attendance->check_out, 0, 5) : '-' }}
                                        </div>
                                    @else
                                        <div class="small text-danger" style="font-size: 0.75rem;">เดิม: ยังไม่มีการลงเวลา</div>
                                    @endif
                                </td>
                                <td style="max-width: 250px;">
                                    <div class="text-truncate" title="{{ $adj->reason }}">{{ $adj->reason }}</div>
                                    @if($adj->status === 'rejected' && $adj->reject_reason)
                                        <div class="small text-danger mt-1">เหตุผลปฏิเสธ: {{ $adj->reject_reason }}</div>
                                    @endif
                                </td>
                                <td>
                                    @if($adj->attachment_url)
                                        <a href="{{ $adj->attachment_url }}" target="_blank" class="btn btn-sm btn-outline-info rounded-pill px-2 py-1">
                                            <i class="bi bi-paperclip me-1"></i>ดูหลักฐาน
                                        </a>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($adj->status === 'approved')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                            <i class="bi bi-check-circle-fill me-1"></i>อนุมัติแล้ว
                                        </span>
                                        @if($adj->approver)
                                            <div class="small text-muted mt-1" style="font-size: 0.75rem;">โดย {{ $adj->approver->name }}</div>
                                        @endif
                                    @elseif($adj->status === 'rejected')
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                            <i class="bi bi-x-circle-fill me-1"></i>ปฏิเสธ
                                        </span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">
                                            <i class="bi bi-hourglass-split me-1"></i>รออนุมัติ
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if($adj->status === 'pending')
                                        @if($adj->user_id === Auth::id())
                                            <span class="badge bg-secondary-subtle text-muted">คำขอของคุณ</span>
                                        @else
                                            <div class="d-inline-flex align-items-center gap-1">
                                                <form action="{{ route('attendances.adjustments.approve', $adj, false) }}" method="POST" class="d-inline"
                                                    onsubmit="return confirm('ยืนยันอนุมัติและปรับปรุงเวลาทำงานให้ {{ $adj->user->name }}?');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-approve">
                                                        <i class="bi bi-check-lg"></i> อนุมัติ
                                                    </button>
                                                </form>
                                                <button type="button" class="btn btn-reject" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $adj->id }}">
                                                    <i class="bi bi-x-lg"></i> ปฏิเสธ
                                                </button>
                                            </div>

                                            <!-- Reject Reason Modal -->
                                            <div class="modal fade text-start" id="rejectModal{{ $adj->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered modal-sm">
                                                    <div class="modal-content form-card p-3 border-0 shadow-lg" style="background: var(--surface-bg); border: 1px solid var(--surface-border); border-radius: 20px;">
                                                        <div class="modal-header border-0 pb-0">
                                                            <h6 class="modal-title fw-bold text-danger">ปฏิเสธคำขอปรับเวลา</h6>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form action="{{ route('attendances.adjustments.reject', $adj, false) }}" method="POST">
                                                            @csrf
                                                            <div class="modal-body">
                                                                <label class="form-label small fw-semibold text-theme">ระบุเหตุผลการปฏิเสธ</label>
                                                                <textarea name="reject_reason" class="form-control filter-input" rows="3" placeholder="ระบุเหตุผล..." required></textarea>
                                                            </div>
                                                            <div class="modal-footer border-0 pt-0">
                                                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-3" data-bs-dismiss="modal">ยกเลิก</button>
                                                                <button type="submit" class="btn btn-danger btn-sm rounded-3">ยืนยันปฏิเสธ</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-muted small">ดำเนินการแล้ว</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                                    <span>ไม่พบรายการคำขอปรับเวลาทำงานตามเงื่อนไข</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($adjustments->hasPages())
                <div class="p-3 border-top border-theme d-flex justify-content-center">
                    {{ $adjustments->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
