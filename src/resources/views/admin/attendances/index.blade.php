@extends('layouts.admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendances/common.css') }}">
<link rel="stylesheet" href="{{ asset('css/attendances/index.css') }}">
@endsection

@section('content')

<div class="page-content">
    <div class="page-container">
     <h1 class="page-title">2023年6月1日の勤怠</h1>

     <div class="attendance-month-nav">
        <a href="#" class="month-link">← 前月</a>
        <i class="fa-regular fa-calendar-days"></i>
        <p class="current-month">2023/06/01</p>
        <a href="#" class="month-link">翌月 →</a>
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
            <tr>
             <td>山田 太郎</td>
             <td>09:00</td>
             <td>18:00</td>
             <td>1:00</td>
             <td>8:00</td>
             <td><a href="" class="detail-link">詳細</a></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>        
</div>
@endsection