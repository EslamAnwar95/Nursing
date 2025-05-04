<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\Patient\ProfilePatientController;

Route::prefix('patient')->name('patient.')->group(function () {
  
    Route::post('update-location', [ProfilePatientController::class, 'updateLocation'])->middleware('auth:patient');


    Route::get('get-notifications', [ProfilePatientController::class, 'getNorifications'])->middleware('auth:patient');

    Route::post('read-notification/{id}', [ProfilePatientController::class, 'readNotification'])->middleware('auth:patient');

    Route::post('mark-all-notifications-as-read', [ProfilePatientController::class, 'markAllNotificationsAsRead'])->middleware('auth:patient');


});