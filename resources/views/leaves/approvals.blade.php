@extends('layouts.app')

@section('title', 'พิจารณาอนุมัติคำขอลา')

@push('styles')
<style>
    .approval-card {
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
    .nav-tabs-custom {
        border-bottom: 1px solid var(--surface-border);
    }
    .nav-tabs-custom .nav-link {
        color: var(--text-muted);
        border: none;
        border-bottom: 2px solid transparent;
        padding: 0.75rem 1.25rem;
        font-weight: 600;
        transition: all 0.2s ease;
    }
    .nav-tabs-custom .nav-link.active {
        color: var(--accent-color);
        border-bottom-color: var(--accent-color);
        background: transparent;
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
                        <i class="bi bi-check2-square"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-1 gradient-text">ศูนย์พิจารณาอนุมัติคำขอลา</h3>
                        <p class="text-muted mb-0 small">
                            ตรวจสอบและพิจารณาอนุมัติคำขอลาของพนักงานในองค์กร (ระบบจะตัดยอดวันลาและซิงค์การลงเวลาอัตโนมัติ)
                        </p>
                    </div>
                </div>
                <div>
                    <a href="{{ route('leaves.types.index', [], false) }}" class="btn btn-outline-secondary rounded-3 px-3 py-2 text-decoration-none">
                        <i class="bi bi-gear-fill me-1"></i> ตั้งค่าประเภทวันลา
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Mini Stat Cards -->
    <div class="col-sm-4">
        <div class="stat-card p-3 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small">รอการอนุมัติ (Pending)</span>
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
                <span class="text-muted small">อนุมัติแล้ว (Approved)</span>
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
                <span class="text-muted small">ไม่อนุมัติ / ปฏิเสธ (Rejected)</span>
                <h4 class="fw-bold mb-0 text-danger">{{ number_format($rejectedCount) }} <span class="fs-6 text-muted fw-normal">รายการ</span></h4>
            </div>
            <div class="stat-icon-wrapper icon-purple" style="width: 44px; height: 44px;">
                <i class="bi bi-x-circle-fill fs-5"></i>
            </div>
        </div>
    </div>

    <!-- Filter & Tabs Card -->
    <div class="col-12">
        <div class="approval-card p-4">
            <!-- Filter Tabs -->
            <ul class="nav nav-tabs nav-tabs-custom mb-4">
                <li class="nav-item">
                    <a class="nav-link {{ $status === 'pending' ? 'active' : '' }}" href="{{ route('leaves.approvals', ['status' => 'pending', 'department_id' => $departmentId, 'search' => $search], false) }}">
                        <i class="bi bi-hourglass-split me-1 text-warning"></i> รออนุมัติ ({{ $pendingCount }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $status === 'approved' ? 'active' : '' }}" href="{{ route('leaves.approvals', ['status' => 'approved', 'department_id' => $departmentId, 'search' => $search], false) }}">
                        <i class="bi bi-check-circle-fill me-1 text-success"></i> อนุมัติแล้ว ({{ $approvedCount }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $status === 'rejected' ? 'active' : '' }}" href="{{ route('leaves.approvals', ['status' => 'rejected', 'department_id' => $departmentId, 'search' => $search], false) }}">
                        <i class="bi bi-x-circle-fill me-1 text-danger"></i> ปฏิเสธ ({{ $rejectedCount }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $status === 'all' ? 'active' : '' }}" href="{{ route('leaves.approvals', ['status' => 'all', 'department_id' => $departmentId, 'search' => $search], false) }}">
                        <i class="bi bi-list-task me-1"></i> คำขอทั้งหมด
                    </a>
                </li>
            </ul>

            <!-- Search Form -->
            <form method="GET" action="{{ route('leaves.approvals', [], false) }}" class="row g-3">
                <input type="hidden" name="status" value="{{ $status }}">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text filter-input border-end-0 text-muted">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" class="form-control filter-input border-start-0" 
                            placeholder="ค้นหาชื่อ หรือรหัสพนักงาน..." value="{{ $search }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <select name="department_id" class="form-select filter-input">
                        <option value="">-- ทุกแผนก --</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ $departmentId == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 rounded-3 d-flex align-items-center justify-content-center gap-1">
                        <i class="bi bi-funnel-fill"></i> กรอง
                    </button>
                    @if(request()->hasAny(['search', 'department_id']))
                        <a href="{{ route('leaves.approvals', ['status' => $status], false) }}" class="btn btn-outline-secondary rounded-3 d-flex align-items-center justify-content-center" title="ล้างตัวกรอง">
                            <i class="bi bi-arrow-clockwise"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Approvals Table -->
    <div class="col-12">
        <div class="approval-card overflow-hidden">
            <div class="table-responsive">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th>พนักงาน</th>
                            <th>ประเภทการลา</th>
                            <th>ช่วงวันที่ลา</th>
                            <th>จำนวนวัน</th>
                            <th>เอกสารแนบ</th>
                            <th>เหตุผลการขอลา</th>
                            <th>สถานะ</th>
                            <th class="text-end" style="width: 180px;">ดำเนินการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leaveRequests as $leave)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-circle" style="width: 36px; height: 36px; font-size: 0.9rem;">
                                            {{ mb_substr($leave->user->name ?? '?', 0, 1) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('employees.show', $leave->user_id, false) }}" class="fw-bold text-theme text-decoration-none d-block">
                                                {{ $leave->user->name ?? 'ไม่พบผู้ใช้' }}
                                            </a>
                                            <span class="text-muted small">
                                                <code>{{ $leave->user->emp_code ?? '-' }}</code> • {{ $leave->user->department->name ?? 'ไม่ระบุแผนก' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary px-2 py-1">
                                        {{ $leave->leaveType->name ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-semibold">
                                        {{ \Carbon\Carbon::parse($leave->start_date)->format('d/m/Y') }}
                                        <span class="text-muted mx-1">-</span>
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
                                <td style="max-width: 250px;">
                                    <div class="small" title="{{ $leave->reason }}">{{ $leave->reason }}</div>
                                </td>
                                <td>
                                    @if($leave->status === 'approved')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                            <i class="bi bi-check-circle-fill me-1"></i>อนุมัติแล้ว
                                        </span>
                                        @if($leave->approver)
                                            <div class="small text-muted mt-1">โดย {{ $leave->approver->name }}</div>
                                        @endif
                                    @elseif($leave->status === 'rejected')
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                            <i class="bi bi-x-circle-fill me-1"></i>ปฏิเสธ
                                        </span>
                                        @if($leave->remark)
                                            <div class="small text-muted mt-1 fst-italic">"{{ $leave->remark }}"</div>
                                        @endif
                                    @else
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">
                                            <i class="bi bi-hourglass-split me-1"></i>รออนุมัติ
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if($leave->status === 'pending')
                                        @if(Auth::id() === $leave->user_id)
                                            <!-- กรณีคำขอลาของตนเอง (Self-Request) ห้ามอนุมัติตัวเอง -->
                                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1" 
                                                title="คุณไม่สามารถอนุมัติคำขอลาของตนเองได้ ต้องรอให้ Admin ดำเนินการ">
                                                <i class="bi bi-lock-fill me-1"></i>รอ Admin อนุมัติ (คำขอของคุณ)
                                            </span>
                                        @else
                                            <div class="d-inline-flex align-items-center gap-1">
                                                <!-- Approve Form -->
                                                <form action="{{ route('leaves.approvals.approve', $leave, false) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn-approve d-inline-flex align-items-center gap-1"
                                                        onclick="return confirm('ยืนยันอนุมัติคำขอลาของ {{ $leave->user->name }} (จำนวน {{ $leave->days_count }} วัน)?\nระบบจะทำการตัดยอดวันลาและซิงค์สถานะลงเวลาทำงานทันที');">
                                                        <i class="bi bi-check-lg"></i> อนุมัติ
                                                    </button>
                                                </form>

                                                <!-- Reject Modal Trigger -->
                                                <button type="button" class="btn-reject d-inline-flex align-items-center gap-1" 
                                                    data-bs-toggle="modal" data-bs-target="#rejectModal{{ $leave->id }}">
                                                    <i class="bi bi-x-lg"></i> ปฏิเสธ
                                                </button>
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
                                    <span>ไม่พบรายการคำขอลาตามเงื่อนไข</span>
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

<!-- Render Modals Outside Table to Fix Backdrop Issue -->
@foreach($leaveRequests as $leave)
    @if($leave->status === 'pending')
        <!-- Reject Modal -->
        <div class="modal fade text-start" id="rejectModal{{ $leave->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content form-card p-3 border-0">
                    <div class="modal-header border-0">
                        <h5 class="modal-title fw-bold text-danger">
                            <i class="bi bi-x-circle-fill me-1"></i> ปฏิเสธคำขอลา
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('leaves.approvals.reject', $leave, false) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <p class="text-muted small">
                                ระบุเหตุผลในการไม่อนุมัติคำขอลาของ <strong>{{ $leave->user->name }}</strong>
                            </p>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-theme">หมายเหตุ / เหตุผลที่ปฏิเสธ</label>
                                <textarea name="remark" class="form-control filter-input" rows="3" 
                                    placeholder="เช่น ติดงานด่วนในช่วงเวลาดังกล่าว, แจ้งลากะทันหันเกินไป..." required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer border-0">
                            <button type="button" class="btn btn-outline-secondary rounded-3" data-bs-dismiss="modal">ยกเลิก</button>
                            <button type="submit" class="btn btn-danger rounded-3">ยืนยันปฏิเสธคำขอ</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endforeach

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
