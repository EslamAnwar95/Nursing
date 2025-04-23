<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Patient\AuthController;

Route::prefix('patient')->name('patient.')->group(function () {
 
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('forget-password', [AuthController::class, 'forgetPassword']);

    Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
  
    Route::post('verify-register-otp', [AuthController::class, 'verifyRegisterOtp']);
  
    Route::post('reset-password', [AuthController::class, 'resetPassword']);
    Route::post('resend-otp', [AuthController::class, 'resendOtp']);
    
    // Route::middleware('auth:patient')->group(function () {
    //     Route::get('profile', [AuthController::class, 'profile']);
    // });
});