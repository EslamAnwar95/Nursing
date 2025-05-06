<?php

use App\Http\Controllers\Api\Patient\PaymentPatientController;
use Illuminate\Support\Facades\Route;

Route::prefix('patient')->middleware('auth:patient')->group(function () {
    Route::post('pay', [PaymentPatientController::class, 'pay'])->name('orders.pay');
    // Route::post('pay/verify', [\App\Http\Controllers\Api\Patient\OrderPatientController::class, 'verifyPayment'])->name('orders.verify-payment');
});