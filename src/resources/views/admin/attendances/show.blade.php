@extends('layouts.admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendances/show.css') }}">
@endsection

@section('content')

<div class="attendance-show-content">
  <div class="attendance-show-container">
     <h1 class="attendance-show-title">勤怠詳細</h1>
     
      @if ($attendance->pendingCorrection)
       <div class="show-table-wrapper pending-wrapper">
        <table class="show-table">
           <tr>
              <th>名前</th>
              <td></td>
              <td>{{ $attendance->user->name }}</td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
           </tr>
           <tr>
              <th>日付</th>
              <td></td>
              <td class="year-cell">{{ $attendance->work_date->format('Y年') }}</td>
              <td></td> 
              <td class="date-cell">{{ $attendance->work_date->format('n月j日') }}</td>
              <td></td>
              <td></td>
           </tr>
           <tr>
              <th>出勤・退勤</th>
              <td></td>
              <td class="input-cell">{{ $attendance->pendingCorrection->requested_clock_in->format('H:i')}}</td>
              <td class="range-cell">～</td>
              <td class="input-cell">{{ $attendance->pendingCorrection->requested_clock_out->format('H:i')}}</td>
              <td></td>
              <td></td>
           </tr>
           @foreach ($attendance->pendingCorrection->breaks as $index => $break)
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
               <td colspan="4">{{ $attendance->pendingCorrection->note}}</td>
               <td></td>
               <td></td>
            </tr>
        </table>
       </div>
     <div class="pending-message">
         ・承認待ちのため修正はできません
     </div>

     @else
     <form action="{{ route('attendance.update',$attendance->id) }}" method="POST">
     @method('PATCH')
     @csrf
      <div class="show-table-wrapper">
        <table class="show-table">
           <tr>
              <th>名前</th>
              <td></td>
              <td>{{ $attendance->user->name }}</td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
           </tr>
           <tr>
              <th>日付</th>
              <td></td>
              <td>{{ $attendance->work_date->format('Y年')}}</td>
              <td></td>
              <td>{{ $attendance->work_date->format('n月j日')}}</td>
              <td></td>
              <td></td>
           </tr>
           <tr>
              <th>出勤・退勤</th>
              <td></td>
              <td>
                <input class="time-input" type="time" name="clock_in" 
                       value="{{ old('clock_in',$attendance->clock_in->format('H:i'))}}">
              </td>
              <td class="range-cell">～</td>
              <td>
                <input class="time-input" type="time" name="clock_out" 
                       value="{{ old('clock_out',$attendance->clock_out ? $attendance->clock_out->format('H:i') : '') }}">
              </td>
              <td></td>
              <td></td>
            </tr>
            @error('work_time')
             <tr>
              <td colspan="3">
                 <div class="input-error">
                      {{ $message }}
                 </div>
              </td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
            </tr>
            @enderror

            @foreach ($attendance->breaks as $index => $break)
            <tr>
               <th>休憩{{ $index === 0 ? '' : $index + 1 }}</th>
               <td></td>
               <td class="input-cell">
                <input class="time-input" type="time" name="break_start[]" 
                       value="{{  old('break_start.' . $index, $break->break_start ? $break->break_start->format('H:i') : '')  }}">
               </td>
               <td class="range-cell">～</td>
               <td class="input-cell">
                <input class="time-input" type="time" name="break_end[]"
                       value="{{ old('break_end.' . $index,$break->break_end ? $break->break_end->format('H:i') : '') }}">
               </td>
               <td></td>
               <td></td>
            </tr>
            @endforeach

            <tr>
               <th>休憩{{ $attendance->breaks->count() + 1  }}</th>
               <td></td>
               <td class="input-cell">
                    <input class="time-input" type="time" name="break_start[]"
                       value="{{ old('break_start.' . $attendance->breaks->count()) }}">
               </td>
               <td class="range-cell">～</td>
               <td class="input-cell">
                    <input class="time-input" type="time" name="break_end[]" 
                       value="{{ old('break_end.' . $attendance->breaks->count()) }}">
               </td>
               <td></td>
               <td></td>
            </tr>
            @error('break_time')
            <tr>
               <td colspan="3">
                    <div class="input-error">
                        {{ $message }}
                    </div>
               </td>
               <td></td>
               <td></td>
               <td></td>
               <td></td>
            </tr>
            @enderror 

            <tr>
                <th>備考</th>
                <td></td>
                <td colspan="3">
                    <textarea class="note-textarea" name="note" >{{ old('note',$attendance->note) }}</textarea>
                </td>
                <td></td>
                <td></td>
            </tr>
            @error('note')
            <tr>
                <td colspan="3">
                       <div class="input-error">
                          {{ $message }}
                       </div>
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            @enderror
              
        </table>
      </div>
    
      <div class="button-area">
            <button class="button" type="submit">修正</button>
      </div>
     </form>
     @endif
  </div>
</div>
@endsection

