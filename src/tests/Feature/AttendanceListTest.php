<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceBreak;
use Carbon\Carbon;

class AttendanceListTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_see_attendance_list()
    {
        Carbon::setTestNow(Carbon::parse('2026-06-10 09:00:00','Asia/Tokyo'));

        $user = User::factory()->create();

        $attendance1 = Attendance::create([
            'user_id' => $user->id,
            'work_date' =>  '2026-06-10',
            'clock_in' =>  '09:00:00',
            'clock_out' => '18:00:00',
            'status' => Attendance::STATUS_DONE,
        ]);

        AttendanceBreak::create([
            'attendance_id' => $attendance1->id, 
            'break_start' => '10:00:00',
            'break_end'     => '11:00:00',
        ]);

         $attendance2 = Attendance::create([
            'user_id' => $user->id,
            'work_date' =>  '2026-06-11',
            'clock_in' =>  '10:00:00',
            'clock_out' => '19:00:00',
            'status' => Attendance::STATUS_DONE,
        ]);

        AttendanceBreak::create([
            'attendance_id' => $attendance2->id, 
            'break_start' => '12:00:00',
            'break_end'   => '13:00:00',
        ]);

        $showResponse = $this->actingAs($user)->get('/attendance/list');
        $showResponse->assertStatus(200);

        $showResponse->assertSee('06/10(水)'); 
        $showResponse->assertSee('09:00'); 
        $showResponse->assertSee('18:00'); 
        $showResponse->assertSee('1:00'); 
        $showResponse->assertSee('8:00'); 

        $showResponse->assertSee('06/11(木)'); 
        $showResponse->assertSee('09:00'); 
        $showResponse->assertSee('18:00'); 
        $showResponse->assertSee('1:00'); 
        $showResponse->assertSee('8:00'); 

        Carbon::setTestNow();
    }

    public function test_user_can_see_attendance_list_current_month()
    {
        Carbon::setTestNow(Carbon::parse('2026-06-10 09:00:00','Asia/Tokyo'));

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/attendance/list');
        $response->assertStatus(200);
        $response->assertSee('2026/06');

        Carbon::setTestNow();
    }

    public function test_user_can_see_attendance_list_before_month()
    {
        Carbon::setTestNow(Carbon::parse('2026-06-10 09:00:00','Asia/Tokyo'));

        $user = User::factory()->create();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' =>  '2026-05-01',
            'clock_in' =>  '09:00:00',
            'clock_out' => '18:00:00',
            'status' => Attendance::STATUS_DONE,
        ]);

        AttendanceBreak::create([
            'attendance_id' => $attendance->id, 
            'break_start' => '10:00:00',
            'break_end'     => '11:00:00',
        ]);

        $showResponse = $this->actingAs($user)->get('/attendance/list?month=2026-05');
        $showResponse->assertStatus(200);

        $showResponse->assertSee('05/01(金)'); 
        $showResponse->assertSee('09:00'); 
        $showResponse->assertSee('18:00'); 
        $showResponse->assertSee('1:00'); 
        $showResponse->assertSee('8:00'); 

        Carbon::setTestNow();
    }

    public function test_user_can_see_attendance_list_next_month()
    {
        Carbon::setTestNow(Carbon::parse('2026-05-01 09:00:00','Asia/Tokyo'));

        $user = User::factory()->create();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' =>  '2026-06-01',
            'clock_in' =>  '09:00:00',
            'clock_out' => '18:00:00',
            'status' => Attendance::STATUS_DONE,
        ]);

        AttendanceBreak::create([
            'attendance_id' => $attendance->id, 
            'break_start' => '10:00:00',
            'break_end'     => '11:00:00',
        ]);

        $showResponse = $this->actingAs($user)->get('/attendance/list?month=2026-06');
        $showResponse->assertStatus(200);

        $showResponse->assertSee('06/01(月)'); 
        $showResponse->assertSee('09:00'); 
        $showResponse->assertSee('18:00'); 
        $showResponse->assertSee('1:00'); 
        $showResponse->assertSee('8:00'); 

        Carbon::setTestNow();
    }

    public function test_user_transition_to_attendance_detail()
    {
        Carbon::setTestNow(Carbon::parse('2026-06-01 09:00:00','Asia/Tokyo'));

        $user = User::factory()->create();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' =>  '2026-06-01',
            'clock_in' =>  '09:00:00',
            'clock_out' => '18:00:00',
            'status' => Attendance::STATUS_DONE,
        ]);

        AttendanceBreak::create([
            'attendance_id' => $attendance->id, 
            'break_start' => '10:00:00',
            'break_end'     => '11:00:00',
        ]);

        $showResponse = $this->actingAs($user)->get("/attendance/detail/{$attendance->id}");
        $showResponse->assertStatus(200);

        Carbon::setTestNow();
    }
}
