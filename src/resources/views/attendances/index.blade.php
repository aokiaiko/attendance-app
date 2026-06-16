@extends('layouts.user')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendances/common.css') }}">
<link rel="stylesheet" href="{{ asset('css/attendances/index.css') }}">
@endsection

@section('content')

<div class="page-content">
    <div class="page-container">
     <h1 class="page-title">勤怠一覧</h1>

     <div class="attendance-month-nav">
        <a href="{{ route('attendance.index', ['month' => $month->copy()->subMonth()->format('Y-m')]) }}" class="month-link">← 前月</a>
        <p class="current-month">
          <i class="fa-regular fa-calendar-days"></i>
          {{ $month->format('Y/m') }}
        </p>
        <a href="{{ route('attendance.index', ['month' => $month->copy()->addMonth()->format('Y-m')]) }}" class="month-link">翌月 →</a>
     </div>

     <div class="table-wrapper">
        <table class="common-table">
          <thead>
           <tr>
             <th>日付</th>
             <th>出勤</th>
             <th>退勤</th>
             <th>休憩</th>
             <th>合計</th>
             <th>詳細</th>
           </tr>
          </thead>
          <tbody>
            @foreach ($calendar as $day)
            <tr>
              <td>
                {{ $day['date']->translatedFormat('m/d(D)') }}
              </td>
              <td>
                {{ $day['attendance'] && $day['attendance']->clock_in
                    ? $day['attendance']->clock_in->format('H:i') 
                    : '' }}
              </td>
              <td>
                {{ $day['attendance'] && $day['attendance']->clock_out
                    ? $day['attendance']->clock_out->format('H:i') 
                    : ''}}
              </td>
              <td>{{ $day['attendance'] ? $day['attendance']->break_time : '' }}</td>
              <td>{{ $day['attendance'] ? $day['attendance']->work_time : '' }}</td>

              <td>
                @if ($day['attendance'])
                   <a href="{{ route('attendance.show', $day['attendance']->id) }}" class="detail-link">詳細</a>
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