<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\Nurse\ProfileNurseController;

Route::prefix('nurse')->middleware('auth:nurse')->name('nurse.')->group(function () {
  
    Route::post('update-location', [ProfileNurseController::class, 'updateLocation']);

    Route::post('add-work-hour', [ProfileNurseController::class, 'addWorkHour']);
    Route::post('update-work-hour/{id}', [ProfileNurseController::class, 'updateWorkHour']);

});