<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Infinitypaul\Idempotency\Middleware\EnsureIdempotency;

Route::middleware(EnsureIdempotency::class)->group(function () {
    Route::post('register', [UserController::class, 'store']);

    Route::post('login', [AuthController::class, 'store']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'destroy']);
});
