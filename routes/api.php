<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\UserController;

// Public routes
Route::prefix('auth')->group(function () {
    // Auth
    Route::post('/register', RegisterController::class);
    Route::post('/login', LoginController::class);
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/auth/logout', LogoutController::class);

    // Users
    Route::get('/user', [UserController::class, 'me']);
    
    // Other protected routes would go here
});
