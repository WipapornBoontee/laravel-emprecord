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
                    <div class="action-icon icon-green">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-1 gradient-text">ประวัติการลงเวลาทำงานของฉัน</h3>
                        <p class="text-muted mb-0 small">ตรวจสอบประวัติเวลาเข้างาน-เลิกงาน และสถิติความตรงต่อเวลารายเดือน</p>
                    </div>
                </div>
                <div>
                    <a href="{{ route('attendances.checkin', [], false) }}" class="btn btn-primary rounded-3 px-4 py-2 text-decoration-none d-flex align-items-center gap-2"
                        style="background: var(--primary-gradient); border: none; font-weight: 600;">
                        <i class="bi bi-stopwatch-fill"></i> หน้าลงเวลาประจำวัน
                    </a>
                </div>
            </div>
        </div>
    </div>

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
                        <i class="bi bi-search"></i> แสดงข้อมูล
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Attendance Table -->
    <div class="col-12">
        <div class="history-card overflow-hidden">
            <div class="table-responsive">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th class="col-2 md-2">วันที่</th>
                            <th class="col-2">เวลาเข้างาน</th>
                            <th class="col-2">เวลาเลิกงาน</th>
                            <th class="col-2">สถานะการลงเวลา</th>
                            <th class="col-2">หมายเหตุ / รายละเอียด</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $record)
                            <tr>
                                <td class="fw-bold text-theme md-2">
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
@endsection
