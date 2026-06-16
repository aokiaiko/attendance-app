<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceBreak;
use App\Models\StampCorrectionRequest;
use Carbon\Carbon;

class StampCorrectionRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_attendance_detail_validation_fails_if_clock_in_is_after_clock_out()
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

        $actionResponse = $this->actingAs($user)->post("/attendance/detail/{$attendance->id}",[
            'clock_in'  => '19:00:00', 
            'clock_out' => '18:00:00',
            'note'                => '電車遅延のため',
        ]);

        $actionResponse->assertStatus(302);
        $actionResponse->assertSessionHasErrors([
             'work_time' => '出勤時間もしくは退勤時間が不適切な値です'
        ]);

        Carbon::setTestNow();
    }

    public function test_attendance_detail_validation_fails_if_break_start_is_after_clock_out()
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

        $actionResponse = $this->actingAs($user)->post("/attendance/detail/{$attendance->id}",[
            'clock_in'    => '09:00:00',
            'clock_out'   => '18:00:00',
            'break_start'  => ['10:00:00','19:00:00'], 
            'break_end' => ['11:00:00','11:00:00'],
            'note'                => '電車遅延のため',
        ]);

        $actionResponse->assertStatus(302);
        $actionResponse->assertSessionHasErrors([
             'break_time' => '休憩時間が不適切な値です'
        ]);

        Carbon::setTestNow();
    }

    public function test_attendance_detail_validation_fails_if_break_end_is_after_clock_out()
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

        $actionResponse = $this->actingAs($user)->post("/attendance/detail/{$attendance->id}",[
            'clock_in'    => '09:00:00',
            'clock_out'   => '18:00:00',
            'break_start'  => ['10:00:00','10:00:00'], 
            'break_end' => ['11:00:00','19:00:00'],
            'note'                => '電車遅延のため',
        ]);

        $actionResponse->assertStatus(302);
        $actionResponse->assertSessionHasErrors([
             'break_time' => '休憩時間もしくは退勤時間が不適切な値です'
        ]);

        Carbon::setTestNow();
    }

    public function test_attendance_detail_validation_fails_if_note_is_null()
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

        $actionResponse = $this->actingAs($user)->post("/attendance/detail/{$attendance->id}",[
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

    public function test_stamp_correction_request_store()
    {
        Carbon::setTestNow(Carbon::parse('2026-06-10 09:00:00','Asia/Tokyo'));

        $user = User::factory()->create();

        $admin = User::factory()->create([
            'role' => 'admin' 
        ]); 

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' =>  '2026-06-10',
            'clock_in' =>  '09:00:00',
            'clock_out' => '18:00:00',
            'status' => Attendance::STATUS_DONE,
        ]);

        $response = $this->actingAs($user)->post("/attendance/detail/{$attendance->id}",[
            'clock_in'  => '09:30:00', 
            'clock_out' => '18:30:00',
            'note'      => '電車遅延のため',
        ]);
        $response->assertStatus(302); 

        $request = StampCorrectionRequest::latest()->first();

        $listResponse = $this->actingAs($admin)->get('/stamp_correction_request/list');
        $listResponse->assertStatus(200);

        $listResponse->assertSee('承認待ち');
        $listResponse->assertSee( $user->name);
        $listResponse->assertSee('2026/06/10');
        $listResponse->assertSee('遅延のため');  
        $listResponse->assertSee('2026/06/10');

        $approveResponse = $this->actingAs($admin)->get("/stamp_correction_request/approve/{$request->id}");
        $approveResponse->assertStatus(200);

        $approveResponse->assertSee( $user->name);
        $approveResponse->assertSee('2026年');
        $approveResponse->assertSee('6月10日');
        $approveResponse->assertSee('09:30');
        $approveResponse->assertSee('18:30'); 
        $approveResponse->assertSee('遅延のため');  
       

        Carbon::setTestNow();
    }

    public function test_user_can_see_own_pending_requests_on_list()
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

        $response = $this->actingAs($user)->post("/attendance/detail/{$attendance1->id}",[
            'clock_in'  => '09:30:00', 
            'clock_out' => '18:30:00',
            'note'      => '電車遅延のため',
        ]);
        $response->assertStatus(302);

        $attendance2 = Attendance::create([
            'user_id' => $user->id,
            'work_date' =>  '2026-06-11',
            'clock_in' =>  '09:00:00',
            'clock_out' => '18:00:00',
            'status' => Attendance::STATUS_DONE,
        ]);

        $response = $this->actingAs($user)->post("/attendance/detail/{$attendance2->id}",[
            'clock_in'  => '10:30:00', 
            'clock_out' => '19:30:00',
            'note'      => '電車遅延のため',
        ]);
        $response->assertStatus(302);

        $listResponse = $this->actingAs($user)->get("/stamp_correction_request/list?status=pending");
        $listResponse->assertStatus(200);

        $listResponse->assertSee('承認待ち');
        $listResponse->assertSee( $user->name);
        $listResponse->assertSee('2026/06/10');
        $listResponse->assertSee('遅延のため');  
        $listResponse->assertSee('2026/06/10');

        $listResponse->assertSee('承認待ち');
        $listResponse->assertSee( $user->name);
        $listResponse->assertSee('2026/06/11');
        $listResponse->assertSee('遅延のため');  
        $listResponse->assertSee('2026/06/11');

        Carbon::setTestNow(); 
    }

    public function test_user_can_see_own_approved_requests_on_list()
    {
        Carbon::setTestNow(Carbon::parse('2026-06-10 09:00:00','Asia/Tokyo'));

        $user = User::factory()->create();
        
        $admin = User::factory()->create([
            'role' => 'admin' 
        ]);

        $attendance1 = Attendance::create([
            'user_id' => $user->id,
            'work_date' =>  '2026-06-10',
            'clock_in' =>  '09:00:00',
            'clock_out' => '18:00:00',
            'status' => Attendance::STATUS_DONE,
        ]);

        $response = $this->actingAs($user)->post("/attendance/detail/{$attendance1->id}",[
            'clock_in'  => '09:30:00', 
            'clock_out' => '18:30:00',
            'note'      => '電車遅延のため',
        ]);
        $response->assertStatus(302); 
        
        $attendance2 = Attendance::create([
            'user_id' => $user->id,
            'work_date' =>  '2026-06-11',
            'clock_in' =>  '10:00:00',
            'clock_out' => '19:00:00',
            'status' => Attendance::STATUS_DONE,
        ]);

        $response = $this->actingAs($user)->post("/attendance/detail/{$attendance2->id}",[
            'clock_in'  => '10:30:00', 
            'clock_out' => '19:30:00',
            'note'      => '電車遅延のため',
        ]);
        $response->assertStatus(302); 

        $requests = StampCorrectionRequest::all();
        $request10 = $requests[0]; 
        $request11 = $requests[1]; 

        $approveAction10 = $this->actingAs($admin)->patch("/stamp_correction_request/approve/{$request10->id}");
        $approveAction10->assertStatus(302);

        $approveAction11 = $this->actingAs($admin)->patch("/stamp_correction_request/approve/{$request11->id}");
        $approveAction11->assertStatus(302);

        $listResponse = $this->actingAs($user)->get('/stamp_correction_request/list?status=approved');
        $listResponse->assertStatus(200);

        $listResponse->assertSee('承認済み');
        $listResponse->assertSee( $user->name);
        $listResponse->assertSee('2026/06/10');
        $listResponse->assertSee('遅延のため');  
        $listResponse->assertSee('2026/06/10');

        $listResponse->assertSee('承認済み');
        $listResponse->assertSee( $user->name);
        $listResponse->assertSee('2026/06/11');
        $listResponse->assertSee('遅延のため');  
        $listResponse->assertSee('2026/06/11');

        Carbon::setTestNow(); 
    }

    public function test_user_can_transition_from_stamp_correction_list_to_attendance_detail()
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

        $response = $this->actingAs($user)->post("/attendance/detail/{$attendance->id}",[
            'clock_in'  => '09:30:00', 
            'clock_out' => '18:30:00',
            'note'      => '電車遅延のため',
        ]);
        $response->assertStatus(302); 

        $listResponse = $this->actingAs($user)->get('/stamp_correction_request/list');
        $listResponse->assertStatus(200);

        $actionResponse = $this->actingAs($user)->get("/attendance/detail/{$attendance->id}");
        $actionResponse->assertStatus(200);

        Carbon::setTestNow(); 
    }
}
