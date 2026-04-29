<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COACHTECH</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/layouts/app.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">


    @yield('css')
</head>
<body>
    <header class="app-header">
     <div class="header__inner">
       <img class="header__logo" src="{{ asset('images/logo.png')}}">


       <nav class="header-nav">
         <ul class="header-nav__item">
          
           <li class="nav-item"><a href="/">勤怠</a></li>
           <li class="nav-item"><a href="/">勤怠一覧</a></li>
           <li class="nav-item">
            <form class="form" action="/" method="POST">
              @csrf
              <button class="nav-link" type="submit">申請</button>
            </form>
           </li>
           @auth
           <li class="nav-item">
            <form class="form" action="/logout" method="POST">
              @csrf
              <input type="hidden" name="logout_type" value="user">
              <button class="nav-link" type="submit">ログアウト</button>
            </form>
           </li>
           @endauth
          
         </ul>
       </nav>
     </div>
    </header>

  <main>
    @yield('content')
  </main>
</body>
</html> 