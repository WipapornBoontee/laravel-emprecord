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

   

    <!-- กรอบเนื้อหาสลิปที่จะเซฟเป็น PDF -->
    <div id="payslip-content" style="padding: 20px; background: white; max-width: 800px; margin: 0 auto; border: 1px solid #ddd; box-shadow: 0 0 10px rgba(0,0,0,0.1);">

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
            @php
                $thaiMonths = [1=>'มกราคม', 2=>'กุมภาพันธ์', 3=>'มีนาคม', 4=>'เมษายน', 5=>'พฤษภาคม', 6=>'มิถุนายน', 7=>'กรกฎาคม', 8=>'สิงหาคม', 9=>'กันยายน', 10=>'ตุลาคม', 11=>'พฤศจิกายน', 12=>'ธันวาคม'];
            @endphp
            <td>{{ $thaiMonths[(int)$month] }} {{ $year }}</td>
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
            <td width="70%" class="font-bold text-right" style="background-color: #f0f0f0;">
                <span style="float: left; font-style: italic; font-weight: normal; color: #555;">({{ $netSalaryText }})</span>
                เงินรับสุทธิ (Net Pay)
            </td>
            <td width="30%" class="font-bold text-right" style="font-size: 16px;">{{ number_format($netSalary, 2) }}</td>
        </tr>
    </table>

    <div class="mt-4" style="font-size: 12px; color: #555;">
        วันที่พิมพ์: {{ $datePrinted }} <br>
        *เอกสารฉบับนี้จัดทำขึ้นโดยระบบคอมพิวเตอร์ ไม่จำเป็นต้องมีลายเซ็นรับรอง
    </div>
     
    </div> <!-- ปิด id="payslip-content" -->
    <div id="action-buttons" style="text-align: center; margin-bottom: 20px; margin-top: 20px;">
        <button onclick="downloadPDF()" style="padding: 10px 20px; font-size: 16px; cursor: pointer; background-color: #28a745; color: white; border: none; border-radius: 5px; font-weight: bold;">
            ดาวน์โหลดสลิป (Export PDF)
        </button>
        <a href="{{ route('salary_slip', [], false) }}" style="padding: 10px 20px; font-size: 16px; cursor: pointer; background-color: #6c757d; color: white; border: none; border-radius: 5px; text-decoration: none; margin-left: 10px;">
            กลับหน้าหลัก
        </a>
    </div>

    <!-- ใช้ html2pdf.js สำหรับดาวน์โหลด PDF โดยไม่ต้องพึ่ง Server -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        function downloadPDF() {
            // ซ่อนปุ่มก่อนโหลด
            document.getElementById('action-buttons').style.display = 'none';
            
            var element = document.getElementById('payslip-content');
            var opt = {
                margin:       10,
                filename:     'slip_{{ $user->emp_code }}_{{ $month }}_{{ $year }}.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2 },
                jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };

            // สร้างและโหลด PDF
            html2pdf().set(opt).from(element).save().then(function() {
                // แสดงปุ่มกลับมาหลังจากโหลดเสร็จ
                document.getElementById('action-buttons').style.display = 'block';
            });
        }
    </script>
</body>
</html>
