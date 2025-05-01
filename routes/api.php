<?php

use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Http\Controllers\AccessTokenController;


// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::group(['middleware' => ['api','set.locale']], function () {

    Route::post('/oauth/token', [AccessTokenController::class, 'issueToken'])
        ->middleware(['throttle']); 

    // 🟡 Routes without verification (just login/register)
    include __DIR__ . "/Api/Nurse/auth.php";
    include __DIR__ . "/Api/Patient/auth.php";

    // 🟢 Verified-only routes (profile, home, etc.)
    Route::middleware(['verified.user'])->group(function () {
        include __DIR__ . "/Api/Nurse/profile.php";
        include __DIR__ . "/Api/Patient/profile.php";
        include __DIR__ . "/Api/Patient/home.php";
        include __DIR__ . "/Api/Nurse/order.php";
        include __DIR__ . "/Api/Patient/order.php";
        include __DIR__ . "/Api/Patient/favorite.php";
        // include __DIR__ . "/Api/Nurse/home.php";
    });

    Route::get('/test-fcm', function (FirebaseService $fcm) {
        $fcm->sendToDevice(
            'd6TCu7icRfmx78W3LU61nT:APA91bErGzC9YB3PY0xhXIN71YZfU0qthMrTV7mYE2zyrQmjngb9kMcBOusop_chJFWkr9JLvAVlB3kQ9gxHRkVw-zG2nOYSxDp7ErBBOaOBO7giirpXqxM',
            'تجربة الإشعار 🎯',
            'هذا إشعار تجريبي من Laravel + Firebase',
            ['click_action' => 'FLUTTER_NOTIFICATION_CLICK']
        );
    
        return 'Notification sent!';
    });
});


