<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Attendance;
use App\Http\Requests\AdminAttendanceRequest;
use Carbon\Carbon;

class AdminAttendanceController extends Controller
{
    public function index()
    {
        Carbon::setLocale('ja');

        $currentDate = request('date', now()->toDateString());

        $date = Carbon::parse($currentDate);

        $users = User::with(['attendances' => function ($query) use ($date) {
            $query->with('breaks')
              ->whereDate('work_date', $date);
        }])
        ->where('role', 'user')
        ->get()
        ->map(function ($user) { 
            $user->attendance = $user->attendances->first(); 
            return $user;
        });

        return view('admin.attendances.index',compact('users', 'date'));
    }

    public function show($attendanceId)
    {
        $attendance = Attendance::with('user', 'breaks','pendingCorrection.breaks')
                     ->findOrFail($attendanceId);

        return view('admin.attendances.show',compact('attendance'));
    }

    public function staffAttendances($userId)
    {
        Carbon::setLocale('ja');

        $user = User::findOrFail($userId);

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

        return view('admin.staffs.attendances',compact('user','calendar','month'));
    }

    public function update(AdminAttendanceRequest $request,$attendanceId)
    {
        $attendance = Attendance::findOrFail($attendanceId);
       
        $workDate =  $attendance->work_date->format('Y-m-d');

        $attendance->update([
           'clock_in' => $request->clock_in ? $workDate . ' ' . $request->clock_in : $attendance->clock_in,
           'clock_out' => $request->clock_out ? $workDate . ' ' . $request->clock_out : $attendance->clock_out,
           'note' => $request->note,
        ]);

        $breakStarts = $request->break_start ?? [];
        $breakEnds = $request->break_end ?? [];

        foreach ($breakStarts as $index => $breakStart) {
          $breakEnd = $breakEnds[$index] ?? null;

          if ($index < $attendance->breaks->count()) {
              $break = $attendance->breaks[$index];
              
              $attendance->breaks[$index]->update([
                 'break_start' =>  $breakStart ? $workDate . ' ' . $breakStart : $break->break_start,
                 'break_end' => $breakEnd ? $workDate . ' ' . $breakEnd : $break->break_end,
               ]);
           } elseif  ($breakStart && $breakEnd) {
                     $attendance->breaks()->create([
                        'break_start' => $workDate . ' ' . $breakStart,
                        'break_end' => $workDate . ' ' . $breakEnd,
                     ]);
           }
        }
        return redirect()->route('admin.attendance.show', $attendance->id);
        
    }


    public function exportCsv($userId)
    {
        $user = User::findOrFail($id);

        $attendances = Attendance::where('user_id', $userId)
            ->get();

        $fileName = "{$user->name}_attendance.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ];

        $callback = function () use ($attendances) {

            $file = fopen('php://output', 'w');

            fwrite($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                '日付',
                '出勤',
                '退勤',
            ]);

            foreach ($attendances as $attendance) {
                fputcsv($file, [
                    $attendance->work_date,
                    $attendance->clock_in,
                    $attendance->clock_out,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

}
