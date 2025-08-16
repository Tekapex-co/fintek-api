<?php

use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;
use Infinitypaul\Idempotency\Middleware\EnsureIdempotency;

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('account')->group(function () {
        Route::get('transactions', [TransactionController::class, 'index']);

        Route::post('transfer', [TransactionController::class, 'store'])
            ->middleware(EnsureIdempotency::class);

        Route::get('transactions/{transaction}', [TransactionController::class, 'show']);
    });
});
