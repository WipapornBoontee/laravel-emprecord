<?php

namespace App\Http\Controllers;

use App\Models\CompanyHoliday;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CompanyHolidayController extends Controller
{
    /**
     * Display a listing of company holidays.
     */
    public function index(Request $request)
    {
        $year = $request->input('year', date('Y'));

        $holidays = CompanyHoliday::whereYear('holiday_date', $year)
            ->orWhere(function ($q) {
                $q->where('is_recurring', true);
            })
            ->orderBy('holiday_date', 'asc')
            ->get();

        return view('settings.holidays.index', compact('holidays', 'year'));
    }

    /**
     * Store a newly created company holiday.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'holiday_date' => 'required|date|unique:company_holidays,holiday_date',
            'is_recurring' => 'nullable|boolean',
        ], [
            'name.required' => 'กรุณาระบุชื่อวันหยุด',
            'holiday_date.required' => 'กรุณาระบุวันที่หยุด',
            'holiday_date.unique' => 'วันที่นี้ถูกกำหนดเป็นวันหยุดไปแล้ว',
        ]);

        CompanyHoliday::create([
            'name' => trim($request->name),
            'holiday_date' => $request->holiday_date,
            'is_recurring' => $request->has('is_recurring'),
        ]);

        return back()->with('success', 'เพิ่มวันหยุดนักขัตฤกษ์/วันหยุดบริษัทสำเร็จเรียบร้อย');
    }

    /**
     * Remove the specified company holiday.
     */
    public function destroy(CompanyHoliday $holiday)
    {
        $holiday->delete();

        return back()->with('success', 'ลบรายการวันหยุดเรียบร้อยแล้ว');
    }
}
