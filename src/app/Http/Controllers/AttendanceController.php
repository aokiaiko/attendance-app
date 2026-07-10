<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\StampCorrectionRequest;


class AttendanceController extends Controller
{
    public function create()
    {
        Carbon::setLocale('ja');
        $now = Carbon::now();

        $user = auth()->user();
        $today = $now->toDateString();

        $attendance = Attendance::where('user_id', $user->id)
          ->whereDate('work_date', $today)
          ->first();

        return view('attendances.create',compact('now','attendance'));
    }


    public function index()
    {
        Carbon::setLocale('ja');

        $user = auth()->user();

        $month = request('month') 
        ? Carbon::parse(request('month')) 
        : Carbon::now();


        $attendances = Attendance::with('breaks')
           ->where('user_id', $user->id)
           ->whereYear('work_date', $month->year)
           ->whereMonth('work_date', $month->month)
           ->get();

        $dates = [];

        $lastDay = $month->daysInMonth;

        for ($day = 1; $day <= $lastDay; $day++) {
          $dates[] = sprintf(
            '%04d-%02d-%02d',
             $month->year,
             $month->month,
             $day
          );
        }   

        $calendar = [];

        foreach ($dates as $date) {
           $attendanceData = null;

           foreach ($attendances as $attendance) {
              if ($attendance->work_date->format('Y-m-d') == $date) {
                  $attendanceData = $attendance;
                  break;
              }
           }

           $calendar[] = [
               'date' => \Carbon\Carbon::parse($date),
               'attendance' => $attendanceData,
           ];
        }

        return view('attendances.index',compact('calendar','month'));
    }


    public function show($attendanceId)
    {
        $attendance = Attendance::with('breaks','user')
          ->where('user_id', auth()->id())
          ->findOrFail($attendanceId);

        $pendingCorrection = StampCorrectionRequest::with('breaks','attendance')
          ->where('attendance_id', $attendanceId)
          ->where('status', 0)
          ->first();  

        return view('attendances.show', compact('attendance','pendingCorrection'));
    }


    public function clockIn()
    {
        $user = auth()->user();
        $today = now()->toDateString();

        if (Attendance::where('user_id', $user->id)
           ->whereDate('work_date', $today)
           ->exists()) {
        return redirect()->route('attendance.create');
        }

        Attendance::create([
         'user_id' => $user->id,
         'work_date' => $today,
         'clock_in' => now(),
         'status' => 1 // 出勤中
        ]);

         return redirect()->route('attendance.create');
    }


    public function clockOut()
    {
        $user = auth()->user();
        $today = now()->toDateString();

        $attendance = Attendance::where('user_id', $user->id)
           ->whereDate('work_date', $today)
           ->first();

        if (
          !$attendance ||
          $attendance->status !== Attendance::STATUS_WORKING ||
          $attendance->clock_out
        ) {
          return redirect()->route('attendance.create');
        }

        $attendance->update([
          'clock_out' => now(),
          'status' => Attendance::STATUS_DONE,
        ]);

        return redirect()->route('attendance.create');

    }

}   

