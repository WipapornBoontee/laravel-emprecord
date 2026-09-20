# DayHub HRMS - ขอบเขตระบบ & แผนพัฒนาเพื่อส่งต่องานฝ่ายบัญชี (Hand-off to Accounting)
> เอกสารวิเคราะห์ความพร้อมของระบบพื้นฐาน แนวทางการพัฒนา และ Business Logic สำหรับระบบบริหารเวลาและวันลา (Time & Attendance Management) โดยเน้น **"การออกรายงานสรุปข้อมูลที่แม่นยำ 100% เพื่อส่งต่อให้ฝ่ายบัญชีนำไปประมวลผลเงินเดือนต่อได้ทันที โดยไม่ยุ่งเกี่ยวกับระบบคำนวณเงินเดือนภายในแอปพลิเคชัน"**

---

## 📌 1. บทสรุปความพร้อมของระบบพื้นฐาน (Current Baseline Status)

### สิ่งที่ระบบมีแล้วและทำได้ดีมาก (Solid Foundation)
1. **โครงสร้างบุคลากร (HR Core)**: จัดการพนักงาน, แผนก, ตำแหน่งงาน, สิทธิ์การใช้งาน (Admin, HR, Employee)
2. **ระบบลงเวลา (Time Attendance)**: บันทึกเวลาเข้า-ออกงานแบบ Real-time พร้อม Live Clock ตรวจจับตรงเวลา/มาสาย
3. **ระบบการลา (Leave Management)**: ยื่นใบลา, แนบเอกสารหลักฐาน, ศูนย์พิจารณาอนุมัติ, และตัดสิทธิ์วันลาคงเหลืออัตโนมัติ
4. **ความเชื่อมโยงของระบบ**: เมื่อคำขอลาได้รับการอนุมัติ ระบบจะลงเวลาสถานะ "ลางาน" ในหน้า Attendance ให้โดยอัตโนมัติ ไม่นับเป็นขาดงาน
5. **Design System & UX/UI**: คุมธีม DayHub สม่ำเสมอทุกหน้า รองรับ Dark/Light Mode 100%

### ขอบเขตที่ชัดเจน (System Scope Decision)
> **ไม่ทำระบบคำนวณเงินเดือนในแอปพลิเคชัน (No In-App Payroll Calculation)**
> - ไม่ต้องคำนวณภาษี, หัก ณ ที่จ่าย, หรือประกันสังคม
> - ไม่ต้องคำนวณสูตรเงินเดือนสุทธิ หรือออกสลิปเงินเดือน (Payslip)
> - **เป้าหมายหลัก**: เป็นระบบ Time & Attendance ที่ทำหน้าที่สรุปตัวเลขสถิติเวลา (วันทำงาน, วันมาสาย, วันขาด, วันลาแต่ละประเภท, และชั่วโมง OT รวม) ให้ออกมาเป็น **Excel/CSV สรุปรายเดือน** เพื่อให้ฝ่ายบัญชีนำไปคูณเงินเดือนต่อในโปรแกรมบัญชีได้ทันที

---

## 🎯 2. สิ่งที่ยังขาดตกบกพร่อง และต้องเพิ่มเติมสำหรับส่งต่องานบัญชี

เพื่อให้ฝ่ายบัญชีได้รับข้อมูลที่ถูกต้อง 100% โดยไม่ต้องส่งข้อมูลกลับมาให้ HR แก้ไข มี **4 สิ่งสำคัญที่ต้องทำเพิ่ม**:

```
[พนักงานลงเวลา & ลางาน]
       │
       ▼
[1. คำนวณวันลาไม่นับเสาร์-อาทิตย์/วันหยุด] ──► [2. ฟอร์มขอปรับเวลาตอนลืมสแกน]
       │                                                   │
       └─────────────────────────┬─────────────────────────┘
                                 ▼
                 [3. สรุปยอดชั่วโมง OT รวมรายเดือน]
                                 │
                                 ▼
         [4. Export Excel/CSV สรุปประจำเดือนส่งให้ฝ่ายบัญชี]
                                 │
                                 ▼
                     (ฝ่ายบัญชีคิดเงินเดือนต่อได้ทันที)
```

