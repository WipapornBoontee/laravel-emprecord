<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Position;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. สร้างข้อมูลแผนกเริ่มต้น
        $itDept = Department::firstOrCreate(['name' => 'IT & Software']);
        $hrDept = Department::firstOrCreate(['name' => 'Human Resources']);
        $salesDept = Department::firstOrCreate(['name' => 'Sales & Marketing']);

        // 2. สร้างข้อมูลตำแหน่งเริ่มต้น
        $adminPos = Position::firstOrCreate(['name' => 'System Administrator']);
        $hrPos = Position::firstOrCreate(['name' => 'HR Officer']);
        $devPos = Position::firstOrCreate(['name' => 'Software Developer']);

        // 3. สร้างบัญชีทดสอบ 3 บทบาท (Username / Password จำง่าย)
        // บทบาท: Admin
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

        // บทบาท: HR
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

        // บทบาท: Employee
        User::updateOrCreate(
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
    }
}
