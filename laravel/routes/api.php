<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\InvestorController;
use App\Http\Controllers\Api\NotificationTokenController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\UserProfileController;
use App\Http\Controllers\Api\UserNotificationController;
use App\Http\Controllers\Api\UserSuggestionsController;
use Illuminate\Support\Facades\Route;

//TODO
//  decrease throttle limit in
//  RouteServiceProvider->configureRateLimiting
//  to 60 after tests!!!
Route::prefix('user')->group(function () {
    Route::post('signup', [AuthController::class, 'signup']);
    Route::get('login', [AuthController::class, 'login']);
    Route::middleware('auth:api')->group(function () {
        Route::get('logout', [AuthController::class, 'logout']);
        Route::post('profile', [UserProfileController::class, 'update']);
        Route::get('profile', [UserProfileController::class, 'show']);
        Route::prefix('notifications')->group(function () {
            Route::get('/', [UserNotificationController::class, 'index']);
            Route::put('/', [UserNotificationController::class, 'update']);
            Route::get('not-viewed', [UserNotificationController::class, 'countNotViewed']);
        });
        Route::get('suggestions', [UserSuggestionsController::class, 'index']);
        Route::prefix('fcm-tokens')->group(function () {
            Route::post('/', [NotificationTokenController::class, 'store']);
            Route::get('/', [NotificationTokenController::class, 'index']);
            Route::put('/', [NotificationTokenController::class, 'update']);
            Route::delete('/', [NotificationTokenController::class, 'destroy']);
        });
    });
});


Route::prefix('projects')->group(function () {
    Route::get('categories', [CategoryController::class, 'getCategories']);
    Route::middleware('auth:api')->group(function () {
        Route::get('/', [ProjectController::class, 'index']);
        Route::get('/liked', [ProjectController::class, 'liked']);
    });
});

Route::prefix('investors')->middleware('auth:api')->group(function () {
    Route::get('/', [InvestorController::class, 'index']);
    Route::get('/liked', [InvestorController::class, 'liked']);
});

Route::prefix('investor')->middleware('auth:api')->group(function () {
    Route::post('like-toggle', [InvestorController::class, 'likeToggle']);
    Route::get('/', [InvestorController::class, 'show']);
});


Route::prefix('project')->middleware('auth:api')->group(function () {
    Route::post('like-toggle', [ProjectController::class, 'likeToggle']);
    Route::get('/', [ProjectController::class, 'show']);
    Route::post('/', [ProjectController::class, 'store']);
    Route::delete('/', [ProjectController::class, 'destroy']);
    Route::post('/update', [ProjectController::class, 'update']);
});
