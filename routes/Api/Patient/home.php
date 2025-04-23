<?php 


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Patient\HomePatientController;

Route::prefix('patient')->name('patient.')->group(function () {
  
        Route::get('home', [HomePatientController::class, 'home'])->middleware('auth:patient');
 
    // Route::middleware('auth:patient')->group(function () {
    //     Route::get('profile', [AuthController::class, 'profile']);
    // });
});