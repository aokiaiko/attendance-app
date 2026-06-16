<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
   use HasFactory;

   protected $fillable = [
       'user_id',
       'work_date',
       'clock_in',
       'clock_out',
       'status',
       'note'
   ];

   protected $casts = [
       'work_date' => 'date',
       'clock_in' => 'datetime',
       'clock_out' => 'datetime',
   ];

   const STATUS_OFF = 0;      
   const STATUS_WORKING = 1;  
   const STATUS_BREAK = 2;    
   const STATUS_DONE = 3;    

   public function user()
   {
      return $this->belongsTo(User::class);
   }

   public function breaks()
   {
      return $this->hasMany(AttendanceBreak::class);
   }

   public function corrections()
   {
      return $this->hasMany(StampCorrectionRequest::class);
   }

   public function pendingCorrection()
   {
      return $this->hasOne(StampCorrectionRequest::class)
          ->where('status', 0);
   }

   public function getBreakTimeAttribute()
   {
      $seconds = $this->breaks->sum(function ($break) {

         if (!$break->break_start || !$break->break_end) {
            return 0;
         }

         return strtotime($break->break_end) - strtotime($break->break_start);
      });

      $hours = floor($seconds / 3600);
      $minutes = floor(($seconds % 3600) / 60);

      return sprintf('%d:%02d', $hours, $minutes);
   }

   public function getWorkTimeAttribute()
   {
      if (!$this->clock_in || !$this->clock_out) {
        return '';
      }

      $workSeconds = strtotime($this->clock_out) - strtotime($this->clock_in);

      $breakSeconds = $this->breaks->sum(function ($break) {
            if (!$break->break_start || !$break->break_end) {
               return 0;
            }

            return strtotime($break->break_end) - strtotime($break->break_start);
      });

      $total = $workSeconds - $breakSeconds;

      $hours = floor($total / 3600);
      $minutes = floor(($total % 3600) / 60);

      return sprintf('%d:%02d', $hours, $minutes);
   }
}
