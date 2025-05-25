<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {   
    return view('welcome');
});



Route::get('/provider/channel', function () {
    return view('channel'); 
});


Route::get('/test-broadcast', function () {
    Http::post('http://localhost:3000/order-created', [
        'order_id' => 99,
        'patient_id' => 1,
        'provider_id' => 7,
        'provider_type' => 'nurse',
    ]);

    return 'تم الإرسال إلى سوكيت سيرفر';
});