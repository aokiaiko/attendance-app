@extends('layouts.auth')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/auth.css') }}">
@endsection

@section('content')

<div class="auth-content">
    <div class="auth-title">
        <h1>ログイン</h1>
    </div>
    <div class="auth-error">
      @if ($errors->has('login'))
            {{ $errors->first('login') }}
      @endif
    </div>
    
    <form class="form" action="/login" method="POST" >
        @csrf
        <input type="hidden" name="login_type" value="user">

        <div class="auth-group">
           <label class="auth-label">メールアドレス</label>
           <input class="auth-input" type="mail"  name="email" value="{{old('email')}}" >
           <div class="auth-error">
            @error('email')
            {{ $message }}
            @enderror
           </div>
        </div>

       <div class="auth-group">
           <label class="auth-label">パスワード</label>
           <input class="auth-input" type="password" name="password">
           <div class="auth-error">
            @error('password')
            {{ $message }}
            @enderror
           </div>
        </div>

        <div class="auth-error">
            @if ($errors->has('email') || $errors->has('password'))
             @else
               @if ($errors->has('login'))
                   {{ $errors->first('login') }}
               @endif
            @endif
        </div>

       <button class="auth-button" type="submit">ログインする</button>
    </form>

    <p class="auth__link">
        <a href="/register">会員登録はこちら</a>
    </p>
</div>

@endsection