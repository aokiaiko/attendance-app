<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceBreak;
use Carbon\Carbon;

class AttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_attendance_detail_has_user_name()
    {
        Carbon::setTestNow(Carbon::parse('2026-06-10 09:00:00','Asia/Tokyo'));

        $user = User::factory()->create();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' =>  '2026-06-10',
            'clock_in' =>  '09:00:00',
            'clock_out' => '18:00:00',
            'status' => Attendance::STATUS_DONE,
        ]);

        $response = $this->actingAs($user)->get("/attendance/detail/{$attendance->id}");
        $response->assertStatus(200);

        $response->assertSee($user->name); 

        Carbon::setTestNow();
    }

    public function test_attendance_detail_displays_selected_date()
    {
        Carbon::setTestNow(Carbon::parse('2026-06-10 09:00:00','Asia/Tokyo'));

        $user = User::factory()->create();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' =>  '2026-06-10',
            'clock_in' =>  '09:00:00',
            'clock_out' => '18:00:00',
            'status' => Attendance::STATUS_DONE,
        ]);

        $response = $this->actingAs($user)->get("/attendance/detail/{$attendance->id}");
        $response->assertStatus(200);

        $response->assertSee('2026年'); 
        $response->assertSee('6月10日'); 

        Carbon::setTestNow();
    }


    public function test_attendance_detail_displays_correct_clock_in_and_out_times()
    {
        Carbon::setTestNow(Carbon::parse('2026-06-10 09:00:00','Asia/Tokyo'));

        $user = User::factory()->create();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' =>  '2026-06-10',
            'clock_in' =>  '09:00:00',
            'clock_out' => '18:00:00',
            'status' => Attendance::STATUS_DONE,
        ]);

        $response = $this->actingAs($user)->get("/attendance/detail/{$attendance->id}");
        $response->assertStatus(200);

        $response->assertSee('09:00'); 
        $response->assertSee('18:00'); 

        Carbon::setTestNow();
    }

    public function test_attendance_detail_displays_correct_break_start_and_end_times()
    {
        Carbon::setTestNow(Carbon::parse('2026-06-10 09:00:00','Asia/Tokyo'));

        $user = User::factory()->create();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' =>  '2026-06-10',
            'clock_in' =>  '09:00:00',
            'clock_out' => '18:00:00',
            'status' => Attendance::STATUS_DONE,
        ]);

        AttendanceBreak::create([
            'attendance_id' => $attendance->id, 
            'break_start' => '10:00:00',
            'break_end'     => '11:00:00',
        ]);

        $response = $this->actingAs($user)->get("/attendance/detail/{$attendance->id}");
        $response->assertStatus(200);

        $response->assertSee('10:00'); 
        $response->assertSee('11:00'); 

        Carbon::setTestNow();
    }




}
