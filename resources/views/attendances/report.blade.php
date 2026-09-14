@extends('layouts.app')

@section('title', 'รายงานสรุปเวลาทำงาน (Attendance Report)')

@push('styles')
<style>
    /* Card & Filter UI Styles */
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

    /* Print Document Preview Box on Screen */
    .formal-document-container {
        background: var(--surface-bg);
        border: 1px solid var(--surface-border);
        border-radius: 20px;
        box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.08);
    }

    .doc-header-border {
        border-bottom: 2px solid var(--surface-border);
    }

    .signature-box {
        border: 1px dashed var(--surface-border);
        border-radius: 12px;
        padding: 20px;
        background: var(--badge-bg);
        text-align: center;
    }

    /* ==========================================================================
       Print & PDF Export Styles (มาตรฐานเอกสารรายงานทางการของบริษัท)
       ========================================================================== */
    @media print {
        @page {
            size: A4 landscape;
            margin: 8mm 12mm 10mm 12mm;
        }

        /* ซ่อนส่วนตกแต่งของเว็บที่ไม่เกี่ยวข้องกับรายงาน */
        .custom-navbar,
        .custom-footer,
        .hero-welcome-card,
        .filter-section,
        .screen-only,
        .btn,
        #themeToggleBtn {
            display: none !important;
        }

        body {
            background: #ffffff !important;
            color: #0f172a !important;
            font-family: 'Sarabun', 'Segoe UI', Tahoma, sans-serif !important;
            font-size: 10pt !important;
            line-height: 1.3 !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .container {
            max-width: 100% !important;
            width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .formal-document-container {
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
        }

        /* Header ทางการตอนพิมพ์ */
        .doc-print-header {
            display: block !important;
            border-bottom: 2px solid #0f172a !important;
            padding-bottom: 10px !important;
            margin-bottom: 14px !important;
        }

        .company-title {
            font-size: 16pt !important;
            font-weight: 800 !important;
            color: #0f172a !important;
            letter-spacing: 0.5px;
        }

        .report-title-text {
            font-size: 13pt !important;
            font-weight: 700 !important;
            color: #1e293b !important;
            margin-top: 4px !important;
        }

        /* KPI Box ตอนพิมพ์ */
        .kpi-print-summary {
            display: flex !important;
            border: 1px solid #cbd5e1 !important;
            background: #f8fafc !important;
            border-radius: 6px !important;
            margin-bottom: 14px !important;
        }

        .kpi-print-item {
            flex: 1;
            padding: 8px 10px !important;
            text-align: center;
            border-right: 1px solid #cbd5e1;
        }

        .kpi-print-item:last-child {
            border-right: none;
        }

        .kpi-print-label {
            font-size: 8.5pt !important;
            color: #475569 !important;
            font-weight: 600 !important;
            display: block;
        }

        .kpi-print-val {
            font-size: 13pt !important;
            font-weight: 800 !important;
            color: #0f172a !important;
        }

        /* ตารางข้อมูลรายงาน */
        .table-corporate {
            width: 100% !important;
            border-collapse: collapse !important;
            font-size: 9pt !important;
        }

        .table-corporate th {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
            font-weight: 700 !important;
            text-align: center !important;
            border: 1px solid #94a3b8 !important;
            padding: 6px 8px !important;
        }

        .table-corporate td {
            border: 1px solid #cbd5e1 !important;
            padding: 5px 8px !important;
            color: #1e293b !important;
            vertical-align: middle !important;
            background: transparent !important;
        }

        .table-corporate tr:nth-child(even) td {
            background-color: #f8fafc !important;
        }

        /* ป้ายสถานะตอนพิมพ์ */
        .badge-corporate {
            display: inline-block !important;
            font-size: 8pt !important;
            font-weight: 700 !important;
            padding: 2px 6px !important;
            border-radius: 4px !important;
            border: 1px solid #94a3b8 !important;
            background: #ffffff !important;
            color: #0f172a !important;
        }

        /* ส่วนลงนามท้ายเอกสาร */
        .signature-section {
            display: flex !important;
            justify-content: space-between !important;
            margin-top: 24px !important;
            page-break-inside: avoid !important;
        }

        .signature-col {
            width: 31% !important;
            text-align: center !important;
            font-size: 9pt !important;
            color: #1e293b !important;
        }

        .signature-line {
            margin-top: 45px !important;
            border-top: 1px dotted #475569 !important;
            padding-top: 4px !important;
        }
    }
</style>
@endpush

@section('content')
<div class="row g-4">
    <!-- 1. Screen Header Banner & Actions (แสดงบนหน้าจอ ไม่แสดงในพิมพ์) -->
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
                            จัดทำรายงานสรุปเวลาทำงานขององค์กรตามรูปแบบมาตรฐานบริษัท พร้อมฟังก์ชันพิมพ์และบันทึกเป็นเอกสาร PDF
                        </p>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-primary rounded-3 px-3 py-2 d-flex align-items-center gap-2 shadow-sm" onclick="window.print();" title="พิมพ์หรือบันทึกเป็น PDF">
                        <i class="bi bi-printer-fill fs-5"></i>
                        <span class="fw-bold">พิมพ์ / บันทึก PDF</span>
                    </button>
                    <a href="{{ route('attendances.checkin', [], false) }}" class="btn btn-outline-secondary rounded-3 px-3 py-2">
                        <i class="bi bi-stopwatch me-1"></i> หน้าลงเวลา
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Filter & Search Card (แสดงบนหน้าจอ) -->
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

    <!-- 3. Mini KPI Cards บนหน้าจอ (Screen Only) -->
    <div class="col-12 screen-only">
        <div class="row g-3">
            <div class="col-6 col-md-4 col-xl-2">
                <div class="stat-card p-3 text-center">
                    <span class="text-muted small d-block mb-1">พนักงานทั้งหมด</span>
                    <h3 class="fw-bold mb-0 text-primary">{{ number_format($totalEmployees) }} <span class="fs-6 fw-normal text-muted">คน</span></h3>
                </div>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
                <div class="stat-card p-3 text-center">
                    <span class="text-muted small d-block mb-1">มาปฏิบัติงาน</span>
                    <h3 class="fw-bold mb-0 text-success">{{ number_format($attendedCount) }} <span class="fs-6 fw-normal text-muted">คน</span></h3>
                </div>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
                <div class="stat-card p-3 text-center">
                    <span class="text-muted small d-block mb-1">ตรงเวลา</span>
                    <h3 class="fw-bold mb-0 text-success">{{ number_format($onTimeCount) }} <span class="fs-6 fw-normal text-muted">คน</span></h3>
                </div>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
                <div class="stat-card p-3 text-center">
                    <span class="text-muted small d-block mb-1">เข้างานสาย</span>
                    <h3 class="fw-bold mb-0 text-warning">{{ number_format($lateCount) }} <span class="fs-6 fw-normal text-muted">คน</span></h3>
                </div>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
                <div class="stat-card p-3 text-center">
                    <span class="text-muted small d-block mb-1">ลางาน (อนุมัติ)</span>
                    <h3 class="fw-bold mb-0 text-info">{{ number_format($leaveCount) }} <span class="fs-6 fw-normal text-muted">คน</span></h3>
                </div>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
                <div class="stat-card p-3 text-center">
                    <span class="text-muted small d-block mb-1">ยังไม่ลงเวลา / ขาด</span>
                    <h3 class="fw-bold mb-0 text-danger">{{ number_format($absentCount) }} <span class="fs-6 fw-normal text-muted">คน</span></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Formal Corporate Report Document (พิมพ์และแสดงผลมาตรฐานบริษัท) -->
    <div class="col-12">
        <div class="formal-document-container p-4 p-md-5">

            <!-- หัวกระดาษเอกสารทางการ (Official Corporate Letterhead) -->
            <div class="doc-header-border pb-3 mb-4">
                <div class="row align-items-center g-3">
                    <div class="col-8">
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-3 p-2 px-3 fw-bold" style="font-size: 1.25rem;">
                                WB
                            </div>
                            <div>
                                <h4 class="fw-bold mb-0 text-theme company-title">บริษัท ดับเบิ้ลยูบี แมเนจเม้นท์ จำกัด</h4>
                                <span class="small text-muted d-block">WB-EMS CORPORATION CO., LTD. • ทะเบียนนิติบุคคล: 0105569000000</span>
                            </div>
                        </div>
                        <h5 class="fw-bold text-primary mt-3 mb-1 report-title-text">
                            <i class="bi bi-card-checklist me-1 screen-only"></i> รายงานสรุปการลงเวลาปฏิบัติงานประจำวัน (Daily Attendance Report)
                        </h5>
                    </div>
                    <div class="col-4 text-end text-muted small">
                        <div class="d-inline-block text-start p-2 px-3 rounded-3" style="background: var(--badge-bg); border: 1px solid var(--surface-border);">
                            <div><strong>เลขที่เอกสาร:</strong> ATT-{{ date('Ymd', strtotime($date)) }}</div>
                            <div><strong>วันที่ตรวจสอบ:</strong> {{ date('d/m/Y', strtotime($date)) }}</div>
                            <div><strong>พิมพ์เมื่อ:</strong> {{ date('d/m/Y H:i') }} น.</div>
                            <div><strong>ผู้ออกเอกสาร:</strong> {{ Auth::user()->name }} ({{ strtoupper(Auth::user()->role) }})</div>
                        </div>
                    </div>
                </div>

                <!-- แถบระบุตัวกรองที่เลือก -->
                <div class="mt-3 pt-2 border-top border-secondary border-opacity-10 d-flex flex-wrap gap-4 small text-muted">
                    <div>
                        <span class="fw-semibold text-theme">แผนกที่เลือก:</span> 
                        <span class="badge bg-secondary-subtle text-secondary px-2">
                            {{ $departmentId ? ($departments->firstWhere('id', $departmentId)->name ?? 'ทั้งหมด') : 'ทุกแผนก' }}
                        </span>
                    </div>
                    <div>
                        <span class="fw-semibold text-theme">ตัวกรองสถานะ:</span> 
                        <span class="badge bg-secondary-subtle text-secondary px-2">
                            @if($status === 'on_time') ตรงเวลา
                            @elseif($status === 'late') มาสาย
                            @elseif($status === 'leave') ลางาน
                            @elseif($status === 'absent') ยังไม่ลงเวลา / ขาด
                            @else ทั้งหมด
                            @endif
                        </span>
                    </div>
                    <div>
                        <span class="fw-semibold text-theme">จำนวนรายการ:</span> 
                        <strong class="text-theme">{{ count($reportData) }}</strong> คน
                    </div>
                </div>
            </div>

            <!-- กล่องสรุปสถิติผู้บริหาร (Executive Summary Box สำหรับพิมพ์) -->
            <div class="kpi-print-summary d-flex mb-4">
                <div class="kpi-print-item p-2 px-3 flex-fill text-center border-end border-secondary border-opacity-25">
                    <span class="kpi-print-label small text-muted d-block">พนักงานทั้งหมด</span>
                    <span class="kpi-print-val fw-bold fs-5 text-primary">{{ number_format($totalEmployees) }}</span>
                </div>
                <div class="kpi-print-item p-2 px-3 flex-fill text-center border-end border-secondary border-opacity-25">
                    <span class="kpi-print-label small text-muted d-block">มาปฏิบัติงาน</span>
                    <span class="kpi-print-val fw-bold fs-5 text-success">{{ number_format($attendedCount) }}</span>
                </div>
                <div class="kpi-print-item p-2 px-3 flex-fill text-center border-end border-secondary border-opacity-25">
                    <span class="kpi-print-label small text-muted d-block">เข้างานตรงเวลา</span>
                    <span class="kpi-print-val fw-bold fs-5 text-success">{{ number_format($onTimeCount) }}</span>
                </div>
                <div class="kpi-print-item p-2 px-3 flex-fill text-center border-end border-secondary border-opacity-25">
                    <span class="kpi-print-label small text-muted d-block">เข้างานสาย</span>
                    <span class="kpi-print-val fw-bold fs-5 text-warning">{{ number_format($lateCount) }}</span>
                </div>
                <div class="kpi-print-item p-2 px-3 flex-fill text-center border-end border-secondary border-opacity-25">
                    <span class="kpi-print-label small text-muted d-block">ลางาน (อนุมัติ)</span>
                    <span class="kpi-print-val fw-bold fs-5 text-info">{{ number_format($leaveCount) }}</span>
                </div>
                <div class="kpi-print-item p-2 px-3 flex-fill text-center">
                    <span class="kpi-print-label small text-muted d-block">ยังไม่ลงเวลา / ขาด</span>
                    <span class="kpi-print-val fw-bold fs-5 text-danger">{{ number_format($absentCount) }}</span>
                </div>
            </div>

            <!-- ตารางข้อมูลพนักงานทางการ (Corporate Attendance Table) -->
            <div class="table-responsive">
                <table class="table table-corporate table-bordered align-middle mb-0">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th style="width: 45px;">ลำดับ</th>
                            <th style="width: 95px;">รหัสพนักงาน</th>
                            <th style="min-width: 170px;" class="text-start">ชื่อ - นามสกุล</th>
                            <th style="width: 130px;">แผนก</th>
                            <th style="width: 130px;">ตำแหน่ง</th>
                            <th style="width: 90px;">เวลาเข้างาน</th>
                            <th style="width: 90px;">เวลาเลิกงาน</th>
                            <th style="width: 120px;">สถานะการเข้างาน</th>
                            <th style="min-width: 140px;">หมายเหตุ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reportData as $index => $row)
                            @php
                                $emp = $row['user'];
                                $rowStatus = $row['status'];
                            @endphp
                            <tr>
                                <td class="text-center text-muted font-monospace">{{ $index + 1 }}</td>
                                <td class="text-center font-monospace fw-semibold">{{ $emp->emp_code }}</td>
                                <td>
                                    <div class="fw-bold text-theme">{{ $emp->name }}</div>
                                    <span class="text-muted d-block" style="font-size: 0.78rem;">{{ $emp->email }}</span>
                                </td>
                                <td>{{ $emp->department->name ?? '-' }}</td>
                                <td>{{ $emp->position->name ?? '-' }}</td>
                                <td class="text-center font-monospace">
                                    @if($row['check_in'])
                                        <span class="text-success fw-bold">{{ substr($row['check_in'], 0, 5) }} น.</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center font-monospace">
                                    @if($row['check_out'])
                                        <span class="text-warning fw-bold">{{ substr($row['check_out'], 0, 5) }} น.</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($rowStatus === 'on_time')
                                        <span class="badge badge-corporate text-success bg-success-subtle border-success">
                                            ตรงเวลา
                                        </span>
                                    @elseif($rowStatus === 'late')
                                        <span class="badge badge-corporate text-warning bg-warning-subtle border-warning">
                                            มาสาย
                                        </span>
                                    @elseif($rowStatus === 'leave')
                                        <span class="badge badge-corporate text-info bg-info-subtle border-info">
                                            ลางาน
                                        </span>
                                    @else
                                        <span class="badge badge-corporate text-danger bg-danger-subtle border-danger">
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
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-1 opacity-50"></i>
                                    <span>ไม่พบข้อมูลพนักงานตามเงื่อนไขที่ระบุ</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- ส่วนลงนามท้ายเอกสาร 3 ฝ่าย (Official Signatures Section) -->
            <div class="signature-section mt-5 pt-3">
                <div class="row g-4 text-center">
                    <div class="col-4 signature-col">
                        <div class="signature-box p-3">
                            <span class="small text-muted fw-semibold d-block mb-4">ผู้จัดทำรายงาน (Prepared By)</span>
                            <div class="signature-line pt-2">
                                <div class="fw-bold text-theme">{{ Auth::user()->name }}</div>
                                <span class="small text-muted d-block">เจ้าหน้าที่ฝ่ายบุคคล (HR Officer)</span>
                                <span class="small text-muted">วันที่: {{ date('d / m / Y') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-4 signature-col">
                        <div class="signature-box p-3">
                            <span class="small text-muted fw-semibold d-block mb-4">ผู้ตรวจสอบรายงาน (Verified By)</span>
                            <div class="signature-line pt-2">
                                <div class="text-muted opacity-50">....................................................</div>
                                <span class="small text-muted d-block">( ผู้จัดการฝ่ายบุคคล / ผู้ตรวจสอบ )</span>
                                <span class="small text-muted">วันที่: ...... / ...... / .........</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-4 signature-col">
                        <div class="signature-box p-3">
                            <span class="small text-muted fw-semibold d-block mb-4">ผู้มีอำนาจอนุมัติ (Approved By)</span>
                            <div class="signature-line pt-2">
                                <div class="text-muted opacity-50">....................................................</div>
                                <span class="small text-muted d-block">( กรรมการผู้จัดการ / ผู้บริหารสูงสุด )</span>
                                <span class="small text-muted">วันที่: ...... / ...... / .........</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-2 border-top border-secondary border-opacity-10 text-center text-muted small" style="font-size: 0.75rem;">
                    เอกสารนี้สร้างขึ้นจากระบบบริหารจัดการข้อมูลพนักงาน (WB-EMS) • ข้อมูลในรายงานนี้ถือเป็นความลับขององค์กร ห้ามคัดลอกหรือเผยแพร่โดยไม่ได้รับอนุญาต
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
