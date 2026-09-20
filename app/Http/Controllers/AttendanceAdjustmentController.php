<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceAdjustment;
use App\Models\Department;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceAdjustmentController extends Controller
{
    /**
     * เวลาเข้างานมาตรฐาน (ก่อนหรือเท่ากับเวลานี้ถือว่าตรงเวลา)
     */
    protected string $standardCheckInTime = '09:00:59';

    /**
     * พนักงานยื่นคำขอปรับเวลาลงเวลาย้อนหลัง (Store Request)
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'target_date' => 'required|date|before_or_equal:today',
            'requested_check_in' => 'nullable|date_format:H:i',
            'requested_check_out' => 'nullable|date_format:H:i',
            'reason' => 'required|string|max:1000',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ], [
            'target_date.required' => 'กรุณาระบุวันที่ที่ต้องการขอปรับเวลา',
            'target_date.before_or_equal' => 'วันที่ขอปรับเวลาต้องไม่เป็นวันในอนาคต',
            'reason.required' => 'กรุณาระบุเหตุผลความจำเป็นในการขอปรับเวลา',
            'attachment.mimes' => 'เอกสารแนบต้องเป็นรูปภาพ (JPG, PNG) หรือ PDF เท่านั้น',
            'attachment.max' => 'เอกสารแนบต้องมีขนาดไม่เกิน 5MB',
        ]);

        if (empty($request->requested_check_in) && empty($request->requested_check_out)) {
            return back()->with('error', 'กรุณาระบุเวลาเข้างานหรือเวลาเลิกงานอย่างน้อยหนึ่งรายการ');
        }

        // ตรวจสอบว่ามีคำขอรอดำเนินการของวันนั้นอยู่แล้วหรือไม่
        $existingPending = AttendanceAdjustment::where('user_id', $user->id)
            ->where('target_date', $request->target_date)
            ->where('status', 'pending')
            ->exists();

        if ($existingPending) {
            return back()->with('error', 'คุณมีคำขอปรับเวลาของวันที่ดังกล่าวที่อยู่ระหว่างการรออนุมัติแล้ว');
        }

        // ค้นหา record การลงเวลาเดิมของวันนั้น (ถ้ามี)
        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $request->target_date)
            ->first();

        // จัดการอัปโหลดไฟล์แนบ (ถ้ามี)
        $attachmentUrl = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = 'adjustment_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/adjustments'), $filename);
            $attachmentUrl = asset('uploads/adjustments/' . $filename);
        }

        AttendanceAdjustment::create([
            'user_id' => $user->id,
            'attendance_id' => $attendance?->id,
            'target_date' => $request->target_date,
            'requested_check_in' => $request->requested_check_in ? $request->requested_check_in . ':00' : null,
            'requested_check_out' => $request->requested_check_out ? $request->requested_check_out . ':00' : null,
            'reason' => trim($request->reason),
            'attachment_url' => $attachmentUrl,
            'status' => 'pending',
        ]);

        return back()->with('success', 'ส่งคำขอปรับเวลาลงเวลาย้อนหลังเรียบร้อยแล้ว กรุณารอหัวหน้าหรือฝ่ายบุคคลพิจารณา');
    }

    /**
     * หน้ารายการพิจารณาอนุมัติคำขอปรับเวลา สำหรับ Admin และ HR (Index)
     */
    public function index(Request $request)
    {
        $status = $request->input('status', 'pending');
        $departmentId = $request->input('department_id');
        $search = $request->input('search');
        $perPage = in_array((int) $request->input('per_page'), [5, 10, 25, 50]) ? (int) $request->input('per_page') : 10;

        $query = AttendanceAdjustment::with(['user.department', 'user.position', 'approver', 'attendance'])
            ->orderBy('target_date', 'desc')
            ->orderBy('id', 'desc');

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

        $adjustments = $query->paginate($perPage)->withQueryString();

        // สถิติยอดรวม
        $pendingCount = AttendanceAdjustment::where('status', 'pending')->count();
        $approvedCount = AttendanceAdjustment::where('status', 'approved')->count();
        $rejectedCount = AttendanceAdjustment::where('status', 'rejected')->count();

        $departments = Department::where('is_active', true)->orderBy('name')->get();

        return view('attendances.adjustments.index', compact(
            'adjustments',
            'status',
            'departmentId',
            'search',
            'perPage',
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'departments'
        ));
    }

    /**
     * อนุมัติคำขอปรับเวลาลงเวลา (Approve)
     */
    public function approve(Request $request, AttendanceAdjustment $adjustment)
    {
        if ($adjustment->status !== 'pending') {
            return back()->with('error', 'คำขอนี้ได้รับการพิจารณาไปแล้ว');
        }

        // อนุมัติตนเองไม่ได้
        if ($adjustment->user_id === Auth::id()) {
            return back()->with('error', 'คุณไม่สามารถอนุมัติคำขอปรับเวลาของตนเองได้');
        }

        // คำนวณสถานะการเข้างาน on_time หรือ late
        $status = 'on_time';
        $checkInTime = $adjustment->requested_check_in ?? ($adjustment->attendance?->check_in);
        if ($checkInTime && $checkInTime > $this->standardCheckInTime) {
            $status = 'late';
        }

        // ค้นหาหรือสร้าง Attendance record ของวันนั้น
        $attendance = Attendance::updateOrCreate(
            [
                'user_id' => $adjustment->user_id,
                'date' => $adjustment->target_date->toDateString(),
            ],
            [
                'check_in' => $adjustment->requested_check_in ?? ($adjustment->attendance?->check_in),
                'check_out' => $adjustment->requested_check_out ?? ($adjustment->attendance?->check_out),
                'status' => $status,
                'notes' => 'ปรับปรุงเวลาตามคำขอ #' . $adjustment->id . ' (' . Auth::user()->name . ')',
            ]
        );

        // อัปเดตสถานะของคำขอ
        $adjustment->update([
            'attendance_id' => $attendance->id,
            'status' => 'approved',
            'approver_id' => Auth::id(),
            'approved_at' => Carbon::now(),
        ]);

        return back()->with('success', "อนุมัติคำขอปรับเวลาของ {$adjustment->user->name} สำเร็จเรียบร้อย ระบบได้ซิงค์ข้อมูลลงเวลาแล้ว");
    }

    /**
     * ปฏิเสธคำขอปรับเวลาลงเวลา (Reject)
     */
    public function reject(Request $request, AttendanceAdjustment $adjustment)
    {
        if ($adjustment->status !== 'pending') {
            return back()->with('error', 'คำขอนี้ได้รับการพิจารณาไปแล้ว');
        }

        $request->validate([
            'reject_reason' => 'nullable|string|max:500',
        ]);

        $adjustment->update([
            'status' => 'rejected',
            'approver_id' => Auth::id(),
            'approved_at' => Carbon::now(),
            'reject_reason' => $request->reject_reason ?? 'ไม่อนุมัติคำขอปรับปรุงเวลา',
        ]);

        return back()->with('success', "ปฏิเสธคำขอปรับเวลาของ {$adjustment->user->name} เรียบร้อยแล้ว");
    }
}
