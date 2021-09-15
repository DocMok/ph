<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('user')->group(function () {
    Route::post('signup', [AuthController::class, 'signup']);
    Route::get('login', [AuthController::class, 'login']);
    Route::middleware('auth:api')->group(function () {
        Route::get('logout', [AuthController::class, 'logout']);
        Route::post('profile', [UserProfileController::class, 'updateProfile']);
        Route::get('profile', [UserProfileController::class, 'getProfile']);
    });
});
