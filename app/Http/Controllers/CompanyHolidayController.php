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
        $search = trim($request->input('search', ''));
        $perPage = (int) $request->input('per_page', 10);
        if (!in_array($perPage, [5, 10, 25, 50])) {
            $perPage = 10;
        }

        $query = CompanyHoliday::where(function ($q) use ($year) {
                $q->whereYear('holiday_date', $year)
                  ->orWhere('is_recurring', true);
            });

        if (!empty($search)) {
            $query->where('name', 'like', "%{$search}%");
        }

        $holidays = $query->orderBy('holiday_date', 'asc')
            ->paginate($perPage)
            ->withQueryString();

        $totalHolidaysCount = CompanyHoliday::where(function ($q) use ($year) {
                $q->whereYear('holiday_date', $year)
                  ->orWhere('is_recurring', true);
            })->count();

        $recurringCount = CompanyHoliday::where('is_recurring', true)->count();

        return view('settings.holidays.index', compact('holidays', 'year', 'search', 'perPage', 'totalHolidaysCount', 'recurringCount'));
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
     * Update the specified company holiday.
     */
    public function update(Request $request, CompanyHoliday $holiday)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'holiday_date' => 'required|date|unique:company_holidays,holiday_date,' . $holiday->id,
            'is_recurring' => 'nullable|boolean',
        ], [
            'name.required' => 'กรุณาระบุชื่อวันหยุด',
            'holiday_date.required' => 'กรุณาระบุวันที่หยุด',
            'holiday_date.unique' => 'วันที่นี้ถูกกำหนดเป็นวันหยุดไปแล้ว',
        ]);

        $holiday->update([
            'name' => trim($request->name),
            'holiday_date' => $request->holiday_date,
            'is_recurring' => $request->has('is_recurring'),
        ]);

        return back()->with('success', 'แก้ไขข้อมูลวันหยุดเรียบร้อยแล้ว');
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
