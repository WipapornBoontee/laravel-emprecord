<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyHoliday extends Model
{
    use HasFactory;

    protected $table = 'company_holidays';

    protected $fillable = [
        'name',
        'holiday_date',
        'is_recurring',
    ];

    protected $casts = [
        'holiday_date' => 'date',
        'is_recurring' => 'boolean',
    ];
}
