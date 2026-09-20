<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Department;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    /**
     * เวลาเปิดให้ลงเวลาเข้างานได้ล่วงหน้า (30 นาทีก่อนเวลาเริ่มงานมาตรฐาน)
     */
    protected string $earliestCheckInTime = '08:30:00';

    /**
     * เวลาเข้างานมาตรฐาน (ก่อนหรือเท่ากับเวลานี้ถือว่าตรงเวลา)
     */
    protected string $standardCheckInTime = '09:00:59';

    /**
     * เวลาเลิกงานมาตรฐาน (สามารถลงเวลาออกงานได้ตั้งแต่เวลานี้เป็นต้นไป)
     */
    protected string $standardCheckOutTime = '17:00:00';

    /**
     * หน้าบันทึกเวลาเข้า-ออกงาน (Check-in / Check-out View)
     */
    public function checkinView()
    {
        $user = Auth::user();
        $today = Carbon::today()->format('Y-m-d');
        $now = Carbon::now();

        // ช่วงเวลาที่อนุญาตให้ลงเวลา
        $earliestCheckIn = Carbon::today()->setTimeFromTimeString($this->earliestCheckInTime);
        $standardCheckIn = Carbon::today()->setTimeFromTimeString($this->standardCheckInTime);
        $standardCheckOut = Carbon::today()->setTimeFromTimeString($this->standardCheckOutTime);

        $canCheckIn = $now->gte($earliestCheckIn);
        $isCheckInLate = $now->gt($standardCheckIn);
        $canCheckOut = $now->gte($standardCheckOut);

        // ตรวจสอบประวัติการลงเวลาของวันนี้
        $todayAttendance = Attendance::with('leaveRequest.leaveType')
            ->where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        // ตรวจสอบว่าวันนี้เป็นวันลาที่ได้รับอนุมัติหรือไม่
        $isLeaveToday = $todayAttendance && $todayAttendance->status === 'leave';

        // ประวัติการลงเวลาย้อนหลัง 7 วันล่าสุดของตนเอง
        $recentAttendances = Attendance::with('leaveRequest.leaveType')
            ->where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->limit(7)
            ->get();

        return view('attendances.checkin', compact(
            'todayAttendance',
            'isLeaveToday',
            'recentAttendances',
            'today',
            'canCheckIn',
            'isCheckInLate',
            'canCheckOut'
        ));
    }

    /**
     * บันทึกเวลาเข้างาน (Check-in Process)
     */
    public function checkIn(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today()->format('Y-m-d');
        $now = Carbon::now();

        // ตรวจสอบว่าวันนี้เป็นวันลาหรือไม่
        $existing = Attendance::where('user_id', $user->id)->where('date', $today)->first();
        if ($existing && $existing->status === 'leave') {
            return back()->with('info', 'วันนี้คุณได้รับอนุมัติการลาแล้ว ไม่จำเป็นต้องลงเวลาเข้างาน');
        }

        // ตรวจสอบว่าเคย Check-in วันนี้ไปแล้วหรือไม่
        if ($existing && !empty($existing->check_in)) {
            return back()->with('warning', 'คุณได้ทำการบันทึกเวลาเข้างานของวันนี้ไปแล้วเมื่อ ' . $existing->check_in);
        }

        // ตรวจสอบว่าถึงเวลาเปิดให้ลงเวลาเข้างานหรือยัง (เปิดล่วงหน้า 30 นาที: 08:30 น.)
        $earliestCheckIn = Carbon::today()->setTimeFromTimeString($this->earliestCheckInTime);
        if ($now->lt($earliestCheckIn)) {
            return back()->with('warning', 'ยังไม่ถึงช่วงเวลาเปิดลงเวลาเข้างาน ระบบเปิดให้บันทึกเวลาเข้างานตั้งแต่เวลา 08:30 น. เป็นต้นไป');
        }

        // ประเมินสถานะ: 08:30 - 09:00:59 = ตรงเวลา, หลัง 09:00 = มาสาย
        $deadline = Carbon::today()->setTimeFromTimeString($this->standardCheckInTime);
        $status = $now->lte($deadline) ? 'on_time' : 'late';

        Attendance::updateOrCreate(
            [
                'user_id' => $user->id,
                'date' => $today,
            ],
            [
                'check_in' => $now->format('H:i:s'),
                'status' => $status,
                'notes' => $status === 'late' ? 'เข้างานสาย' : 'เข้างานตรงเวลา',
            ]
        );

        $statusText = $status === 'on_time' ? 'ตรงเวลา' : 'มาสาย';
        return back()->with('success', "บันทึกเวลาเข้างานสำเร็จ: {$now->format('H:i:s')} น. (สถานะ: {$statusText})");
    }

    /**
     * บันทึกเวลาเลิกงาน (Check-out Process)
     */
    public function checkOut(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today()->format('Y-m-d');
        $now = Carbon::now();

        $attendance = Attendance::where('user_id', $user->id)->where('date', $today)->first();

        if (!$attendance || empty($attendance->check_in)) {
            return back()->with('error', 'คุณยังไม่ได้บันทึกเวลาเข้างานของวันนี้ กรุณาลงเวลาเข้างานก่อน');
        }

        if (!empty($attendance->check_out)) {
            return back()->with('info', 'คุณได้บันทึกเวลาเลิกงานของวันนี้ไปแล้วเมื่อ ' . $attendance->check_out);
        }

        // ตรวจสอบว่าถึงเวลาเลิกงานแล้วหรือยัง (17:00 น. เป็นต้นไป)
        $standardCheckOut = Carbon::today()->setTimeFromTimeString($this->standardCheckOutTime);
        if ($now->lt($standardCheckOut)) {
            return back()->with('warning', 'ยังไม่ถึงเวลาเลิกงาน สามารถบันทึกเวลาออกงานได้ตั้งแต่เวลา 17:00 น. เป็นต้นไป');
        }

        $attendance->update([
            'check_out' => $now->format('H:i:s'),
        ]);

        return back()->with('success', "บันทึกเวลาเลิกงานสำเร็จ: {$now->format('H:i:s')} น. ขอให้เดินทางกลับโดยสวัสดิภาพ");
    }

    /**
     * ดูประวัติการลงเวลาทำงานของตนเอง (My Attendance History)
     */
    public function myHistory(Request $request)
    {
        $user = Auth::user();
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));

        $query = Attendance::with('leaveRequest.leaveType')
            ->where('user_id', $user->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month);

        $perPage = in_array((int) $request->input('per_page'), [5, 10, 15, 25, 50]) ? (int) $request->input('per_page') : 15;
        $attendances = $query->orderBy('date', 'desc')->paginate($perPage)->withQueryString();

        // สรุปสถิติประจำเดือนที่เลือก
        $totalDays = Attendance::where('user_id', $user->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->count();

        $onTimeCount = Attendance::where('user_id', $user->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->where('status', 'on_time')
            ->count();

        $lateCount = Attendance::where('user_id', $user->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->where('status', 'late')
            ->count();

        $leaveCount = Attendance::where('user_id', $user->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->where('status', 'leave')
            ->count();

        return view('attendances.my-history', compact(
            'attendances',
            'month',
            'year',
            'perPage',
            'totalDays',
            'onTimeCount',
            'lateCount',
            'leaveCount'
        ));
    }

    /**
     * สรุปรายงานการเข้างานทั้งหมดสำหรับ Admin & HR (Attendance Report)
     */
    public function report(Request $request)
    {
        $data = $this->getReportData($request);
        return view('attendances.report', $data);
    }

    /**
     * ออกรายงานสรุปเวลาทำงานในรูปแบบเอกสารทางการ (Corporate PDF / Print Report)
     */
    public function printReport(Request $request)
    {
        $data = $this->getReportData($request);
        return view('attendances.print-report', $data);
    }

    /**
     * รวบรวมข้อมูลสถิติและการลงเวลาสำหรับออกรายงาน
     */
    protected function getReportData(Request $request): array
    {
        $date = $request->input('date', Carbon::today()->format('Y-m-d'));
        $departmentId = $request->input('department_id');
        $status = $request->input('status');

        // ดึงพนักงาน active ทั้งหมดตามเงื่อนไขแผนก
        $usersQuery = User::with(['department', 'position'])
            ->where('status', 'active');

        if (!empty($departmentId)) {
            $usersQuery->where('department_id', $departmentId);
        }

        $allUsers = $usersQuery->orderBy('emp_code')->get();
        $allUserIds = $allUsers->pluck('id')->toArray();

        // ดึงข้อมูลการลงเวลาเฉพาะพนักงานในเงื่อนไขการค้นหา ของวันที่เลือก
        $attendances = Attendance::with(['user.department', 'user.position', 'leaveRequest.leaveType'])
            ->whereIn('user_id', $allUserIds)
            ->where('date', $date)
            ->get()
            ->keyBy('user_id');

        // รวมข้อมูลพนักงานทุกคนกับการลงเวลา (ถ้าไม่มีข้อมูลการลงเวลา ให้ถือเป็น 'absent' = ยังไม่ลงเวลา / ขาดงาน)
        $allReportData = $allUsers->map(function ($emp) use ($attendances) {
            $att = $attendances->get($emp->id);
            return [
                'user' => $emp,
                'attendance' => $att,
                'status' => $att ? $att->status : 'absent',
                'check_in' => $att ? $att->check_in : null,
                'check_out' => $att ? $att->check_out : null,
                'notes' => $att ? $att->notes : 'ยังไม่ลงเวลา',
            ];
        });

        // สรุปสถิติประจำวันที่เลือก (นับจากรายการพนักงานตามเงื่อนไขแผนกอย่างแม่นยำ 100%)
        $totalEmployees = $allReportData->count();
        $onTimeCount = $allReportData->where('status', 'on_time')->count();
        $lateCount = $allReportData->where('status', 'late')->count();
        $attendedCount = $onTimeCount + $lateCount;
        $leaveCount = $allReportData->where('status', 'leave')->count();
        $absentCount = $allReportData->where('status', 'absent')->count();

        // กรองตาม status ถ้ามีการเลือก (สำหรับแสดงข้อมูลในตาราง)
        $reportData = $allReportData;
        if (!empty($status)) {
            $reportData = $reportData->where('status', $status);
        }

        $departments = Department::where('is_active', true)->orderBy('name')->get();

        return compact(
            'reportData',
            'departments',
            'date',
            'departmentId',
            'status',
            'totalEmployees',
            'attendedCount',
            'onTimeCount',
            'lateCount',
            'leaveCount',
            'absentCount'
        );
    }

    /**
     * ส่งออกไฟล์รายงานสรุปเวลาทำงานประจำเดือน (Monthly Payroll CSV Export) สำหรับส่งฝ่ายบัญชี
     */
    public function exportMonthlySummaryCsv(Request $request)
    {
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));
        $departmentId = $request->input('department_id');

        $fileName = "attendance_payroll_summary_{$year}_{$month}.csv";

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename={$fileName}",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        // คำนวณจำนวนวันทำงานตามปฏิทินในเดือนนั้น (ไม่รวมเสาร์-อาทิตย์ และวันหยุดบริษัท)
        $startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        $holidayDates = \App\Models\CompanyHoliday::whereBetween('holiday_date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->orWhere('is_recurring', true)
            ->pluck('holiday_date')
            ->map(fn($d) => Carbon::parse($d)->format('m-d'))
            ->toArray();

        $calendarWorkDays = 0;
        $curr = $startOfMonth->copy();
        while ($curr->lte($endOfMonth)) {
            $isWeekend = $curr->isSaturday() || $curr->isSunday();
            $isHoliday = in_array($curr->format('m-d'), $holidayDates);
            if (!$isWeekend && !$isHoliday) {
                $calendarWorkDays++;
            }
            $curr->addDay();
        }

        $callback = function () use ($year, $month, $departmentId, $calendarWorkDays) {
            $file = fopen('php://output', 'w');
            // ใส่ UTF-8 BOM เพื่อให้ Microsoft Excel เปิดภาษาไทยได้ถูกต้อง 100%
            fputs($file, "\xEF\xBB\xBF");

            // Header คอลัมน์สำหรับฝ่ายบัญชี
            fputcsv($file, [
                'ลำดับ',
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

            $usersQuery = User::with(['department', 'position'])
                ->where('status', 'active');

            if (!empty($departmentId)) {
                $usersQuery->where('department_id', $departmentId);
            }

            $employees = $usersQuery->orderBy('emp_code')->get();

            foreach ($employees as $index => $emp) {
                // ดึงข้อมูลการลงเวลาของพนักงานในเดือนนี้
                $attendances = Attendance::where('user_id', $emp->id)
                    ->whereYear('date', $year)
                    ->whereMonth('date', $month)
                    ->get();

                $actualWorkDays = $attendances->whereIn('status', ['on_time', 'late'])->count();
                $lateCount = $attendances->where('status', 'late')->count();
                $absentCount = $attendances->where('status', 'absent')->count();

                // สรุปวันลาแต่ละประเภทที่ได้รับการอนุมัติ
                $leaveRequests = \App\Models\LeaveRequest::with('leaveType')
                    ->where('user_id', $emp->id)
                    ->where('status', 'approved')
                    ->whereYear('start_date', $year)
                    ->whereMonth('start_date', $month)
                    ->get();

                $sickLeave = 0;
                $personalLeave = 0;
                $annualLeave = 0;
                $otherLeave = 0;

                foreach ($leaveRequests as $lr) {
                    $typeName = $lr->leaveType->name ?? '';
                    if (str_contains($typeName, 'ป่วย')) {
                        $sickLeave += $lr->days_count;
                    } elseif (str_contains($typeName, 'กิจ')) {
                        $personalLeave += $lr->days_count;
                    } elseif (str_contains($typeName, 'พักร้อน') || str_contains($typeName, 'ประจำปี')) {
                        $annualLeave += $lr->days_count;
                    } else {
                        $otherLeave += $lr->days_count;
                    }
                }

                // สรุปชั่วโมง OT ที่ได้รับการอนุมัติ
                $totalOtHours = \App\Models\Overtime::where('user_id', $emp->id)
                    ->whereYear('date', $year)
                    ->whereMonth('date', $month)
                    ->where('status', 'approved')
                    ->sum('hours');

                fputcsv($file, [
                    $index + 1,
                    $emp->emp_code,
                    $emp->name,
                    $emp->department->name ?? '-',
                    $emp->position->name ?? '-',
                    $calendarWorkDays,
                    $actualWorkDays,
                    $lateCount,
                    $absentCount,
                    $sickLeave,
                    $personalLeave,
                    $annualLeave,
                    $otherLeave,
                    number_format($totalOtHours, 1),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
