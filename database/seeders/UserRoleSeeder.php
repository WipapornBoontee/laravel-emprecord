<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Position;
use App\Models\User;
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
        $itDept = Department::firstOrCreate(['name' => 'IT & Software']);
        $hrDept = Department::firstOrCreate(['name' => 'Human Resources']);
        $salesDept = Department::firstOrCreate(['name' => 'Sales & Marketing']);

        // 2. สร้างตำแหน่งเริ่มต้น
        $adminPos = Position::firstOrCreate(['name' => 'System Administrator']);
        $hrPos = Position::firstOrCreate(['name' => 'HR Officer']);
        $devPos = Position::firstOrCreate(['name' => 'Software Developer']);

        // 3. สร้างบัญชี Admin (Username: admin / Password: admin123)
        User::updateOrCreate(
            ['emp_code' => 'admin'],
            [
                'name' => 'Admin System',
                'email' => 'admin@example.com',
                'password' => Hash::make('admin123'),
                'phone' => '081-111-1111',
                'address' => 'Bangkok, Thailand',
                'role' => 'admin',
                'department_id' => $itDept->id,
                'position_id' => $adminPos->id,
                'start_date' => '2024-01-01',
                'status' => 'active',
            ]
        );

        // 4. สร้างบัญชี HR (Username: hr / Password: hr123)
        User::updateOrCreate(
            ['emp_code' => 'hr'],
            [
                'name' => 'HR Manager',
                'email' => 'hr@example.com',
                'password' => Hash::make('hr123'),
                'phone' => '082-222-2222',
                'address' => 'Bangkok, Thailand',
                'role' => 'hr',
                'department_id' => $hrDept->id,
                'position_id' => $hrPos->id,
                'start_date' => '2024-02-01',
                'status' => 'active',
            ]
        );

        // 5. สร้างบัญชี Employee (Username: employee / Password: emp123)
        $empUser = User::updateOrCreate(
            ['emp_code' => 'employee'],
            [
                'name' => 'สมชาย มั่นคง (พนักงานทั่วไป)',
                'email' => 'employee@example.com',
                'password' => Hash::make('emp123'),
                'phone' => '083-333-3333',
                'address' => 'Bangkok, Thailand',
                'role' => 'employee',
                'department_id' => $itDept->id,
                'position_id' => $devPos->id,
                'start_date' => '2025-01-15',
                'status' => 'active',
            ]
        );

        // 6. สร้างประเภทการลาเริ่มต้นตาม database_schema.md
        $sickLeave = \App\Models\LeaveType::firstOrCreate(
            ['name' => 'ลาป่วย'],
            ['default_days' => 30]
        );

        $personalLeave = \App\Models\LeaveType::firstOrCreate(
            ['name' => 'ลากิจ'],
            ['default_days' => 6]
        );

        $vacationLeave = \App\Models\LeaveType::firstOrCreate(
            ['name' => 'ลาพักร้อน'],
            ['default_days' => 6]
        );

        // 7. จัดสรรโควตาวันลาเริ่มต้นให้พนักงาน (ปีปัจจุบัน)
        $currentYear = (int) date('Y');
        $allLeaveTypes = [$sickLeave, $personalLeave, $vacationLeave];

        foreach ([$empUser] as $user) {
            foreach ($allLeaveTypes as $leaveType) {
                \App\Models\LeaveBalance::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'leave_type_id' => $leaveType->id,
                        'year' => $currentYear,
                    ],
                    [
                        'total_days' => $leaveType->default_days,
                        'used_days' => 0.0,
                        'remaining_days' => $leaveType->default_days,
                    ]
                );
            }
        }
    }
}
