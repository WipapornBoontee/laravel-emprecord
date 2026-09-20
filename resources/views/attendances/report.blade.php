@extends('layouts.app')

@section('title', 'รายงานสรุปเวลาทำงาน (Attendance Report)')

@push('styles')
<style>
    /* Card & Container Polish */
    .report-card {
        background: var(--surface-bg);
        border: 1px solid var(--surface-border);
        border-radius: 20px;
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.05);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    /* Interactive Segmented Pill Tabs */
    .report-nav-tabs {
        display: flex;
        gap: 0.5rem;
        background: var(--badge-bg);
        padding: 0.4rem;
        border-radius: 16px;
        border: 1px solid var(--surface-border);
        overflow-x: auto;
        scrollbar-width: none;
    }
    .report-nav-tabs::-webkit-scrollbar {
        display: none;
    }
    .report-nav-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.6rem 1.1rem;
        border-radius: 12px;
        color: var(--text-muted);
        text-decoration: none;
        font-weight: 600;
        font-size: 0.88rem;
        white-space: nowrap;
        transition: all 0.25s ease;
        border: 1px solid transparent;
    }
    .report-nav-pill:hover {
        color: var(--text-main);
        background: rgba(255, 255, 255, 0.08);
    }
    .report-nav-pill.active {
        background: var(--primary-gradient);
        color: #ffffff !important;
        box-shadow: 0 4px 15px var(--accent-glow);
    }
    .report-nav-pill .pill-counter {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        padding: 0.15rem 0.55rem;
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.2);
        color: currentColor;
        font-weight: 700;
    }
    .report-nav-pill:not(.active) .pill-counter {
        background: var(--surface-border);
        color: var(--text-muted);
    }

    /* Input & Filter Controls */
    .filter-input {
        background: var(--badge-bg);
        border: 1px solid var(--surface-border);
        color: var(--text-main);
        border-radius: 12px;
        padding: 0.65rem 1rem;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }
    .filter-input:focus {
        background: var(--badge-bg);
        border-color: var(--accent-color);
        color: var(--text-main);
        box-shadow: 0 0 0 3px var(--accent-glow);
        outline: none;
    }
    .filter-input option {
        background: var(--dropdown-bg);
        color: var(--text-main);
    }

    /* KPI Summary Cards */
    .kpi-stat-card {
        background: var(--surface-bg);
        border: 1px solid var(--surface-border);
        border-radius: 18px;
        padding: 1.25rem 1rem;
        position: relative;
        overflow: hidden;
        transition: all 0.25s ease;
        text-decoration: none;
        display: block;
    }
    .kpi-stat-card:hover {
        transform: translateY(-3px);
        border-color: var(--card-hover-border);
        box-shadow: 0 12px 25px -8px rgba(0, 0, 0, 0.1);
    }
    .kpi-stat-card.is-active {
        border-color: var(--accent-color);
        box-shadow: 0 0 0 2px var(--accent-color);
    }
    .kpi-icon-wrapper {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        margin-bottom: 0.75rem;
    }

    /* Table System */
    .table-custom {
        color: var(--text-main);
        vertical-align: middle;
        margin-bottom: 0;
    }
    .table-custom th {
        background: var(--badge-bg);
        color: var(--text-muted);
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        border-bottom: 1px solid var(--surface-border);
        padding: 14px 18px;
        white-space: nowrap;
    }
    .table-custom td {
        background: transparent;
        color: var(--text-main);
        border-bottom: 1px solid var(--surface-border);
        padding: 14px 18px;
        font-size: 0.9rem;
    }
    .table-custom tbody tr:hover td {
        background: rgba(255, 255, 255, 0.02);
    }

    /* Print & PDF Media Styles */
    .print-only {
        display: none !important;
    }

    @media print {
        @page {
            size: A4 landscape;
            margin: 10mm 12mm;
        }
        header, .sidebar, .screen-only, .btn, .filter-section, nav, .report-nav-tabs {
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
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="action-icon icon-indigo" style="width: 52px; height: 52px; font-size: 1.5rem;">
                        <i class="bi bi-file-earmark-bar-graph-fill text-primary"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-1 gradient-text">รายงานสรุปเวลาทำงานพนักงาน</h3>
                        <p class="text-muted mb-0 small">
                            ตรวจสอบภาพรวมการเข้างาน การมาสาย การลางาน และการทำงานล่วงเวลา (OT) ประจำวัน
                        </p>
                    </div>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <a href="{{ route('attendances.checkin', [], false) }}" class="btn btn-outline-secondary btn-sm rounded-3 px-3 py-2 text-decoration-none">
                        <i class="bi bi-fingerprint me-1 text-primary"></i> หน้าลงเวลา
                    </a>
                    <a href="{{ route('attendances.my-history', [], false) }}" class="btn btn-outline-secondary btn-sm rounded-3 px-3 py-2 text-decoration-none">
                        <i class="bi bi-clock-history me-1 text-indigo"></i> ประวัติของฉัน
                    </a>
                    <a href="{{ route('attendances.report.export-csv', ['date' => $date, 'department_id' => $departmentId, 'status' => $status, 'report_type' => $reportType], false) }}" class="btn btn-outline-success btn-sm rounded-3 px-3 py-2 d-inline-flex align-items-center gap-2" title="ดาวน์โหลดไฟล์ CSV ของรายงานที่กำลังดูอยู่">
                        <i class="bi bi-file-earmark-excel-fill"></i>
                        <span>Export รายงาน (CSV)</span>
                    </a>
                    <a href="{{ route('attendances.report.print', ['date' => $date, 'department_id' => $departmentId, 'status' => $status, 'report_type' => $reportType], false) }}" target="_blank" class="btn btn-primary btn-sm rounded-3 px-3 py-2 d-inline-flex align-items-center gap-2 shadow-sm" style="background: var(--primary-gradient); border: none; font-weight: 600;">
                        <i class="bi bi-file-earmark-pdf-fill"></i>
                        <span>พิมพ์รายงาน PDF</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Print Only Header -->
    <div class="col-12 print-only mb-3">
        <div class="d-flex justify-content-between align-items-end border-bottom pb-2">
            <div>
                <h3 class="fw-bold mb-1">
                    @if($reportType === 'attendance') รายงานสรุปการมาปฏิบัติงาน (Attendance)
                    @elseif($reportType === 'overtime') รายงานการทำงานล่วงเวลา (Overtime - OT)
                    @elseif($reportType === 'leave') รายงานการลางาน (Leave Report)
                    @elseif($reportType === 'late') รายงานการเข้างานสาย (Late Arrival Report)
                    @else รายงานสรุปการลงเวลาปฏิบัติงานประจำวัน
                    @endif
                </h3>
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

    <!-- 2. Segmented Pill Tabs for Report Types (UX Upgrade) -->
    <div class="col-12 screen-only">
        <div class="report-nav-tabs">
            <a href="{{ route('attendances.report', array_filter(['date' => $date, 'department_id' => $departmentId, 'report_type' => 'all']), false) }}" 
               class="report-nav-pill {{ ($reportType ?? 'all') === 'all' && empty($status) ? 'active' : '' }}">
                <i class="bi bi-grid-fill"></i>
                <span>ภาพรวมทั้งหมด</span>
                <span class="pill-counter">{{ number_format($totalEmployees) }}</span>
            </a>

            <a href="{{ route('attendances.report', array_filter(['date' => $date, 'department_id' => $departmentId, 'report_type' => 'attendance']), false) }}" 
               class="report-nav-pill {{ ($reportType ?? '') === 'attendance' ? 'active' : '' }}">
                <i class="bi bi-check2-circle"></i>
                <span>การเข้างาน</span>
                <span class="pill-counter">{{ number_format($attendedCount) }}</span>
            </a>

            <a href="{{ route('attendances.report', array_filter(['date' => $date, 'department_id' => $departmentId, 'report_type' => 'overtime']), false) }}" 
               class="report-nav-pill {{ ($reportType ?? '') === 'overtime' ? 'active' : '' }}">
                <i class="bi bi-lightning-charge-fill"></i>
                <span>การทำ OT</span>
                <span class="pill-counter">{{ number_format($otCount) }}</span>
            </a>

            <a href="{{ route('attendances.report', array_filter(['date' => $date, 'department_id' => $departmentId, 'report_type' => 'leave']), false) }}" 
               class="report-nav-pill {{ ($reportType ?? '') === 'leave' ? 'active' : '' }}">
                <i class="bi bi-sun-fill "></i>
                <span>การลางาน</span>
                <span class="pill-counter">{{ number_format($leaveCount) }}</span>
            </a>

            <a href="{{ route('attendances.report', array_filter(['date' => $date, 'department_id' => $departmentId, 'report_type' => 'late']), false) }}" 
               class="report-nav-pill {{ ($reportType ?? '') === 'late' ? 'active' : '' }}">
                <i class="bi bi-exclamation-triangle-fill "></i>
                <span>มาสาย</span>
                <span class="pill-counter">{{ number_format($lateCount) }}</span>
            </a>
        </div>
    </div>

    <!-- 3. Filter Bar (Clean 1-Row Toolbar) -->
    <div class="col-12 screen-only filter-section">
        <div class="report-card p-3 p-md-4">
            <form method="GET" action="{{ route('attendances.report', [], false) }}" class="row g-3 align-items-end">
                <input type="hidden" name="report_type" value="{{ $reportType ?? 'all' }}">
                
                <div class="col-12 col-md-4 col-lg-3">
                    <label class="form-label small fw-semibold text-theme mb-1">
                        <i class="bi bi-calendar-event me-1 text-primary"></i> วันที่ตรวจสอบ
                    </label>
                    <input type="date" name="date" class="form-control filter-input" value="{{ $date }}" required>
                </div>

                <div class="col-12 col-md-4 col-lg-3">
                    <label class="form-label small fw-semibold text-theme mb-1">
                        <i class="bi bi-diagram-3 me-1 text-info"></i> แผนกงาน
                    </label>
                    <select name="department_id" class="form-select filter-input">
                        <option value="">-- ทุกแผนก (All) --</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ $departmentId == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-4 col-lg-3">
                    <label class="form-label small fw-semibold text-theme mb-1">
                        <i class="bi bi-funnel me-1 text-secondary"></i> สถานะย่อย
                    </label>
                    <select name="status" class="form-select filter-input">
                        <option value="">-- ทุกสถานะ --</option>
                        <option value="on_time" {{ $status === 'on_time' ? 'selected' : '' }}>ตรงเวลา (On Time)</option>
                        <option value="late" {{ $status === 'late' ? 'selected' : '' }}>มาสาย (Late)</option>
                        <option value="leave" {{ $status === 'leave' ? 'selected' : '' }}>ลางาน (Leave)</option>
                        <option value="absent" {{ $status === 'absent' ? 'selected' : '' }}>ยังไม่ลงเวลา / ขาดงาน</option>
                    </select>
                </div>

                <div class="col-12 col-lg-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary rounded-3 flex-grow-1 d-flex align-items-center justify-content-center gap-2 py-2" style="background: var(--primary-gradient); border: none; font-weight: 600;">
                        <i class="bi bi-search"></i>
                        <span>ค้นหาข้อมูล</span>
                    </button>
                    <a href="{{ route('attendances.report', ['date' => date('Y-m-d')], false) }}" class="btn btn-outline-secondary rounded-3 px-3 d-flex align-items-center justify-content-center" title="รีเซ็ตตัวกรอง">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- 4. KPI Stat Cards (Responsive Grid) -->
    <div class="col-6 col-md-4 col-xl-2">
        <a href="{{ route('attendances.report', array_filter(['date' => $date, 'department_id' => $departmentId, 'report_type' => 'all']), false) }}" 
           class="kpi-stat-card {{ ($reportType ?? 'all') === 'all' && empty($status) ? 'is-active' : '' }}">
            <div class="kpi-icon-wrapper" style="background: rgba(99, 102, 241, 0.12); color: #6366f1;">
                <i class="bi bi-people-fill"></i>
            </div>
            <span class="text-muted small d-block mb-1">พนักงานทั้งหมด</span>
            <h3 class="fw-bold mb-0 text-primary">{{ number_format($totalEmployees) }} <span class="fs-6 fw-normal text-muted">คน</span></h3>
        </a>
    </div>

    <div class="col-6 col-md-4 col-xl-2">
        <a href="{{ route('attendances.report', array_filter(['date' => $date, 'department_id' => $departmentId, 'status' => 'on_time']), false) }}" 
           class="kpi-stat-card {{ $status === 'on_time' ? 'is-active' : '' }}">
            <div class="kpi-icon-wrapper" style="background: rgba(16, 185, 129, 0.12); color: #10b981;">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <span class="text-muted small d-block mb-1">เข้างานตรงเวลา</span>
            <h3 class="fw-bold mb-0 text-success">{{ number_format($onTimeCount) }} <span class="fs-6 fw-normal text-muted">คน</span></h3>
        </a>
    </div>

    <div class="col-6 col-md-4 col-xl-2">
        <a href="{{ route('attendances.report', array_filter(['date' => $date, 'department_id' => $departmentId, 'status' => 'late', 'report_type' => 'late']), false) }}" 
           class="kpi-stat-card {{ ($reportType === 'late' || $status === 'late') ? 'is-active' : '' }}">
            <div class="kpi-icon-wrapper" style="background: rgba(245, 158, 11, 0.12); color: #f59e0b;">
                <i class="bi bi-alarm-fill"></i>
            </div>
            <span class="text-muted small d-block mb-1">เข้างานสาย</span>
            <h3 class="fw-bold mb-0 text-warning">{{ number_format($lateCount) }} <span class="fs-6 fw-normal text-muted">คน</span></h3>
        </a>
    </div>

    <div class="col-6 col-md-4 col-xl-2">
        <a href="{{ route('attendances.report', array_filter(['date' => $date, 'department_id' => $departmentId, 'status' => 'leave', 'report_type' => 'leave']), false) }}" 
           class="kpi-stat-card {{ ($reportType === 'leave' || $status === 'leave') ? 'is-active' : '' }}">
            <div class="kpi-icon-wrapper" style="background: rgba(6, 182, 212, 0.12); color: #06b6d4;">
                <i class="bi bi-calendar2-check-fill"></i>
            </div>
            <span class="text-muted small d-block mb-1">ลางาน (อนุมัติ)</span>
            <h3 class="fw-bold mb-0 text-info">{{ number_format($leaveCount) }} <span class="fs-6 fw-normal text-muted">คน</span></h3>
        </a>
    </div>

    <div class="col-6 col-md-4 col-xl-2">
        <a href="{{ route('attendances.report', array_filter(['date' => $date, 'department_id' => $departmentId, 'status' => 'absent']), false) }}" 
           class="kpi-stat-card {{ $status === 'absent' ? 'is-active' : '' }}">
            <div class="kpi-icon-wrapper" style="background: rgba(239, 68, 68, 0.12); color: #ef4444;">
                <i class="bi bi-person-x-fill"></i>
            </div>
            <span class="text-muted small d-block mb-1">ยังไม่ลงเวลา</span>
            <h3 class="fw-bold mb-0 text-danger">{{ number_format($absentCount) }} <span class="fs-6 fw-normal text-muted">คน</span></h3>
        </a>
    </div>

    <div class="col-6 col-md-4 col-xl-2">
        <a href="{{ route('attendances.report', array_filter(['date' => $date, 'department_id' => $departmentId, 'report_type' => 'overtime']), false) }}" 
           class="kpi-stat-card {{ ($reportType === 'overtime' || $status === 'overtime') ? 'is-active' : '' }}">
            <div class="kpi-icon-wrapper" style="background: rgba(168, 85, 247, 0.12); color: #a855f7;">
                <i class="bi bi-lightning-charge-fill"></i>
            </div>
            <span class="text-muted small d-block mb-1">ทำ OT (อนุมัติ)</span>
            <h3 class="fw-bold mb-0" style="color: #a855f7;">{{ number_format($otCount) }} <span class="fs-6 fw-normal text-muted">({{ number_format($totalOtHours, 1) }} ชม.)</span></h3>
        </a>
    </div>

    <!-- 5. Data Table -->
    <div class="col-12">
        <div class="report-card overflow-hidden">
            <div class="p-3 px-4 border-bottom border-theme d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-list-columns-reverse text-primary fs-5"></i>
                    <div>
                        <span class="fw-bold text-theme">รายการบันทึกเวลาประจำวันที่ {{ \Carbon\Carbon::parse($date)->locale('th')->isoFormat('D MMMM YYYY') }}</span>
                        @if($reportType && $reportType !== 'all')
                            <span class="badge bg-secondary-subtle text-secondary ms-2">ประเภท: {{ $reportType }}</span>
                        @endif
                    </div>
                </div>
                <div>
                    <span class="badge bg-primary-subtle text-primary px-3 py-2 fw-semibold rounded-pill">
                        <i class="bi bi-people me-1"></i> พบทั้งหมด {{ count($reportData) }} รายการ
                    </span>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-custom mb-0 align-middle">
                    <thead>
                        <tr>
                            <th style="width: 110px;" class="text-center">รหัสพนักงาน</th>
                            <th>ข้อมูลพนักงาน</th>
                            <th>แผนก / ตำแหน่ง</th>
                            <th class="text-center">เวลาเข้างาน</th>
                            <th class="text-center">เวลาเลิกงาน</th>
                            <th class="text-center">ทำงานล่วงเวลา (OT)</th>
                            <th class="text-center">สถานะการทำงาน</th>
                            <th>หมายเหตุ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reportData as $row)
                            @php
                                $emp = $row['user'];
                                $rowStatus = $row['status'];
                                $ot = $row['overtime'] ?? null;
                            @endphp
                            <tr>
                                <td class="text-center">
                                    <span class="badge bg-secondary-subtle text-secondary font-monospace px-2 py-1">
                                        {{ $emp->emp_code }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-circle" style="width: 38px; height: 38px; font-size: 0.9rem; flex-shrink: 0; background: var(--primary-gradient); color: #ffffff; font-weight: 700;">
                                            {{ mb_substr($emp->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('employees.show', $emp, false) }}" class="fw-bold text-theme text-decoration-none d-block">
                                                {{ $emp->name }}
                                            </a>
                                            <span class="text-muted small">{{ $emp->email }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-theme">{{ $emp->department->name ?? '-' }}</div>
                                    <span class="text-muted small">{{ $emp->position->name ?? '-' }}</span>
                                </td>
                                <td class="text-center">
                                    @if($row['check_in'])
                                        <span class="font-monospace fw-bold text-success d-inline-flex align-items-center gap-1">
                                            <i class="bi bi-box-arrow-in-right"></i>
                                            {{ substr($row['check_in'], 0, 5) }} น.
                                        </span>
                                    @else
                                        <span class="text-muted opacity-50">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($row['check_out'])
                                        <span class="font-monospace fw-bold text-warning d-inline-flex align-items-center gap-1">
                                            <i class="bi bi-box-arrow-right"></i>
                                            {{ substr($row['check_out'], 0, 5) }} น.
                                        </span>
                                    @else
                                        <span class="text-muted opacity-50">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($ot)
                                        @php
                                            $otBadgeClass = match($ot->ot_type) {
                                                'holiday' => 'bg-warning-subtle text-warning border-warning',
                                                'holiday_ot' => 'bg-danger-subtle text-danger border-danger',
                                                default => 'bg-primary-subtle text-primary border-primary',
                                            };
                                        @endphp
                                        <div class="d-inline-block">
                                            <span class="badge {{ $otBadgeClass }} border font-monospace px-2 py-1">
                                                <i class="bi bi-clock-fill me-1"></i>{{ $ot->hours }} ชม.
                                            </span>
                                            <div class="small text-muted mt-1" style="font-size: 0.72rem;">
                                                {{ $ot->start_time ? substr($ot->start_time, 0, 5) : '' }} - {{ $ot->end_time ? substr($ot->end_time, 0, 5) : '' }}
                                                <span class="badge bg-secondary-subtle text-secondary" style="font-size: 0.65rem;">{{ $ot->ot_type_label }}</span>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted opacity-50">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($rowStatus === 'on_time')
                                        <span class="badge bg-success-subtle text-success px-2 py-1 rounded-pill">
                                            <i class="bi bi-check-circle-fill me-1"></i>ตรงเวลา
                                        </span>
                                    @elseif($rowStatus === 'late')
                                        <span class="badge bg-warning-subtle text-warning px-2 py-1 rounded-pill">
                                            <i class="bi bi-exclamation-triangle-fill me-1"></i>มาสาย
                                        </span>
                                    @elseif($rowStatus === 'leave')
                                        <span class="badge bg-info-subtle text-info px-2 py-1 rounded-pill">
                                            <i class="bi bi-sun-fill me-1"></i>ลางาน
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger px-2 py-1 rounded-pill">
                                            <i class="bi bi-dash-circle-fill me-1"></i>ยังไม่ลงเวลา
                                        </span>
                                    @endif
                                </td>
                                <td class="text-muted small">
                                    {{ $row['notes'] ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="py-4">
                                        <i class="bi bi-search fs-1 d-block mb-3 text-muted opacity-50"></i>
                                        <h5 class="fw-bold text-theme mb-1">ไม่พบข้อมูลตามเงื่อนไขที่เลือก</h5>
                                        <p class="text-muted small mb-3">ลองปรับเปลี่ยนวันที่ แผนก หรือประเภทรายงานที่ต้องการตรวจสอบ</p>
                                        <a href="{{ route('attendances.report', ['date' => date('Y-m-d')], false) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i> ล้างตัวกรองทั้งหมด
                                        </a>
                                    </div>
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
