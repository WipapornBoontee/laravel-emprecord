<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalaryEmp;

class SalaryController extends Controller
{
    public function show()
    {
        return view('salary.salary_show');
    }
}
