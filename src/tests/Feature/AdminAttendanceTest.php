<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceBreak;
use App\Models\StampCorrectionRequest;
use Carbon\Carbon;

class AdminAttendanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_see_attendance_list()
    {
        Carbon::setTestNow(Carbon::parse('2026-06-10 09:00:00','Asia/Tokyo'));

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $user1 = User::factory()->create();
        $attendance = Attendance::create([
            'user_id' => $user1->id,
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

        $user2 = User::factory()->create();
        $attendance = Attendance::create([
            'user_id' => $user2->id,
            'work_date' =>  '2026-06-10',
            'clock_in' =>  '10:00:00',
            'clock_out' => '19:00:00',
            'status' => Attendance::STATUS_DONE,
        ]);

        AttendanceBreak::create([
            'attendance_id' => $attendance->id, 
            'break_start' => '12:00:00',
            'break_end'     => '13:00:00',
        ]);

        $showResponse = $this->actingAs($admin)->get('/admin/attendance/list');
        $showResponse->assertStatus(200);

        $showResponse->assertSee($user1->name); 
        $showResponse->assertSee('09:00'); 
        $showResponse->assertSee('18:00'); 
        $showResponse->assertSee('1:00'); 
        $showResponse->assertSee('8:00'); 

        $showResponse->assertSee($user2->name); 
        $showResponse->assertSee('10:00'); 
        $showResponse->assertSee('19:00'); 
        $showResponse->assertSee('1:00'); 
        $showResponse->assertSee('8:00'); 

        Carbon::setTestNow();
    }

    public function test_admin_attendance_page_displays_current_date()
    {
        Carbon::setTestNow(Carbon::parse('2026-06-10 09:00:00','Asia/Tokyo'));

        $user = User::factory()->create();

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

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

        $showResponse = $this->actingAs($admin)->get('/admin/attendance/list');
        $showResponse->assertStatus(200);

        $showResponse->assertSee('2026/06/10'); 

        Carbon::setTestNow();
    }

    public function test_admin_can_see_admin_attendance_list_before_day()
    {
        Carbon::setTestNow(Carbon::parse('2026-06-10 09:00:00','Asia/Tokyo'));

        $user = User::factory()->create();

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' =>  '2026-06-09',
            'clock_in' =>  '09:00:00',
            'clock_out' => '18:00:00',
            'status' => Attendance::STATUS_DONE,
        ]);

        AttendanceBreak::create([
            'attendance_id' => $attendance->id, 
            'break_start' => '10:00:00',
            'break_end'     => '11:00:00',
        ]);

        $showResponse = $this->actingAs($admin)->get("/admin/attendance/list?date=2026-06-09");
        $showResponse->assertStatus(200);

        $showResponse->assertSee($user->name); 
        $showResponse->assertSee('09:00'); 
        $showResponse->assertSee('18:00'); 
        $showResponse->assertSee('1:00'); 
        $showResponse->assertSee('8:00'); 

        Carbon::setTestNow();
    }

    public function test_admin_can_see_admin_attendance_list_after_day()
    {
        Carbon::setTestNow(Carbon::parse('2026-06-10 09:00:00','Asia/Tokyo'));

        $user = User::factory()->create();

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' =>  '2026-06-11',
            'clock_in' =>  '09:00:00',
            'clock_out' => '18:00:00',
            'status' => Attendance::STATUS_DONE,
        ]);

        AttendanceBreak::create([
            'attendance_id' => $attendance->id, 
            'break_start' => '10:00:00',
            'break_end'     => '11:00:00',
        ]);

        $showResponse = $this->actingAs($admin)->get("/admin/attendance/list?date=2026-06-11");
        $showResponse->assertStatus(200);

        $showResponse->assertSee($user->name); 
        $showResponse->assertSee('09:00'); 
        $showResponse->assertSee('18:00'); 
        $showResponse->assertSee('1:00'); 
        $showResponse->assertSee('8:00'); 

        Carbon::setTestNow();
    }

    public function test_admin_attendance_detail_validation_fails_if_clock_in_is_after_clock_out()
    {
        Carbon::setTestNow(Carbon::parse('2026-06-10 09:00:00','Asia/Tokyo'));

        $user = User::factory()->create();

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' =>  '2026-06-10',
            'clock_in' =>  '09:00:00',
            'clock_out' => '18:00:00',
            'status' => Attendance::STATUS_DONE,
        ]);

        $response = $this->actingAs($admin)->get("/admin/attendance/{$attendance->id}");
        $response->assertStatus(200);

        $actionResponse = $this->actingAs($admin)->patch("/attendance/{$attendance->id}",[
            'clock_in'  => '19:00:00', 
            'clock_out' => '18:00:00',
            'note'      => '電車遅延のため',
        ]);

        $actionResponse->assertStatus(302);
        $actionResponse->assertSessionHasErrors([
             'work_time' => '出勤時間もしくは退勤時間が不適切な値です'
        ]);

        Carbon::setTestNow();
    }

    public function test_admin_attendance_detail_validation_fails_if_break_start_is_after_clock_out()
    {
        Carbon::setTestNow(Carbon::parse('2026-06-10 09:00:00','Asia/Tokyo'));

        $user = User::factory()->create();

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

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

        $response = $this->actingAs($admin)->get("/admin/attendance/{$attendance->id}");
        $response->assertStatus(200);

        $actionResponse = $this->actingAs($admin)->patch("/attendance/{$attendance->id}",[
            'clock_in'    => '09:00:00',
            'clock_out'   => '18:00:00',
            'break_start'  => ['10:00:00','19:00:00'], 
            'break_end' => ['11:00:00','11:00:00'],
            'note'      => '電車遅延のため',
        ]);

        $actionResponse->assertStatus(302);
        $actionResponse->assertSessionHasErrors([
             'break_time.1' => '休憩2の時間が不適切な値です'
        ]);

        Carbon::setTestNow();
    }

    public function test_admin_attendance_detail_validation_fails_if_break_end_is_after_clock_out()
    {
        Carbon::setTestNow(Carbon::parse('2026-06-10 09:00:00','Asia/Tokyo'));

        $user = User::factory()->create();

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

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

        $response = $this->actingAs($admin)->get("/admin/attendance/{$attendance->id}");
        $response->assertStatus(200);

        $actionResponse = $this->actingAs($admin)->patch("/attendance/{$attendance->id}",[
            'clock_in'    => '09:00:00',
            'clock_out'   => '18:00:00',
            'break_start'  => ['10:00:00','10:00:00'], 
            'break_end' => ['11:00:00','19:00:00'],
            'note'      => '電車遅延のため',
        ]);

        $actionResponse->assertStatus(302);
        $actionResponse->assertSessionHasErrors([
             'break_time.1' => '休憩2の時間もしくは退勤時間が不適切な値です'
        ]);

        Carbon::setTestNow();
    }

    public function test_admin_attendance_detail_validation_fails_if_note_is_null()
    {
        Carbon::setTestNow(Carbon::parse('2026-06-10 09:00:00','Asia/Tokyo'));

        $user = User::factory()->create();

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' =>  '2026-06-10',
            'clock_in' =>  '09:00:00',
            'clock_out' => '18:00:00',
            'status' => Attendance::STATUS_DONE,
        ]);

        $response = $this->actingAs($admin)->get("/admin/attendance/{$attendance->id}");
        $response->assertStatus(200);

        $actionResponse = $this->actingAs($admin)->patch("/attendance/{$attendance->id}",[
            'clock_in'    => '09:00:00',
            'clock_out'   => '18:00:00',
            'break_start'  => ['10:00:00','10:00:00'], 
            'break_end' => ['11:00:00','11:00:00'],
            'note'                => null,
        ]);

        $actionResponse->assertStatus(302);
        $actionResponse->assertSessionHasErrors([
             'note' => '備考を記入してください'
        ]);

        Carbon::setTestNow();
    }

    public function test_admin_can_see_staff_list()
    {
        $users = User::factory()->count(2)->create();

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get('/admin/staff/list');
        $response->assertStatus(200);

        foreach ($users as $user) {
            $response->assertSee($user->name); 
            $response->assertSee($user->email); 
        }
    }

    public function test_admin_can_see_selected_user_attendance_list()
    {
        Carbon::setTestNow(Carbon::parse('2026-06-10 09:00:00','Asia/Tokyo'));

        $user = User::factory()->create();

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

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

        $response = $this->actingAs($admin)->get('/admin/staff/list');
        $response->assertStatus(200);

        $showResponse = $this->actingAs($admin)->get("/admin/attendance/staff/{$user->id}");
        $showResponse->assertStatus(200);

        $showResponse->assertSee('06/10(水)'); 
        $showResponse ->assertSee('09:00'); 
        $showResponse ->assertSee('18:00'); 
        $showResponse ->assertSee('1:00'); 
        $showResponse ->assertSee('8:00'); 

        Carbon::setTestNow();
    }

    public function test_admin_can_see_attendance_list_before_month()
    {
        Carbon::setTestNow(Carbon::parse('2026-06-10 09:00:00','Asia/Tokyo'));

        $user = User::factory()->create();

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

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

        $response = $this->actingAs($admin)->get("/admin/attendance/staff/{$user->id}");
        $response->assertStatus(200);

        $showResponse = $this->actingAs($admin)->get("/admin/attendance/staff/{$user->id}?month=2026-05");
        $showResponse->assertStatus(200);

        $showResponse->assertSee('05/01(金)'); 
        $showResponse->assertSee('09:00'); 
        $showResponse->assertSee('18:00'); 
        $showResponse->assertSee('1:00'); 
        $showResponse->assertSee('8:00'); 

        Carbon::setTestNow();
    }

    public function test_admin_can_see_attendance_list_next_month()
    {
        Carbon::setTestNow(Carbon::parse('2026-05-01 09:00:00','Asia/Tokyo'));

        $user = User::factory()->create();

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

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

        $response = $this->actingAs($admin)->get("/admin/attendance/staff/{$user->id}");
        $response->assertStatus(200);

        $showResponse = $this->actingAs($admin)->get("/admin/attendance/staff/{$user->id}?month=2026-06");
        $showResponse->assertStatus(200);

        $showResponse->assertSee('06/01(月)'); 
        $showResponse->assertSee('09:00'); 
        $showResponse->assertSee('18:00'); 
        $showResponse->assertSee('1:00'); 
        $showResponse->assertSee('8:00'); 

        Carbon::setTestNow();
    }


    public function test_admin_transition_to_attendance_detail()
    {
        Carbon::setTestNow(Carbon::parse('2026-06-01 09:00:00','Asia/Tokyo'));

        $user = User::factory()->create();

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

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

        $showResponse = $this->actingAs($admin)->get("/admin/attendance/{$attendance->id}");
        $showResponse->assertStatus(200);

        Carbon::setTestNow();
    }

    public function test_admin_stamp_correction_request_page_displays_pending()
    {
        Carbon::setTestNow(Carbon::parse('2026-06-01 09:00:00','Asia/Tokyo'));

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $user1 = User::factory()->create();
        $attendance1 = Attendance::create([
            'user_id' => $user1->id,
            'work_date' =>  '2026-06-10',
            'clock_in' =>  '09:00:00',
            'clock_out' => '18:00:00',
            'status' => Attendance::STATUS_DONE,
        ]);

        $response = $this->actingAs($user1)->post("/attendance/detail/{$attendance1->id}",[
            'clock_in'  => '09:30:00', 
            'clock_out' => '18:30:00',
            'note'      => '電車遅延のため',
        ]);
        $response->assertStatus(302); 

        $user2 = User::factory()->create();
        $attendance2 = Attendance::create([
            'user_id' => $user2->id,
            'work_date' =>  '2026-06-11',
            'clock_in' =>  '09:00:00',
            'clock_out' => '18:00:00',
            'status' => Attendance::STATUS_DONE,
        ]);

        $response = $this->actingAs($user2)->post("/attendance/detail/{$attendance2->id}",[
            'clock_in'  => '10:30:00', 
            'clock_out' => '19:30:00',
            'note'      => '電車遅延のため',
        ]);
        $response->assertStatus(302); 

        $listResponse = $this->actingAs($admin)->get("/stamp_correction_request/list?status=pending");
        $listResponse->assertStatus(200);

       
        $listResponse->assertSee('承認待ち');
        $listResponse->assertSee($user1->name);
        $listResponse->assertSee('2026/06/10');
        $listResponse->assertSee('遅延のため');  
        $listResponse->assertSee('2026/06/10');

        $listResponse->assertSee('承認待ち');
        $listResponse->assertSee($user2->name);
        $listResponse->assertSee('2026/06/11');
        $listResponse->assertSee('遅延のため');  
        $listResponse->assertSee('2026/06/11');
        
        Carbon::setTestNow();
    }

    public function test_admin_stamp_correction_request_page_displays_approved()
    {
        Carbon::setTestNow(Carbon::parse('2026-06-01 09:00:00','Asia/Tokyo'));

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $user1 = User::factory()->create();
        $attendance1 = Attendance::create([
            'user_id' => $user1->id,
            'work_date' =>  '2026-06-10',
            'clock_in' =>  '09:00:00',
            'clock_out' => '18:00:00',
            'status' => Attendance::STATUS_DONE,
        ]);

        $response = $this->actingAs($user1)->post("/attendance/detail/{$attendance1->id}",[
            'clock_in'  => '09:30:00', 
            'clock_out' => '18:30:00',
            'note'      => '電車遅延のため',
        ]);
        $response->assertStatus(302); 

        $user2 = User::factory()->create();
        $attendance2 = Attendance::create([
            'user_id' => $user2->id,
            'work_date' =>  '2026-06-11',
            'clock_in' =>  '09:00:00',
            'clock_out' => '18:00:00',
            'status' => Attendance::STATUS_DONE,
        ]);

        $response = $this->actingAs($user2)->post("/attendance/detail/{$attendance2->id}",[
            'clock_in'  => '10:30:00', 
            'clock_out' => '19:30:00',
            'note'      => '電車遅延のため',
        ]);
        $response->assertStatus(302); 

        $requests = StampCorrectionRequest::all();
        $request1 = $requests[0]; 
        $request2 = $requests[1];

        $approveAction10 = $this->actingAs($admin)->patch("/stamp_correction_request/approve/{$request1->id}");
        $approveAction10->assertStatus(302);

        $approveAction11 = $this->actingAs($admin)->patch("/stamp_correction_request/approve/{$request2->id}");
        $approveAction11->assertStatus(302);

        $listResponse = $this->actingAs($admin)->get("/stamp_correction_request/list?status=approved");
        $listResponse->assertStatus(200);

        $listResponse->assertSee('承認済み');
        $listResponse->assertSee($user1->name);
        $listResponse->assertSee('2026/06/10');
        $listResponse->assertSee('遅延のため');  
        $listResponse->assertSee('2026/06/10');

        $listResponse->assertSee('承認済み');
        $listResponse->assertSee($user2->name);
        $listResponse->assertSee('2026/06/11');
        $listResponse->assertSee('遅延のため');  
        $listResponse->assertSee('2026/06/11');
        
        Carbon::setTestNow();
    }

    public function test_admin_can_see_stamp_correction_request()
    {
        Carbon::setTestNow(Carbon::parse('2026-06-01 09:00:00','Asia/Tokyo'));

        $user = User::factory()->create();

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' =>  '2026-06-01',
            'clock_in' =>  '09:00:00',
            'clock_out' => '18:00:00',
            'status' => Attendance::STATUS_DONE,
        ]);

        AttendanceBreak::create([
            'attendance_id' => $attendance->id, 
            'break_start' => '13:00:00',
            'break_end'     => '14:00:00',
        ]);

        $response = $this->actingAs($user)->post("/attendance/detail/{$attendance->id}",[
            'clock_in'  => '10:30:00', 
            'clock_out' => '19:30:00',
            'break_start' => ['13:00:00', ''], 
            'break_end'   => ['14:00:00', ''],
            'note'      => '電車遅延のため',
        ]);
        $response->assertStatus(302); 

        $request = StampCorrectionRequest::latest()->first();

        $showResponse = $this->actingAs($admin)->get("/stamp_correction_request/approve/{$request->id}");
        $showResponse->assertSee($user->name);
        $showResponse->assertSee('2026年');
        $showResponse->assertSee('6月1日');
        $showResponse->assertSee('10:30');
        $showResponse->assertSee('19:30');
        $showResponse->assertSee('13:00');
        $showResponse->assertSee('14:00'); 
        $showResponse->assertSee('電車遅延のため');

         Carbon::setTestNow();
    }

    public function test_admin_can_approve_stamp_correction_request()
    {
        Carbon::setTestNow(Carbon::parse('2026-06-01 09:00:00','Asia/Tokyo'));

        $user = User::factory()->create();

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

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

        $response = $this->actingAs($user)->post("/attendance/detail/{$attendance->id}",[
            'clock_in'  => '10:30:00', 
            'clock_out' => '19:30:00',
            'note'      => '電車遅延のため',
        ]);
        $response->assertStatus(302); 

        $request = StampCorrectionRequest::latest()->first();

        $actionResponse = $this->actingAs($admin)->patch("/stamp_correction_request/approve/{$request->id}");
        $actionResponse->assertStatus(302);

        $updatedAttendance = $attendance->fresh();
        $this->assertEquals('10:30:00', $updatedAttendance->clock_in->format('H:i:s'));
        $this->assertEquals('19:30:00', $updatedAttendance->clock_out->format('H:i:s'));

        Carbon::setTestNow();
    }
}


    




















    