<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\StampCorrectionRequest;
use App\Models\StampCorrectionRequestBreak;
use App\Http\Requests\StoreStampCorrectionRequest;

class StampCorrectionRequestController extends Controller
{
    public function index()
    {
        $status = request('status', 'pending');
        $statusValue = $status === 'approved' ? 1 : 0;

        if (auth()->user()->role === 'admin'){
          $corrections=StampCorrectionRequest::with('attendance.user')
             ->where('status', $statusValue)
             ->get();

        return view('stamp_correction_requests.index',compact('corrections'));
        }

        $attendanceIds = Attendance::where('user_id', auth()->id())
           ->pluck('id');

        $corrections = StampCorrectionRequest::with('attendance.user')
           ->whereIn('attendance_id',$attendanceIds)
           ->where('status', $statusValue)
           ->get();  

        return view('stamp_correction_requests.index',compact('corrections'));
    }

    public function store(StoreStampCorrectionRequest $request,$id)
    {
       $attendance = Attendance::where('id', $id)
         ->where('user_id', auth()->id())
         ->firstOrFail();

        $workDate = $attendance->work_date->format('Y-m-d');

        $correction = StampCorrectionRequest::create([
          'user_id' => auth()->id(),
          'attendance_id' => $id,
          'requested_clock_in' =>  $request->clock_in ? $workDate . ' ' . $request->clock_in : $attendance->clock_in,
          'requested_clock_out' => $request->clock_out ?$workDate . ' ' . $request->clock_out : $attendance->clock_out,
          'note' => $request->note,
          'status' => 0,
        ]);

        $breakStarts = $request->break_start ?? [];
        $breakEnds = $request->break_end ?? [];

        foreach ($breakStarts as $index => $breakStart) {
            $breakEnd = $breakEnds[$index] ?? null;

            if ($breakStart && $breakEnd) {
                StampCorrectionRequestBreak::create([
                  'stamp_correction_request_id' => $correction->id,
                  'requested_break_start' => $workDate . ' ' . $breakStart,
                  'requested_break_end' => $workDate . ' ' . $breakEnd,
                ]);
            }
        }

        return redirect()->route('attendance.index');


    
    }
}
