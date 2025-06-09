<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\TransactionController;


Route::middleware('auth:api')->group(function () {
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('transactions', TransactionController::class);

    Route::get('/dashboard', [DashboardController::class, 'dashboard']);
});