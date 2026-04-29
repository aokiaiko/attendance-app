@extends('layouts.user')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendances/create.css') }}">
@endsection

@php
    use App\Models\Attendance;
@endphp

@section('content')
<div class="attendance-content">
    <div class="attendance-card">

        <div class="attendance-status">
            @if (!$attendance)
               勤務外
                 @elseif ($attendance->status === Attendance::STATUS_WORKING)
                  出勤中
                 @elseif ($attendance->status === Attendance::STATUS_BREAK)
                  休憩中
                 @elseif ($attendance->status === Attendance::STATUS_DONE)
                  退勤済
            @endif
        </div>

        <p class="attendance-date">
            {{ $now->translatedFormat('Y年n月j日（D）') }}
        </p>

        <p class="attendance-time">
            {{ $now->format('H:i') }}
        </p>

        <div class="attendance-actions">
          @if (!$attendance)
              <form  action=/attendance method=POST>
              @csrf
                <button class="attendance-button attendance-button--primary">
                  出勤
                </button>
              </form>

            @elseif ($attendance->status === Attendance::STATUS_WORKING)
              <div class="attendance-actions attendance-actions--double">
               <form  action=/attendance/clock-out method=POST>
               @csrf
                 <button class="attendance-button attendance-button--primary">
                   退勤
                 </button>
               </form>
               <form  action=/attendance/break-start method=POST>
               @csrf
                 <button class="attendance-button attendance-button--secondary">
                   休憩入
                 </button>
               </form>
              </div>
       
            @elseif ($attendance->status === Attendance::STATUS_BREAK)
               <div class="attendance-actions">
                <form  action=/attendance/break-end method=POST>
                @csrf
                  <button class="attendance-button attendance-button--secondary">
                    休憩戻
                  </button>
                </form>
               </div>

            @elseif ($attendance->status === Attendance::STATUS_DONE)
               <p class="attendance-message">
                 お疲れ様でした。
               </p>
           
          @endif
        </div>

    </div>
</div>
@endsection