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
    .filter-input option {
        background: var(--dropdown-bg);
        color: var(--text-main);
    }
    .btn-apply-leave {
        background: var(--primary-gradient);
        color: white;
        border: none;
        border-radius: 12px;
        padding: 8px 20px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 14px -3px rgba(99, 102, 241, 0.5);
        text-decoration: none;
    }
    .btn-apply-leave:hover {
        transform: translateY(-2px);
        color: white;
        box-shadow: 0 8px 20px -4px rgba(99, 102, 241, 0.7);
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
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <a href="{{ route('leaves.balances', [], false) }}" class="btn btn-outline-secondary rounded-3 px-3 py-2 text-decoration-none">
                        <i class="bi bi-pie-chart-fill me-1 text-primary"></i> สิทธิ์วันลาคงเหลือ
                    </a>
                    @if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('HR'))
                        <a href="{{ route('leaves.approvals', [], false) }}" class="btn btn-outline-secondary rounded-3 px-3 py-2 text-decoration-none">
                            <i class="bi bi-check2-square me-1 text-success"></i> ศูนย์พิจารณาอนุมัติ
                        </a>
                    @endif
                    <a href="{{ route('leaves.create', [], false) }}" class="btn btn-apply-leave d-inline-flex align-items-center gap-2">
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
                <input type="hidden" name="per_page" value="{{ $perPage ?? 10 }}">
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
            <div class="p-3 px-4 border-bottom border-theme d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="fw-bold text-theme">
                    <i class="bi bi-table me-1 text-primary"></i> รายการประวัติการลา ({{ $leaveRequests->total() }} รายการ)
                </div>
                <!-- Rows Per Page Selector -->
                <form action="{{ route('leaves.index', [], false) }}" method="GET" class="d-flex align-items-center gap-2 m-0">
                    <input type="hidden" name="status" value="{{ $status ?? '' }}">
                    <input type="hidden" name="year" value="{{ $year ?? date('Y') }}">
                    <label for="per_page_select_history" class="small text-muted mb-0 text-nowrap">แสดงต่อหน้า:</label>
                    <select name="per_page" id="per_page_select_history" class="form-select form-select-sm filter-input py-1" style="width: 85px;" onchange="this.form.submit()">
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
                            <th style="width: 80px;">#</th>
                            <th>ประเภทการลา</th>
                            <th>ช่วงวันที่ลา</th>
                            <th>จำนวนวัน</th>
                            <th>เหตุผลการลา</th>
                            <th>เอกสารแนบ</th>
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
                                 <td style="max-width: 250px;">
                                    @if($leave->attachment_url)
                                        <button type="button" 
                                            class="btn btn-sm btn-outline-info rounded-pill px-2 py-1 view-attachment-btn" 
                                            data-url="{{ $leave->attachment_url }}"
                                            data-title="เอกสารแนบ - {{ $leave->user->name }} ({{ $leave->leaveType->name ?? 'การลา' }})">
                                            <i class="bi bi-paperclip me-1"></i>ดูเอกสารแนบ
                                        </button>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
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
                                        <!-- <div class="small fw-semibold">{{ $leave->approver->name }}</div> -->
                                        <!-- <span class="text-muted small">{{ $leave->approved_at ? \Carbon\Carbon::parse($leave->approved_at)->format('d/m/Y H:i') : '' }}</span> -->
                                    @endif
                                    @if($leave->status === 'rejected' && $leave->remark)
                                        <div class="small text-danger mt-1">
                                            <strong>เหตุผล:</strong> {{ $leave->remark }}
                                        </div>
                                    @elseif($leave->remark)
                                        <div class="text-muted small fst-italic">"{{ $leave->remark }}"</div>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
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
                                <td colspan="9" class="text-center py-5 text-muted">
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

<!-- Modal สำหรับแสดงเอกสารแนบ -->
<div class="modal fade" id="attachmentPreviewModal" tabindex="-1" aria-labelledby="attachmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content form-card border-0 shadow-lg">
            <div class="modal-header border-bottom border-theme pb-3">
                <h5 class="modal-title fw-bold text-theme d-flex align-items-center gap-2" id="attachmentModalLabel">
                    <i class="bi bi-file-earmark-text text-primary fs-5"></i>
                    <span id="modalAttachmentTitle">เอกสารแนบการลา</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 text-center" style="min-height: 300px; background: rgba(0,0,0,0.03);">
                <div id="attachmentImageWrapper" class="d-none">
                    <img id="attachmentImage" src="" alt="เอกสารแนบ" class="img-fluid rounded shadow-sm" style="max-height: 70vh; object-fit: contain;">
                </div>
                <div id="attachmentPdfWrapper" class="d-none" style="height: 70vh;">
                    <iframe id="attachmentPdf" src="" style="width: 100%; height: 100%; border: none; border-radius: 8px;"></iframe>
                </div>
            </div>
            <div class="modal-footer border-top border-theme pt-2 d-flex justify-content-between">
                <a id="attachmentDownloadBtn" href="" target="_blank" download class="btn btn-sm btn-outline-primary rounded-pill px-3">
                    <i class="bi bi-box-arrow-up-right me-1"></i>เปิดในแท็บใหม่ / ดาวน์โหลด
                </a>
                <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">ปิดหน้าต่าง</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const previewModalEl = document.getElementById('attachmentPreviewModal');
        if (!previewModalEl) return;
        const previewModal = new bootstrap.Modal(previewModalEl);
        const titleEl = document.getElementById('modalAttachmentTitle');
        const imgWrapper = document.getElementById('attachmentImageWrapper');
        const imgEl = document.getElementById('attachmentImage');
        const pdfWrapper = document.getElementById('attachmentPdfWrapper');
        const pdfEl = document.getElementById('attachmentPdf');
        const downloadBtn = document.getElementById('attachmentDownloadBtn');

        document.querySelectorAll('.view-attachment-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const url = this.getAttribute('data-url');
                const title = this.getAttribute('data-title') || 'เอกสารแนบ';
                
                titleEl.textContent = title;
                downloadBtn.href = url;

                const isPdf = url.toLowerCase().split('?')[0].endsWith('.pdf');

                if (isPdf) {
                    imgWrapper.classList.add('d-none');
                    imgEl.src = '';
                    pdfEl.src = url;
                    pdfWrapper.classList.remove('d-none');
                } else {
                    pdfWrapper.classList.add('d-none');
                    pdfEl.src = '';
                    imgEl.src = url;
                    imgWrapper.classList.remove('d-none');
                }

                previewModal.show();
            });
        });

        // Clear preview on hide
        previewModalEl.addEventListener('hidden.bs.modal', function () {
            imgEl.src = '';
            pdfEl.src = '';
        });
    });
</script>
@endpush
@endsection
