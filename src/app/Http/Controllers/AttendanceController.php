<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Attendance;


class AttendanceController extends Controller
{
    public function create()
    {
        Carbon::setLocale('ja');
        $now = Carbon::now();

        $user = auth()->user();
        $today = $now->toDateString();

        $attendance = Attendance::where('user_id', $user->id)
          ->where('work_date', $today)
          ->first();

        return view('attendances.create',compact('now','attendance'));
    }

    public function index()
    {
        return view('attendances.index');
    }

    public function show()
    {
        return view('attendances.show');
    }

    public function clockIn()
    {
        $user = auth()->user();
        $today = now()->toDateString();

        if (Attendance::where('user_id', $user->id)
           ->where('work_date', $today)
           ->exists()) {
        return redirect('/attendance');
        }

        Attendance::create([
         'user_id' => $user->id,
         'work_date' => $today,
         'clock_in' => now(),
         'status' => 1 // 出勤中
        ]);

         return redirect('/attendance');
    }

    public function clockOut()
    {
        $user = auth()->user();
        $today = now()->toDateString();

        $attendance = Attendance::where('user_id', $user->id)
           ->where('work_date', $today)
           ->first();

        if (
          !$attendance ||
          $attendance->status !== Attendance::STATUS_WORKING ||
          $attendance->clock_out
        ) {
          return redirect('/attendance');
        }

        $attendance->update([
          'clock_out' => now(),
          'status' => Attendance::STATUS_DONE,
        ]);

        return redirect('/attendance');

    }

}   

