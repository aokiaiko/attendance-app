@extends('layouts.user')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendances/common.css') }}">
<link rel="stylesheet" href="{{ asset('css/stamp_correction_requests/index.css') }}">
@endsection

@section('content')

<div class="page-content">
    <div class="page-container">
     <h1 class="page-title">申請一覧</h1>

     <div class="request-nav">
        <a href="#" class="request-link active">承認待ち</a>
        <a href="#" class="request-link">承認済み</a>
     </div>

     <div class="table-wrapper">
        <table class="common-table">
          <thead>
           <tr>
             <th>状態</th>
             <th>名前</th>
             <th>対象日時</th>
             <th>申請理由</th>
             <th>申請日時</th>
             <th>詳細</th>
           </tr>
          </thead>
          <tbody>
            <tr>
             <td>承認待ち</td>
             <td>西怜奈</td>
             <td>2023/06/01</td>
             <td>遅延のため</td>
             <td>2023/06/01</td>
             <td><a href="" class="detail-link">詳細</a></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>        
</div>
@endsection
