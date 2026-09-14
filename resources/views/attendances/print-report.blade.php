<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายงานสรุปการลงเวลาปฏิบัติงานประจำวัน - {{ date('d/m/Y', strtotime($date)) }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Sarabun', Tahoma, 'Segoe UI', sans-serif;
            background-color: #525659;
            color: #0f172a;
            font-size: 11pt;
            line-height: 1.4;
        }

        /* Screen Preview Toolbar */
        .print-toolbar {
            position: sticky;
            top: 0;
            left: 0;
            right: 0;
            background: #1e293b;
            color: #ffffff;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 12px rgba(0,0,0,0.25);
            z-index: 9999;
        }

        .toolbar-title {
            font-size: 1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .toolbar-actions {
            display: flex;
            gap: 12px;
        }

        .btn-toolbar {
            border: none;
            padding: 8px 18px;
            border-radius: 6px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-print {
            background: #2563eb;
            color: #ffffff;
        }
        .btn-print:hover {
            background: #1d4ed8;
        }

        .btn-close-window {
            background: #475569;
            color: #ffffff;
        }
        .btn-close-window:hover {
            background: #334155;
        }

        /* Paper Document Sheet */
        .paper-container {
            display: flex;
            justify-content: center;
            padding: 24px 16px;
        }

        .doc-sheet {
            background: #ffffff;
            width: 297mm;
            min-height: 210mm;
            padding: 15mm 18mm;
            box-shadow: 0 8px 24px rgba(0,0,0,0.3);
            border-radius: 4px;
        }

        /* Header / Corporate Letterhead */
        .doc-letterhead {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #1e293b;
            padding-bottom: 12px;
            margin-bottom: 12px;
        }

        .company-brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .company-logo {
            width: 48px;
            height: 48px;
            background: #0f172a;
            color: #ffffff;
            font-weight: 900;
            font-size: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            letter-spacing: -0.5px;
        }

        .company-info h1 {
            font-size: 15pt;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 2px;
            line-height: 1.2;
        }

        .company-info p {
            font-size: 8.5pt;
            color: #475569;
            line-height: 1.3;
        }

        .doc-meta {
            text-align: right;
            font-size: 8.5pt;
            color: #334155;
            line-height: 1.5;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 6px 12px;
            border-radius: 4px;
        }

        .doc-meta strong {
            color: #0f172a;
        }

        /* Document Title */
        .report-header-title {
            text-align: center;
            margin-bottom: 12px;
        }

        .report-header-title h2 {
            font-size: 14pt;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
        }

        .report-header-title .report-subtitle {
            font-size: 9pt;
            color: #475569;
        }

        /* Filter Summary Bar */
        .filter-summary-bar {
            display: flex;
            justify-content: space-between;
            font-size: 8.5pt;
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 5px 10px;
            border-radius: 4px;
            margin-bottom: 10px;
        }

        /* Executive Summary Table */
        .summary-kpi-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            text-align: center;
        }

        .summary-kpi-table th {
            background-color: #e2e8f0;
            border: 1px solid #94a3b8;
            font-size: 8pt;
            font-weight: 600;
            color: #334155;
            padding: 5px 6px;
        }

        .summary-kpi-table td {
            border: 1px solid #94a3b8;
            font-size: 11pt;
            font-weight: 700;
            color: #0f172a;
            padding: 6px;
            background: #ffffff;
        }

        /* Main Data Table */
        .report-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            margin-bottom: 14px;
        }

        .report-table th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: 700;
            text-align: center;
            border: 1px solid #475569;
            padding: 6px 5px;
            line-height: 1.2;
        }

        .report-table td {
            border: 1px solid #94a3b8;
            padding: 5px 6px;
            vertical-align: middle;
            color: #0f172a;
        }

        .report-table tbody tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        .text-center { text-align: center; }
        .text-start { text-align: left; }
        .text-end { text-align: right; }
        .font-mono { font-family: Consolas, monospace; }
        .fw-bold { font-weight: 700; }

        .status-badge-corp {
            display: inline-block;
            padding: 1px 6px;
            border-radius: 3px;
            font-size: 7.5pt;
            font-weight: 700;
            border: 1px solid #64748b;
            background: #ffffff;
            white-space: nowrap;
        }

        .status-on-time {
            color: #15803d;
            border-color: #16a34a;
            background: #f0fdf4;
        }
        .status-late {
            color: #b45309;
            border-color: #d97706;
            background: #fffbeb;
        }
        .status-leave {
            color: #0369a1;
            border-color: #0284c7;
            background: #f0f9ff;
        }
        .status-absent {
            color: #b91c1c;
            border-color: #dc2626;
            background: #fef2f2;
        }

        /* Signatures Section */
        .signatures-container {
            margin-top: 14px;
            page-break-inside: avoid;
        }

        .signatures-grid {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            width: 100%;
        }

        .signature-box {
            flex: 1;
            border: 1px solid #94a3b8;
            border-radius: 4px;
            padding: 8px 10px;
            text-align: center;
            background: #ffffff;
        }

        .sig-title {
            font-size: 8.5pt;
            font-weight: 700;
            color: #1e293b;
            display: block;
            margin-bottom: 24px;
        }

        .sig-line {
            border-top: 1px dotted #475569;
            padding-top: 5px;
            font-size: 8pt;
            line-height: 1.4;
            color: #1e293b;
        }

        /* Footer Note */
        .doc-footer-note {
            margin-top: 10px;
            padding-top: 6px;
            border-top: 1px solid #cbd5e1;
            text-align: center;
            font-size: 7pt;
            color: #64748b;
        }

        /* Print Specific Optimization */
        @media print {
            @page {
                size: A4 landscape;
                margin: 8mm 10mm;
            }

            body {
                background: #ffffff !important;
                color: #000000 !important;
            }

            .print-toolbar {
                display: none !important;
            }

            .paper-container {
                padding: 0 !important;
            }

            .doc-sheet {
                width: 100% !important;
                min-height: auto !important;
                padding: 0 !important;
                box-shadow: none !important;
                border-radius: 0 !important;
            }

            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            thead {
                display: table-header-group;
            }

            tr {
                page-break-inside: avoid;
            }

            .signatures-container {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>

    <!-- Screen Preview Floating Toolbar (ซ่อนอัตโนมัติเมื่อสั่งพิมพ์) -->
    <div class="print-toolbar">
        <div class="toolbar-title">
            <i class="bi bi-file-earmark-pdf-fill text-danger fs-5"></i>
            <span>ตัวอย่างก่อนพิมพ์รายงาน (Print Preview) - มาตรฐานเอกสารบริษัท</span>
        </div>
        <div class="toolbar-actions">
            <button onclick="window.print();" class="btn-toolbar btn-print">
                <i class="bi bi-printer-fill"></i>
                <span>พิมพ์เอกสาร / บันทึกเป็น PDF</span>
            </button>
            <button onclick="window.close();" class="btn-toolbar btn-close-window">
                <i class="bi bi-x-circle"></i>
                <span>ปิดหน้าต่าง</span>
            </button>
        </div>
    </div>

    <!-- Paper Container -->
    <div class="paper-container">
        <div class="doc-sheet">

            <!-- Corporate Letterhead -->
            <div class="doc-letterhead">
                <div class="company-brand">
                    <div class="company-logo">WB</div>
                    <div class="company-info">
                        <h1>บริษัท ดับเบิ้ลยูบี แมเนจเม้นท์ จำกัด</h1>
                        <p><strong>WB-EMS CORPORATION CO., LTD.</strong> (สำนักงานใหญ่)</p>
                        <p>เลขประจำตัวผู้เสียภาษีอากร: 0105569000000 • ระบบบริหารทรัพยากรบุคคลและเวลาทำงาน</p>
                    </div>
                </div>

                <div class="doc-meta">
                    <div><strong>เลขที่เอกสาร:</strong> ATT-{{ date('Ymd', strtotime($date)) }}</div>
                    <div><strong>วันที่ของข้อมูล:</strong> {{ date('d/m/Y', strtotime($date)) }}</div>
                    <div><strong>พิมพ์เมื่อ:</strong> {{ date('d/m/Y H:i') }} น.</div>
                    <div><strong>ผู้ออกเอกสาร:</strong> {{ Auth::user()->name }} ({{ strtoupper(Auth::user()->role) }})</div>
                </div>
            </div>

            <!-- Report Header Title -->
            <div class="report-header-title">
                <h2>รายงานสรุปการลงเวลาปฏิบัติงานประจำวัน</h2>
                <div class="report-subtitle">DAILY ATTENDANCE & TIME RECORDING REPORT</div>
            </div>

            <!-- Filter Summary Bar -->
            <div class="filter-summary-bar">
                <div>
                    <strong>แผนกที่ตรวจสอบ:</strong> 
                    <span>{{ $departmentId ? ($departments->firstWhere('id', $departmentId)->name ?? 'ทั้งหมด') : 'ทุกแผนก (All Departments)' }}</span>
                    &nbsp;&bull;&nbsp;
                    <strong>เงื่อนไขสถานะ:</strong> 
                    <span>
                        @if($status === 'on_time') ตรงเวลา (On Time)
                        @elseif($status === 'late') มาสาย (Late)
                        @elseif($status === 'leave') ลางานที่ได้รับอนุมัติ (Approved Leave)
                        @elseif($status === 'absent') ยังไม่ลงเวลา / ขาดงาน
                        @else ทั้งหมด (All Status)
                        @endif
                    </span>
                </div>
                <div>
                    <strong>จำนวนพนักงานในรายงาน:</strong> <strong>{{ count($reportData) }}</strong> คน
                </div>
            </div>

            <!-- Executive Summary KPI Table -->
            <table class="summary-kpi-table">
                <thead>
                    <tr>
                        <th style="width: 16.66%;">พนักงานทั้งหมด</th>
                        <th style="width: 16.66%;">มาปฏิบัติงาน</th>
                        <th style="width: 16.66%;">เข้างานตรงเวลา</th>
                        <th style="width: 16.66%;">เข้างานสาย</th>
                        <th style="width: 16.66%;">ลางาน (อนุมัติ)</th>
                        <th style="width: 16.66%;">ยังไม่ลงเวลา / ขาด</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ number_format($totalEmployees) }} <span style="font-size: 8pt; font-weight: normal; color: #64748b;">คน</span></td>
                        <td style="color: #15803d;">{{ number_format($attendedCount) }} <span style="font-size: 8pt; font-weight: normal; color: #64748b;">คน</span></td>
                        <td style="color: #15803d;">{{ number_format($onTimeCount) }} <span style="font-size: 8pt; font-weight: normal; color: #64748b;">คน</span></td>
                        <td style="color: #b45309;">{{ number_format($lateCount) }} <span style="font-size: 8pt; font-weight: normal; color: #64748b;">คน</span></td>
                        <td style="color: #0369a1;">{{ number_format($leaveCount) }} <span style="font-size: 8pt; font-weight: normal; color: #64748b;">คน</span></td>
                        <td style="color: #b91c1c;">{{ number_format($absentCount) }} <span style="font-size: 8pt; font-weight: normal; color: #64748b;">คน</span></td>
                    </tr>
                </tbody>
            </table>

            <!-- Detailed Attendance Table -->
            <table class="report-table">
                <thead>
                    <tr>
                        <th style="width: 35px;">ลำดับ</th>
                        <th style="width: 85px;">รหัสพนักงาน</th>
                        <th style="min-width: 160px;" class="text-start">ชื่อ - นามสกุล พนักงาน</th>
                        <th style="width: 110px;">แผนก</th>
                        <th style="width: 110px;">ตำแหน่ง</th>
                        <th style="width: 80px;">เวลาเข้างาน</th>
                        <th style="width: 80px;">เวลาเลิกงาน</th>
                        <th style="width: 110px;">สถานะ</th>
                        <th style="min-width: 110px;">หมายเหตุ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportData as $index => $row)
                        @php
                            $emp = $row['user'];
                            $rowStatus = $row['status'];
                        @endphp
                        <tr>
                            <td class="text-center font-mono">{{ $index + 1 }}</td>
                            <td class="text-center font-mono fw-bold">{{ $emp->emp_code }}</td>
                            <td class="text-start">
                                <strong>{{ $emp->name }}</strong>
                            </td>
                            <td>{{ $emp->department->name ?? '-' }}</td>
                            <td>{{ $emp->position->name ?? '-' }}</td>
                            <td class="text-center font-mono">
                                @if($row['check_in'])
                                    <strong>{{ substr($row['check_in'], 0, 5) }} น.</strong>
                                @else
                                    <span style="color: #94a3b8;">-</span>
                                @endif
                            </td>
                            <td class="text-center font-mono">
                                @if($row['check_out'])
                                    <strong>{{ substr($row['check_out'], 0, 5) }} น.</strong>
                                @else
                                    <span style="color: #94a3b8;">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($rowStatus === 'on_time')
                                    <span class="status-badge-corp status-on-time">ตรงเวลา</span>
                                @elseif($rowStatus === 'late')
                                    <span class="status-badge-corp status-late">มาสาย</span>
                                @elseif($rowStatus === 'leave')
                                    <span class="status-badge-corp status-leave">ลางาน (อนุมัติ)</span>
                                @else
                                    <span class="status-badge-corp status-absent">ยังไม่ลงเวลา / ขาด</span>
                                @endif
                            </td>
                            <td style="font-size: 8pt; color: #475569;">
                                {{ $row['notes'] ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center" style="padding: 24px; color: #64748b;">
                                ไม่พบข้อมูลการลงเวลาของพนักงานตามเงื่อนไขที่ระบุ
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Formal 3-Parties Signature Section -->
            <div class="signatures-container">
                <div class="signatures-grid">
                    <div class="signature-box">
                        <span class="sig-title">ผู้จัดทำรายงาน (Prepared By)</span>
                        <div class="sig-line">
                            <div><strong>{{ Auth::user()->name }}</strong></div>
                            <div>เจ้าหน้าที่ฝ่ายทรัพยากรบุคคล (HR Officer)</div>
                            <div>วันที่: {{ date('d / m / Y') }}</div>
                        </div>
                    </div>

                    <div class="signature-box">
                        <span class="sig-title">ผู้ตรวจสอบรายงาน (Verified By)</span>
                        <div class="sig-line">
                            <div>....................................................</div>
                            <div>( ผู้จัดการฝ่ายทรัพยากรบุคคล / HR Manager )</div>
                            <div>วันที่: ...... / ...... / .........</div>
                        </div>
                    </div>

                    <div class="signature-box">
                        <span class="sig-title">ผู้มีอำนาจอนุมัติ (Approved By)</span>
                        <div class="sig-line">
                            <div>....................................................</div>
                            <div>( กรรมการผู้จัดการ / Managing Director )</div>
                            <div>วันที่: ...... / ...... / .........</div>
                        </div>
                    </div>
                </div>

                <div class="doc-footer-note">
                    เอกสารนี้สร้างขึ้นจากระบบบริหารจัดการข้อมูลพนักงาน (WB-EMS) • ข้อมูลในรายงานนี้ถือเป็นความลับขององค์กร ห้ามคัดลอกหรือเผยแพร่โดยไม่ได้รับอนุญาต
                </div>
            </div>

        </div>
    </div>

</body>
</html>
