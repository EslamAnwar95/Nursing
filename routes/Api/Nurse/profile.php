<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\Nurse\ProfileNurseController;

Route::prefix('nurse')->name('nurse.')->group(function () {
  
    Route::post('update-location', [ProfileNurseController::class, 'updateLocation'])->middleware('auth:nurse');


});