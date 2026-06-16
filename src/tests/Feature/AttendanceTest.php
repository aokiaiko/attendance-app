<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceBreak;
use Carbon\Carbon;


class AttendanceTest extends TestCase
{
    use RefreshDatabase;
   
    public function test_attendance_page_displays_current_date()
    {
        Carbon::setTestNow(Carbon::parse('2026-06-10 09:00:00','Asia/Tokyo'));

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertStatus(200);
        $response->assertSee('2026年6月10日(水)');
        $response->assertSee('09:00');
        

        Carbon::setTestNow();
    }

    public function test_attendance_status_is_off_duty()
    {
        Carbon::setTestNow(Carbon::parse('2026-06-10 09:00:00','Asia/Tokyo'));

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertStatus(200);
        $response->assertSee('勤務外');

        Carbon::setTestNow();
    }

    public function test_attendance_status_is_working()
    {
        Carbon::setTestNow(Carbon::parse('2026-06-10 09:00:00','Asia/Tokyo'));

        $user = User::factory()->create();

        Attendance::create([
           'user_id'   => $user->id,
           'work_date' => Carbon::now('Asia/Tokyo')->toDateString(), 
           'clock_in'  => Carbon::now('Asia/Tokyo'),
           'clock_out' => null,
           'status'    => Attendance::STATUS_WORKING,
        ]);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertStatus(200);
        $response->assertSee('出勤中');

         Carbon::setTestNow();
    }

    public function test_attendance_status_is_breaking()
    {
        Carbon::setTestNow(Carbon::parse('2026-06-10 09:00:00','Asia/Tokyo'));

        $user = User::factory()->create();

        Attendance::create([
            'user_id' => $user->id,
            'work_date' =>  Carbon::now('Asia/Tokyo')->toDateString(),
            'clock_in' =>  Carbon::now('Asia/Tokyo'),
            'clock_out' => null,
            'status' => Attendance::STATUS_BREAK,
        ]);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertStatus(200);
        $response->assertSee('休憩中');

         Carbon::setTestNow();
    }

    public function test_attendance_status_is_done()
    {
        Carbon::setTestNow(Carbon::parse('2026-06-10 09:00:00','Asia/Tokyo'));

        $user = User::factory()->create();

        Attendance::create([
            'user_id' => $user->id,
            'work_date' =>  Carbon::now('Asia/Tokyo')->toDateString(),
            'clock_in' =>  Carbon::now('Asia/Tokyo'),
            'clock_out' => Carbon::now('Asia/Tokyo'),
            'status' => Attendance::STATUS_DONE,
        ]);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertStatus(200);
        $response->assertSee('退勤済');

         Carbon::setTestNow();
    }

    public function test_user_can_clock_in()
    {
        Carbon::setTestNow(Carbon::parse('2026-06-10 09:00:00','Asia/Tokyo'));

        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/attendance');
        $response->assertRedirect('/attendance'); 


        $showResponse = $this->actingAs($user)->get('/attendance');
        $showResponse->assertStatus(200);
        $showResponse->assertSee('出勤');

        $actionResponse = $this->actingAs($user)->post('/attendance');
        $actionResponse->assertRedirect('/attendance'); 

        $showResponse = $this->actingAs($user)->get('/attendance');
        $showResponse->assertStatus(200);
        $showResponse->assertSee('出勤中');

        Carbon::setTestNow();
    }

    public function test_attendance_page_displays_off_duty()
    {
        Carbon::setTestNow(Carbon::parse('2026-06-10 09:00:00','Asia/Tokyo'));

        $user = User::factory()->create();

        Attendance::create([
            'user_id' => $user->id,
            'work_date' =>  Carbon::now('Asia/Tokyo')->toDateString(),
            'clock_in' =>  Carbon::now('Asia/Tokyo'),
            'clock_out' => Carbon::now('Asia/Tokyo'),
            'status' => Attendance::STATUS_DONE,
        ]);

        $response = $this->actingAs($user)->get('/attendance');
        $response->assertStatus(200);

        $response->assertSee('退勤済');
        $response->assertDontSee('出勤');

        Carbon::setTestNow();
    }

