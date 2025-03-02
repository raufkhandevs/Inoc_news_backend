<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\AuthorController;

// Public routes
Route::prefix('auth')->group(function () {
    // Auth
    Route::post('/register', RegisterController::class);
    Route::post('/login', LoginController::class);
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::prefix('auth')->group(function () {
        Route::post('/logout', LogoutController::class);
        Route::get('/me', [UserController::class, 'me']);
        Route::patch('/preferences', [UserController::class, 'preferences']);
    });

    // Categories
    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoryController::class, 'index']);
    });

    // Authors
    Route::prefix('authors')->group(function () {
        Route::get('/', [AuthorController::class, 'index']);
    });

    // Articles
    Route::prefix('articles')->group(function () {
        Route::get('/', [ArticleController::class, 'index']);
        Route::get('/my-feeds', [ArticleController::class, 'myFeeds']);
    });
});
