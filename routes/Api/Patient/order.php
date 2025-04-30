<?php

use App\Http\Controllers\Api\Nurse\OrderNurseController;
use App\Http\Controllers\Api\Patient\OrderPatientController;
use Illuminate\Support\Facades\Route;

Route::prefix('patient')->middleware('auth:patient')->group(function () {
    Route::apiResource('orders', OrderPatientController::class)->only(['index', 'store', 'show']);

    // Route::post('orders/{order}/update', [OrderNurseController::class, 'update'])->name('orders.update');
 // route to get orders statuses

 
    // get nurse work hours
    Route::get('get-nurse-work-hours/{id}', [OrderPatientController::class, 'getNurseWorkHours'])->name('orders.get-nurse-work-hours');
});



