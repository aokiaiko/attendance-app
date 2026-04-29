<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;



class AuthController extends Controller
{
    public function showLogin()
    {
       return view('auth.login');
    }

    public function showRegister()
    {
       return view('auth.register');
    }

    public function store(RegisterRequest $request)
    {
        $user=$request->only(['name','email','password']);
        $user['password'] = Hash::make($user['password']);
        $user['role'] = 'user';

        $user=User::create($user);
        Auth::login($user); 

        //event(new Registered($user));メール認証実装したら入れる//

        $request->session()->regenerate();

        return redirect('/attendance');
    }
}

