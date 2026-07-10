<?php

namespace App\Http\Controllers;

class AdminAuthController extends Controller
{
    public function showLogin()
    {
       return view('admin.auth.login');
    }

    
}
