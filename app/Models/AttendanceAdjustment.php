<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceAdjustment extends Model
{
    use HasFactory;

    protected $table = 'attendance_adjustments';

    protected $fillable = [
        'user_id',
        'attendance_id',
        'target_date',
        'requested_check_in',
        'requested_check_out',
        'reason',
        'attachment_url',
        'status',
        'approver_id',
        'approved_at',
        'reject_reason',
    ];

    protected $casts = [
        'target_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}
