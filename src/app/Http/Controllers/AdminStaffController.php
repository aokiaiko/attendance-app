<?php

namespace App\Http\Controllers;

use App\Models\User;

class AdminStaffController extends Controller
{
   public function index()
    {
        $users = User::where('role','user')->get();

        return view('admin.staffs.index',compact('users'));
    }

}
