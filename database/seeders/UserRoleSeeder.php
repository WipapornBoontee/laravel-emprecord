<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Department;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Overtime;
use App\Models\Position;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ปิด Foreign Key Checks ชั่วคราวเพื่อเคลียร์ข้อมูลเก่าทั้งหมดอย่างสมบูรณ์
        Schema::disableForeignKeyConstraints();
        DB::table('attendances')->truncate();
        DB::table('overtimes')->truncate();
        DB::table('leave_requests')->truncate();
        DB::table('leave_balances')->truncate();
        DB::table('attendance_adjustments')->truncate();
        DB::table('users')->truncate();
        DB::table('positions')->truncate();
        DB::table('departments')->truncate();
        DB::table('leave_types')->truncate();
        Schema::enableForeignKeyConstraints();

        // 1. สร้างแผนกงาน (Departments)
        $deptIt = Department::create(['name' => 'IT & Software Development', 'is_active' => true]);
        $deptHr = Department::create(['name' => 'Human Resources & Admin', 'is_active' => true]);
        $deptSales = Department::create(['name' => 'Sales & Business Development', 'is_active' => true]);
        $deptMkt = Department::create(['name' => 'Digital Marketing', 'is_active' => true]);
        $deptAcc = Department::create(['name' => 'Accounting & Finance', 'is_active' => true]);
        $deptOps = Department::create(['name' => 'Customer Operations & Support', 'is_active' => true]);

        $departments = [$deptIt, $deptHr, $deptSales, $deptMkt, $deptAcc, $deptOps];

        // 2. สร้างตำแหน่งงาน (Positions)
        $posAdmin = Position::create(['name' => 'Chief Technology Officer (Admin)', 'department_id' => $deptIt->id, 'is_active' => true]);
        $posHrMgr = Position::create(['name' => 'HR Manager', 'department_id' => $deptHr->id, 'is_active' => true]);
        $posHrSenior = Position::create(['name' => 'Senior HR Specialist', 'department_id' => $deptHr->id, 'is_active' => true]);
        $posHrOfficer = Position::create(['name' => 'HR Officer & Payroll', 'department_id' => $deptHr->id, 'is_active' => true]);

        // ตำแหน่งสำหรับพนักงานทั่วไป
        $positionsByDept = [
            $deptIt->id => [
                Position::create(['name' => 'Senior Backend Developer', 'department_id' => $deptIt->id, 'is_active' => true]),
                Position::create(['name' => 'Frontend Developer', 'department_id' => $deptIt->id, 'is_active' => true]),
                Position::create(['name' => 'Fullstack Engineer', 'department_id' => $deptIt->id, 'is_active' => true]),
                Position::create(['name' => 'QA / Automation Tester', 'department_id' => $deptIt->id, 'is_active' => true]),
                Position::create(['name' => 'DevOps & Cloud Engineer', 'department_id' => $deptIt->id, 'is_active' => true]),
            ],
            $deptHr->id => [
                Position::create(['name' => 'Talent Acquisition Specialist', 'department_id' => $deptHr->id, 'is_active' => true]),
                Position::create(['name' => 'People & Culture Officer', 'department_id' => $deptHr->id, 'is_active' => true]),
            ],
            $deptSales->id => [
                Position::create(['name' => 'Sales Manager', 'department_id' => $deptSales->id, 'is_active' => true]),
                Position::create(['name' => 'Key Account Executive', 'department_id' => $deptSales->id, 'is_active' => true]),
                Position::create(['name' => 'Corporate Sales Officer', 'department_id' => $deptSales->id, 'is_active' => true]),
            ],
            $deptMkt->id => [
                Position::create(['name' => 'Marketing Lead', 'department_id' => $deptMkt->id, 'is_active' => true]),
                Position::create(['name' => 'Content & Media Creator', 'department_id' => $deptMkt->id, 'is_active' => true]),
                Position::create(['name' => 'Performance Marketing Specialist', 'department_id' => $deptMkt->id, 'is_active' => true]),
            ],
            $deptAcc->id => [
                Position::create(['name' => 'Finance Director', 'department_id' => $deptAcc->id, 'is_active' => true]),
                Position::create(['name' => 'Senior Accountant', 'department_id' => $deptAcc->id, 'is_active' => true]),
                Position::create(['name' => 'Tax & Payroll Accountant', 'department_id' => $deptAcc->id, 'is_active' => true]),
            ],
            $deptOps->id => [
                Position::create(['name' => 'Operations Manager', 'department_id' => $deptOps->id, 'is_active' => true]),
                Position::create(['name' => 'Customer Support Lead', 'department_id' => $deptOps->id, 'is_active' => true]),
                Position::create(['name' => 'Customer Service Representative', 'department_id' => $deptOps->id, 'is_active' => true]),
            ],
        ];

        // 3. สร้างประเภทการลา (Leave Types)
        $sickLeave = LeaveType::create(['name' => 'ลาป่วย', 'default_days' => 30, 'is_active' => true]);
        $personalLeave = LeaveType::create(['name' => 'ลากิจ', 'default_days' => 6, 'is_active' => true]);
        $vacationLeave = LeaveType::create(['name' => 'ลาพักร้อน', 'default_days' => 10, 'is_active' => true]);
        $maternityLeave = LeaveType::create(['name' => 'ลาคลอดบุตร', 'default_days' => 98, 'is_active' => true]);
        $trainingLeave = LeaveType::create(['name' => 'ลาฝึกอบรม/สัมมนา', 'default_days' => 7, 'is_active' => true]);

        $leaveTypes = [$sickLeave, $personalLeave, $vacationLeave, $maternityLeave, $trainingLeave];
        $currentYear = (int) date('Y');

        // รายชื่อภาษาไทยสำหรับสุ่มสร้างพนักงาน 50 คน
        $firstNames = [
            'สมชาย', 'สมศรี', 'กิตติศักดิ์', 'ณัฐวุฒิ', 'วราภรณ์', 'พิชญุตม์', 'ชลธิชา', 'ธีรภัทร', 
            'ธนกฤต', 'พงศกร', 'กมลวรรณ', 'จิรวัฒน์', 'ปิยะวัฒน์', 'รัตนาภรณ์', 'ศุภกร', 'สิรินทรา', 
            'อภิสิทธิ์', 'อรทัย', 'เอกราช', 'เกศรินทร์', 'จักรพันธ์', 'ชนินทร์', 'ฐิติมา', 'ดนัย', 
            'ทศพล', 'นฤมล', 'บุญญฤทธิ์', 'ประภาส', 'ปริญญา', 'พชร', 'ภานุมาศ', 'มงคล', 
            'ยศกร', 'ลลิตา', 'วัชระ', 'วิภาดา', 'ศุภโชค', 'สมบัติ', 'สุริยา', 'อนันต์', 
            'อัครพล', 'อัจฉรา', 'อิสระ', 'อุเทน', 'กรกนก', 'ขวัญชัย', 'คงเดช', 'จารุวรรณ', 
            'ชนากานต์', 'ชานนท์'
        ];

        $lastNames = [
            'สุขสมบูรณ์', 'ใจดี', 'ชัยชนะ', 'เจริญผล', 'มั่นคง', 'รัตนโกสินทร์', 'วงษ์สวรรค์', 'ศรีสุข', 
            'ทองคำ', 'อินทรวิเศษ', 'แซ่ลิ้ม', 'ปัญญาวงศ์', 'พงษ์พาณิชย์', 'ประสิทธิ์', 'แสงสว่าง', 'พงศ์พิพัฒน์', 
            'เจริญรุ่งเรือง', 'สุขสวัสดิ์', 'โสภณ', 'บุญส่ง', 'สว่างอารมณ์', 'เกตุแก้ว', 'วิจิตรศิลป์', 'ดำรงไทย', 
            'นิมิตมงคล', 'ศิริรัตน์', 'อารีวรรณ', 'มณีรัตน์', 'บุญมา', 'เพชรประเสริฐ', 'วรกิจเจริญ', 'คงทอง', 
            'จารุวัฒน์', 'สมบูรณ์สุข', 'เกิดผล', 'เจริญวัฒนา', 'ชัชวาล', 'เดชาวัฒน์', 'ตระกูลไทย', 'ทรัพย์สิน', 
            'นพรัตน์', 'บวรรัตน', 'ประสพโชค', 'พิทักษ์', 'ภักดี', 'ยอดมงคล', 'รุ่งเรือง', 'วัฒนา', 
            'สมเกียรติ', 'หิรัญญ์'
        ];

        // 4. สร้าง Admin 1 คน
        $adminUser = User::create([
            'emp_code' => 'admin',
            'name' => 'Admin System',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin123'),
            'phone' => '081-000-0001',
            'address' => '123/1 อาคารไอทีสแควร์ ชั้น 15 ถ.พหลโยธิน แขวงลาดยาว เขตจตุจักร กรุงเทพมหานคร 10900',
            'id_card' => '1-1001-00000-01-1',
            'role' => 'admin',
            'department_id' => $deptIt->id,
            'position_id' => $posAdmin->id,
            'start_date' => '2023-01-01',
            'status' => 'active',
        ]);

        // 5. สร้าง HR 3 คน (hr1, hr2, hr3)
        $hrData = [
            [
                'emp_code' => 'hr1',
                'name' => 'วาสนา บุญมี (HR Manager)',
                'email' => 'hr1@example.com',
                'password' => Hash::make('hr123'),
                'phone' => '082-000-0001',
                'address' => '45/12 ถ.สุขุมวิท 21 แขวงคลองเตยเหนือ เขตวัฒนา กรุงเทพมหานคร 10110',
                'id_card' => '1-1001-00000-02-2',
                'role' => 'hr',
                'department_id' => $deptHr->id,
                'position_id' => $posHrMgr->id,
                'start_date' => '2023-06-01',
                'status' => 'active',
            ],
            [
                'emp_code' => 'hr2',
                'name' => 'ชานนท์ วงศ์สุวรรณ (Senior HR)',
                'email' => 'hr2@example.com',
                'password' => Hash::make('hr123'),
                'phone' => '082-000-0002',
                'address' => '78/9 ถ.พระราม 9 แขวงห้วยขวาง เขตห้วยขวาง กรุงเทพมหานคร 10310',
                'id_card' => '1-1001-00000-03-3',
                'role' => 'hr',
                'department_id' => $deptHr->id,
                'position_id' => $posHrSenior->id,
                'start_date' => '2023-08-15',
                'status' => 'active',
            ],
            [
                'emp_code' => 'hr3',
                'name' => 'กานต์รวี เจริญโภคทรัพย์ (HR Officer)',
                'email' => 'hr3@example.com',
                'password' => Hash::make('hr123'),
                'phone' => '082-000-0003',
                'address' => '99/5 ถ.สาทรใต้ แขวงยานนาวา เขตสาทร กรุงเทพมหานคร 10120',
                'id_card' => '1-1001-00000-04-4',
                'role' => 'hr',
                'department_id' => $deptHr->id,
                'position_id' => $posHrOfficer->id,
                'start_date' => '2024-01-10',
                'status' => 'active',
            ],
        ];

        $hrUsers = [];
        foreach ($hrData as $hrItem) {
            $hrUsers[] = User::create($hrItem);
        }

        // 6. สร้างพนักงาน 50 คน (emp001 - emp050)
        $employeeUsers = [];
        for ($i = 1; $i <= 50; $i++) {
            $codeNum = str_pad($i, 3, '0', STR_PAD_LEFT);
            $empCode = "emp{$codeNum}";
            $firstName = $firstNames[($i - 1) % count($firstNames)];
            $lastName = $lastNames[($i - 1) % count($lastNames)];
            $fullName = "{$firstName} {$lastName}";
            $email = "emp{$codeNum}@example.com";

            // สุ่มแผนกและตำแหน่ง
            $dept = $departments[$i % count($departments)];
            $posList = $positionsByDept[$dept->id];
            $pos = $posList[$i % count($posList)];

            $phoneNum = '08' . rand(3, 9) . '-' . rand(100, 999) . '-' . str_pad($i * 73 % 10000, 4, '0', STR_PAD_LEFT);
            $idCard = '1-' . rand(1000, 9999) . '-' . rand(10000, 99999) . '-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-' . rand(1, 9);

            $emp = User::create([
                'emp_code' => $empCode,
                'name' => $fullName,
                'email' => $email,
                'password' => Hash::make('emp123'),
                'phone' => $phoneNum,
                'address' => (rand(1, 199)) . '/' . rand(1, 80) . ' ซอยสุขสวัสดิ์ ' . rand(1, 50) . ' กรุงเทพมหานคร',
                'id_card' => $idCard,
                'role' => 'employee',
                'department_id' => $dept->id,
                'position_id' => $pos->id,
                'start_date' => Carbon::create(2023, 1, 1)->addDays($i * 12)->toDateString(),
                'status' => 'active',
            ]);

            $employeeUsers[] = $emp;
        }

        // รวมผู้ใช้ทั้งหมด (Admin + HR 3 + Emp 50 = 54 คน)
        $allUsers = array_merge([$adminUser], $hrUsers, $employeeUsers);

        // 7. จัดสรรโควตาวันลาประจำปี (Leave Balances) ให้ทุกคน
        foreach ($allUsers as $u) {
            foreach ($leaveTypes as $lt) {
                LeaveBalance::create([
                    'user_id' => $u->id,
                    'leave_type_id' => $lt->id,
                    'year' => $currentYear,
                    'total_days' => $lt->default_days,
                    'used_days' => 0.0,
                    'remaining_days' => $lt->default_days,
                ]);
            }
        }

        // 8. สร้างข้อมูลการลา (Leave Requests) ตัวอย่าง
        $approverAdmin = $adminUser;
        $approverHr = $hrUsers[0];

        // 8.1 คำขอลาของ HR (ให้ Admin เป็นผู้อนุมัติ)
        $lrHr = LeaveRequest::create([
            'user_id' => $hrUsers[1]->id, // hr2 ลาพักร้อน
            'leave_type_id' => $vacationLeave->id,
            'start_date' => Carbon::today()->format('Y-m-d'),
            'end_date' => Carbon::today()->format('Y-m-d'),
            'days_count' => 1.0,
            'reason' => 'ติดต่อธุระส่วนตัวที่ต่างจังหวัด',
            'status' => 'approved',
            'approved_by' => $approverAdmin->id,
            'approved_at' => Carbon::now()->subHours(5),
            'remark' => 'อนุมัติเรียบร้อยโดย Admin',
        ]);

        // อัปเดตยอดวันลาของ hr2
        $hrBal = LeaveBalance::where('user_id', $hrUsers[1]->id)->where('leave_type_id', $vacationLeave->id)->where('year', $currentYear)->first();
        if ($hrBal) {
            $hrBal->update(['used_days' => 1.0, 'remaining_days' => $hrBal->total_days - 1.0]);
        }

        // 8.2 คำขอลาของพนักงานตัวอย่าง (Approved, Pending, Rejected, Cancelled)
        for ($k = 0; $k < 8; $k++) {
            $targetEmp = $employeeUsers[$k];
            $lType = $leaveTypes[$k % count($leaveTypes)];

            if ($k === 0 || $k === 1) { // ลาที่อนุมัติวันนี้
                $lr = LeaveRequest::create([
                    'user_id' => $targetEmp->id,
                    'leave_type_id' => $lType->id,
                    'start_date' => Carbon::today()->format('Y-m-d'),
                    'end_date' => Carbon::today()->format('Y-m-d'),
                    'days_count' => 1.0,
                    'reason' => 'มีไข้สูง ปวดศีรษะ ไปพบแพทย์ที่คลินิก',
                    'status' => 'approved',
                    'approved_by' => $approverHr->id,
                    'approved_at' => Carbon::now()->subHours(6),
                    'remark' => 'อนุมัติการลา พักผ่อนให้หายดีครับ',
                ]);
                $b = LeaveBalance::where('user_id', $targetEmp->id)->where('leave_type_id', $lType->id)->where('year', $currentYear)->first();
                if ($b) $b->update(['used_days' => 1.0, 'remaining_days' => $b->total_days - 1.0]);
            } elseif ($k === 2 || $k === 3) { // รออนุมัติ
                LeaveRequest::create([
                    'user_id' => $targetEmp->id,
                    'leave_type_id' => $lType->id,
                    'start_date' => Carbon::today()->addDays(2)->format('Y-m-d'),
                    'end_date' => Carbon::today()->addDays(3)->format('Y-m-d'),
                    'days_count' => 2.0,
                    'reason' => 'ไปทำธุระต่อใบขับขี่และงานราชการ',
                    'status' => 'pending',
                ]);
            } elseif ($k === 4) { // ปฏิเสธ
                LeaveRequest::create([
                    'user_id' => $targetEmp->id,
                    'leave_type_id' => $lType->id,
                    'start_date' => Carbon::today()->subDays(2)->format('Y-m-d'),
                    'end_date' => Carbon::today()->subDays(2)->format('Y-m-d'),
                    'days_count' => 1.0,
                    'reason' => 'พักผ่อน',
                    'status' => 'rejected',
                    'approved_by' => $approverHr->id,
                    'approved_at' => Carbon::now()->subDays(2),
                    'remark' => 'เนื่องจากมีงานด่วนของฝ่ายและแจ้งกระชั้นชิดเกินไป',
                ]);
            } elseif ($k === 5) { // ยกเลิก
                LeaveRequest::create([
                    'user_id' => $targetEmp->id,
                    'leave_type_id' => $lType->id,
                    'start_date' => Carbon::today()->addDays(5)->format('Y-m-d'),
                    'end_date' => Carbon::today()->addDays(6)->format('Y-m-d'),
                    'days_count' => 2.0,
                    'reason' => 'เลื่อนวันเดินทางไปท่องเที่ยว',
                    'status' => 'cancelled',
                ]);
            }
        }

        // 9. สร้างข้อมูลการลงเวลาประจำวัน (Attendance Today) สำหรับพนักงานทุกคนอย่างสมจริง
        $today = Carbon::today()->format('Y-m-d');

        // hr2 และ emp001, emp002 ติดสถานะลา (leave)
        Attendance::create([
            'user_id' => $hrUsers[1]->id,
            'date' => $today,
            'status' => 'leave',
            'leave_request_id' => $lrHr->id,
            'notes' => 'อนุมัติการลา: ลาพักร้อน',
            'hr_id' => $adminUser->id,
        ]);

        Attendance::create([
            'user_id' => $employeeUsers[0]->id,
            'date' => $today,
            'status' => 'leave',
            'notes' => 'อนุมัติการลา: ลาป่วย',
            'hr_id' => $approverHr->id,
        ]);

        Attendance::create([
            'user_id' => $employeeUsers[1]->id,
            'date' => $today,
            'status' => 'leave',
            'notes' => 'อนุมัติการลา: ลากิจ',
            'hr_id' => $approverHr->id,
        ]);

        // พนักงานคนที่ 2 ถึง 35 ลงเวลาเข้างานตรงเวลา (On time: 08:30 - 08:58)
        for ($i = 2; $i <= 35; $i++) {
            $u = $employeeUsers[$i];
            $inMin = str_pad(rand(30, 58), 2, '0', STR_PAD_LEFT);
            $inSec = str_pad(rand(10, 59), 2, '0', STR_PAD_LEFT);
            $checkInTime = "08:{$inMin}:{$inSec}";
            
            // สุ่มเวลาเลิกงาน
            $outTime = ($i % 3 === 0) ? '17:' . rand(15, 45) . ':00' : null;

            Attendance::create([
                'user_id' => $u->id,
                'date' => $today,
                'check_in' => $checkInTime,
                'check_out' => $outTime,
                'status' => 'on_time',
                'notes' => 'เข้างานตรงเวลา',
            ]);
        }

        // พนักงานคนที่ 36 ถึง 44 เข้างานสาย (Late: 09:05 - 09:40)
        for ($i = 36; $i <= 44; $i++) {
            $u = $employeeUsers[$i];
            $inMin = str_pad(rand(5, 40), 2, '0', STR_PAD_LEFT);
            $inSec = str_pad(rand(10, 59), 2, '0', STR_PAD_LEFT);
            $checkInTime = "09:{$inMin}:{$inSec}";

            Attendance::create([
                'user_id' => $u->id,
                'date' => $today,
                'check_in' => $checkInTime,
                'check_out' => null,
                'status' => 'late',
                'notes' => 'เข้างานสาย',
            ]);
        }

        // Admin & HR ที่เหลือลงเวลาตรงเวลา
        Attendance::create([
            'user_id' => $adminUser->id,
            'date' => $today,
            'check_in' => '08:25:00',
            'check_out' => null,
            'status' => 'on_time',
            'notes' => 'เข้างานตรงเวลา',
        ]);
        Attendance::create([
            'user_id' => $hrUsers[0]->id,
            'date' => $today,
            'check_in' => '08:35:12',
            'check_out' => null,
            'status' => 'on_time',
            'notes' => 'เข้างานตรงเวลา',
        ]);
        Attendance::create([
            'user_id' => $hrUsers[2]->id,
            'date' => $today,
            'check_in' => '08:42:50',
            'check_out' => null,
            'status' => 'on_time',
            'notes' => 'เข้างานตรงเวลา',
        ]);

        // (พนักงาน 45 ถึง 49 ยังไม่ลงเวลา / Absent จะไม่สร้าง attendance หรือปล่อยเป็นค่าว่าง)

        // 10. สร้างข้อมูล OT (Overtime Requests) สำหรับวันนี้และสัปดาห์นี้
        $otEmployees = [$employeeUsers[2], $employeeUsers[3], $employeeUsers[4], $employeeUsers[5], $employeeUsers[10], $employeeUsers[12]];

        // OT อนุมัติแล้ว วันนี้
        Overtime::create([
            'user_id' => $otEmployees[0]->id,
            'date' => $today,
            'start_time' => '17:30:00',
            'end_time' => '20:30:00',
            'break_minutes' => 0,
            'hours' => 3.0,
            'ot_type' => 'normal',
            'description' => 'พัฒนาระบบ API Endpoint เชื่อมต่อฐานข้อมูลลูกค้าองค์กร',
            'status' => 'approved',
            'hr_id' => $approverHr->id,
            'hr_approved_at' => Carbon::now()->subHours(2),
        ]);

        Overtime::create([
            'user_id' => $otEmployees[1]->id,
            'date' => $today,
            'start_time' => '17:30:00',
            'end_time' => '19:30:00',
            'break_minutes' => 0,
            'hours' => 2.0,
            'ot_type' => 'normal',
            'description' => 'ทดสอบระบบความปลอดภัยและปิดช่องโหว่เซิร์ฟเวอร์',
            'status' => 'approved',
            'hr_id' => $approverHr->id,
            'hr_approved_at' => Carbon::now()->subHours(1),
        ]);

        Overtime::create([
            'user_id' => $otEmployees[2]->id,
            'date' => $today,
            'start_time' => '17:30:00',
            'end_time' => '21:30:00',
            'break_minutes' => 30,
            'hours' => 3.5,
            'ot_type' => 'normal',
            'description' => 'จัดทำเอกสารและสรุปงบการเงินเร่งด่วนประจำไตรมาส',
            'status' => 'approved',
            'hr_id' => $approverHr->id,
            'hr_approved_at' => Carbon::now()->subMinutes(45),
        ]);

        // OT รออนุมัติ (Pending)
        Overtime::create([
            'user_id' => $otEmployees[3]->id,
            'date' => $today,
            'start_time' => '17:30:00',
            'end_time' => '20:30:00',
            'break_minutes' => 0,
            'hours' => 3.0,
            'ot_type' => 'normal',
            'description' => 'สนับสนุนงานประสานงานลูกค้าต่างประเทศและแก้ไขเคสด่วน',
            'status' => 'pending',
        ]);

        Overtime::create([
            'user_id' => $otEmployees[4]->id,
            'date' => $today,
            'start_time' => '17:30:00',
            'end_time' => '19:30:00',
            'break_minutes' => 0,
            'hours' => 2.0,
            'ot_type' => 'normal',
            'description' => 'จัดทำแคมเปญโฆษณาออนไลน์ช่วง Flash Sale',
            'status' => 'pending',
        ]);

        // OT ปฏิเสธ (Rejected)
        Overtime::create([
            'user_id' => $otEmployees[5]->id,
            'date' => Carbon::today()->subDays(1)->format('Y-m-d'),
            'start_time' => '17:30:00',
            'end_time' => '19:30:00',
            'break_minutes' => 0,
            'hours' => 2.0,
            'ot_type' => 'normal',
            'description' => 'เคลียร์งานเอกสารทั่วไป',
            'status' => 'rejected',
            'hr_id' => $approverHr->id,
            'hr_reject_reason' => 'งานสามารถดำเนินการต่อในวันถัดไปได้ ยังไม่มีความจำเป็นเร่งด่วน',
        ]);
    }
}
