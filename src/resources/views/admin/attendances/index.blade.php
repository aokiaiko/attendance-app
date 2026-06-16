@extends('layouts.admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendances/common.css') }}">
<link rel="stylesheet" href="{{ asset('css/attendances/index.css') }}">
@endsection

@section('content')

<div class="page-content">
    <div class="page-container">
     <h1 class="page-title">
        {{ $date->translatedFormat('Y年n月j日の勤怠') }}
     </h1>

     <div class="attendance-month-nav">
        <a href="{{ route('admin.attendance.index', ['date' => $date->copy()->subDay()->toDateString()]) }}" 
           class="month-link"
        >
           ←  前日
        </a>
        <p class="current-month">
          <i class="fa-regular fa-calendar-days"></i>
          {{ $date->format('Y/m/d') }}
        </p>
        <a href="{{ route('admin.attendance.index', ['date' => $date->copy()->addDay()->toDateString()]) }}"
           class="month-link"
        >
           翌日  →
        </a>
     </div>

     <div class="table-wrapper">
        <table class="common-table">
          <thead>
           <tr>
             <th>名前</th>
             <th>出勤</th>
             <th>退勤</th>
             <th>休憩</th>
             <th>合計</th>
             <th>詳細</th>
           </tr>
          </thead>
          <tbody>
            @foreach($users as $user)
            <tr>
             <td>{{ $user->name}}</td>
             <td>{{ $user->attendance && $user->attendance->clock_in
                    ? $user->attendance->clock_in->format('H:i') 
                    : '' }}
             </td>
             <td>{{ $user->attendance && $user->attendance->clock_out
                    ? $user->attendance->clock_out->format('H:i') 
                    : '' }}
             </td>
             <td>{{ $user->attendance ? $user->attendance->break_time : '' }}</td>
             <td>{{ $user->attendance ? $user->attendance->work_time  : ''}}</td>
            <td>
                @if ($user->attendance)
                   <a href="{{ route('admin.attendance.show', $user->attendance->id) }}" class="detail-link">詳細</a>
                @else
                   <span class=detail-link>詳細</span>
                @endif
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>        
</div>
@endsection