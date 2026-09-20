<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Overtime extends Model
{

    use HasFactory;

    protected $fillable = [
        'id',
        'user_id',
        'date',
        'start_time',
        'end_time',
        'break_minutes',
        'ot_type',
        'hours',
        'description',
        'early_checkout_reason',
        'hr_reject_reason',
        'status',
        'hr_approved_at',
        'hr_id',
        'created_at',
        'updated_at',
    ];

    public function getOtTypeLabelAttribute(): string
    {
        return match($this->ot_type) {
            'holiday' => 'OT วันหยุด (2.0x)',
            'holiday_ot' => 'OT วันหยุดล่วงเวลา (3.0x)',
            default => 'OT วันทำงานปกติ (1.5x)',
        };
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hr()
    {
        return $this->belongsTo(User::class, 'hr_id');
    }

    // สร้างฟังก์ชันช่วยเหลือ (Helper Method) สำหรับคำนวณชั่วโมงทำงานจริง
    public function getWorkingHours()
    {
        // กรณีที่ข้อมูลถูกลบไปแล้ว
        if (!$this->exists) {
            return 0;
        }

        // หาสถิติการเข้า-ออกงานในวันนั้นๆ
        $attendance = Attendance::where('user_id', $this->user_id)
            ->whereDate('date', $this->date)
            ->first();

        if (!$attendance) {
            return 0;
        }

        // คำนวณเวลาทำงานจริง
        $checkIn  = \Carbon\Carbon::parse($attendance->check_in);
        $checkOut = \Carbon\Carbon::parse($attendance->check_out);
        $workingHours = $checkIn->diffInHours($checkOut);
        
        return $workingHours;
    }

}