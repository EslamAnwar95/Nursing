<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Http\Controllers\AccessTokenController;


// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::group(['middleware' => ['api']], function () {

    Route::post('/oauth/token', [AccessTokenController::class, 'issueToken'])
        ->middleware(['throttle']); 

    include __DIR__ . "/Api/Nurse/auth.php";
    include __DIR__ . "/Api/Nurse/profile.php";
    // include __DIR__ . "/Api/Nurse/home.php";
    include __DIR__ . "/Api/Patient/home.php";
    include __DIR__ . "/Api/Patient/auth.php";
    include __DIR__ . "/Api/Patient/profile.php";

});