<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'ss_min_salary',
        'ss_percent',
        'ss_max_deduction',
        'tax_min_salary',
        'tax_percent',
    ];
}
