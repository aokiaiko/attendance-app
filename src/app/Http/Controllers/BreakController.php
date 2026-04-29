<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\AttendanceBreak;
use App\Models\Attendance;


class BreakController extends Controller
{
    public function breakStart()
    {
        $user = auth()->user();
        $today = now()->toDateString();

        $attendance = $user->attendances()
          ->where('work_date', $today) 
          ->where('status', Attendance::STATUS_WORKING) 
          ->whereNull('clock_out') 
          ->first();
        
        if (!$attendance) { 
            return redirect('/attendance');
        }

        $activeBreak = $attendance->breaks()
          ->whereNull('break_end')
          ->exists();

        if ($activeBreak) {
            return redirect('/attendance');
        }

        AttendanceBreak::create([
         'attendance_id' => $attendance->id,
         'break_start' => now(),
        ]);

        $attendance->update([
          'status' => Attendance::STATUS_BREAK,
        ]);

         return redirect('/attendance');
    }

     public function breakEnd()
    {
        $user = auth()->user();
        $today = now()->toDateString();

        $attendance = $user->attendances()
          ->where('work_date', $today) 
          ->where('status', Attendance::STATUS_BREAK) 
          ->whereNull('clock_out') 
          ->first();

        if (!$attendance) {
          return redirect('/attendance');
        }

        $break = $attendance->breaks()
          ->whereNull('break_end')
          ->latest()
          ->first();

        if (!$break) {
          return redirect('/attendance');
        }

        $break->update([
          'break_end' => now(),
        ]);

        $attendance->update([
          'status' => Attendance::STATUS_WORKING,
        ]);

        return redirect('/attendance');

    }

}
