<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Nurse\AuthController;

Route::prefix('nurse')->name('nurse.')->group(function () {
 
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('forget-password', [AuthController::class, 'forgetPassword']);
    // Route::middleware('auth:nurse')->group(function () {
    //     Route::get('profile', [AuthController::class, 'profile']);
    // });
});