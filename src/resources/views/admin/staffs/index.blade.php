@extends('layouts.admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendances/common.css') }}">
<link rel="stylesheet" href="{{ asset('css/attendances/index.css') }}">
@endsection

@section('content')

<div class="page-content">
    <div class="page-container">
     <h1 class="page-title">スタッフ一覧</h1>

     <div class="table-wrapper">
        <table class="common-table">
          <thead>
           <tr>
             <th>名前</th>
             <th>メールアドレス</th>
             <th>月次勤怠</th>
           </tr>
          </thead>
          <tbody>
            <tr>
             <td>西 怜奈</td>
             <td>reina.n@coachtech.com</td>
             <td><a href="" class="detail-link">詳細</a></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>        
</div>
@endsection