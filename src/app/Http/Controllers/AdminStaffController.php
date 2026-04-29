<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminStaffController extends Controller
{
   public function index()
    {
        return view('admin.staffs.index');
    }

    public function attendanceList()
    {
        return view('admin.staffs.attendances');
    }
}
