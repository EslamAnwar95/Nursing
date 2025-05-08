<?php 


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Patient\HomePatientController;

Route::prefix('patient')->name('patient.')->group(function () {
  
        Route::get('home', [HomePatientController::class, 'home'])->middleware('auth:patient');
    
        Route::get('most-common-favorite-providers', [HomePatientController::class, 'getMostCommonFavoriteProviders'])->middleware('auth:patient');
        
        Route::post('cancel-reservation', [HomePatientController::class, 'cancelReservation'])->middleware('auth:patient');

        Route::post('reschedule-reservation', [HomePatientController::class, 'rescheduleReservation'])->middleware('auth:patient');
    
        // Route::middleware('auth:patient')->group(function () {
    //     Route::get('profile', [AuthController::class, 'profile']);
    // });
});