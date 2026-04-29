<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminStampCorrectionRequestController extends Controller
{
    public function index()
    {
        return view('admin.stamp_correction_requests.index');

    }

    public function approve()
    {
        return view('admin.stamp_correction_requests.approve');

    }

    
}
