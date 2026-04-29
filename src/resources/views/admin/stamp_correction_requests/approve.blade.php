@extends('layouts.admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendances/show.css') }}">
@endsection

@section('content')

<div class="attendance-show-content">
  <div class="attendance-show-container">
     <h1 class="attendance-show-title">勤怠詳細</h1>

     <form class="" action="" method="POST">
      @csrf
      <div class="show-table-wrapper">
        <table class="show-table">
           <tr>
              <th>名前</th>
              <td colspan="3">西怜奈</td>
           </tr>
           <tr>
              <th>日付</th>
              <td>2023年</td>
              <td colspan="2">6月1日</td>
           </tr>
           <tr>
              <th>出勤・退勤</th>
              <td>09:00</td>
              <td class="range-cell">～</td>
              <td>18:00</td>
            </tr>
            <tr>
               <th>休憩</th>
               <td>12:00</td>
               <td class="range-cell">～</td>
               <td>13:00</td>
            </tr>
            <tr>
                <th>備考</th>
                <td colspan="3">電車遅延のため</td>
            </tr>
        </table>
      </div>
    
      <div class="attendance-show-button-area">
            <button class="attendance-show-button" type="submit">承認</button>
      </div>
     </form>
  </div>
</div>
@endsection

