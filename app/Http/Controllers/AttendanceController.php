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
     * เวลาเข้างานมาตรฐาน (ก่อนหรือเท่ากับเวลานี้ถือว่าตรงเวลา)
     */
    protected string $standardCheckInTime = '09:00:59';

    /**
     * หน้าบันทึกเวลาเข้า-ออกงาน (Check-in / Check-out View)
     */
    public function checkinView()
    {
        $user = Auth::user();
        $today = Carbon::today()->format('Y-m-d');

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
            'today'
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

        // ประเมินสถานะ: ก่อนหรือเท่ากับ 09:00:59 = ตรงเวลา, หลัง 09:00 = มาสาย
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

        $departments = Department::orderBy('name')->get();

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
}
