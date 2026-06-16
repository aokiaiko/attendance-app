@extends('layouts.admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendances/show.css') }}">
@endsection

@section('content')

<div class="attendance-show-content">
  <div class="attendance-show-container">
     <h1 class="attendance-show-title">勤怠詳細</h1>

     <form  action="{{ route('stamp_correction_request.approve', $approve->id)}}" method="POST">
      @method('PATCH')
      @csrf
      <div class="show-table-wrapper">
        <table class="show-table">
           <tr>
              <th>名前</th>
              <td></td>
              <td>{{$approve->attendance->user->name}}</td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>

           </tr>
           <tr>
              <th>日付</th>
              <td></td>
              <td>{{$approve->attendance->work_date->format('Y年')}}</td>
              <td></td>
              <td>{{$approve->attendance->work_date->format('n月j日')}}</td>
              <td></td>
              <td></td>
           </tr>
           <tr>
              <th>出勤・退勤</th>
              <td></td>
              <td class="input-cell">{{ $approve->requested_clock_in->format('H:i')}}</td>
              <td class="range-cell">～</td>
              <td class="input-cell">{{ $approve->requested_clock_out->format('H:i')}}</td>
              <td></td>
              <td></td>
            </tr>
            @foreach ($approve->breaks as $index => $break)
            <tr>
               <th>休憩{{ $index === 0 ? '' : $index + 1 }}</th>
               <td></td>
               <td class="input-cell">{{ $break->requested_break_start->format('H:i')}}</td>
               <td class="range-cell">～</td>
               <td class="input-cell">{{ $break->requested_break_end->format('H:i')}}</td>
               <td></td>
               <td></td>
            </tr>
            @endforeach
            <tr>
                <th>備考</th>
                <td></td>
                <td colspan="3">{{$approve->note}}</td>
                <td></td>
                <td></td>
            </tr>
        </table>
      </div>

      @if($approve->status === 0)
      <div class="button-area">
            <button class="button" type="submit">承認</button>
      </div>
      @elseif($approve->status === 1)
      <div class="button-area">
         <span class="approved">承認済み</span>
      </div>
      @endif
     </form>
     
  </div>
</div>
@endsection

