<?php

use App\Http\Controllers\Api\Patient\ReservationController;
use Illuminate\Support\Facades\Route;

Route::prefix('patient')->middleware('auth:patient')->group(function () {
    Route::apiResource('reservations', ReservationController::class)->only(['index', 'store', 'show']);
   
});