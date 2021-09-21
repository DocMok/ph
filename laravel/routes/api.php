<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\InvestorController;
use App\Http\Controllers\Api\ProjectController;
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

Route::prefix('projects')->group(function () {
    Route::get('categories', [CategoryController::class, 'getCategories']);
    Route::middleware('auth:api')->group(function () {
        Route::get('/', [ProjectController::class, 'index']);
    });
});

Route::prefix('investors')->middleware('auth:api')->group(function () {
    Route::get('/', [InvestorController::class, 'index']);
});

Route::middleware('auth:api')->post('investor/like-toggle', [InvestorController::class, 'likeToggle']);

Route::prefix('project')->middleware('auth:api')->group(function () {
    Route::post('like-toggle', [ProjectController::class, 'likeToggle']);
    Route::get('/', [ProjectController::class, 'show']);
    Route::post('/', [ProjectController::class, 'store']);
    Route::post('/update', [ProjectController::class, 'update']);
});
