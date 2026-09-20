@extends('layouts.app')

@section('title', 'ประวัติการลงเวลาของฉัน')

@push('styles')
<style>
    .history-card {
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
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-1 gradient-text">ประวัติการลงเวลาทำงานของฉัน</h3>
                        <p class="text-muted mb-0 small">ตรวจสอบประวัติเวลาเข้างาน-เลิกงาน และสถิติความตรงต่อเวลารายเดือน</p>
                    </div>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <button type="button" class="btn btn-primary rounded-3 px-3 py-2 d-inline-flex align-items-center gap-2 shadow-sm"
                        data-bs-toggle="modal" data-bs-target="#requestAdjustmentModal"
                        style="background: var(--primary-gradient); border: none; font-weight: 600;">
                        <i class="bi bi-clock-history"></i>
                        <span>ขอปรับเวลาทำงานย้อนหลัง</span>
                    </button>
                    <a href="{{ route('attendances.checkin', [], false) }}" class="btn btn-outline-secondary rounded-3 px-3 py-2 text-decoration-none">
                        <i class="bi bi-fingerprint me-1 text-primary"></i> หน้าลงเวลาประจำวัน
                    </a>
                    @if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('HR'))
                        <a href="{{ route('attendances.adjustments.index', [], false) }}" class="btn btn-outline-secondary rounded-3 px-3 py-2 text-decoration-none">
                            <i class="bi bi-check2-circle me-1 text-warning"></i> พิจารณาคำขอปรับเวลา
                        </a>
                        <a href="{{ route('attendances.report', [], false) }}" class="btn btn-outline-secondary rounded-3 px-3 py-2 text-decoration-none">
                            <i class="bi bi-file-earmark-bar-graph-fill me-1 text-success"></i> รายงานสรุปเวลาทำงาน
                        </a>
                    @endif
                    <a href="{{ route('leaves.create', [], false) }}" class="btn btn-outline-secondary rounded-3 px-3 py-2 text-decoration-none">
                        <i class="bi bi-calendar-plus me-1 text-warning"></i> ยื่นใบลา
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
    <div class="col-sm-3">
        <div class="stat-card p-3 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small">วันทำงานในเดือนนี้</span>
                <h4 class="fw-bold mb-0 text-primary">{{ number_format($totalDays) }} <span class="fs-6 text-muted fw-normal">วัน</span></h4>
            </div>
            <div class="stat-icon-wrapper icon-indigo" style="width: 44px; height: 44px;">
                <i class="bi bi-calendar-check fs-5"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="stat-card p-3 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small">เข้างานตรงเวลา</span>
                <h4 class="fw-bold mb-0 text-success">{{ number_format($onTimeCount) }} <span class="fs-6 text-muted fw-normal">วัน</span></h4>
            </div>
            <div class="stat-icon-wrapper icon-green" style="width: 44px; height: 44px;">
                <i class="bi bi-check-circle-fill fs-5"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="stat-card p-3 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small">เข้างานสาย</span>
                <h4 class="fw-bold mb-0 text-warning">{{ number_format($lateCount) }} <span class="fs-6 text-muted fw-normal">วัน</span></h4>
            </div>
            <div class="stat-icon-wrapper icon-amber" style="width: 44px; height: 44px;">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="stat-card p-3 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small">วันลาที่ได้รับอนุมัติ</span>
                <h4 class="fw-bold mb-0 text-info">{{ number_format($leaveCount) }} <span class="fs-6 text-muted fw-normal">วัน</span></h4>
            </div>
            <div class="stat-icon-wrapper icon-purple" style="width: 44px; height: 44px;">
                <i class="bi bi-sun-fill fs-5"></i>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="col-12">
        <div class="history-card p-4">
            <form method="GET" action="{{ route('attendances.my-history', [], false) }}" class="row g-3">
                <input type="hidden" name="per_page" value="{{ $perPage ?? 15 }}">
                <div class="col-md-5">
                    <select name="month" class="form-select filter-input">
                        @for($m = 1; $m <= 12; $m++)
                            @php $val = sprintf('%02d', $m); @endphp
                            <option value="{{ $val }}" {{ $month == $val ? 'selected' : '' }}>
                                เดือน {{ DateTime::createFromFormat('!m', $m)->format('F') }} ({{ $val }})
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-5">
                    <select name="year" class="form-select filter-input">
                        @for($y = date('Y'); $y >= date('Y') - 3; $y--)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>ประจำปี {{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 rounded-3 d-flex align-items-center justify-content-center gap-1">
                        <i class="bi bi-funnel-fill"></i> แสดงข้อมูล
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Attendance Table -->
    <div class="col-12">
        <div class="history-card overflow-hidden">
            <div class="p-3 px-4 border-bottom border-theme d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="fw-bold text-theme">
                    <i class="bi bi-table me-1 text-primary"></i> รายการประวัติการลงเวลา ({{ $attendances->total() }} รายการ)
                </div>
                <!-- Rows Per Page Selector -->
                <form action="{{ route('attendances.my-history', [], false) }}" method="GET" class="d-flex align-items-center gap-2 m-0">
                    <input type="hidden" name="month" value="{{ $month }}">
                    <input type="hidden" name="year" value="{{ $year }}">
                    <label for="per_page_select_history" class="small text-muted mb-0 text-nowrap">แสดงต่อหน้า:</label>
                    <select name="per_page" id="per_page_select_history" class="form-select form-select-sm filter-input py-1" style="width: 85px;" onchange="this.form.submit()">
                        <option value="5" {{ ($perPage ?? 15) == 5 ? 'selected' : '' }}>5</option>
                        <option value="10" {{ ($perPage ?? 15) == 10 ? 'selected' : '' }}>10</option>
                        <option value="15" {{ ($perPage ?? 15) == 15 ? 'selected' : '' }}>15</option>
                        <option value="25" {{ ($perPage ?? 15) == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ ($perPage ?? 15) == 50 ? 'selected' : '' }}>50</option>
                    </select>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th class="col-2 me-2">วันที่</th>
                            <th class="col-2">เวลาเข้างาน</th>
                            <th class="col-2">เวลาเลิกงาน</th>
                            <th class="col-2">สถานะการลงเวลา</th>
                            <th class="col-2">หมายเหตุ / รายละเอียด</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $record)
                            <tr>
                                <td class="fw-bold text-theme me-2">
                                    {{ \Carbon\Carbon::parse($record->date)->format('d/m/Y') }}
                                    <span class="badge bg-secondary-subtle text-secondary small ms-1">
                                        {{ \Carbon\Carbon::parse($record->date)->format('D') }}
                                    </span>
                                </td>
                                <td>
                                    @if($record->check_in)
                                        <span class="font-monospace fw-semibold text-success">{{ $record->check_in }} น.</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($record->check_out)
                                        <span class="font-monospace fw-semibold text-warning">{{ $record->check_out }} น.</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($record->status === 'on_time')
                                        <span class="badge bg-success-subtle text-success px-2 py-1">
                                            <i class="bi bi-check-circle-fill me-1"></i>เข้างานตรงเวลา
                                        </span>
                                    @elseif($record->status === 'late')
                                        <span class="badge bg-warning-subtle text-warning px-2 py-1">
                                            <i class="bi bi-exclamation-triangle-fill me-1"></i>เข้างานสาย
                                        </span>
                                    @elseif($record->status === 'leave')
                                        <span class="badge bg-info-subtle text-info px-2 py-1">
                                            <i class="bi bi-sun-fill me-1"></i>ลางาน (อนุมัติแล้ว)
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger px-2 py-1">
                                            <i class="bi bi-x-circle-fill me-1"></i>ขาดงาน
                                        </span>
                                    @endif
                                </td>
                                <td class="text-muted small">
                                    {{ $record->notes ?? '-' }}
                                    @if($record->leaveRequest)
                                        <span class="ms-1">({{ $record->leaveRequest->leaveType->name ?? '' }})</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bi bi-calendar-x fs-1 d-block mb-2 opacity-50"></i>
                                    <span>ไม่พบประวัติการลงเวลาในเดือนที่เลือก</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($attendances->hasPages())
                <div class="p-3 border-top border-theme d-flex justify-content-center">
                    {{ $attendances->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal สำหรับยื่นคำขอปรับเวลาทำงานย้อนหลัง -->
<div class="modal fade text-start" id="requestAdjustmentModal" tabindex="-1" aria-labelledby="requestAdjustmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content form-card p-3 border-0 shadow-lg" style="background: var(--surface-bg); border: 1px solid var(--surface-border); border-radius: 20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-theme d-flex align-items-center gap-2" id="requestAdjustmentModalLabel">
                    <i class="bi bi-clock-history text-primary"></i> ขอปรับปรุงเวลาลงเวลาย้อนหลัง
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('attendances.adjustments.store', [], false) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <p class="text-muted small mb-3">
                        สำหรับกรณีลืมสแกนเวลา หรือติดภารกิจงานนอกสถานที่ โดยต้องผ่านการพิจารณาอนุมัติจากฝ่ายบุคคล
                    </p>

                    <div class="mb-3">
                        <label for="target_date" class="form-label small fw-semibold text-theme">วันที่ต้องการขอปรับเวลา <span class="text-danger">*</span></label>
                        <input type="date" name="target_date" id="target_date" class="form-control filter-input" 
                            max="{{ date('Y-m-d') }}" required value="{{ date('Y-m-d') }}">
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label for="requested_check_in" class="form-label small fw-semibold text-theme">เวลาเข้างานจริง</label>
                            <input type="time" name="requested_check_in" id="requested_check_in" class="form-control filter-input" value="09:00">
                        </div>
                        <div class="col-6">
                            <label for="requested_check_out" class="form-label small fw-semibold text-theme">เวลาเลิกงานจริง</label>
                            <input type="time" name="requested_check_out" id="requested_check_out" class="form-control filter-input" value="18:00">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="reason" class="form-label small fw-semibold text-theme">เหตุผลความจำเป็น <span class="text-danger">*</span></label>
                        <textarea name="reason" id="reason" rows="3" class="form-control filter-input" 
                            placeholder="ระบุเหตุผล เช่น ลืมกดลงเวลา, เดินทางไปพบลูกค้านอกสถานที่..." required></textarea>
                    </div>

                    <div class="mb-2">
                        <label for="attachment" class="form-label small fw-semibold text-theme">เอกสาร/หลักฐานแนบ (ถ้ามี)</label>
                        <input type="file" name="attachment" id="attachment" class="form-control filter-input" accept=".jpg,.jpeg,.png,.pdf">
                        <span class="text-muted small" style="font-size: 0.75rem;">รองรับ JPG, PNG, PDF ขนาดไม่เกิน 5MB</span>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary rounded-3 px-3" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4" style="background: var(--primary-gradient); border: none; font-weight: 600;">
                        <i class="bi bi-send-fill me-1"></i> ส่งคำขอปรับเวลา
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
