<?php

use App\Http\Controllers\Api\Nurse\OrderNurseController;
use App\Http\Controllers\Api\Patient\OrderPatientController;
use Illuminate\Support\Facades\Route;

Route::prefix('patient')->middleware('auth:patient')->group(function () {
    Route::apiResource('orders', OrderPatientController::class)->only(['index', 'store', 'show']);

    Route::post('orders/{order}/update', [OrderNurseController::class, 'update'])->name('orders.update');
 // route to get orders statuses

});


Route::prefix('nurse')->middleware('auth:nurse')->group(function () {
    
    Route::get('orders', [OrderNurseController::class, 'getOrders'])->name('orders.index');
    Route::post('orders/{order}/update', [OrderNurseController::class, 'update'])->name('orders.update');

    //show 
    Route::get('orders/{order}',[OrderNurseController::class, "show"])->name('orders.show');

    Route::get('order-statuses', [OrderNurseController::class, 'getStatuses'])->name('orders.statuses');

    // accept order
    Route::post('orders/{order}/accept', [OrderNurseController::class, 'acceptOrder'])->name('orders.accept');
    // reject order
    Route::post('orders/{order}/reject', [OrderNurseController::class, 'rejectOrder'])->name('orders.reject');
 // route to get orders statuses

});
