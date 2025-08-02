<?php

use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('account')->group(function () {
        Route::get('transactions', [TransactionController::class, 'index']);

        Route::post('transfer', [TransactionController::class, 'store']);

        Route::get('transactions/{transaction}', [TransactionController::class, 'show']);
    });
});
