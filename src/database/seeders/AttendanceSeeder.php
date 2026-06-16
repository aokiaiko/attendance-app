<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceBreak;
use Carbon\Carbon;


class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::whereIn('email', [
          'test@test.com',
          'test2@test.com',
        ])->get();

        $months = [
        '2026-03',
        '2026-04',
        '2026-05',
        ];

        foreach ($users as $user)

         foreach ($months as $month) {
            $year = substr($month, 0, 4);
            $monthNumber = substr($month, 5, 2);

            $lastDay = date('t', strtotime($month . '-01'));



           for ($day = 1; $day <= $lastDay; $day++) {
              $date = sprintf('%s-%02d', $month, $day);

              if ($date >= now()->toDateString()) {
                 continue;
              }

              $week = date('w', strtotime($date));
              if ($week == 0 || $week == 6) {
                 continue;
              }

              $attendance=Attendance::create([
                'user_id' => $user->id,
                'work_date' => $date,
                'clock_in' => $date . ' 09:00:00',
                'clock_out' => $date .' 18:00:00',
                'status' => Attendance::STATUS_DONE,        
              ]);

              if ($day === 1) {
                 AttendanceBreak::create([
                   'attendance_id' => $attendance->id,
                   'break_start' => $date . ' 12:00:00',
                   'break_end' => $date . ' 12:45:00',
                 ]);

                 AttendanceBreak::create([
                   'attendance_id' => $attendance->id,
                   'break_start' => $date . ' 15:00:00',
                   'break_end' => $date . ' 15:15:00',
                 ]);

               } else {
                 AttendanceBreak::create([
                   'attendance_id' => $attendance->id,
                   'break_start' => $date . ' 12:00:00',
                   'break_end' => $date . ' 13:00:00',
                 ]);
               }
            
          }
        }
    }
}

