<?php

namespace App\Http\Controllers;

use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class LeaveController extends Controller
{
    /**
     * แสดงประวัติการลาของตนเอง (My Leave History)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $status = $request->input('status');
        $year = $request->input('year', date('Y'));

        $query = LeaveRequest::with(['leaveType', 'approver'])
            ->where('user_id', $user->id);

        if (!empty($status)) {
            $query->where('status', $status);
        }

        if (!empty($year)) {
            $query->whereYear('start_date', $year);
        }

        $perPage = in_array((int) $request->input('per_page'), [5, 10, 25, 50]) ? (int) $request->input('per_page') : 10;
        $leaveRequests = $query->orderBy('id', 'desc')->paginate($perPage)->withQueryString();

        // สรุปสถิติคำขอลาของตนเอง
        $pendingCount = LeaveRequest::where('user_id', $user->id)->where('status', 'pending')->count();
        $approvedCount = LeaveRequest::where('user_id', $user->id)->where('status', 'approved')->count();
        $rejectedCount = LeaveRequest::where('user_id', $user->id)->where('status', 'rejected')->count();

        return view('leaves.index', compact(
            'leaveRequests',
            'status',
            'year',
            'perPage',
            'pendingCount',
            'approvedCount',
            'rejectedCount'
        ));
    }

    /**
     * แสดงแบบฟอร์มยื่นคำขอลาใหม่ (Create Leave Request)
     */
    public function create()
    {
        $user = Auth::user();
        $currentYear = (int) date('Y');

        // ดึงประเภทการลาที่เปิดใช้งาน พร้อมยอดคงเหลือของพนักงานในปีนี้
        $leaveBalances = LeaveBalance::whereHas('leaveType', function ($query) {
                $query->where('is_active', true);
            })
            ->with('leaveType')
            ->where('user_id', $user->id)
            ->where('year', $currentYear)
            ->get();

        // หากยังไม่มีการจัดสรรยอดคงเหลือ ให้ดึง LeaveType ที่เปิดใช้งานทั้งหมด
        $leaveTypes = LeaveType::where('is_active', true)->get();

        // ดึงวันหยุดบริษัทสำหรับ JavaScript คำนวณวันลาจริงหน้าบ้าน
        $companyHolidays = \App\Models\CompanyHoliday::all(['name', 'holiday_date', 'is_recurring']);

        return view('leaves.create', compact('leaveBalances', 'leaveTypes', 'companyHolidays'));
    }

    /**
     * บันทึกคำขอลาใหม่ (Store Leave Request)
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $currentYear = (int) date('Y');

        $request->validate([
            'leave_type_id' => [
                'required',
                Rule::exists('leave_types', 'id')->where('is_active', true),
            ],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['required', 'string', 'max:1000'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ], [
            'leave_type_id.required' => 'กรุณาเลือกประเภทการลา',
            'leave_type_id.exists' => 'ประเภทการลาที่เลือกไม่ถูกต้อง หรือถูกปิดการใช้งานแล้ว',
            'start_date.required' => 'กรุณาระบุวันที่เริ่มต้น',
            'end_date.required' => 'กรุณาระบุวันที่สิ้นสุด',
            'end_date.after_or_equal' => 'วันที่สิ้นสุดต้องไม่น้อยกว่าวันที่เริ่มต้น',
            'reason.required' => 'กรุณาระบุเหตุผลการลา',
            'attachment.mimes' => 'เอกสารแนบต้องเป็นไฟล์รูปภาพ (JPG, PNG) หรือ PDF เท่านั้น',
            'attachment.max' => 'ขนาดเอกสารแนบต้องไม่เกิน 5MB',
        ]);

        $start = Carbon::parse($request->start_date);
        $end = Carbon::parse($request->end_date);

        // ดึงรายการวันหยุดนักขัตฤกษ์/วันหยุดบริษัทในช่วงเวลาที่ขอลา
        $holidayDates = \App\Models\CompanyHoliday::whereBetween('holiday_date', [$start->toDateString(), $end->toDateString()])
            ->orWhere('is_recurring', true)
            ->pluck('holiday_date')
            ->map(fn($d) => Carbon::parse($d)->format('m-d'))
            ->toArray();

        $daysCount = 0;
        $current = $start->copy();
        while ($current->lte($end)) {
            // ไม่นับวันเสาร์ และวันอาทิตย์
            $isWeekend = $current->isSaturday() || $current->isSunday();
            // ตรวจสอบว่าตรงกับวันหยุดนักขัตฤกษ์หรือไม่ (รวมถึง recurring)
            $isHoliday = in_array($current->format('m-d'), $holidayDates);

            if (!$isWeekend && !$isHoliday) {
                $daysCount++;
            }
            $current->addDay();
        }

        if ($daysCount === 0) {
            return back()
                ->withInput()
                ->withErrors([
                    'start_date' => 'ช่วงวันที่ที่เลือกตรงกับวันหยุดสุดสัปดาห์ (เสาร์-อาทิตย์) หรือวันหยุดนักขัตฤกษ์ทั้งหมด จึงไม่นับเป็นวันลาทำงาน',
                ]);
        }

        // ตรวจสอบสิทธิ์วันลาคงเหลือในตาราง leave_balances
        $balance = LeaveBalance::firstOrCreate(
            [
                'user_id' => $user->id,
                'leave_type_id' => $request->leave_type_id,
                'year' => $currentYear,
            ],
            [
                'total_days' => LeaveType::find($request->leave_type_id)->default_days ?? 0,
                'used_days' => 0.0,
                'remaining_days' => LeaveType::find($request->leave_type_id)->default_days ?? 0,
            ]
        );

        if ($balance->remaining_days < $daysCount) {
            return back()
                ->withInput()
                ->withErrors([
                    'leave_type_id' => "สิทธิ์วันลาคงเหลือไม่เพียงพอ (ขอลา {$daysCount} วัน แต่วันลาคงเหลือมี {$balance->remaining_days} วัน)",
                ]);
        }

        // ตรวจสอบไม่ให้ลาซ้ำซ้อนกับช่วงวันที่ที่เคยอนุมัติหรือรออนุมัติอยู่
        $overlapping = LeaveRequest::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved'])
            ->where(function ($q) use ($request) {
                $q->whereBetween('start_date', [$request->start_date, $request->end_date])
                    ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                    ->orWhere(function ($sub) use ($request) {
                        $sub->where('start_date', '<=', $request->start_date)
                            ->where('end_date', '>=', $request->end_date);
                    });
            })
            ->exists();

        if ($overlapping) {
            return back()
                ->withInput()
                ->withErrors([
                    'start_date' => 'ช่วงวันที่ที่ระบุ ซ้ำซ้อนกับคำขอลาเดิมที่รออนุมัติหรือได้รับการอนุมัติแล้ว',
                ]);
        }

        // จัดการอัปโหลดไฟล์ไปที่ Local Storage (public disk)
        $attachmentUrl = null;
        if ($request->hasFile('attachment')) {
            try {
                $path = $request->file('attachment')->store('leave_requests', 'public');
                $attachmentUrl = asset('storage/' . $path);
            } catch (\Exception $e) {
                return back()->withInput()->with('error', 'เกิดข้อผิดพลาดในการอัปโหลดไฟล์: ' . $e->getMessage());
            }
        }

        // สร้างคำขอลาใหม่
        LeaveRequest::create([
            'user_id' => $user->id,
            'leave_type_id' => $request->leave_type_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'days_count' => $daysCount,
            'reason' => $request->reason,
            'attachment_url' => $attachmentUrl,
            'status' => 'pending',
        ]);

        return redirect()->to(route('leaves.index', [], false))->with('success', 'ส่งคำขอลาสำเร็จ กรุณารอการพิจารณาอนุมัติจากผู้บังคับบัญชาหรือฝ่ายบุคคล');
    }

    /**
     * ยกเลิกคำขอลาที่ยังรอการอนุมัติ (Cancel Pending Leave Request)
     */
    public function cancel(LeaveRequest $leaveRequest)
    {
        $user = Auth::user();

        // ตรวจสอบว่าเป็นเจ้าของคำขอลา
        if ($leaveRequest->user_id !== $user->id) {
            abort(403, 'คุณไม่มีสิทธิ์ยกเลิกคำขอนี้');
        }

        // ยกเลิกได้เฉพาะคำขอที่ยัง pending เท่านั้น
        if ($leaveRequest->status !== 'pending') {
            return redirect()->to(route('leaves.index', [], false))->with('error', 'ไม่สามารถยกเลิกคำขอนี้ได้เนื่องจากได้รับการพิจารณาไปแล้ว');
        }

        $leaveRequest->delete();

        return redirect()->to(route('leaves.index', [], false))->with('success', 'ยกเลิกคำขอลาเรียบร้อยแล้ว');
    }

    /**
     * ตรวจสอบสิทธิ์วันลาคงเหลือของตนเอง (My Leave Balances)
     */
    public function balances()
    {
        $user = Auth::user();
        $currentYear = (int) date('Y');

        // ดึงข้อมูลสิทธิ์วันลาคงเหลือ
        $leaveBalances = LeaveBalance::with('leaveType')
            ->where('user_id', $user->id)
            ->where('year', $currentYear)
            ->get();

        // ประวัติการลาที่ได้รับอนุมัติแล้วในปีนี้
        $approvedLeaves = LeaveRequest::with('leaveType')
            ->where('user_id', $user->id)
            ->where('status', 'approved')
            ->whereYear('start_date', $currentYear)
            ->orderBy('start_date', 'desc')
            ->get();

        return view('leaves.balances', compact('leaveBalances', 'approvedLeaves', 'currentYear'));
    }
}
