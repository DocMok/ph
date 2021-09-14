<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('user')->group(function () {
    Route::post('signup', [AuthController::class, 'signup']);
    Route::get('login', [AuthController::class, 'login']);
    Route::get('logout', [AuthController::class, 'logout'])->middleware('auth:api');
});
