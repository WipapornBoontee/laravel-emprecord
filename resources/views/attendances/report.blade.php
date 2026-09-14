@extends('layouts.app')

@section('title', 'รายงานสรุปเวลาทำงาน (Attendance Report)')

@push('styles')
<style>
    .report-card {
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

    .table-custom {
        color: var(--text-main);
        vertical-align: middle;
    }
    .table-custom th {
        background: var(--badge-bg);
        color: var(--text-muted);
        font-size: 0.82rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid var(--surface-border);
        padding: 14px 18px;
    }
    .table-custom td {
        background: transparent;
        color: var(--text-main);
        border-bottom: 1px solid var(--surface-border);
        padding: 14px 18px;
        font-size: 0.92rem;
    }

    /* Print / PDF Styles */
    .print-only {
        display: none !important;
    }

    @media print {
        @page {
            size: A4 landscape;
            margin: 10mm 12mm;
        }

        header, .sidebar, .screen-only, .btn, .filter-section, nav {
            display: none !important;
        }

        body, .main-content {
            background: #ffffff !important;
            color: #0f172a !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .print-only {
            display: block !important;
        }

        .stat-card, .report-card {
            border: 1px solid #cbd5e1 !important;
            box-shadow: none !important;
            background: #ffffff !important;
            color: #0f172a !important;
            border-radius: 8px !important;
        }

        .table-custom th {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
            border: 1px solid #94a3b8 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            padding: 8px 10px !important;
        }

        .table-custom td {
            border: 1px solid #cbd5e1 !important;
            color: #0f172a !important;
            padding: 8px 10px !important;
        }

        .badge {
            border: 1px solid #94a3b8 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
    }
</style>
@endpush

@section('content')
<div class="row g-4">
    <!-- 1. Header Banner -->
    <div class="col-12 screen-only">
        <div class="hero-welcome-card p-4">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="action-icon icon-indigo">
                        <i class="bi bi-file-earmark-bar-graph-fill"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-1 gradient-text">รายงานสรุปเวลาทำงานพนักงาน</h3>
                        <p class="text-muted mb-0 small">
                            ตรวจสอบภาพรวมการเข้างาน การมาสาย การขาด และการลาของพนักงานทุกคนในองค์กร
                        </p>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('attendances.report.print', ['date' => $date, 'department_id' => $departmentId, 'status' => $status], false) }}" target="_blank" class="btn btn-primary rounded-3 px-3 py-2 d-flex align-items-center gap-2 shadow-sm" title="เปิดหน้าพิมพ์รายงาน PDF รูปแบบมาตรฐานบริษัท">
                        <i class="bi bi-file-earmark-pdf-fill fs-5"></i>
                        <span class="fw-bold">พิมพ์รายงาน PDF</span>
                    </a>
                    <a href="{{ route('attendances.checkin', [], false) }}" class="btn btn-outline-secondary rounded-3 px-3 py-2 d-flex align-items-center gap-2">
                        <i class="bi bi-stopwatch"></i>
                        <span>หน้าลงเวลา</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Print Only Header -->
    <div class="col-12 print-only mb-3">
        <div class="d-flex justify-content-between align-items-end border-bottom pb-2">
            <div>
                <h3 class="fw-bold mb-1">รายงานสรุปการลงเวลาปฏิบัติงานประจำวัน</h3>
                <span class="text-muted small">
                    วันที่ตรวจสอบ: {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }} 
                    @if($departmentId)
                        • แผนก: {{ $departments->firstWhere('id', $departmentId)->name ?? '-' }}
                    @endif
                </span>
            </div>
            <div class="text-end text-muted small">
                พิมพ์เมื่อ: {{ date('d/m/Y H:i') }} น. | ผู้ออกเอกสาร: {{ Auth::user()->name }}
            </div>
        </div>
    </div>

    <!-- 2. Filter Card -->
    <div class="col-12 screen-only filter-section">
        <div class="report-card p-4">
            <form method="GET" action="{{ route('attendances.report', [], false) }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-theme">
                        <i class="bi bi-calendar-event me-1"></i> วันที่ตรวจสอบ
                    </label>
                    <input type="date" name="date" class="form-control filter-input" value="{{ $date }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-theme">
                        <i class="bi bi-diagram-3 me-1"></i> แผนกงาน
                    </label>
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
                    <label class="form-label small fw-semibold text-theme">
                        <i class="bi bi-funnel me-1"></i> สถานะการลงเวลา
                    </label>
                    <select name="status" class="form-select filter-input">
                        <option value="">-- ทุกสถานะ --</option>
                        <option value="on_time" {{ $status === 'on_time' ? 'selected' : '' }}>ตรงเวลา (On Time)</option>
                        <option value="late" {{ $status === 'late' ? 'selected' : '' }}>มาสาย (Late)</option>
                        <option value="leave" {{ $status === 'leave' ? 'selected' : '' }}>ลางาน (Leave)</option>
                        <option value="absent" {{ $status === 'absent' ? 'selected' : '' }}>ยังไม่ลงเวลา / ขาดงาน</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100 rounded-3 d-flex align-items-center justify-content-center" style="height: 42px;" title="ค้นหา">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 3. Card สรุปผล (KPI Stat Cards) -->
    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card p-3 text-center">
            <span class="text-muted small d-block mb-1">พนักงานทั้งหมด</span>
            <h3 class="fw-bold mb-0 text-primary">{{ number_format($totalEmployees) }}</h3>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card p-3 text-center">
            <span class="text-muted small d-block mb-1">เข้างานแล้ว</span>
            <h3 class="fw-bold mb-0 text-success">{{ number_format($attendedCount) }}</h3>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card p-3 text-center">
            <span class="text-muted small d-block mb-1">เข้างานตรงเวลา</span>
            <h3 class="fw-bold mb-0 text-success">{{ number_format($onTimeCount) }}</h3>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card p-3 text-center">
            <span class="text-muted small d-block mb-1">เข้างานสาย</span>
            <h3 class="fw-bold mb-0 text-warning">{{ number_format($lateCount) }}</h3>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card p-3 text-center">
            <span class="text-muted small d-block mb-1">ลางาน (อนุมัติ)</span>
            <h3 class="fw-bold mb-0 text-info">{{ number_format($leaveCount) }}</h3>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card p-3 text-center">
            <span class="text-muted small d-block mb-1">ยังไม่ลงเวลา / ขาด</span>
            <h3 class="fw-bold mb-0 text-danger">{{ number_format($absentCount) }}</h3>
        </div>
    </div>

    <!-- 4. ตารางแสดงผล ด้านล่าง Card สรุปผล (Report Table) -->
    <div class="col-12">
        <div class="report-card overflow-hidden">
            <div class="p-4 border-bottom border-theme d-flex align-items-center justify-content-between">
                <h5 class="fw-bold mb-0 text-theme">
                    <i class="bi bi-table me-2 text-primary"></i>รายการลงเวลาประจำวันที่ {{ \Carbon\Carbon::parse($date)->format('d F Y') }}
                </h5>
                <span class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill">
                    แสดงข้อมูล <strong>{{ count($reportData) }}</strong> คน
                </span>
            </div>

            <div class="table-responsive">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th style="width: 100px;">รหัสพนักงาน</th>
                            <th>พนักงาน</th>
                            <th>แผนก / ตำแหน่ง</th>
                            <th>เวลาเข้างาน</th>
                            <th>เวลาเลิกงาน</th>
                            <th>สถานะการเข้างาน</th>
                            <th>หมายเหตุ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reportData as $row)
                            @php
                                $emp = $row['user'];
                                $rowStatus = $row['status'];
                            @endphp
                            <tr>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary font-monospace px-2 py-1">
                                        {{ $emp->emp_code }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-circle" style="width: 36px; height: 36px; font-size: 0.85rem;">
                                            {{ mb_substr($emp->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('employees.show', $emp, false) }}" class="fw-bold text-theme text-decoration-none">
                                                {{ $emp->name }}
                                            </a>
                                            <span class="d-block text-muted small">{{ $emp->email }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $emp->department->name ?? '-' }}</div>
                                    <span class="text-muted small">{{ $emp->position->name ?? '-' }}</span>
                                </td>
                                <td>
                                    @if($row['check_in'])
                                        <span class="font-monospace fw-semibold text-success">{{ substr($row['check_in'], 0, 5) }} น.</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($row['check_out'])
                                        <span class="font-monospace fw-semibold text-warning">{{ substr($row['check_out'], 0, 5) }} น.</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($rowStatus === 'on_time')
                                        <span class="badge bg-success-subtle text-success px-2 py-1">
                                            <i class="bi bi-check-circle-fill me-1"></i>ตรงเวลา
                                        </span>
                                    @elseif($rowStatus === 'late')
                                        <span class="badge bg-warning-subtle text-warning px-2 py-1">
                                            <i class="bi bi-exclamation-triangle-fill me-1"></i>มาสาย
                                        </span>
                                    @elseif($rowStatus === 'leave')
                                        <span class="badge bg-info-subtle text-info px-2 py-1">
                                            <i class="bi bi-sun-fill me-1"></i>ลางาน (อนุมัติแล้ว)
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger px-2 py-1">
                                            <i class="bi bi-x-circle-fill me-1"></i>ยังไม่ลงเวลา
                                        </span>
                                    @endif
                                </td>
                                <td class="text-muted small">
                                    {{ $row['notes'] ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                                    <span>ไม่พบข้อมูลตามเงื่อนไขที่เลือก</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