    public function test_attendance_list_page_displays_clock_in_time()
    {
        Carbon::setTestNow(Carbon::parse('2026-06-10 09:00:00','Asia/Tokyo'));

        $user = User::factory()->create();

        Attendance::create([
           'user_id'   => $user->id,
           'work_date' => Carbon::now('Asia/Tokyo')->toDateString(), 
           'clock_in'  => Carbon::now('Asia/Tokyo'),
           'clock_out' => null,
           'status'    => Attendance::STATUS_WORKING,
        ]);

        $showResponse = $this->actingAs($user)->get('/attendance/list');
        $showResponse->assertStatus(200);

        $showResponse->assertSee('09:00'); 

        Carbon::setTestNow();
    }

    public function test_user_can_break_start()
    {
        Carbon::setTestNow(Carbon::parse('2026-06-10 09:00:00','Asia/Tokyo'));

        $user = User::factory()->create();

        Attendance::create([
           'user_id'   => $user->id,
           'work_date' => Carbon::now('Asia/Tokyo')->toDateString(), 
           'clock_in'  => Carbon::now('Asia/Tokyo'),
           'clock_out' => null,
           'status'    => Attendance::STATUS_WORKING,
        ]);

        $response = $this->actingAs($user)->get('/attendance');
        $response->assertStatus(200);
        $response->assertSee('休憩入');

        $actionResponse = $this->actingAs($user)->post('/attendance/break-start');
        $actionResponse->assertRedirect('/attendance'); 

        $showResponse = $this->actingAs($user)->get('/attendance');
        $showResponse->assertStatus(200);
        $showResponse->assertSee('休憩中');

        Carbon::setTestNow();
    }

    public function test_attendance_page_displays_break_start_multiple_times()
    {
        Carbon::setTestNow(Carbon::parse('2026-06-10 09:00:00','Asia/Tokyo'));

        $user = User::factory()->create();

        Attendance::create([
           'user_id'   => $user->id,
           'work_date' => Carbon::now('Asia/Tokyo')->toDateString(), 
           'clock_in'  => Carbon::now('Asia/Tokyo'),
           'clock_out' => null,
           'status'    => Attendance::STATUS_WORKING,
        ]);

        $breakStartResponse = $this->actingAs($user)->post('/attendance/break-start');
        $breakStartResponse->assertRedirect('/attendance');

        $breakEndResponse = $this->actingAs($user)->post('/attendance/break-end');
        $breakEndResponse->assertRedirect('/attendance');
   
        $response = $this->actingAs($user)->get('/attendance');
        $response->assertStatus(200);
        $response->assertSee('休憩入');

        Carbon::setTestNow();
    }

    public function test_user_can_break_end()
    {
        Carbon::setTestNow(Carbon::parse('2026-06-10 09:00:00','Asia/Tokyo'));

        $user = User::factory()->create();

        Attendance::create([
           'user_id'   => $user->id,
           'work_date' => Carbon::now('Asia/Tokyo')->toDateString(), 
           'clock_in'  => Carbon::now('Asia/Tokyo'),
           'clock_out' => null,
           'status'    => Attendance::STATUS_WORKING,
        ]);

        $breakEndResponse = $this->actingAs($user)->post('/attendance/break-start');
        $breakEndResponse->assertRedirect('/attendance');

        $response = $this->actingAs($user)->get('/attendance');
        $response->assertStatus(200);
        $response->assertSee('休憩戻');

        $breakEndResponse = $this->actingAs($user)->post('/attendance/break-end');
        $breakEndResponse->assertRedirect('/attendance');

        $response = $this->actingAs($user)->get('/attendance');
        $response->assertStatus(200);
        $response->assertSee('出勤中');

        Carbon::setTestNow();
    }

