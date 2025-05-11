<?php

use App\Http\Controllers\Api\Nurse\NotificationNurseController;
use Illuminate\Support\Facades\Route;


Route::prefix('nurse')->middleware('auth:nurse')->group(function () {
    
    Route::get('notifications', [NotificationNurseController::class, 'getNorifications'])->name('notifications.index');
    Route::post('notifications/{notification}/read', [NotificationNurseController::class, 'readNotification'])->name('notifications.read');
    Route::post('notifications/mark-all-as-read', [NotificationNurseController::class, 'markAllNotificationsAsRead'])->name('notifications.mark-all-as-read');

});