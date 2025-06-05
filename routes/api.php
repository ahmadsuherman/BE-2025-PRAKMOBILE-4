<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);


Route::middleware('auth:api')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'dashboard']);
    Route::post('/logout', [AuthController::class, 'logout']);
});