    public function test_attendance_page_displays_break_end_multiple_times()
    {
        Carbon::setTestNow(Carbon::parse('2026-06-10 09:00:00','Asia/Tokyo'));

        $user = User::factory()->create();

        Attendance::create([
           'user_id'   => $user->id,
           'work_date' => Carbon::now('Asia/Tokyo')->toDateString(), 
           'clock_in'  => Carbon::now('Asia/Tokyo'),
           'clock_out' => null,
           'status'    => Attendance::STATUS_WORKING,
        ]);

        $breakStartResponse = $this->actingAs($user)->post('/attendance/break-start');
        $breakStartResponse->assertRedirect('/attendance');

        $breakEndResponse = $this->actingAs($user)->post('/attendance/break-end');
        $breakEndResponse->assertRedirect('/attendance');

        $breakStartResponse = $this->actingAs($user)->post('/attendance/break-start');
        $breakStartResponse->assertRedirect('/attendance');

        $response = $this->actingAs($user)->get('/attendance');
        $response->assertStatus(200);
        $response->assertSee('休憩戻');

        Carbon::setTestNow();
    }

    public function test_attendance_list_page_displays_break_time()
    {
        Carbon::setTestNow(Carbon::parse('2026-06-10 09:00:00','Asia/Tokyo'));

        $user = User::factory()->create();

        Attendance::create([
           'user_id'   => $user->id,
           'work_date' => Carbon::now('Asia/Tokyo')->toDateString(), 
           'clock_in'  => Carbon::now('Asia/Tokyo'),
           'clock_out' => null,
           'status'    => Attendance::STATUS_WORKING,
        ]);

        $breakStartResponse = $this->actingAs($user)->post('/attendance/break-start');
        $breakStartResponse->assertRedirect('/attendance');

        Carbon::setTestNow(Carbon::parse('2026-06-10 10:00:00','Asia/Tokyo'));

        $breakEndResponse = $this->actingAs($user)->post('/attendance/break-end');
        $breakEndResponse->assertRedirect('/attendance');

        $showResponse = $this->actingAs($user)->get('/attendance/list');
        $showResponse->assertStatus(200);

        $showResponse->assertSee('1:00'); 

         Carbon::setTestNow();
    }

    public function test_user_can_clock_out()
    {
        Carbon::setTestNow(Carbon::parse('2026-06-10 09:00:00','Asia/Tokyo'));

        $user = User::factory()->create();

        Attendance::create([
           'user_id'   => $user->id,
           'work_date' => Carbon::now('Asia/Tokyo')->toDateString(), 
           'clock_in'  => Carbon::now('Asia/Tokyo'),
           'clock_out' => null,
           'status'    => Attendance::STATUS_WORKING,
        ]);

        $response = $this->actingAs($user)->get('/attendance');
        $response->assertStatus(200);
        $response->assertSee('退勤');

        $actionResponse = $this->actingAs($user)->post('/attendance/clock-out');
        $actionResponse->assertRedirect('/attendance'); 

        $showResponse = $this->actingAs($user)->get('/attendance');
        $showResponse->assertStatus(200);
        $showResponse->assertSee('退勤済');

        Carbon::setTestNow();
    }

    public function test_attendance_list_page_displays_clock_out()
    {
        Carbon::setTestNow(Carbon::parse('2026-06-10 09:00:00','Asia/Tokyo'));

        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/attendance');
        $response->assertRedirect('/attendance'); 

        Carbon::setTestNow(Carbon::parse('2026-06-10 10:00:00','Asia/Tokyo'));

        $actionResponse = $this->actingAs($user)->post('/attendance/clock-out');
        $actionResponse->assertRedirect('/attendance'); 

        $showResponse = $this->actingAs($user)->get('/attendance/list');
        $showResponse->assertStatus(200);

        $showResponse->assertSee('10:00'); 

        Carbon::setTestNow();
    }
}
