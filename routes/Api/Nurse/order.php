<?php

use App\Http\Controllers\Api\Nurse\OrderNurseController;
use Illuminate\Support\Facades\Route;

Route::prefix('patient')->middleware('auth:patient')->group(function () {
    Route::apiResource('orders', OrderNurseController::class)->only(['index', 'store', 'show']);

    
});