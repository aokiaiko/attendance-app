@extends('layouts.auth')

@section('css')


<link rel="stylesheet" href="{{ asset('css/auth/verify.css') }}">

@endsection

@section('content')

<div class="verify-email__container">
 <div class="verify-email__wrapper">
    <div class="verify-email__text">
        登録していただいたメールアドレスに認証メールを送付しました。<br>
        メール認証を完了してください。
    </div>

<a href="http://localhost:8025" class="verify-email__button" target="_blank" rel="noopener noreferrer">
        認証はこちらから
    </a>

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="resend-button">
            認証メールを再送する
        </button>
    </form>

    @if (session('status') === 'verification-link-sent')
        <div class="success-message">
            認証メールを再送しました。
        </div>
    @endif
 </div>
</div>

@endsection    