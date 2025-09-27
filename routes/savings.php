<?php

use App\Http\Controllers\SavingsController;
use Infinitypaul\Idempotency\Middleware\EnsureIdempotency;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/savings', [SavingsController::class, 'index']);

    Route::post('/savings', [SavingsController::class, 'store'])
        ->middleware(EnsureIdempotency::class);

    Route::get('/savings/{savings}', [SavingsController::class, 'show']);
});
