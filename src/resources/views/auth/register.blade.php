@extends('layouts.auth')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/auth.css') }}">
@endsection

@section('content')

<div class="auth-content">
    <div class="auth-title">
        <h1>会員登録</h1>
    </div>
    <form class="form" action="/register" method="POST" novalidate>
        @csrf
        <div class="auth-group">
           <label class="auth-label">お名前</label>
           <input class="auth-input" type="text" name="name" value="{{old('name')}}" />
           <div class="auth-error">
              @error('name')
              {{$message}}
              @enderror
           </div>
       </div>

       <div class="auth-group">
           <label class="auth-label">メールアドレス</label>
           <input class="auth-input" type="email" name="email" value="{{old('email')}}" />
           <div class="auth-error">
              @error('email')
              {{$message}}
              @enderror
           </div>
        </div>

       <div class="auth-group">
          <label class="auth-label">パスワード</label>
          <input class="auth-input" type="password" name="password" />
          <div class="auth-error">
              @error('password')
              {{$message}}
              @enderror
          </div>
       </div>

       <div class="auth-group">
          <label class="auth-label">パスワード確認</label>
          <input class="auth-input" type="password" name="password_confirmation" />
         <div class="auth-error">
              @error('password_confirmation')
              {{$message}}
              @enderror
         </div>
       </div>

       <button class="auth-button" type="submit">登録する</button>
    </form>

    <p class="auth__link">
        <a href="/login">ログインはこちら</a>
    </p>
</div>

@endsection