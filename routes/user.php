<?php

use App\Http\Controllers\PushNotificationController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', [UserController::class, 'show']);

    Route::post('notification-token', PushNotificationController::class);
});
