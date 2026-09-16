<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>สลิปเงินเดือน - {{ $user->name }}</title>
    <style>
        body {
            font-family: 'garuda', sans-serif;
            font-size: 14px;
            color: #000;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .w-100 { width: 100%; }
        .mt-4 { margin-top: 20px; }
        .mb-2 { margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        
        /* สไตล์ตารางมาตรฐานบริษัท */
        .payslip-table { width: 100%; border: 1px solid #000; border-collapse: collapse; margin-top: 15px; }
        .payslip-table th, .payslip-table td { border: 1px solid #000; padding: 6px 10px; }
        .payslip-table th { background-color: #f0f0f0; text-align: center; }
        
        .header-title { font-size: 22px; font-weight: bold; margin-bottom: 5px; letter-spacing: 1px; }
        .company-name { font-size: 18px; font-weight: bold; margin-bottom: 20px; }
        
        .info-table td { padding: 4px 0; }
        .text-danger { color: red; }
    </style>
</head>
<body>

    <div class="text-center">
        <div class="company-name">บริษัท ดับบลิวบี-อีเอ็มเอส จำกัด (WB-EMS Co., Ltd.)</div>
        <div class="header-title">ใบแจ้งเงินเดือน / PAYSLIP</div>
    </div>

    <table class="w-100 info-table mt-4 mb-2">
        <tr>
            <td width="15%" class="font-bold">ชื่อพนักงาน :</td>
            <td width="35%">{{ $user->name }}</td>
            <td width="15%" class="font-bold">รหัสพนักงาน :</td>
            <td width="35%">{{ $user->emp_code ?? '-' }}</td>
        </tr>
        <tr>
            <td class="font-bold">ตำแหน่ง :</td>
            <td>{{ $user->position->name ?? '-' }}</td>
            <td class="font-bold">ประจำเดือน :</td>
            <td>{{ Carbon\Carbon::create()->month((int)$month)->translatedFormat('F') }} {{ $year }}</td>
        </tr>
    </table>

    <table class="payslip-table">
        <thead>
            <tr>
                <th width="50%">รายได้ (Earnings)</th>
                <th width="50%">รายการหัก (Deductions)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td valign="top" style="padding: 0; border-bottom: none;">
                    <table class="w-100" style="border: none;">
                        <tr>
                            <td style="border: none;">เงินเดือนพื้นฐาน (Base Salary)</td>
                            <td class="text-right" style="border: none;">{{ number_format($baseSalary, 2) }}</td>
                        </tr>
                        <tr>
                            <td style="border: none;">ค่าล่วงเวลา (Overtime)</td>
                            <td class="text-right" style="border: none;">{{ number_format($totalOtPay, 2) }}</td>
                        </tr>
                    </table>
                </td>
                <td valign="top" style="padding: 0; border-bottom: none;">
                    <table class="w-100" style="border: none;">
                        <tr>
                            <td style="border: none;">หักภาษี ณ ที่จ่าย (Tax)</td>
                            <td class="text-right" style="border: none;">0.00</td>
                        </tr>
                        <tr>
                            <td style="border: none;">ประกันสังคม (Social Security)</td>
                            <td class="text-right" style="border: none;">0.00</td>
                        </tr>
                        <tr>
                            <td style="border: none;" class="text-danger">หักกรณีไม่อนุมัติลา ({{ number_format($rejectedLeaveDays, 1) }} วัน)</td>
                            <td class="text-right text-danger" style="border: none;">{{ number_format($leaveDeduction, 2) }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
            
            <!-- เว้นช่องว่างให้ตารางดูเต็ม -->
            <tr>
                <td style="border-top: none; height: 50px;"></td>
                <td style="border-top: none; height: 50px;"></td>
            </tr>

            <!-- สรุปยอดรวม -->
            <tr class="font-bold">
                <td style="padding: 0;">
                    <table class="w-100" style="border: none;">
                        <tr>
                            <td style="border: none;">รวมรายได้ (Total Earnings)</td>
                            <td class="text-right" style="border: none;">{{ number_format($baseSalary + $totalOtPay, 2) }}</td>
                        </tr>
                    </table>
                </td>
                <td style="padding: 0;">
                    <table class="w-100" style="border: none;">
                        <tr>
                            <td style="border: none;">รวมรายการหัก (Total Deductions)</td>
                            <td class="text-right" style="border: none;">{{ number_format($leaveDeduction, 2) }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>

    <table class="payslip-table">
        <tr>
            <td width="70%" class="font-bold text-right" style="background-color: #f0f0f0;">เงินรับสุทธิ (Net Pay)</td>
            <td width="30%" class="font-bold text-right" style="font-size: 16px;">{{ number_format($netSalary, 2) }}</td>
        </tr>
    </table>

    <div class="mt-4" style="font-size: 12px; color: #555;">
        วันที่พิมพ์: {{ $datePrinted }} <br>
        *เอกสารฉบับนี้จัดทำขึ้นโดยระบบคอมพิวเตอร์ ไม่จำเป็นต้องมีลายเซ็นรับรอง
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
