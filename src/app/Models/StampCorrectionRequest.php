<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StampCorrectionRequest extends Model
{
    use HasFactory;

    protected $fillable = [
       'attendance_id',
       'requested_clock_in',
       'requested_clock_out',
       'note',
       'status',
       'approved_by',
       'approved_at',
    ];

    protected $casts = [
        'requested_clock_in' => 'datetime',
        'requested_clock_out' => 'datetime',
        'approved_at' => 'datetime'
    ];

    const STATUS_PENDING = 0;
    const STATUS_APPROVED = 1;
    const STATUS_REJECTED = 2;


    public function approver()
    {
       return $this->belongsTo(User::class,'approved_by');
    }

    public function attendance()
    {
       return $this->belongsTo(Attendance::class);
    }

    public function breaks()
    {
       return $this->hasMany(StampCorrectionRequestBreak::class);
    }
}
