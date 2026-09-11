<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveType;
use App\Models\LeaveRequest;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LeaveController extends Controller
{
    /**
     * Show the form for creating a new leave request.
     */
    public function create()
    {
        // Get all available leave types from the database
        $leaveTypes = LeaveType::all();
        
        return view('apply-leave.apply_leave', compact('leaveTypes'));
    }

    /**
     * Store a newly created leave request in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
        ]);

        $start = Carbon::parse($request->start_date);
        $end = Carbon::parse($request->end_date);
        
        // Calculate total days (inclusive)
        $daysCount = $start->diffInDays($end) + 1;

        // Create the leave request
        LeaveRequest::create([
            'user_id' => Auth::id(),
            'leave_type_id' => $request->leave_type_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'days_count' => $daysCount,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        // Redirect back to dashboard with a success message
        return redirect()->route('dashboard')->with('success', 'ยื่นคำขอลาสำเร็จ กรุณารอการอนุมัติ');
    }
}
