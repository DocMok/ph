<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\UserProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('user')->group(function () {
    Route::post('signup', [AuthController::class, 'signup']);
    Route::get('login', [AuthController::class, 'login']);
    Route::middleware('auth:api')->group(function () {
        Route::get('logout', [AuthController::class, 'logout']);
        Route::put('profile', [UserProfileController::class, 'updateProfile']);
        Route::get('profile', [UserProfileController::class, 'getProfile']);
    });
});

Route::prefix('projects')->group(function () {
    Route::get('categories', [CategoryController::class, 'getCategories']);
    Route::middleware('auth:api')->group(function () {
        Route::post('/', [ProjectController::class, 'store']);
        Route::put('/', [ProjectController::class, 'update']);
    });
});

