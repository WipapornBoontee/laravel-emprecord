<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สลิปเงินเดือน - {{ $user->name }}</title>
    <!-- ใช้ Bootstrap ล่าสุดเพื่อความสวยงาม -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Sarabun', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        .slip-container {
            max-width: 800px;
            margin: 40px auto;
            background: #fff;
            padding: 40px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .company-logo {
            font-size: 2rem;
            color: #0d6efd;
        }
        .slip-title {
            letter-spacing: 2px;
        }
        .table-bordered th, .table-bordered td {
            border-color: #dee2e6;
        }
        
        /* ตั้งค่าสำหรับการปริ้น (Save as PDF) */
        @media print {
            body {
                background-color: #fff;
            }
            .slip-container {
                box-shadow: none;
                margin: 0;
                padding: 0;
                width: 100%;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <!-- ปุ่มคำสั่งปริ้นซ่อนตอนปริ้นจริง -->
    <div class="text-center mt-4 no-print">
        <button onclick="window.print()" class="btn btn-primary btn-lg px-5 rounded-pill shadow">
            <i class="bi bi-printer"></i> บันทึกเป็น PDF (Print)
        </button>
        <a href="{{ route('salary_slip', [], false) }}" class="btn btn-outline-secondary btn-lg rounded-pill ms-2">
            กลับหน้าหลัก
        </a>
        <p class="text-muted mt-2 small">คำแนะนำ: กดปุ่มด้านบน แล้วเลือก <strong>Destination เป็น "Save as PDF"</strong></p>
    </div>

    <div class="slip-container border rounded">
        <div class="row mb-4 border-bottom pb-3">
            <div class="col-sm-6 d-flex align-items-center">
                <div>
                    <h3 class="mb-0 fw-bold text-primary slip-title">ใบแจ้งเงินเดือน</h3>
                    <p class="text-muted mb-0">PAYSLIP</p>
                </div>
            </div>
            <div class="col-sm-6 text-sm-end text-center mt-3 mt-sm-0">
                <h5 class="fw-bold mb-1">บริษัท ดับบลิวบี-อีเอ็มเอส จำกัด</h5>
                <p class="text-muted mb-0 small">WB-EMS Co., Ltd.<br>123 ถนนสุขุมวิท กรุงเทพมหานคร 10110</p>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-6">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td width="35%" class="text-muted">ชื่อพนักงาน :</td>
                        <td class="fw-bold">{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">ตำแหน่ง :</td>
                        <td>{{ $user->position->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">แผนก :</td>
                        <td>{{ $user->department->name ?? '-' }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-6">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td width="40%" class="text-muted">รหัสพนักงาน :</td>
                        <td class="fw-bold">{{ $user->emp_code ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">ประจำเดือน :</td>
                        <td>{{ Carbon\Carbon::create()->month((int)$month)->translatedFormat('F') }} {{ $year }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">วันที่จ่าย :</td>
                        <td>{{ Carbon\Carbon::create()->month((int)$month)->endOfMonth()->format('d/m/Y') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-6">
                <div class="card h-100 border-0 rounded-0 border-end">
                    <div class="card-header bg-light fw-bold text-center border-bottom-0">รายได้ (Earnings)</div>
                    <div class="card-body p-0">
                        <table class="table mb-0">
                            <tbody>
                                <tr>
                                    <td>เงินเดือนพื้นฐาน</td>
                                    <td class="text-end">{{ number_format($baseSalary, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>ค่าล่วงเวลา (OT)</td>
                                    <td class="text-end text-success">{{ number_format($totalOtPay, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card h-100 border-0 rounded-0">
                    <div class="card-header bg-light fw-bold text-center border-bottom-0">รายการหัก (Deductions)</div>
                    <div class="card-body p-0">
                        <table class="table mb-0">
                            <tbody>
                                <tr>
                                    <td>หักภาษี ณ ที่จ่าย</td>
                                    <td class="text-end">0.00</td>
                                </tr>
                                <tr>
                                    <td>ประกันสังคม</td>
                                    <td class="text-end">0.00</td>
                                </tr>
                                <tr>
                                    <td class="text-danger">หักกรณีไม่อนุมัติลา ({{ number_format($rejectedLeaveDays, 1) }} วัน)</td>
                                    <td class="text-end text-danger">{{ number_format($leaveDeduction, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row border-top border-bottom py-2 bg-light mb-4">
            <div class="col-6 border-end">
                <div class="d-flex justify-content-between px-2 fw-bold">
                    <span>รวมรายได้ทั้งหมด</span>
                    <span>{{ number_format($baseSalary + $totalOtPay, 2) }}</span>
                </div>
            </div>
            <div class="col-6">
                <div class="d-flex justify-content-between px-2 fw-bold">
                    <span>รวมรายการหักทั้งหมด</span>
                    <span class="text-danger">{{ number_format($leaveDeduction, 2) }}</span>
                </div>
            </div>
        </div>

        <div class="row mb-4 bg-primary text-white p-3 rounded mx-0">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h4 class="mb-0 fw-bold">เงินรับสุทธิ (Net Pay)</h4>
                <h3 class="mb-0 fw-bold">฿ {{ number_format($netSalary, 2) }}</h3>
            </div>
        </div>

        <div class="text-center mt-5">
            <p class="text-muted small mb-1">*เอกสารฉบับนี้จัดทำขึ้นด้วยระบบคอมพิวเตอร์ ไม่จำเป็นต้องมีลายเซ็นรับรอง</p>
            <p class="text-muted small">พิมพ์เมื่อ: {{ $datePrinted }}</p>
        </div>
    </div>

    <script>
        // เปิดหน้าต่าง Print อัตโนมัติเมื่อโหลดหน้าเว็บเสร็จ
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>
