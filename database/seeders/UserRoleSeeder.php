<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Department;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Position;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. สร้างแผนกเริ่มต้น
        $deptIt = Department::firstOrCreate(['name' => 'IT & Software']);
        $deptHr = Department::firstOrCreate(['name' => 'Human Resources']);
        $deptSales = Department::firstOrCreate(['name' => 'Sales & Marketing']);
        $deptAcc = Department::firstOrCreate(['name' => 'Accounting & Finance']);
        $deptOps = Department::firstOrCreate(['name' => 'Operations & Support']);

        // 2. สร้างตำแหน่งเริ่มต้น
        $posAdmin = Position::firstOrCreate(['name' => 'System Administrator']);
        $posHrMgr = Position::firstOrCreate(['name' => 'HR Manager']);
        $posHrOff = Position::firstOrCreate(['name' => 'HR Officer']);
        $posDev = Position::firstOrCreate(['name' => 'Software Developer']);
        $posFrontend = Position::firstOrCreate(['name' => 'Frontend Engineer']);
        $posQa = Position::firstOrCreate(['name' => 'QA Engineer']);
        $posSalesMgr = Position::firstOrCreate(['name' => 'Sales Director']);
        $posSales = Position::firstOrCreate(['name' => 'Sales Executive']);
        $posMarketing = Position::firstOrCreate(['name' => 'Marketing Specialist']);
        $posAccMgr = Position::firstOrCreate(['name' => 'Accounting Manager']);
        $posAcc = Position::firstOrCreate(['name' => 'Senior Accountant']);
        $posSupport = Position::firstOrCreate(['name' => 'Customer Support Specialist']);
        $posOps = Position::firstOrCreate(['name' => 'Operations Officer']);

        // 3. สร้างประเภทการลาเริ่มต้น
        $sickLeave = LeaveType::firstOrCreate(
            ['name' => 'ลาป่วย'],
            ['default_days' => 30]
        );
        $personalLeave = LeaveType::firstOrCreate(
            ['name' => 'ลากิจ'],
            ['default_days' => 6]
        );
        $vacationLeave = LeaveType::firstOrCreate(
            ['name' => 'ลาพักร้อน'],
            ['default_days' => 6]
        );
        $maternityLeave = LeaveType::firstOrCreate(
            ['name' => 'ลาคลอด / เลี้ยงดูบุตร'],
            ['default_days' => 98]
        );
        $trainingLeave = LeaveType::firstOrCreate(
            ['name' => 'ลาฝึกอบรม'],
            ['default_days' => 10]
        );

        $allLeaveTypes = [$sickLeave, $personalLeave, $vacationLeave, $maternityLeave, $trainingLeave];
        $currentYear = (int) date('Y');

        // 4. บัญชีพนักงานครอบคลุมทุกแผนก ทุกระดับตำแหน่ง
        $employeesData = [
            [
                'emp_code' => 'admin',
                'name' => 'Admin System (ผู้ดูแลระบบ)',
                'email' => 'admin@example.com',
                'password' => Hash::make('admin123'),
                'phone' => '081-111-1111',
                'address' => 'Bangkok, Thailand',
                'role' => 'admin',
                'department_id' => $deptIt->id,
                'position_id' => $posAdmin->id,
                'start_date' => '2024-01-01',
                'status' => 'active',
            ],
            [
                'emp_code' => 'hr',
                'name' => 'วาสนา บุญมี (HR Manager)',
                'email' => 'hr@example.com',
                'password' => Hash::make('hr123'),
                'phone' => '082-222-2222',
                'address' => 'Bangkok, Thailand',
                'role' => 'hr',
                'department_id' => $deptHr->id,
                'position_id' => $posHrMgr->id,
                'start_date' => '2024-02-01',
                'status' => 'active',
            ],
            [
                'emp_code' => 'EMP002',
                'name' => 'กิตติศักดิ์ ชัยชนะ',
                'email' => 'kittisak@example.com',
                'password' => Hash::make('emp123'),
                'phone' => '082-333-4455',
                'address' => 'Nonthaburi, Thailand',
                'role' => 'hr',
                'department_id' => $deptHr->id,
                'position_id' => $posHrOff->id,
                'start_date' => '2024-05-15',
                'status' => 'active',
            ],
            [
                'emp_code' => 'employee',
                'name' => 'สมชาย มั่นคง',
                'email' => 'employee@example.com',
                'password' => Hash::make('emp123'),
                'phone' => '083-333-3333',
                'address' => 'Bangkok, Thailand',
                'role' => 'employee',
                'department_id' => $deptIt->id,
                'position_id' => $posDev->id,
                'start_date' => '2025-01-15',
                'status' => 'active',
            ],
            [
                'emp_code' => 'EMP003',
                'name' => 'วิภาดา สดใส',
                'email' => 'wiphada@example.com',
                'password' => Hash::make('emp123'),
                'phone' => '084-555-6677',
                'address' => 'Pathum Thani, Thailand',
                'role' => 'employee',
                'department_id' => $deptIt->id,
                'position_id' => $posFrontend->id,
                'start_date' => '2024-06-01',
                'status' => 'active',
            ],
            [
                'emp_code' => 'EMP004',
                'name' => 'ณัฐวุฒิ เก่งกาจ',
                'email' => 'natthawut@example.com',
                'password' => Hash::make('emp123'),
                'phone' => '085-666-7788',
                'address' => 'Bangkok, Thailand',
                'role' => 'employee',
                'department_id' => $deptIt->id,
                'position_id' => $posQa->id,
                'start_date' => '2024-08-15',
                'status' => 'active',
            ],
            [
                'emp_code' => 'EMP005',
                'name' => 'วรเมธ สุริยะ',
                'email' => 'worameth@example.com',
                'password' => Hash::make('emp123'),
                'phone' => '086-777-8899',
                'address' => 'Samut Prakan, Thailand',
                'role' => 'employee',
                'department_id' => $deptSales->id,
                'position_id' => $posSalesMgr->id,
                'start_date' => '2023-11-01',
                'status' => 'active',
            ],
            [
                'emp_code' => 'EMP006',
                'name' => 'ศิริพร บุญเจริญ',
                'email' => 'siriporn@example.com',
                'password' => Hash::make('emp123'),
                'phone' => '087-888-9900',
                'address' => 'Bangkok, Thailand',
                'role' => 'employee',
                'department_id' => $deptSales->id,
                'position_id' => $posMarketing->id,
                'start_date' => '2024-03-01',
                'status' => 'active',
            ],
            [
                'emp_code' => 'EMP007',
                'name' => 'กมลวรรณ รัตนชัย',
                'email' => 'kamonwan@example.com',
                'password' => Hash::make('emp123'),
                'phone' => '088-999-0011',
                'address' => 'Bangkok, Thailand',
                'role' => 'employee',
                'department_id' => $deptAcc->id,
                'position_id' => $posAccMgr->id,
                'start_date' => '2023-09-01',
                'status' => 'active',
            ],
            [
                'emp_code' => 'EMP008',
                'name' => 'ธีรภัทร ชาญวิทย์',
                'email' => 'teerapat@example.com',
                'password' => Hash::make('emp123'),
                'phone' => '089-111-2233',
                'address' => 'Nonthaburi, Thailand',
                'role' => 'employee',
                'department_id' => $deptAcc->id,
                'position_id' => $posAcc->id,
                'start_date' => '2024-07-01',
                'status' => 'active',
            ],
            [
                'emp_code' => 'EMP009',
                'name' => 'สุนิสา ใจดี',
                'email' => 'sunisa@example.com',
                'password' => Hash::make('emp123'),
                'phone' => '090-222-3344',
                'address' => 'Bangkok, Thailand',
                'role' => 'employee',
                'department_id' => $deptOps->id,
                'position_id' => $posSupport->id,
                'start_date' => '2024-09-15',
                'status' => 'active',
            ],
            [
                'emp_code' => 'EMP010',
                'name' => 'อนุรักษ์ วงศ์มณี',
                'email' => 'anurak@example.com',
                'password' => Hash::make('emp123'),
                'phone' => '091-333-4455',
                'address' => 'Samut Sakhon, Thailand',
                'role' => 'employee',
                'department_id' => $deptOps->id,
                'position_id' => $posOps->id,
                'start_date' => '2024-04-01',
                'status' => 'active',
            ],
            [
                'emp_code' => 'EMP011',
                'name' => 'พิมพ์มาดา สุวรรณ',
                'email' => 'pimmada@example.com',
                'password' => Hash::make('emp123'),
                'phone' => '092-444-5566',
                'address' => 'Bangkok, Thailand',
                'role' => 'employee',
                'department_id' => $deptSales->id,
                'position_id' => $posSales->id,
                'start_date' => '2025-02-01',
                'status' => 'active',
            ],
            [
                'emp_code' => 'EMP012',
                'name' => 'ชานนท์ เมธากุล',
                'email' => 'chanon@example.com',
                'password' => Hash::make('emp123'),
                'phone' => '093-555-6677',
                'address' => 'Bangkok, Thailand',
                'role' => 'employee',
                'department_id' => $deptIt->id,
                'position_id' => $posDev->id,
                'start_date' => '2025-03-01',
                'status' => 'active',
            ],
        ];

        $createdUsers = [];
        foreach ($employeesData as $emp) {
            $user = User::updateOrCreate(
                ['emp_code' => $emp['emp_code']],
                $emp
            );
            $createdUsers[$emp['emp_code']] = $user;

            // จัดสรรโควตาวันลาเริ่มต้นให้พนักงานทุกคน
            foreach ($allLeaveTypes as $lt) {
                LeaveBalance::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'leave_type_id' => $lt->id,
                        'year' => $currentYear,
                    ],
                    [
                        'total_days' => $lt->default_days,
                        'used_days' => 0.0,
                        'remaining_days' => $lt->default_days,
                    ]
                );
            }
        }

        $adminUser = $createdUsers['admin'];
        $hrUser = $createdUsers['hr'];
        $wiphadaUser = $createdUsers['EMP003'];
        $somchaiUser = $createdUsers['employee'];
        $teerapatUser = $createdUsers['EMP008'];
        $kamonwanUser = $createdUsers['EMP007'];

        // 5. สร้างใบลาทดสอบ (Leave Requests) หลากหลายสถานะ
        $today = Carbon::today()->format('Y-m-d');
        $nextMonday = Carbon::today()->next(Carbon::MONDAY)->format('Y-m-d');
        $nextTuesday = Carbon::today()->next(Carbon::TUESDAY)->format('Y-m-d');

        // ใบลาที่ 1: Approved - EMP003 (วิภาดา) ลาพักร้อนวันนี้ 1 วัน
        $approvedLeaveToday = LeaveRequest::updateOrCreate(
            [
                'user_id' => $wiphadaUser->id,
                'start_date' => $today,
                'end_date' => $today,
            ],
            [
                'leave_type_id' => $vacationLeave->id,
                'days_count' => 1.0,
                'reason' => 'ไปทำธุระครอบครัวต่างจังหวัด',
                'status' => 'approved',
                'approved_by' => $hrUser->id,
                'approved_at' => Carbon::now()->subDay(),
                'remark' => 'อนุมัติเรียบร้อย',
            ]
        );

        // ปรับยอด leave_balance ของ EMP003
        $balance = LeaveBalance::where('user_id', $wiphadaUser->id)
            ->where('leave_type_id', $vacationLeave->id)
            ->where('year', $currentYear)
            ->first();
        if ($balance) {
            $balance->used_days = 1.0;
            $balance->remaining_days = max(0, $balance->total_days - 1.0);
            $balance->save();
        }

        // ใบลาที่ 2: Pending - สมชาย มั่นคง ยื่นลากิจ 2 วัน สัปดาห์หน้า (รอ Admin/HR อนุมัติ)
        LeaveRequest::updateOrCreate(
            [
                'user_id' => $somchaiUser->id,
                'start_date' => $nextMonday,
                'end_date' => $nextTuesday,
            ],
            [
                'leave_type_id' => $personalLeave->id,
                'days_count' => 2.0,
                'reason' => 'ติดต่อทำธุรกรรมทางราชการที่สำนักงานที่ดิน',
                'status' => 'pending',
                'approved_by' => null,
                'approved_at' => null,
                'remark' => null,
            ]
        );

        // ใบลาที่ 3: Pending - EMP008 (ธีรภัทร) ยื่นลาพักร้อน 1 วัน สัปดาห์หน้า
        LeaveRequest::updateOrCreate(
            [
                'user_id' => $teerapatUser->id,
                'start_date' => $nextMonday,
                'end_date' => $nextMonday,
            ],
            [
                'leave_type_id' => $vacationLeave->id,
                'days_count' => 1.0,
                'reason' => 'เดินทางกลับบ้านต่างจังหวัด',
                'status' => 'pending',
                'approved_by' => null,
                'approved_at' => null,
                'remark' => null,
            ]
        );

        // ใบลาที่ 4: Rejected - EMP007 (กมลวรรณ) เคยยื่นลาพักร้อนแต่ไม่อนุมัติ
        LeaveRequest::updateOrCreate(
            [
                'user_id' => $kamonwanUser->id,
                'start_date' => Carbon::today()->subDays(10)->format('Y-m-d'),
                'end_date' => Carbon::today()->subDays(8)->format('Y-m-d'),
            ],
            [
                'leave_type_id' => $vacationLeave->id,
                'days_count' => 3.0,
                'reason' => 'พักผ่อนประจำปี',
                'status' => 'rejected',
                'approved_by' => $adminUser->id,
                'approved_at' => Carbon::today()->subDays(11),
                'remark' => 'ช่วงดังกล่าวตรงกับกำหนดการปิดงบการเงินประจำเดือน ขอให้เลื่อนวันลา',
            ]
        );

        // 6. สร้างข้อมูลการลงเวลาทำงาน (Attendances)
        // 6.1 ข้อมูลวันนี้ (Today)
        // - เข้างานตรงเวลา (on_time: ก่อน 09:00)
        $onTimeToday = [
            ['code' => 'admin', 'in' => '08:42:00', 'out' => null, 'notes' => 'เข้างานปกติ'],
            ['code' => 'hr', 'in' => '08:50:00', 'out' => null, 'notes' => 'เข้างานปกติ'],
            ['code' => 'EMP002', 'in' => '08:35:00', 'out' => null, 'notes' => 'เข้างานเช้า'],
            ['code' => 'EMP005', 'in' => '08:48:00', 'out' => null, 'notes' => 'เข้างานปกติ'],
            ['code' => 'EMP006', 'in' => '08:55:00', 'out' => null, 'notes' => 'เข้างานปกติ'],
        ];

        foreach ($onTimeToday as $item) {
            $u = $createdUsers[$item['code']] ?? null;
            if ($u) {
                Attendance::updateOrCreate(
                    ['user_id' => $u->id, 'date' => $today],
                    [
                        'check_in' => $item['in'],
                        'check_out' => $item['out'],
                        'status' => 'on_time',
                        'notes' => $item['notes'],
                        'leave_request_id' => null,
                    ]
                );
            }
        }

        // - มาสาย (late: หลัง 09:00)
        $lateToday = [
            ['code' => 'employee', 'in' => '09:18:00', 'out' => null, 'notes' => 'รถไฟฟ้าขัดข้อง'],
            ['code' => 'EMP007', 'in' => '09:32:00', 'out' => null, 'notes' => 'การจราจรติดขัด'],
            ['code' => 'EMP009', 'in' => '09:15:00', 'out' => null, 'notes' => 'มาสาย 15 นาที'],
        ];

        foreach ($lateToday as $item) {
            $u = $createdUsers[$item['code']] ?? null;
            if ($u) {
                Attendance::updateOrCreate(
                    ['user_id' => $u->id, 'date' => $today],
                    [
                        'check_in' => $item['in'],
                        'check_out' => $item['out'],
                        'status' => 'late',
                        'notes' => $item['notes'],
                        'leave_request_id' => null,
                    ]
                );
            }
        }

        // - ลางาน (leave: ผูกกับใบลาที่อนุมัติแล้วของวิภาดา)
        Attendance::updateOrCreate(
            ['user_id' => $wiphadaUser->id, 'date' => $today],
            [
                'check_in' => null,
                'check_out' => null,
                'status' => 'leave',
                'notes' => 'ลาพักร้อน (อนุมัติแล้ว)',
                'leave_request_id' => $approvedLeaveToday->id,
            ]
        );

        // 6.2 ข้อมูลย้อนหลัง 5 วันทำการ เพื่อให้หน้า Dashboard & My History มีประวัติสมบูรณ์
        for ($i = 1; $i <= 5; $i++) {
            $pastDate = Carbon::today()->subDays($i);
            // ข้ามวันเสาร์-อาทิตย์
            if ($pastDate->isWeekend()) {
                continue;
            }
            $pDateStr = $pastDate->format('Y-m-d');

            foreach ($createdUsers as $code => $user) {
                $isLate = ($code === 'employee' && $i === 2);
                $checkIn = $isLate ? '09:15:00' : '08:' . str_pad(30 + ($user->id % 25), 2, '0', STR_PAD_LEFT) . ':00';
                $checkOut = '17:' . str_pad(30 + ($user->id % 25), 2, '0', STR_PAD_LEFT) . ':00';
                $status = $isLate ? 'late' : 'on_time';

                Attendance::updateOrCreate(
                    ['user_id' => $user->id, 'date' => $pDateStr],
                    [
                        'check_in' => $checkIn,
                        'check_out' => $checkOut,
                        'status' => $status,
                        'notes' => $isLate ? 'มาสาย' : 'ปกติ',
                        'leave_request_id' => null,
                    ]
                );
            }
        }
    }
}
