<?php

use App\Http\Controllers\AccountController;

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('accounts')->group(function () {
        Route::get('{account}/transactions', [AccountController::class, 'index']);

        Route::get('{account}/transactions/{transaction}', [AccountController::class, 'show']);
    });
});
