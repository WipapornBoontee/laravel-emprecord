@extends('layouts.app')

@section('title', 'จัดการคำขอทำงานล่วงเวลา (OT)')

@push('styles')
<style>
    .ot-card {
        background: var(--surface-bg);
        border: 1px solid var(--surface-border);
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        overflow: hidden;
        margin-bottom: 2rem;
        transition: background-color 0.3s ease, border-color 0.3s ease;
    }
    .ot-header {
        background: var(--primary-gradient);
        padding: 20px 25px;
        color: white;
        border-radius: 20px 20px 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }
    .ot-header.handled {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    }
    :root[data-bs-theme="dark"] .ot-header.handled {
        background: linear-gradient(135deg, rgba(30, 41, 59, 0.9) 0%, rgba(15, 23, 42, 0.9) 100%);
        border-bottom: 1px solid var(--surface-border);
    }
    .ot-empty-state {
        padding: 40px 20px;
        text-align: center;
        background: var(--badge-bg);
        border-radius: 16px;
        border: 1px dashed var(--surface-border);
        color: var(--text-muted);
    }
    .ot-badge-type {
        font-size: 0.75rem;
        padding: 0.25rem 0.6rem;
        border-radius: 50rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .ot-badge-normal {
        background-color: rgba(59, 130, 246, 0.15);
        color: #2563eb;
        border: 1px solid rgba(59, 130, 246, 0.3);
    }
    .ot-badge-holiday {
        background-color: rgba(245, 158, 11, 0.15);
        color: #d97706;
        border: 1px solid rgba(245, 158, 11, 0.3);
    }
    .ot-badge-holiday-ot {
        background-color: rgba(239, 68, 68, 0.15);
        color: #dc2626;
        border: 1px solid rgba(239, 68, 68, 0.3);
    }
    .ot-time-tag {
        font-family: monospace;
        font-weight: 600;
        background: var(--badge-bg);
        padding: 2px 8px;
        border-radius: 6px;
        border: 1px solid var(--surface-border);
    }
    .ot-detail-card {
        background: var(--surface-bg);
        border: 1px solid var(--surface-border);
        border-radius: 14px;
        padding: 1rem;
    }
</style>
@endpush

@section('content')
<div class="container py-4">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-x-circle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- ส่วนที่ 1: รายการรออนุมัติ -->
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="ot-card fade-in">
                <div class="ot-header">
                    <div>
                        <h5 class="mb-0 fw-bold"><i class="bi bi-hourglass-split me-2"></i>คำขอ OT ที่รอการอนุมัติ (Pending)</h5>
                        <small class="opacity-75">ตรวจสอบเวลาสแกนนิ้วจริงเทียบกับคำขอ ก่อนดำเนินการอนุมัติ</small>
                    </div>
                    <span class="badge bg-light text-dark fw-bold px-3 py-2 rounded-pill fs-6">
                        {{ count($pendingRequests) }} รายการ
                    </span>
                </div>
                
                <div class="card-body p-4">
                    @if(count($pendingRequests) > 0)
                        <form action="{{ route('overtime.bulkApprove') }}" method="POST" id="bulkApproveForm">
                            @csrf
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <button type="submit" class="btn btn-success fw-bold">
                                    <i class="bi bi-check2-all me-1"></i> อนุมัติรายการที่เลือก
                                </button>
                                <div class="text-muted small">
                                    <i class="bi bi-info-circle me-1"></i> กฎหมายแรงงาน: OT รวมไม่เกิน 36 ชม./สัปดาห์
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-custom mb-0 align-middle">
                                    <thead>
                                        <tr>
                                            <th style="width: 40px;"><input type="checkbox" id="selectAll" class="form-check-input"></th>
                                            <th>พนักงาน / แผนก</th>
                                            <th>วันที่ & ประเภท OT</th>
                                            <th>ช่วงเวลาที่ขอ (OT Time)</th>
                                            <th>การลงเวลาจริง (Attendance)</th>
                                            <th>รายละเอียดงาน</th>
                                            <th class="text-center" style="min-width: 170px;">จัดการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pendingRequests as $request)
                                            @php
                                                $att = $request->attendance;
                                                $hasAtt = $att && ($att->check_in || $att->check_out);
                                                $checkInTime = $att && $att->check_in ? \Carbon\Carbon::parse($att->check_in)->format('H:i') : null;
                                                $checkOutTime = $att && $att->check_out ? \Carbon\Carbon::parse($att->check_out)->format('H:i') : null;
                                                
                                                $typeClass = match($request->ot_type) {
                                                    'holiday' => 'ot-badge-holiday',
                                                    'holiday_ot' => 'ot-badge-holiday-ot',
                                                    default => 'ot-badge-normal',
                                                };
                                                
                                                $typeIcon = match($request->ot_type) {
                                                    'holiday' => 'bi-sun-fill',
                                                    'holiday_ot' => 'bi-fire',
                                                    default => 'bi-briefcase-fill',
                                                };
                                            @endphp
                                            <tr>
                                                <td><input type="checkbox" name="overtime_ids[]" value="{{ $request->id }}" class="form-check-input ot-checkbox"></td>
                                                <td>
                                                    <div class="fw-bold text-dark">{{ $request->user->name }}</div>
                                                    <small class="text-muted">{{ $request->user->department->name ?? 'ไม่ระบุแผนก' }}</small>
                                                </td>
                                                <td>
                                                    <div class="fw-medium">{{ \Carbon\Carbon::parse($request->date)->format('d/m/Y') }}</div>
                                                    <span class="ot-badge-type {{ $typeClass }} mt-1">
                                                        <i class="bi {{ $typeIcon }}"></i> {{ $request->ot_type_label }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($request->start_time && $request->end_time)
                                                        <span class="ot-time-tag">{{ \Carbon\Carbon::parse($request->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($request->end_time)->format('H:i') }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                    <div class="mt-1">
                                                        <span class="badge bg-primary fs-6">{{ $request->hours }} ชม.</span>
                                                        @if($request->break_minutes > 0)
                                                            <small class="text-muted ms-1">(พัก {{ $request->break_minutes }} น.)</small>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($hasAtt)
                                                        <div class="small">
                                                            <span class="text-success"><i class="bi bi-box-arrow-in-right"></i> {{ $checkInTime ?? '-' }}</span>
                                                            <span class="mx-1 text-muted">|</span>
                                                            <span class="text-danger"><i class="bi bi-box-arrow-right"></i> {{ $checkOutTime ?? '-' }}</span>
                                                        </div>
                                                        @if($checkOutTime)
                                                            <span class="badge bg-success-subtle text-success border border-success mt-1" style="font-size: 0.7rem;">
                                                                <i class="bi bi-check-circle me-1"></i>มีบันทึกสแกนออก
                                                            </span>
                                                        @else
                                                            <span class="badge bg-warning-subtle text-warning border border-warning mt-1" style="font-size: 0.7rem;">
                                                                <i class="bi bi-clock me-1"></i>ยังไม่สแกนออก
                                                            </span>
                                                        @endif
                                                    @else
                                                        <span class="badge bg-secondary-subtle text-secondary border" style="font-size: 0.7rem;">
                                                            <i class="bi bi-question-circle me-1"></i>ไม่มีบันทึกเวลา
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="text-start text-muted small" style="max-width: 200px;">
                                                    <span class="d-inline-block text-truncate" style="max-width: 190px;" title="{{ $request->description }}">
                                                        {{ $request->description }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex justify-content-center gap-1">
                                                        <!-- ปุ่มดูรายละเอียด (Modal) -->
                                                        <button type="button" class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#otDetailModal{{ $request->id }}" title="ดูรายละเอียดแบบเต็ม">
                                                            <i class="bi bi-eye"></i> รายละเอียด
                                                        </button>
                                                        <!-- ปุ่มอนุมัติเดี่ยว -->
                                                        <button type="button" class="btn btn-sm btn-outline-success" onclick="event.preventDefault(); document.getElementById('approve-form-{{ $request->id }}').submit();" title="อนุมัติคำขอ">
                                                            <i class="bi bi-check-lg"></i>
                                                        </button>
                                                        <!-- ปุ่มปฏิเสธ (เปิด Modal) -->
                                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $request->id }}" title="ปฏิเสธคำขอ">
                                                            <i class="bi bi-x-lg"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </form>
                    @else
                        <div class="ot-empty-state">
                            <i class="bi bi-check-circle text-success mb-3" style="font-size: 3rem;"></i>
                            <h6 class="fw-bold text-dark">ไม่มีคำขอ OT ที่รอการอนุมัติ</h6>
                            <p class="mb-0 small">พนักงานทั้งหมดได้รับการจัดการเรียบร้อยแล้ว</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- ส่วนที่ 2: ประวัติการทำรายการ -->
    <div class="row justify-content-center mt-3">
        <div class="col-lg-12">
            <div class="ot-card fade-in">
                <div class="ot-header handled d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <h5 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2"></i>ประวัติคำขอที่จัดการแล้ว (Handled)</h5>
                        <small class="text-white-50">ทั้งหมด {{ $handledRequests->total() }} รายการ</small>
                    </div>
                    <!-- Rows Per Page Selector -->
                    <form action="{{ route('overtime.index', [], false) }}" method="GET" class="d-flex align-items-center gap-2 m-0">
                        <label for="per_page_select_ot" class="small text-white-50 mb-0 text-nowrap">แสดงต่อหน้า:</label>
                        <select name="per_page" id="per_page_select_ot" class="form-select form-select-sm bg-dark text-white border-secondary py-1" style="width: 80px;" onchange="this.form.submit()">
                            <option value="5" {{ ($perPage ?? 10) == 5 ? 'selected' : '' }}>5</option>
                            <option value="10" {{ ($perPage ?? 10) == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ ($perPage ?? 10) == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ ($perPage ?? 10) == 50 ? 'selected' : '' }}>50</option>
                        </select>
                    </form>
                </div>
                
                <div class="card-body p-4">
                    @if($handledRequests->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-custom mb-0 align-middle">
                                <thead>
                                    <tr>
                                        <th>วันที่ & ประเภท OT</th>
                                        <th>ชื่อพนักงาน</th>
                                        <th>ช่วงเวลา / ชั่วโมง</th>
                                        <th>เวลาสแกนจริง</th>
                                        <th>รายละเอียดงาน</th>
                                        <th>สถานะ</th>
                                        <th>ผู้อนุมัติ / เหตุผล</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($handledRequests as $request)
                                        @php
                                            $att = $request->attendance;
                                            $checkInTime = $att && $att->check_in ? \Carbon\Carbon::parse($att->check_in)->format('H:i') : '-';
                                            $checkOutTime = $att && $att->check_out ? \Carbon\Carbon::parse($att->check_out)->format('H:i') : '-';
                                            
                                            $typeClass = match($request->ot_type) {
                                                'holiday' => 'ot-badge-holiday',
                                                'holiday_ot' => 'ot-badge-holiday-ot',
                                                default => 'ot-badge-normal',
                                            };
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="fw-medium">{{ \Carbon\Carbon::parse($request->date)->format('d/m/Y') }}</div>
                                                <span class="ot-badge-type {{ $typeClass }} mt-1">
                                                    {{ $request->ot_type_label }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="fw-bold">{{ $request->user->name }}</div>
                                                <small class="text-muted">{{ $request->user->department->name ?? '-' }}</small>
                                            </td>
                                            <td>
                                                @if($request->start_time && $request->end_time)
                                                    <span class="ot-time-tag small">{{ \Carbon\Carbon::parse($request->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($request->end_time)->format('H:i') }}</span>
                                                @endif
                                                <div><strong>{{ $request->hours }}</strong> ชม.</div>
                                            </td>
                                            <td class="small">
                                                @if($att && ($att->check_in || $att->check_out))
                                                    <span class="text-success"><i class="bi bi-box-arrow-in-right"></i> {{ $checkInTime }}</span>
                                                    <span class="mx-1 text-muted">|</span>
                                                    <span class="text-danger"><i class="bi bi-box-arrow-right"></i> {{ $checkOutTime }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-start text-muted small" style="max-width: 200px;">
                                                <span class="d-inline-block text-truncate" style="max-width: 190px;" title="{{ $request->description }}">
                                                    {{ $request->description }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($request->status == 'approved')
                                                    <span class="badge bg-success-subtle text-success border border-success"><i class="bi bi-check-circle-fill me-1"></i>อนุมัติแล้ว</span>
                                                @elseif($request->status == 'rejected')
                                                    <span class="badge bg-danger-subtle text-danger border border-danger"><i class="bi bi-x-circle-fill me-1"></i>ปฏิเสธ</span>
                                                @endif
                                            </td>
                                            <td class="small text-start">
                                                @if($request->status == 'approved')
                                                    <div class="text-success"><i class="bi bi-person-check me-1"></i>{{ $request->hr->name ?? 'HR' }}</div>
                                                    <small class="text-muted">{{ $request->hr_approved_at ? \Carbon\Carbon::parse($request->hr_approved_at)->format('d/m/Y H:i') : '' }}</small>
                                                @elseif($request->status == 'rejected')
                                                    <div class="text-danger fw-medium">{{ $request->hr_reject_reason ?? 'ไม่ระบุเหตุผล' }}</div>
                                                    <small class="text-muted">โดย {{ $request->hr->name ?? 'HR' }}</small>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination Links -->
                        @if($handledRequests->hasPages())
                            <div class="pt-3 border-top border-theme d-flex justify-content-center">
                                {{ $handledRequests->links() }}
                            </div>
                        @endif
                    @else
                        <div class="ot-empty-state">
                            <i class="bi bi-inbox text-muted opacity-50 mb-3" style="font-size: 3rem;"></i>
                            <h6 class="fw-bold text-dark">ยังไม่มีประวัติการทำรายการ</h6>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- Modals & Standalone Forms for Pending Requests                            -->
<!-- (Render ไว้ข้างนอก .ot-card เพื่อป้องกัน Bootstrap Backdrop ติด Blur / Overflow) -->
<!-- ========================================================================= -->
@if(count($pendingRequests) > 0)
    @foreach($pendingRequests as $request)
        @php
            $att = $request->attendance;
            $typeClass = match($request->ot_type) {
                'holiday' => 'ot-badge-holiday',
                'holiday_ot' => 'ot-badge-holiday-ot',
                default => 'ot-badge-normal',
            };
        @endphp

        <!-- Form อนุมัติเดี่ยว -->
        <form id="approve-form-{{ $request->id }}" action="{{ route('overtime.approve', $request->id) }}" method="POST" class="d-none">
            @csrf
        </form>

        <!-- Modal ปฏิเสธ -->
        <div class="modal fade text-start" id="rejectModal{{ $request->id }}" tabindex="-1" aria-labelledby="rejectModalLabel{{ $request->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="background: var(--surface-bg); border: 1px solid var(--surface-border) !important;">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title fw-bold" id="rejectModalLabel{{ $request->id }}"><i class="bi bi-x-circle-fill me-2"></i>ปฏิเสธคำขอ OT</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <p class="mb-3">ปฏิเสธคำขอของ <strong>{{ $request->user->name }}</strong> วันที่ {{ \Carbon\Carbon::parse($request->date)->format('d/m/Y') }} (จำนวน {{ $request->hours }} ชม.)</p>
                        <form action="{{ route('overtime.reject', $request->id) }}" method="POST" class="d-flex flex-column w-100">
                            @csrf
                            <div class="mb-3">
                                <label for="hr_reject_reason_{{ $request->id }}" class="form-label fw-bold">เหตุผลที่ปฏิเสธ <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="hr_reject_reason_{{ $request->id }}" name="hr_reject_reason" rows="3" required placeholder="กรุณาระบุเหตุผลเพื่อให้พนักงานทราบ..."></textarea>
                            </div>
                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                                <button type="submit" class="btn btn-danger fw-bold">ยืนยันการปฏิเสธ</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal ดูรายละเอียดฉบับเต็ม (View Detail Modal) -->
        <div class="modal fade text-start" id="otDetailModal{{ $request->id }}" tabindex="-1" aria-labelledby="otDetailModalLabel{{ $request->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="background: var(--surface-bg); border: 1px solid var(--surface-border) !important;">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fw-bold" id="otDetailModalLabel{{ $request->id }}">
                            <i class="bi bi-file-earmark-text-fill me-2"></i>รายละเอียดคำขอทำงานล่วงเวลา (OT) #{{ $request->id }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <!-- ข้อมูลพนักงาน -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <div class="ot-detail-card">
                                    <div class="small text-muted mb-1"><i class="bi bi-person me-1"></i> ข้อมูลพนักงาน</div>
                                    <h6 class="fw-bold mb-1">{{ $request->user->name }}</h6>
                                    <div class="small text-muted">
                                        <span>รหัส: <code>{{ $request->user->emp_code ?? '-' }}</code></span><br>
                                        <span>แผนก: <strong>{{ $request->user->department->name ?? '-' }}</strong></span> | 
                                        <span>ตำแหน่ง: <strong>{{ $request->user->position->name ?? '-' }}</strong></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="ot-detail-card">
                                    <div class="small text-muted mb-1"><i class="bi bi-speedometer2 me-1"></i> OT สะสมสัปดาห์นี้</div>
                                    <h6 class="fw-bold text-primary mb-1">{{ $request->weekly_approved_hours ?? 0 }} / 36.0 ชม.</h6>
                                    <div class="progress mt-2" style="height: 6px;">
                                        @php
                                            $weeklyPercent = min(100, (($request->weekly_approved_hours ?? 0) / 36) * 100);
                                        @endphp
                                        <div class="progress-bar {{ $weeklyPercent >= 90 ? 'bg-danger' : ($weeklyPercent >= 70 ? 'bg-warning' : 'bg-primary') }}" style="width: {{ $weeklyPercent }}%"></div>
                                    </div>
                                    <small class="text-muted" style="font-size: 0.75rem;">ขีดจำกัดตามกฎหมายไม่เกิน 36 ชม./สัปดาห์</small>
                                </div>
                            </div>
                        </div>

                        <!-- เปรียบเทียบข้อมูล OT กับ Attendance จริง -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <div class="ot-detail-card border-primary">
                                    <div class="small fw-bold text-primary mb-2"><i class="bi bi-calendar-event me-1"></i> รายละเอียดที่ยื่นขอ OT</div>
                                    <ul class="list-unstyled mb-0 small">
                                        <li class="mb-2"><strong>วันที่:</strong> {{ \Carbon\Carbon::parse($request->date)->format('d/m/Y') }}</li>
                                        <li class="mb-2"><strong>ประเภท:</strong> <span class="ot-badge-type {{ $typeClass }}">{{ $request->ot_type_label }}</span></li>
                                        <li class="mb-2"><strong>เวลา OT:</strong> <span class="ot-time-tag">{{ $request->start_time ? \Carbon\Carbon::parse($request->start_time)->format('H:i') : '-' }} - {{ $request->end_time ? \Carbon\Carbon::parse($request->end_time)->format('H:i') : '-' }}</span></li>
                                        <li class="mb-2"><strong>เวลาพัก:</strong> {{ $request->break_minutes ?? 0 }} นาที</li>
                                        <li><strong>คำนวณสุทธิ:</strong> <span class="badge bg-primary fs-6">{{ $request->hours }} ชม.</span></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="ot-detail-card border-success">
                                    <div class="small fw-bold text-success mb-2"><i class="bi bi-fingerprint me-1"></i> บันทึกเวลาสแกนจริง (Attendance)</div>
                                    @if($request->attendance)
                                        <ul class="list-unstyled mb-0 small">
                                            <li class="mb-2"><strong>เข้างานจริง:</strong> <span class="text-success fw-bold">{{ $request->attendance->check_in ? \Carbon\Carbon::parse($request->attendance->check_in)->format('H:i:s น.') : 'ไม่มีบันทึก' }}</span></li>
                                            <li class="mb-2"><strong>ออกงานจริง:</strong> <span class="text-danger fw-bold">{{ $request->attendance->check_out ? \Carbon\Carbon::parse($request->attendance->check_out)->format('H:i:s น.') : 'ยังไม่สแกนออก' }}</span></li>
                                            <li class="mb-2"><strong>สถานะการเข้างาน:</strong> {{ $request->attendance->status ?? 'ปกติ' }}</li>
                                            <li>
                                                <strong>ความสอดคล้อง:</strong>
                                                @if($request->attendance->check_out)
                                                    <span class="badge bg-success-subtle text-success border border-success">สแกนออกตรง/ครอบคลุมเวลา OT</span>
                                                @else
                                                    <span class="badge bg-warning-subtle text-warning border border-warning">รอสแกนออกงาน</span>
                                                @endif
                                            </li>
                                        </ul>
                                    @else
                                        <div class="text-center py-3 text-muted">
                                            <i class="bi bi-exclamation-circle text-warning fs-3 d-block mb-1"></i>
                                            <span>ไม่พบประวัติการลงเวลาในระบบในวันนี้</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- รายละเอียดงาน -->
                        <div class="ot-detail-card mb-3">
                            <div class="small fw-bold mb-1"><i class="bi bi-card-text me-1"></i> รายละเอียดงานที่ปฏิบัติงานล่วงเวลา:</div>
                            <div class="p-3 rounded small" style="background: var(--badge-bg); border: 1px solid var(--surface-border); white-space: pre-line;">{{ $request->description }}</div>
                        </div>

                        @if($request->early_checkout_reason)
                            <div class="alert alert-warning py-2 px-3 small mb-0">
                                <strong><i class="bi bi-exclamation-triangle me-1"></i> เหตุผลกลับก่อนเวลาที่พนักงานแจ้ง:</strong>
                                {{ $request->early_checkout_reason }}
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid var(--surface-border);">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $request->id }}">
                            <i class="bi bi-x-lg me-1"></i> ปฏิเสธ
                        </button>
                        <button type="button" class="btn btn-success fw-bold" onclick="event.preventDefault(); document.getElementById('approve-form-{{ $request->id }}').submit();">
                            <i class="bi bi-check-lg me-1"></i> อนุมัติคำขอนี้
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endif

@push('scripts')
<script>
    // สคริปต์สำหรับ Checkbox เลือกทั้งหมด (Bulk Approve)
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllCheckbox = document.getElementById('selectAll');
        const otCheckboxes = document.querySelectorAll('.ot-checkbox');
        
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                otCheckboxes.forEach(checkbox => {
                    checkbox.checked = selectAllCheckbox.checked;
                });
            });
        }
    });
</script>
@endpush
@endsection