---

## 🛠️ 3. รายละเอียด Business Logic & การพัฒนาทั้ง 4 ส่วน

### ส่วนที่ 1: การคำนวณวันลาไม่รวมวันหยุด (Holidays & Weekends Exclusion)

#### 1.1 ปัญหาปัจจุบัน
ระบบนับวันตามปฏิทินทั้งหมด (`diffInDays + 1`) หากพนักงานลาศุกร์-จันทร์ ระบบจะนับเป็น 4 วัน ทำให้โควตาวันลาถูกหักเกินจริง 2 วัน ส่งผลให้ฝ่ายบัญชีได้รับยอดวันลาที่ผิดพลาด

#### 1.2 โครงสร้างตาราง (Database Schema)
```sql
CREATE TABLE company_holidays (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL COMMENT 'ชื่อวันหยุด เช่น วันสงกรานต์, วันแรงงาน',
    holiday_date DATE NOT NULL UNIQUE COMMENT 'วันที่หยุด',
    is_recurring BOOLEAN DEFAULT FALSE COMMENT 'เกิดซ้ำทุกปีหรือไม่',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

#### 1.3 Business Logic
เมื่อพนักงานเลือก `start_date` และ `end_date`:
1. ดึงรายการวันหยุดจาก `company_holidays` ในช่วงเวลาดังกล่าว
2. วนลูปตรวจสอบทีละวันตั้งแต่วันเริ่มต้นจนถึงวันสิ้นสุด:
   - ถ้าเป็นวันเสาร์ (`$date->isSaturday()`) หรือวันอาทิตย์ (`$date->isSunday()`) ➔ **ข้าม ไม่นับวันลา**
   - ถ้าตรงกับวันหยุดใน `company_holidays` ➔ **ข้าม ไม่นับวันลา**
   - ถ้าเป็นวันทำงานปกติ ➔ **นับ `$daysCount++`**
3. ตรวจสอบว่า `$daysCount` ต้องมากกว่า 0 (กรณีเลือกเฉพาะวันเสาร์-อาทิตย์ จะไม่อนุญาตให้ยื่น)
4. นำ `$daysCount` ไปตรวจสอบกับโควตาคงเหลือ (`remaining_days`) ก่อนบันทึก

---

### ส่วนที่ 2: ระบบขอแก้ไขเวลาลงเวลาย้อนหลัง (Attendance Adjustment Request)

#### 2.1 วัตถุประสงค์
รองรับกรณีมนุษย์เงินเดือน **"ลืมสแกนนิ้ว/ลืมกดลงเวลา"** หรือ **"ไปพบลูกค้านอกสถานที่"** ป้องกันไม่ให้ระบบบันทึกเป็น "ขาดงาน (Absent)" โดยผิดพลาดก่อนส่งข้อมูลให้บัญชีสิ้นเดือน

#### 2.2 โครงสร้างตาราง (Database Schema)
```sql
CREATE TABLE attendance_adjustments (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT NOT NULL,
    attendance_id BIGINT NULL COMMENT 'ผูกกับ attendance เดิม (ถ้ามี)',
    target_date DATE NOT NULL COMMENT 'วันที่ที่ต้องการขอปรับเวลา',
    requested_check_in TIME NULL COMMENT 'เวลาเข้างานจริง',
    requested_check_out TIME NULL COMMENT 'เวลาเลิกงานจริง',
    reason TEXT NOT NULL COMMENT 'เหตุผล เช่น ลืมกดลงเวลา, ไปพบลูกค้า',
    attachment_url VARCHAR(255) NULL COMMENT 'หลักฐานแนบ (ถ้ามี)',
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    approver_id BIGINT NULL,
    approved_at TIMESTAMP NULL,
    reject_reason TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

#### 2.3 Business Logic
1. **พนักงานยื่นคำร้อง**: เลือกวันที่ย้อนหลัง (ไม่เกิน 30 วัน), ระบุเวลาเข้า-ออกจริง, กรอกเหตุผล
2. **หัวหน้างาน/HR พิจารณา**:
   - หาก **"อนุมัติ"**: ระบบจะไป `updateOrCreate` ข้อมูลในตาราง `attendances` ของวันนั้นทันที พร้อมปรับสถานะเป็น `on_time` หรือ `late` ตามเวลาจริง และใส่หมายเหตุว่า *"ปรับปรุงย้อนหลังโดยคำขอ #ID"*
   - หาก **"ปฏิเสธ"**: บันทึกเหตุผลการปฏิเสธ และส่งสถานะกลับไปให้พนักงาน

---

### ส่วนที่ 3: การตรวจสอบและสรุปยอดชั่วโมง OT รวมรายเดือน (Monthly Overtime Summary)

#### 3.1 วัตถุประสงค์
ฝ่ายบัญชีต้องการตัวเลข **"จำนวนชั่วโมง OT รวมของพนักงานแต่ละคนในเดือนนั้น"** เพื่อนำไปคูณอัตราจ้างต่อชั่วโมงในระบบ Payroll

#### 3.2 Logic การตรวจสอบความสอดคล้องกับเวลาเลิกงานจริง
เพื่อป้องกันการขอ OT แต่กลับบ้านก่อน:
- ในหน้าอนุมัติ OT ของ HR ให้ดึงเวลา `check_out` จริงมาเทียบกับเวลาสิ้นสุด OT
  - หาก `check_out` จริง >= เวลาสิ้นสุด OT ➔ แสดงแท็กเขียว `✓ ปฏิบัติงานครบตามเวลา OT`
  - หาก `check_out` จริง < เวลาสิ้นสุด OT ➔ แสดงแท็กเตือนสีแดง `⚠️ สแกนออกก่อนเวลา OT (เวลาจริง: XX:XX)`

#### 3.3 Logic การสรุปยอดชั่วโมง OT ประจำเดือน
```php
$totalOtHours = OvertimeRequest::where('user_id', $user->id)
    ->whereYear('date', $year)
    ->whereMonth('date', $month)
    ->where('status', 'approved')
    ->sum('duration_hours');
```

---

### ส่วนที่ 4: การ Export ไฟล์ Excel / CSV สรุปประจำเดือนส่งฝ่ายบัญชี (Monthly Payroll Export)

#### 4.1 รูปแบบข้อมูลที่ฝ่ายบัญชีต้องการมากที่สุด (Columns Specification)
รายงานจะถูกสรุปเป็นรายพนักงาน 1 คน ต่อ 1 แถว (สำหรับรอบเดือนที่เลือก):

| ลำดับ | รหัสพนักงาน | ชื่อ-นามสกุล | แผนก | ตำแหน่ง | วันทำงานตามปฏิทิน | มาทำงานจริง (วัน) | มาสาย (ครั้ง) | ขาดงาน (วัน) | ลาป่วย (วัน) | ลากิจ (วัน) | ลาพักร้อน (วัน) | ลาอื่นๆ (วัน) | ชม. OT รวม (ชม.) |
| :---: | :---: | :--- | :--- | :--- | :---: | :---: | :---: | :---: | :---: | :---: | :---: | :---: | :---: |
| 1 | EMP001 | นายสมชาย ใจดี | IT | Developer | 22 | 21 | 1 | 0 | 1 | 0 | 0 | 0 | 8.5 |
| 2 | EMP002 | น.ส.วิภาวรรณ บุญมี | HR | HR Officer | 22 | 22 | 0 | 0 | 0 | 0 | 0 | 0 | 0.0 |

#### 4.2 Logic การ Implement ด้วย StreamedResponse (High Performance & ภาษาไทยไม่เพี้ยน)
```php
public function exportMonthlySummaryCsv(Request $request)
{
    $month = $request->input('month', date('m'));
    $year = $request->input('year', date('Y'));
    $fileName = "payroll_attendance_summary_{$year}_{$month}.csv";

    $headers = [
        "Content-type"        => "text/csv; charset=UTF-8",
        "Content-Disposition" => "attachment; filename=$fileName",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $callback = function() use ($month, $year) {
        $file = fopen('php://output', 'w');
        // ใส่ UTF-8 BOM เพื่อให้ Excel เปิดภาษาไทยได้ถูกต้อง 100% ไม่เป็นภาษาต่างดาว
        fputs($file, "\xEF\xBB\xBF");

        // Header แถวแรกสำหรับฝ่ายบัญชี
        fputcsv($file, [
            'รหัสพนักงาน',
            'ชื่อ-นามสกุล',
            'แผนก',
            'ตำแหน่ง',
            'วันทำงานตามปฏิทิน',
            'มาทำงานจริง (วัน)',
            'มาสาย (ครั้ง)',
            'ขาดงาน (วัน)',
            'ลาป่วย (วัน)',
            'ลากิจ (วัน)',
            'ลาพักร้อน (วัน)',
            'ลาอื่นๆ (วัน)',
            'ชั่วโมง OT รวม (ชม.)'
        ]);

        // ดึงพนักงาน Active ทั้งหมด และรวมยอดสถิติประจำเดือน
        $employees = User::with(['department', 'position'])->where('status', 'active')->orderBy('emp_code')->get();

        foreach ($employees as $emp) {
            $stats = $this->calculateMonthlyEmployeeStats($emp->id, $year, $month);
            fputcsv($file, [
                $emp->emp_code,
                $emp->name,
                $emp->department->name ?? '-',
                $emp->position->name ?? '-',
                $stats['calendar_work_days'],
                $stats['actual_work_days'],
                $stats['late_count'],
                $stats['absent_days'],
                $stats['sick_leave_days'],
                $stats['personal_leave_days'],
                $stats['annual_leave_days'],
                $stats['other_leave_days'],
                $stats['approved_ot_hours'],
            ]);
        }
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}
```

---

## 📋 5. สิ่งที่ไม่จำเป็นต้องทำในระบบนี้ (Out of Scope)

เพื่อไม่ให้ระบบซับซ้อนเกินความจำเป็นและอยู่นอกเหนือบทบาทของ HR Time Management:
- ❌ **ไม่ทำตารางคำนวณฐานเงินเดือนและเบี้ยขยัน**: ให้เป็นหน้าที่ของระบบบัญชี/ERP
- ❌ **ไม่ทำสูตรหักภาษี ณ ที่จ่าย และประกันสังคม**: ป้องกันข้อผิดพลาดทางกฎหมายภาษี
- ❌ **ไม่ทำระบบพิมพ์สลิปเงินเดือน (Payslip)**: ให้ฝ่ายบัญชีออกผ่านซอฟต์แวร์การเงินของบริษัท

---

## 🚀 6. แผนงานและลำดับการพัฒนาที่แนะนำ (Next Steps)

1. **Step 1**: เพิ่มตารางวันหยุด `company_holidays` และปรับการคำนวณวันลาให้หักเฉพาะวันทำงานจริง (เสาร์-อาทิตย์ไม่นับ)
2. **Step 2**: พัฒนาฟังก์ชัน **Export CSV/Excel สรุปรายเดือน** ในหน้า `attendance/report` เพื่อส่งต่อให้ฝ่ายบัญชี
3. **Step 3**: เพิ่มฟอร์ม **ขอแก้ไขเวลาลงเวลาย้อนหลัง** สำหรับพนักงานที่ลืมสแกนนิ้ว เพื่อให้ข้อมูลก่อนส่งบัญชีครบถ้วน 100%
4. **Step 4**: เพิ่มการสรุป **ชั่วโมง OT รวมรายเดือน** ในหน้ารายงาน OT
