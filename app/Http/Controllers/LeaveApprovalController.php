<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Department;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LeaveApprovalController extends Controller
{
    /**
     * ศูนย์รวมคำขอลาสำหรับผู้อนุมัติ (Admin & HR)
     */
    public function index(Request $request)
    {
        $status = $request->input('status', 'pending');
        $departmentId = $request->input('department_id');
        $search = $request->input('search');

        $query = LeaveRequest::with(['user.department', 'user.position', 'leaveType', 'approver']);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if (!empty($departmentId)) {
            $query->whereHas('user', function ($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        if (!empty($search)) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('emp_code', 'like', "%{$search}%");
            });
        }

        $perPage = (int) $request->input('per_page', 10);
        if (!in_array($perPage, [5, 10, 25, 50])) {
            $perPage = 10;
        }

        $leaveRequests = $query->orderBy('created_at', 'desc')->paginate($perPage)->withQueryString();
        $departments = Department::orderBy('name')->get();

        // สรุปสถิติภาพรวม
        $pendingCount = LeaveRequest::where('status', 'pending')->count();
        $approvedCount = LeaveRequest::where('status', 'approved')->count();
        $rejectedCount = LeaveRequest::where('status', 'rejected')->count();

        return view('leaves.approvals', compact(
            'leaveRequests',
            'departments',
            'status',
            'departmentId',
            'search',
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'perPage'
        ));
    }

    /**
     * อนุมัติคำขอลา (Approve Leave Request)
     * - ตัดยอดวันลาใน leave_balances
     * - ซิงค์สถานะเข้า attendances ล่วงหน้าเป็น 'leave'
     */
    public function approve(Request $request, LeaveRequest $leaveRequest)
    {
        // ป้องกันไม่ให้พนักงานหรือ HR อนุมัติคำขอลาของตนเอง (Self-Approval)
        if ($leaveRequest->user_id === Auth::id()) {
            return back()->with('error', 'คุณไม่สามารถอนุมัติคำขอลาของตนเองได้ ต้องให้ผู้ดูแลระบบ (Admin) เป็นผู้อนุมัติ');
        }

        $request->validate([
            'remark' => ['nullable', 'string', 'max:500'],
        ]);

        if ($leaveRequest->status === 'approved') {
            return back()->with('info', 'คำขอนี้ได้รับการอนุมัติไปแล้ว');
        }

        DB::transaction(function () use ($request, $leaveRequest) {
            $currentYear = Carbon::parse($leaveRequest->start_date)->year;

            // 1. อัปเดตสถานะคำขอลา
            $leaveRequest->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'remark' => $request->input('remark'),
            ]);

            // 2. หักยอดวันลาใน leave_balances
            $balance = LeaveBalance::firstOrCreate(
                [
                    'user_id' => $leaveRequest->user_id,
                    'leave_type_id' => $leaveRequest->leave_type_id,
                    'year' => $currentYear,
                ],
                [
                    'total_days' => $leaveRequest->leaveType->default_days ?? 0,
                    'used_days' => 0.0,
                    'remaining_days' => $leaveRequest->leaveType->default_days ?? 0,
                ]
            );

            $newUsedDays = $balance->used_days + $leaveRequest->days_count;
            $newRemainingDays = max(0, $balance->total_days - $newUsedDays);

            $balance->update([
                'used_days' => $newUsedDays,
                'remaining_days' => $newRemainingDays,
            ]);

            // 3. จุดเชื่อมโยงสำคัญ: ซิงค์การลงเวลา attendances ล่วงหน้า
            // สร้างสถานะ 'leave' ให้กับพนักงานในทุกวันที่อยู่ในช่วงลา
            $startDate = Carbon::parse($leaveRequest->start_date);
            $endDate = Carbon::parse($leaveRequest->end_date);
            $period = CarbonPeriod::create($startDate, $endDate);

            foreach ($period as $date) {
                Attendance::updateOrCreate(
                    [
                        'user_id' => $leaveRequest->user_id,
                        'date' => $date->format('Y-m-d'),
                    ],
                    [
                        'status' => 'leave',
                        'leave_request_id' => $leaveRequest->id,
                        'notes' => 'อนุมัติการลา: ' . ($leaveRequest->leaveType->name ?? 'ลางาน'),
                        'hr_id' => Auth::id(),
                    ]
                );
            }
        });

        return back()->with('success', "อนุมัติคำขอลาของ {$leaveRequest->user->name} เรียบร้อยแล้ว (ตัดยอดวันลา {$leaveRequest->days_count} วัน และซิงค์ระบบลงเวลาอัตโนมัติ)");
    }

    /**
     * ปฏิเสธคำขอลา (Reject Leave Request)
     */
    public function reject(Request $request, LeaveRequest $leaveRequest)
    {
        // ป้องกันไม่ให้พนักงานหรือ HR ปฏิเสธคำขอลาของตนเอง (ต้องให้ Admin ดำเนินการ)
        if ($leaveRequest->user_id === Auth::id()) {
            return back()->with('error', 'คุณไม่สามารถปฏิเสธคำขอลาของตนเองได้ ต้องให้ผู้ดูแลระบบ (Admin) เป็นผู้ดำเนินการ');
        }

        $request->validate([
            'remark' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($request, $leaveRequest) {
            // หากคำขอนี้เคยได้รับอนุมัติมาก่อน แล้วถูกเปลี่ยนเป็นปฏิเสธ ให้คืนยอดวันลาและลบ attendance วันลาออก
            if ($leaveRequest->status === 'approved') {
                $currentYear = Carbon::parse($leaveRequest->start_date)->year;
                $balance = LeaveBalance::where('user_id', $leaveRequest->user_id)
                    ->where('leave_type_id', $leaveRequest->leave_type_id)
                    ->where('year', $currentYear)
                    ->first();

                if ($balance) {
                    $newUsedDays = max(0, $balance->used_days - $leaveRequest->days_count);
                    $newRemainingDays = min($balance->total_days, $balance->total_days - $newUsedDays);
                    $balance->update([
                        'used_days' => $newUsedDays,
                        'remaining_days' => $newRemainingDays,
                    ]);
                }

                // ลบการบันทึกสถานะ leave ออกจาก attendance
                Attendance::where('leave_request_id', $leaveRequest->id)->delete();
            }

            // อัปเดตสถานะเป็น rejected
            $leaveRequest->update([
                'status' => 'rejected',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'remark' => $request->input('remark'),
            ]);
        });

        return back()->with('success', "ปฏิเสธคำขอลาของ {$leaveRequest->user->name} เรียบร้อยแล้ว");
    }
}
