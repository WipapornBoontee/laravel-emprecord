<?php

namespace Database\Seeders;

use App\Models\CompanyHoliday;
use App\Models\AttendanceAdjustment;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SystemEnhancementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currentYear = date('Y');

        // 1. ข้อมูลวันหยุดประเพณี/วันหยุดบริษัท (Company Holidays) ประจำปี
        $holidays = [
            ['name' => 'วันขึ้นปีใหม่', 'holiday_date' => "{$currentYear}-01-01", 'is_recurring' => true],
            ['name' => 'วันมาฆบูชา', 'holiday_date' => "{$currentYear}-02-24", 'is_recurring' => false],
            ['name' => 'วันจักรี', 'holiday_date' => "{$currentYear}-04-06", 'is_recurring' => true],
            ['name' => 'วันสงกรานต์', 'holiday_date' => "{$currentYear}-04-13", 'is_recurring' => true],
            ['name' => 'วันสงกรานต์', 'holiday_date' => "{$currentYear}-04-14", 'is_recurring' => true],
            ['name' => 'วันสงกรานต์', 'holiday_date' => "{$currentYear}-04-15", 'is_recurring' => true],
            ['name' => 'วันแรงงานแห่งชาติ', 'holiday_date' => "{$currentYear}-05-01", 'is_recurring' => true],
            ['name' => 'วันฉัตรมงคล', 'holiday_date' => "{$currentYear}-05-04", 'is_recurring' => true],
            ['name' => 'วันวิสาขบูชา', 'holiday_date' => "{$currentYear}-05-22", 'is_recurring' => false],
            ['name' => 'วันเฉลิมพระชนมพรรษาสมเด็จพระนางเจ้าฯ พระบรมราชินี', 'holiday_date' => "{$currentYear}-06-03", 'is_recurring' => true],
            ['name' => 'วันอาสาฬหบูชา', 'holiday_date' => "{$currentYear}-07-20", 'is_recurring' => false],
            ['name' => 'วันเข้าพรรษา', 'holiday_date' => "{$currentYear}-07-21", 'is_recurring' => false],
            ['name' => 'วันเฉลิมพระชนมพรรษาพระบาทสมเด็จพระเจ้าอยู่หัว', 'holiday_date' => "{$currentYear}-07-28", 'is_recurring' => true],
            ['name' => 'วันแม่แห่งชาติ', 'holiday_date' => "{$currentYear}-08-12", 'is_recurring' => true],
            ['name' => 'วันคล้ายวันสวรรคต ร.9 (วันนวมินทรมหาราช)', 'holiday_date' => "{$currentYear}-10-13", 'is_recurring' => true],
            ['name' => 'วันปิยมหาราช', 'holiday_date' => "{$currentYear}-10-23", 'is_recurring' => true],
            ['name' => 'วันพ่อแห่งชาติ', 'holiday_date' => "{$currentYear}-12-05", 'is_recurring' => true],
            ['name' => 'วันรัฐธรรมนูญ', 'holiday_date' => "{$currentYear}-12-10", 'is_recurring' => true],
            ['name' => 'วันสิ้นปี', 'holiday_date' => "{$currentYear}-12-31", 'is_recurring' => true],
        ];

        foreach ($holidays as $h) {
            CompanyHoliday::updateOrCreate(
                ['holiday_date' => $h['holiday_date']],
                ['name' => $h['name'], 'is_recurring' => $h['is_recurring']]
            );
        }

        // 2. ตัวอย่างข้อมูลขอปรับเวลาลงเวลาย้อนหลัง (Attendance Adjustment Sample)
        $employee = User::where('role', 'employee')->first() ?? User::first();
        $hrManager = User::where('role', 'hr')->first() ?? User::where('role', 'admin')->first();

        if ($employee) {
            $yesterday = Carbon::yesterday()->format('Y-m-d');
            $att = Attendance::where('user_id', $employee->id)->where('date', $yesterday)->first();

            // คำขอรอพิจารณา 1 รายการ
            AttendanceAdjustment::updateOrCreate(
                [
                    'user_id' => $employee->id,
                    'target_date' => $yesterday,
                ],
                [
                    'attendance_id' => $att ? $att->id : null,
                    'requested_check_in' => '08:30:00',
                    'requested_check_out' => '17:30:00',
                    'reason' => 'ระบบสแกนนิ้วขัดข้อง และมีงานด่วนนอกสถานที่ช่วงเช้า',
                    'attachment_url' => null,
                    'status' => 'pending',
                ]
            );

            // คำขอย้อนหลัง 3 วันที่ได้รับการอนุมัติแล้ว 1 รายการ
            $pastDate = Carbon::today()->subDays(3)->format('Y-m-d');
            $pastAtt = Attendance::where('user_id', $employee->id)->where('date', $pastDate)->first();

            AttendanceAdjustment::updateOrCreate(
                [
                    'user_id' => $employee->id,
                    'target_date' => $pastDate,
                ],
                [
                    'attendance_id' => $pastAtt ? $pastAtt->id : null,
                    'requested_check_in' => '08:25:00',
                    'requested_check_out' => '17:35:00',
                    'reason' => 'ลืมสแกนนิ้วมือตอนเช้าเนื่องจากรีบเข้าประชุมฝ่าย',
                    'attachment_url' => null,
                    'status' => 'approved',
                    'approver_id' => $hrManager ? $hrManager->id : null,
                    'approved_at' => Carbon::today()->subDays(2),
                ]
            );
        }
    }
}
