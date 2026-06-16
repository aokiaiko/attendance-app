@extends(auth()->user()->role === 'admin' ? 'layouts.admin' : 'layouts.user')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendances/common.css') }}">
<link rel="stylesheet" href="{{ asset('css/stamp_correction_requests/index.css') }}">
@endsection

@section('content')

<div class="page-content">
    <div class="page-container">
     <h1 class="page-title">申請一覧</h1>

     <div class="request-nav">
        <a 
          href="{{ route('stamp_correction_request.index', ['status' => 'pending']) }}" 
          class="request-link {{ request('status', 'pending') === 'pending' ? 'active-tab' : '' }}"
        >
        承認待ち
        </a>
        <a 
          href="{{ route('stamp_correction_request.index', ['status' => 'approved']) }}" 
          class="request-link {{ request('status') === 'approved' ? 'active-tab' : '' }}"
        >
          承認済み
        </a>
     </div>

     <div class="table-wrapper">
        <table class="common-table">
          
           <tr>
             <th>状態</th>
             <th>名前</th>
             <th>対象日時</th>
             <th>申請理由</th>
             <th>申請日時</th>
             <th>詳細</th>
           </tr>
         
          @foreach($corrections as $correction)
           <tr>
             <td class="index">
              {{ $correction->status === 0 ? '承認待ち' : '承認済み' }}
             </td>
             <td class="index">{{ $correction->attendance->user->name}}</td>
             <td class="index">{{ $correction->attendance->work_date->format('Y/m/d')}}</td>
             <td class="index">{{ $correction->note}}</td>
             <td class="index">{{ $correction->created_at->format('Y/m/d')}}</td>

              @if(auth()->user()->role === 'user')
                <td class="index"><a href="{{ route('attendance.show' , $correction->attendance->id) }}"  class="detail-link">詳細</a></td>
              @elseif(auth()->user()->role === 'admin')
                <td class="index"><a href="{{ route('stamp_correction_request.show' , $correction->id) }}"  class="detail-link">詳細</a></td>
              @endif
           </tr>
          @endforeach

        </table>
      </div>
      
    </div>        
</div>
@endsection
