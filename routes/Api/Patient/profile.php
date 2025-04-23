<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\Patient\ProfilePatientController;

Route::prefix('patient')->name('patient.')->group(function () {
  
    Route::post('update-location', [ProfilePatientController::class, 'updateLocation'])->middleware('auth:patient');


});