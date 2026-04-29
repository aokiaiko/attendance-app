<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\BreakController;
use App\Http\Controllers\StampCorrectionRequestController;
use App\Http\Controllers\AdminAttendanceController;
use App\Http\Controllers\AdminStaffController;
use App\Http\Controllers\AdminStampCorrectionRequestController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::middleware(['auth','verified'])->group(function () {
    Route::get('/attendance', [AttendanceController::class, 'create']);
    Route::post('/attendance', [AttendanceController::class, 'clockIn']);
    Route::post('/attendance/clock-out', [AttendanceController::class, 'clockOut']);
    Route::post('/attendance/break-start', [BreakController::class, 'breakStart']);
    Route::post('/attendance/break-end', [BreakController::class, 'breakEnd']);



    Route::get('/attendance/list', [AttendanceController::class, 'index']);
    Route::get('/attendance/detail', [AttendanceController::class, 'show']);
    Route::get('/stamp_correction_request/list', [StampCorrectionRequestController::class, 'index']);
});  

Route::middleware(['auth','admin'])->group(function () {    
    Route::get('/admin/attendance/list', [AdminAttendanceController::class, 'index']);
    Route::get('/admin/attendance', [AdminAttendanceController::class, 'show']);
    Route::get('/admin/staff/list', [AdminStaffController::class, 'index']);
    Route::get('/admin/attendance/staff', [AdminStaffController::class, 'attendanceList']);
    Route::get('/stamp_correction_request/list', [AdminStampCorrectionRequestController::class, 'index']);
    Route::get('/stamp_correction_request/approve', [AdminStampCorrectionRequestController::class, 'approve']);
});  
  

Route::get('/admin/login', [AdminAuthController::class,'showLogin'])->name('admin.login');
