<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\Patient\ProfilePatientController;

Route::prefix('patient')->name('patient.')->middleware('auth:patient')->group(function () {
  
    Route::post('update-location', [ProfilePatientController::class, 'updateLocation']);


    


    Route::get('notifications', [ProfilePatientController::class, 'getNorifications'])->name('notifications.index');
    Route::post('notifications/{notification}/read', [ProfilePatientController::class, 'readNotification'])->name('notifications.read');
    Route::post('notifications/mark-all-as-read', [ProfilePatientController::class, 'markAllNotificationsAsRead'])->name('notifications.mark-all-as-read');

});