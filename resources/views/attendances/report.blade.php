@extends('layouts.app')

@section('title', 'รายงานสรุปเวลาทำงาน (Attendance Report)')

@push('styles')
<style>
    /* -------------------------------------------------------------
       Screen Display UI Styles (แสดงผลบนหน้าจอเว็บ)
       ------------------------------------------------------------- */
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

    .formal-document-container {
        background: var(--surface-bg);
        border: 1px solid var(--surface-border);
        border-radius: 20px;
        box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.08);
    }

    .doc-header-border {
        border-bottom: 2px solid var(--surface-border);
    }

    .signature-card {
        border: 1px dashed var(--surface-border);
        border-radius: 12px;
        padding: 18px;
        background: var(--badge-bg);
        text-align: center;
    }

    /* -------------------------------------------------------------
       Corporate Print & PDF Export Styles (@media print)
       มาตรฐานเอกสารรายงานทางการของบริษัท จัดพอดี 1 หน้ากระดาษ A4 Landscape
       ------------------------------------------------------------- */
    @media print {
        @page {
            size: A4 landscape;
            margin: 6mm 10mm 6mm 10mm;
        }

        /* ซ่อนส่วนที่ไม่เกี่ยวข้องกับเอกสารรายงานทั้งหมด */
        .custom-navbar,
        .custom-footer,
        .hero-welcome-card,
        .filter-section,
        .screen-only,
        .btn,
        #themeToggleBtn,
        .alert {
            display: none !important;
        }

        html, body {
            background: #ffffff !important;
            color: #0f172a !important;
            font-family: 'Sarabun', 'Segoe UI', Tahoma, Arial, sans-serif !important;
            font-size: 8.5pt !important;
            line-height: 1.25 !important;
            margin: 0 !important;
            padding: 0 !important;
            height: auto !important;
        }

        .container {
            max-width: 100% !important;
            width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .formal-document-container {
            background: #ffffff !important;
            border: none !important;
            box-shadow: none !important;
            border-radius: 0 !important;
            padding: 0 !important;
            margin: 0 !important;
            page-break-inside: avoid !important;
            page-break-after: avoid !important;
        }

        /* ส่วนหัวเอกสารรายงานทางการ */
        .doc-print-header {
            border-bottom: 2px solid #0f172a !important;
            padding-bottom: 6px !important;
            margin-bottom: 8px !important;
        }

        .company-logo-box {
            background: #1e293b !important;
            color: #ffffff !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            font-size: 13pt !important;
            font-weight: 800 !important;
            padding: 4px 10px !important;
            border-radius: 4px !important;
        }

        .company-title {
            font-size: 13pt !important;
            font-weight: 800 !important;
            color: #0f172a !important;
            margin: 0 !important;
            line-height: 1.2 !important;
        }

        .company-subtitle {
            font-size: 8pt !important;
            color: #64748b !important;
        }

        .report-title-text {
            font-size: 11pt !important;
            font-weight: 700 !important;
            color: #1e3a8a !important;
            margin-top: 2px !important;
            margin-bottom: 0 !important;
        }

        .meta-info-box {
            background: #f8fafc !important;
            border: 1px solid #cbd5e1 !important;
            font-size: 7.5pt !important;
            line-height: 1.35 !important;
            padding: 4px 8px !important;
            border-radius: 4px !important;
        }

        .filter-tags-bar {
            font-size: 8pt !important;
            margin-top: 4px !important;
            padding-top: 3px !important;
            border-top: 1px dotted #cbd5e1 !important;
        }

        /* กล่องสรุปสถิติผู้บริหาร (Executive KPI Summary Bar) */
        .kpi-print-summary {
            display: flex !important;
            border: 1px solid #94a3b8 !important;
            background: #f8fafc !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            border-radius: 4px !important;
            margin-bottom: 8px !important;
        }

        .kpi-print-item {
            flex: 1 !important;
            padding: 4px 6px !important;
            text-align: center !important;
            border-right: 1px solid #cbd5e1 !important;
        }

        .kpi-print-item:last-child {
            border-right: none !important;
        }

        .kpi-print-label {
            font-size: 7.5pt !important;
            color: #475569 !important;
            font-weight: 600 !important;
            display: block !important;
            margin-bottom: 1px !important;
        }

        .kpi-print-val {
            font-size: 11pt !important;
            font-weight: 800 !important;
            color: #0f172a !important;
            line-height: 1 !important;
        }

        /* ตารางข้อมูลรายงานทางการ (Formal Corporate Table) */
        .table-corporate {
            width: 100% !important;
            border-collapse: collapse !important;
            font-size: 8pt !important;
            margin-bottom: 8px !important;
        }

        .table-corporate th {
            background-color: #f1f5f9 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color: #0f172a !important;
            font-weight: 700 !important;
            text-align: center !important;
            border: 1px solid #64748b !important;
            padding: 4px 5px !important;
        }

        .table-corporate td {
            border: 1px solid #94a3b8 !important;
            padding: 4px 6px !important;
            color: #0f172a !important;
            vertical-align: middle !important;
            background: transparent !important;
        }

        .table-corporate tr:nth-child(even) td {
            background-color: #f8fafc !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .badge-corporate {
            display: inline-block !important;
            font-size: 7.5pt !important;
            font-weight: 700 !important;
            padding: 1px 5px !important;
            border-radius: 3px !important;
            border: 1px solid #64748b !important;
            background: #ffffff !important;
            color: #0f172a !important;
            white-space: nowrap !important;
        }

        /* ส่วนลงนามท้ายเอกสาร 3 ฝ่าย (จัดเรียง 3 คอลัมน์เต็มหน้า และหมายเหตุด้านล่าง) */
        .signature-container {
            width: 100% !important;
            margin-top: 10px !important;
            page-break-inside: avoid !important;
        }

        .signature-grid {
            display: flex !important;
            flex-direction: row !important;
            justify-content: space-between !important;
            width: 100% !important;
            gap: 12px !important;
        }

        .signature-col {
            flex: 1 !important;
            width: 32% !important;
            text-align: center !important;
        }

        .signature-card {
            border: 1px solid #94a3b8 !important;
            background: #ffffff !important;
            padding: 6px 8px !important;
            border-radius: 4px !important;
        }

        .signature-role-title {
            font-size: 8pt !important;
            font-weight: 700 !important;
            color: #1e293b !important;
            display: block !important;
            margin-bottom: 18px !important;
        }

        .signature-line {
            border-top: 1px dotted #475569 !important;
            padding-top: 3px !important;
            font-size: 7.5pt !important;
            line-height: 1.3 !important;
        }

        .document-footer-note {
            display: block !important;
            width: 100% !important;
            margin-top: 6px !important;
            padding-top: 4px !important;
            border-top: 1px solid #cbd5e1 !important;
            text-align: center !important;
            font-size: 6.5pt !important;
            color: #64748b !important;
        }
    }
</style>
@endpush

@section('content')
<div class="row g-4">
    <!-- 1. Screen Header Banner & Actions (แสดงบนหน้าจอเว็บเท่านั้น) -->
    <div class="col-12 screen-only">
        <div class="hero-welcome-card p-4">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="action-icon icon-indigo">
                        <i class="bi bi-file-earmark-pdf-fill"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-1 gradient-text">รายงานการลงเวลาปฏิบัติงาน (Attendance Report)</h3>
                        <p class="text-muted mb-0 small">
                            จัดทำเอกสารรายงานสรุปเวลาทำงานตามรูปแบบมาตรฐานบริษัท พร้อมระบบพิมพ์และบันทึกเป็นเอกสาร PDF
                        </p>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-primary rounded-3 px-3 py-2 d-flex align-items-center gap-2 shadow-sm" onclick="window.print();" title="พิมพ์หรือบันทึกเป็น PDF (Save as PDF)">
                        <i class="bi bi-printer-fill fs-5"></i>
                        <span class="fw-bold">พิมพ์ / บันทึกเป็น PDF</span>
                    </button>
                    <a href="{{ route('attendances.checkin', [], false) }}" class="btn btn-outline-secondary rounded-3 px-3 py-2">
                        <i class="bi bi-stopwatch me-1"></i> หน้าลงเวลา
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Filter & Search Card (แสดงบนหน้าจอเว็บเท่านั้น) -->
    <div class="col-12 screen-only filter-section">
        <div class="report-card p-4">
            <form method="GET" action="{{ route('attendances.report', [], false) }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-theme">
                        <i class="bi bi-calendar-event me-1"></i> วันที่ตรวจสอบข้อมูล
                    </label>
                    <input type="date" name="date" class="form-control filter-input" value="{{ $date }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-theme">
                        <i class="bi bi-diagram-3 me-1"></i> แผนกงาน
                    </label>
                    <select name="department_id" class="form-select filter-input">
                        <option value="">-- ทุกแผนก (All Departments) --</option>
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
                        <option value="">-- ทุกสถานะ (All Status) --</option>
                        <option value="on_time" {{ $status === 'on_time' ? 'selected' : '' }}>ตรงเวลา (On Time)</option>
                        <option value="late" {{ $status === 'late' ? 'selected' : '' }}>มาสาย (Late)</option>
                        <option value="leave" {{ $status === 'leave' ? 'selected' : '' }}>ลางาน (Approved Leave)</option>
                        <option value="absent" {{ $status === 'absent' ? 'selected' : '' }}>ยังไม่ลงเวลา / ขาดงาน</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100 rounded-3 d-flex align-items-center justify-content-center" style="height: 42px;" title="ค้นหาข้อมูล">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 3. Screen Mini KPI Cards (แสดงบนหน้าจอเว็บเท่านั้น) -->
    <div class="col-12 screen-only">
        <div class="row g-3">
            <div class="col-6 col-md-4 col-xl-2">
                <div class="stat-card p-3 text-center">
                    <span class="text-muted small d-block mb-1">พนักงานทั้งหมด</span>
                    <h4 class="fw-bold mb-0 text-primary">{{ number_format($totalEmployees) }} <span class="fs-6 fw-normal text-muted">คน</span></h4>
                </div>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
                <div class="stat-card p-3 text-center">
                    <span class="text-muted small d-block mb-1">มาปฏิบัติงาน</span>
                    <h4 class="fw-bold mb-0 text-success">{{ number_format($attendedCount) }} <span class="fs-6 fw-normal text-muted">คน</span></h4>
                </div>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
                <div class="stat-card p-3 text-center">
                    <span class="text-muted small d-block mb-1">ตรงเวลา</span>
                    <h4 class="fw-bold mb-0 text-success">{{ number_format($onTimeCount) }} <span class="fs-6 fw-normal text-muted">คน</span></h4>
                </div>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
                <div class="stat-card p-3 text-center">
                    <span class="text-muted small d-block mb-1">เข้างานสาย</span>
                    <h4 class="fw-bold mb-0 text-warning">{{ number_format($lateCount) }} <span class="fs-6 fw-normal text-muted">คน</span></h4>
                </div>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
                <div class="stat-card p-3 text-center">
                    <span class="text-muted small d-block mb-1">ลางาน (อนุมัติ)</span>
                    <h4 class="fw-bold mb-0 text-info">{{ number_format($leaveCount) }} <span class="fs-6 fw-normal text-muted">คน</span></h4>
                </div>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
                <div class="stat-card p-3 text-center">
                    <span class="text-muted small d-block mb-1">ยังไม่ลงเวลา / ขาด</span>
                    <h4 class="fw-bold mb-0 text-danger">{{ number_format($absentCount) }} <span class="fs-6 fw-normal text-muted">คน</span></h4>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Formal Corporate Report Document (พิมพ์พอดี 1 หน้ากระดาษ A4 Landscape) -->
    <div class="col-12">
        <div class="formal-document-container p-4 p-md-5">

            <!-- ส่วนหัวเอกสารรายงานทางการ (Official Corporate Letterhead) -->
            <div class="doc-print-header pb-2 mb-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="company-logo-box">WB</span>
                            <div>
                                <h4 class="company-title">บริษัท ดับเบิ้ลยูบี แมเนจเม้นท์ จำกัด</h4>
                                <span class="company-subtitle">WB-EMS CORPORATION CO., LTD. • ทะเบียนนิติบุคคล: 0105569000000</span>
                            </div>
                        </div>
                        <h5 class="report-title-text">
                            รายงานสรุปการลงเวลาปฏิบัติงานประจำวัน (Daily Attendance Report)
                        </h5>
                    </div>

                    <div class="text-end">
                        <div class="meta-info-box text-start d-inline-block">
                            <div><strong>เลขที่เอกสาร:</strong> ATT-{{ date('Ymd', strtotime($date)) }}</div>
                            <div><strong>วันที่ตรวจสอบ:</strong> {{ date('d/m/Y', strtotime($date)) }}</div>
                            <div><strong>พิมพ์เมื่อ:</strong> {{ date('d/m/Y H:i') }} น.</div>
                            <div><strong>ผู้ออกเอกสาร:</strong> {{ Auth::user()->name }} ({{ strtoupper(Auth::user()->role) }})</div>
                        </div>
                    </div>
                </div>

                <!-- แถบสรุปตัวกรองเงื่อนไข -->
                <div class="filter-tags-bar d-flex justify-content-between text-muted">
                    <div>
                        <strong>แผนกที่เลือก:</strong> 
                        <span>{{ $departmentId ? ($departments->firstWhere('id', $departmentId)->name ?? 'ทั้งหมด') : 'ทุกแผนก' }}</span>
                        &nbsp;&bull;&nbsp;
                        <strong>ตัวกรองสถานะ:</strong> 
                        <span>
                            @if($status === 'on_time') ตรงเวลา
                            @elseif($status === 'late') มาสาย
                            @elseif($status === 'leave') ลางาน
                            @elseif($status === 'absent') ยังไม่ลงเวลา / ขาด
                            @else ทั้งหมด
                            @endif
                        </span>
                    </div>
                    <div>
                        <strong>จำนวนรายการ:</strong> <strong>{{ count($reportData) }}</strong> คน
                    </div>
                </div>
            </div>

            <!-- กล่องสรุปสถิติผู้บริหาร (Executive KPI Summary Bar) -->
            <div class="kpi-print-summary d-flex">
                <div class="kpi-print-item">
                    <span class="kpi-print-label">พนักงานทั้งหมด</span>
                    <span class="kpi-print-val text-primary">{{ number_format($totalEmployees) }}</span>
                </div>
                <div class="kpi-print-item">
                    <span class="kpi-print-label">มาปฏิบัติงาน</span>
                    <span class="kpi-print-val text-success">{{ number_format($attendedCount) }}</span>
                </div>
                <div class="kpi-print-item">
                    <span class="kpi-print-label">เข้างานตรงเวลา</span>
                    <span class="kpi-print-val text-success">{{ number_format($onTimeCount) }}</span>
                </div>
                <div class="kpi-print-item">
                    <span class="kpi-print-label">เข้างานสาย</span>
                    <span class="kpi-print-val text-warning">{{ number_format($lateCount) }}</span>
                </div>
                <div class="kpi-print-item">
                    <span class="kpi-print-label">ลางาน (อนุมัติ)</span>
                    <span class="kpi-print-val text-info">{{ number_format($leaveCount) }}</span>
                </div>
                <div class="kpi-print-item">
                    <span class="kpi-print-label">ยังไม่ลงเวลา / ขาด</span>
                    <span class="kpi-print-val text-danger">{{ number_format($absentCount) }}</span>
                </div>
            </div>

            <!-- ตารางข้อมูลรายงานทางการ (Formal Corporate Attendance Table) -->
            <div class="table-responsive">
                <table class="table-corporate">
                    <thead>
                        <tr>
                            <th style="width: 40px;">ลำดับ</th>
                            <th style="width: 90px;">รหัสพนักงาน</th>
                            <th style="min-width: 170px;" class="text-start">ชื่อ - นามสกุล</th>
                            <th style="width: 120px;">แผนก</th>
                            <th style="width: 120px;">ตำแหน่ง</th>
                            <th style="width: 80px;">เวลาเข้างาน</th>
                            <th style="width: 80px;">เวลาเลิกงาน</th>
                            <th style="width: 110px;">สถานะการเข้างาน</th>
                            <th style="min-width: 120px;">หมายเหตุ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reportData as $index => $row)
                            @php
                                $emp = $row['user'];
                                $rowStatus = $row['status'];
                            @endphp
                            <tr>
                                <td class="text-center font-monospace">{{ $index + 1 }}</td>
                                <td class="text-center font-monospace fw-semibold">{{ $emp->emp_code }}</td>
                                <td>
                                    <strong>{{ $emp->name }}</strong>
                                    <span class="text-muted d-block screen-only" style="font-size: 0.75rem;">{{ $emp->email }}</span>
                                </td>
                                <td>{{ $emp->department->name ?? '-' }}</td>
                                <td>{{ $emp->position->name ?? '-' }}</td>
                                <td class="text-center font-monospace">
                                    @if($row['check_in'])
                                        <strong>{{ substr($row['check_in'], 0, 5) }} น.</strong>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center font-monospace">
                                    @if($row['check_out'])
                                        <strong>{{ substr($row['check_out'], 0, 5) }} น.</strong>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($rowStatus === 'on_time')
                                        <span class="badge-corporate text-success">
                                            ตรงเวลา
                                        </span>
                                    @elseif($rowStatus === 'late')
                                        <span class="badge-corporate text-warning">
                                            มาสาย
                                        </span>
                                    @elseif($rowStatus === 'leave')
                                        <span class="badge-corporate text-info">
                                            ลางาน
                                        </span>
                                    @else
                                        <span class="badge-corporate text-danger">
                                            ยังไม่ลงเวลา
                                        </span>
                                    @endif
                                </td>
                                <td class="small text-muted">
                                    {{ $row['notes'] ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    <span>ไม่พบข้อมูลพนักงานตามเงื่อนไขที่ระบุ</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- ส่วนลงนามท้ายเอกสาร 3 ฝ่าย (Official Signatures Section - เต็มความกว้าง ไม่หลุดไปหน้า 2) -->
            <div class="signature-container">
                <div class="signature-grid">
                    <div class="signature-col">
                        <div class="signature-card">
                            <span class="signature-role-title">ผู้จัดทำรายงาน (Prepared By)</span>
                            <div class="signature-line">
                                <strong>{{ Auth::user()->name }}</strong>
                                <div>เจ้าหน้าที่ฝ่ายบุคคล (HR Officer)</div>
                                <div>วันที่: {{ date('d / m / Y') }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="signature-col">
                        <div class="signature-card">
                            <span class="signature-role-title">ผู้ตรวจสอบรายงาน (Verified By)</span>
                            <div class="signature-line">
                                <div>....................................................</div>
                                <div>( ผู้จัดการฝ่ายบุคคล / ผู้ตรวจสอบ )</div>
                                <div>วันที่: ...... / ...... / .........</div>
                            </div>
                        </div>
                    </div>

                    <div class="signature-col">
                        <div class="signature-card">
                            <span class="signature-role-title">ผู้มีอำนาจอนุมัติ (Approved By)</span>
                            <div class="signature-line">
                                <div>....................................................</div>
                                <div>( กรรมการผู้จัดการ / ผู้บริหารสูงสุด )</div>
                                <div>วันที่: ...... / ...... / .........</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="document-footer-note">
                    เอกสารนี้สร้างขึ้นจากระบบบริหารจัดการข้อมูลพนักงาน (WB-EMS) • ข้อมูลในรายงานนี้ถือเป็นความลับขององค์กร ห้ามคัดลอกหรือเผยแพร่โดยไม่ได้รับอนุญาต
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
