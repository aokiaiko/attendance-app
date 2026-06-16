<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StampCorrectionRequest;
use App\Models\Attendance;
use App\Models\AttendanceBreak;

class AdminStampCorrectionRequestController extends Controller
{
    public function show($attendance_correct_request_id)
    {
        $approve = StampCorrectionRequest::with('attendance.user','breaks')
             ->findOrFail($attendance_correct_request_id);

        return view('admin.stamp_correction_requests.approve',compact('approve'));

    }

    public function approve($attendance_correct_request_id)
    {
        $approve = StampCorrectionRequest::with('attendance.breaks','breaks')
             ->findOrFail($attendance_correct_request_id);

        $approve->update([
            'status' => 1 ,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        $attendance = $approve->attendance;

        $attendance->update([
            'clock_in' => $approve->requested_clock_in,
            'clock_out' => $approve->requested_clock_out,
            'note' => $approve->note,
        ]);


        foreach ($approve->breaks as $index => $requestedBreak) {
           $attendanceBreak = $attendance->breaks[$index] ?? null;
        
           if ($attendanceBreak) {
              $attendanceBreak->update([
                'break_start' => $requestedBreak->requested_break_start,
                'break_end' => $requestedBreak->requested_break_end,
              ]);
           } else {
              $attendance->breaks()->create([ 
                'break_start' => $requestedBreak->requested_break_start, 
                'break_end' => $requestedBreak->requested_break_end,
              ]);
           }
        }

        return redirect()->route('stamp_correction_request.show',$approve->id);
      
      }
}
