<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalaryEmp;

class SalaryController extends Controller
{
    public function show()
    {
        // $user = \Illuminate\Support\Facades\Auth::user();
        // $salaryData = $user->salaryEmp;
        
        // $baseSalary = $salaryData ? $salaryData->salary : 0;
        // $daysInMonth = \Carbon\Carbon::now()->daysInMonth;
        
        // $dailyWage = 0;
        // if ($daysInMonth > 0 && $baseSalary > 0) {
        //     $dailyWage = $baseSalary / $daysInMonth;
        // }

        // return view('salary.salary_show', compact('baseSalary', 'daysInMonth', 'dailyWage'));
        return view('salary.salary_show');
    }
}
