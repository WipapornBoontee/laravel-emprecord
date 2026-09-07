# โครงสร้างโฟลเดอร์ Views (Views Directory Structure)

เอกสารอธิบายการจัดหมวดหมู่และโครงสร้างโฟลเดอร์ใน `resources/views/` เพื่อรองรับระบบบริหารจัดการข้อมูลพนักงาน วันลา และบันทึกเวลาทำงาน ตาม **[database_schema.md](file:///d:/xampp/htdocs/laravel-emprecord/database_schema.md)**

---

## 📁 โครงสร้างโฟลเดอร์และไฟล์ปัจจุบัน (Current Structure)

```text
resources/views/
│
├── layouts/                       # 1. โครงสร้างและ Template หลักของเว็บ (รองรับ Light / Dark Mode)
│   ├── app.blade.php              #    - Layout หลักสำหรับหน้าที่เข้าสู่ระบบแล้ว (Design Tokens + Anti-flicker Theme Script)
│   └── partials/                  #    - ชิ้นส่วนย่อย (Reusable UI Components)
│       ├── header.blade.php       #      * Navbar ด้านบน (Logo, เมนูตาม Role, ปุ่ม Toggle Theme, Profile & Logout)
│       └── footer.blade.php       #      * Footer แสดงชื่อระบบ, เวอร์ชัน, และลิขสิทธิ์
│
├── auth/                          # 2. ระบบยืนยันตัวตน (Authentication)
│   └── login.blade.php            #    - หน้าเข้าสู่ระบบ (Username/EMP Code และ Password + Theme Switcher)
│
├── dashboard/                     # 3. หน้าแดชบอร์ดหลัก (Dashboard Overview)
│   └── home.blade.php             #    - แดชบอร์ดภาพรวม (Hero Banner, Live Clock, KPI Stat Cards, Quick Actions)
│
├── employees/                     # 4. ระบบจัดการข้อมูลพนักงาน (ตาราง users) [Admin / HR]
│   ├── index.blade.php            #    - ตารางรายชื่อพนักงานทั้งหมด + ตัวกรองค้นหา
│   ├── create.blade.php           #    - ฟอร์มเพิ่มพนักงานใหม่ และกำหนดสิทธิ์ (Role)
│   ├── edit.blade.php             #    - ฟอร์มแก้ไขข้อมูลส่วนตัว / สถานะการทำงาน / แผนก / ตำแหน่ง
│   └── show.blade.php             #    - หน้ารายละเอียดโปรไฟล์พนักงานรายบุคคล
│
├── departments/                   # 5. ระบบจัดการแผนกและตำแหน่ง [Admin / HR]
│   ├── index.blade.php            #    - รายการและฟอร์มจัดการแผนก (ตาราง departments)
│   └── positions.blade.php        #    - รายการและฟอร์มจัดการตำแหน่งงาน (ตาราง positions)
│
├── leaves/                        # 6. ระบบจัดการการลา (Leave Management)
│   ├── index.blade.php            #    - ประวัติและรายการคำขอลาของตนเอง (ตาราง leave_requests)
│   ├── create.blade.php           #    - แบบฟอร์มยื่นขออนุมัติการลาใหม่
│   ├── balances.blade.php         #    - ตรวจสอบสิทธิ์วันลาคงเหลือรายปี (ตาราง leave_balances)
│   ├── approvals.blade.php        #    - หน้าพิจารณาอนุมัติ/ปฏิเสธคำขอลา [Admin / HR]
│   └── types/                     #    - จัดการประเภทการลาและโควตาพื้นฐาน (ตาราง leave_types) [Admin / HR]
│       └── index.blade.php        #      * รายการและตั้งค่าวันลาเริ่มต้นต่อปี
│
└── attendances/                   # 7. ระบบบันทึกเวลาเข้า-ออกงาน (Time Attendance)
    ├── checkin.blade.php          #    - หน้ากดลงเวลาเข้างาน - เลิกงาน (Check-in / Check-out)
    ├── my-history.blade.php       #    - ประวัติการลงเวลาทำงานของตนเอง (ตาราง attendances)
    └── report.blade.php           #    - สรุปรายงานการเข้างาน, มาสาย, ขาด, ลา ทั้งหมด [Admin / HR]
```

---

## 🎨 มาตรฐานระบบธีม (Theme Guidelines: Light & Dark Mode)

- ทุกหน้าจะต้องสืบทอดจาก **`@extends('layouts.app')`**
- ใช้ตัวแปรสี **CSS Variables** ในการแต่งหน้าตาเสมอ เพื่อรองรับการสลับโหมดอัตโนมัติ:
  - `var(--bg-color)` : สีพื้นหลังหลักของหน้าเว็บ
  - `var(--surface-bg)` : สีพื้นหลังของการ์ด (Card / Modal)
  - `var(--surface-border)` : สีเส้นขอบการ์ด/กล่อง
  - `var(--text-main)` : สีข้อความหลัก
  - `var(--text-muted)` : สีข้อความรอง/คำอธิบาย
  - `var(--primary-gradient)` : สี Gradient สำหรับปุ่มหลักและจุดเด่น

---

## 👥 ตารางการเข้าถึงตามสิทธิ์ (Access Control Matrix)

| โฟลเดอร์ / เมนู | ความสัมพันธ์ตารางใน Database | สิทธิ์ที่เข้าถึงได้ | คำอธิบาย |
| :--- | :--- | :--- | :--- |
| `auth/login` | `users` | Guest (ทุกคน) | เข้าสู่ระบบ |
| `dashboard/home` | รวมทุกตาราง | `admin`, `hr`, `employee` | หน้าแรกแสดงข้อมูลสรุปส่วนตัว และเวลา Realtime |
| `employees/` | `users`, `departments`, `positions` | `admin`, `hr` | จัดการข้อมูลพนักงานในองค์กร |
| `departments/` | `departments`, `positions` | `admin`, `hr` | จัดการโครงสร้างองค์กร |
| `leaves/` (ยื่นลา/ดูยอด) | `leave_requests`, `leave_balances` | `admin`, `hr`, `employee` | การจัดการการลาของตนเอง |
| `leaves/approvals` | `leave_requests` | `admin`, `hr` | ผู้อนุมัติการลา |
| `leaves/types/` | `leave_types` | `admin`, `hr` | ตั้งค่าประเภทและโควตาวันลา |
| `attendances/checkin` | `attendances` | `admin`, `hr`, `employee` | บันทึกเวลาเข้า-ออกงาน |
| `attendances/report` | `attendances` | `admin`, `hr` | รายงานสรุปเวลาทำงานของพนักงานทั้งหมด |
