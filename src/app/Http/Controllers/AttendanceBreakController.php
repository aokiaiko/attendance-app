<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\AttendanceBreak;
use App\Models\Attendance;


class AttendanceBreakController extends Controller
{
    public function breakStart()
    {
        $user = auth()->user();
        $today = now()->toDateString();

        $attendance = $user->attendances()
          ->whereDate('work_date', $today) 
          ->where('status', Attendance::STATUS_WORKING) 
          ->whereNull('clock_out') 
          ->first();
        
        if (!$attendance) { 
            return redirect()->route('attendance.create');
        }

        $activeBreak = $attendance->breaks()
          ->whereNull('break_end')
          ->exists();

        if ($activeBreak) {
            return redirect()->route('attendance.create');
        }

        AttendanceBreak::create([
         'attendance_id' => $attendance->id,
         'break_start' => now(),
        ]);

        $attendance->update([
          'status' => Attendance::STATUS_BREAK,
        ]);

         return redirect()->route('attendance.create');
    }

     public function breakEnd()
    {
        $user = auth()->user();
        $today = now()->toDateString();

        $attendance = $user->attendances()
          ->whereDate('work_date', $today) 
          ->where('status', Attendance::STATUS_BREAK) 
          ->whereNull('clock_out') 
          ->first();

        if (!$attendance) {
          return redirect()->route('attendance.create');
        }

        $break = $attendance->breaks()
          ->whereNull('break_end')
          ->latest()
          ->first();

        if (!$break) {
          return redirect()->route('attendance.create');
        }

        $break->update([
          'break_end' => now(),
        ]);

        $attendance->update([
          'status' => Attendance::STATUS_WORKING,
        ]);

        return redirect()->route('attendance.create');

    }

    

}
