<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\Admin\EventController as AdminEventController;
use App\Http\Controllers\Api\Admin\AuthController;

// Public API routes
Route::prefix('events')->group(function () {
    Route::get('/', [EventController::class, 'index']);
    Route::get('/featured', [EventController::class, 'featured']);
    Route::get('/categories', [EventController::class, 'categories']);
    Route::get('/calendar', [EventController::class, 'calendar']);
    Route::get('/{event}', [EventController::class, 'show']);
});

// Admin authentication routes
Route::prefix('admin')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    // Protected admin routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/profile', [AuthController::class, 'updateProfile']);
        Route::put('/password', [AuthController::class, 'updatePassword']);

        // Admin event management
        Route::prefix('events')->group(function () {
            Route::get('/', [AdminEventController::class, 'index']);
            Route::post('/', [AdminEventController::class, 'store']);
            Route::get('/{event}', [AdminEventController::class, 'show']);
            Route::post('/{event}', [AdminEventController::class, 'update']);
            Route::delete('/{event}', [AdminEventController::class, 'destroy']);
            Route::patch('/{event}/toggle-featured', [AdminEventController::class, 'toggleFeatured']);
        });
    });
});

