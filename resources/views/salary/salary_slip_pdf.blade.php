<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>สลิปเงินเดือน - {{ $user->name }}</title>
    <style>
        body {
            font-family: 'dejavu sans', sans-serif;
            font-size: 14px;
            color: #333;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .w-100 { width: 100%; }
        .mt-4 { margin-top: 20px; }
        .mb-2 { margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        .border-table th, .border-table td { border: 1px solid #000; padding: 8px; }
        .border-table th { background-color: #f0f0f0; }
        .header-title { font-size: 20px; font-weight: bold; margin-bottom: 5px; }
        .company-name { font-size: 16px; margin-bottom: 20px; }
    </style>
</head>
<body>

    <div class="text-center">
        <div class="header-title">ใบแจ้งเงินเดือน (Payslip)</div>
        <div class="company-name">บริษัท ดับบลิวบี-อีเอ็มเอส จำกัด (WB-EMS Co., Ltd.)</div>
    </div>

    <table class="w-100 mt-4 mb-2">
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

    <table class="w-100 border-table mt-4">
        <thead>
            <tr>
                <th width="50%">รายได้ (Earnings)</th>
                <th width="50%">รายการหัก (Deductions)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td valign="top">
                    <table class="w-100">
                        <tr>
                            <td>เงินเดือนพื้นฐาน (Base Salary)</td>
                            <td class="text-right">{{ number_format($baseSalary, 2) }}</td>
                        </tr>
                        <tr>
                            <td>ค่าล่วงเวลา (Overtime)</td>
                            <td class="text-right">{{ number_format($totalOtPay, 2) }}</td>
                        </tr>
                    </table>
                </td>
                <td valign="top">
                    <table class="w-100">
                        <tr>
                            <td>หักภาษี ณ ที่จ่าย (Tax)</td>
                            <td class="text-right">0.00</td>
                        </tr>
                        <tr>
                            <td>ประกันสังคม (Social Security)</td>
                            <td class="text-right">0.00</td>
                        </tr>
                        <tr>
                            <td class="text-danger">หักกรณีไม่อนุมัติลา ({{ number_format($rejectedLeaveDays, 1) }} วัน)</td>
                            <td class="text-right text-danger">{{ number_format($leaveDeduction, 2) }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="font-bold">
                    <table class="w-100">
                        <tr>
                            <td>รวมรายได้ (Total Earnings)</td>
                            <td class="text-right">{{ number_format($netSalary, 2) }}</td>
                        </tr>
                    </table>
                </td>
                <td class="font-bold">
                    <table class="w-100">
                        <tr>
                            <td>รวมรายการหัก (Total Deductions)</td>
                            <td class="text-right">{{ number_format($leaveDeduction, 2) }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>

    <table class="w-100 border-table mt-4">
        <tr>
            <td width="70%" class="font-bold text-right" style="background-color: #f0f0f0;">เงินรับสุทธิ (Net Pay)</td>
            <td width="30%" class="font-bold text-right" style="font-size: 16px;">{{ number_format($netSalary, 2) }}</td>
        </tr>
    </table>

    <div class="mt-4" style="font-size: 11px; color: #666;">
        พิมพ์เมื่อ: {{ $datePrinted }} <br>
        *เอกสารฉบับนี้สร้างโดยระบบอัตโนมัติ ไม่ต้องมีลายเซ็น
    </div>

</body>
</html>
