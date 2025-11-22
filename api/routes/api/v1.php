<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Discovery\DiscoveryController;
use App\Http\Controllers\Api\V1\Like\LikeController;
use App\Http\Controllers\Api\V1\Dislike\DislikeController;
use App\Http\Controllers\Api\V1\Profile\ProfileController;
use App\Http\Controllers\Api\V1\Picture\PictureController;

/**
 * API V1 Routes
 * 
 * Base URL: /api/v1
 */

// Public Routes (Authentication)
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register'])->name('auth.register');
    Route::post('login', [AuthController::class, 'login'])->name('auth.login');
});

// Protected Routes (Require Authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Authentication
    Route::prefix('auth')->group(function () {
        Route::get('me', [AuthController::class, 'me'])->name('auth.me');
        Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');
    });

    // Discovery - Get recommended people
    Route::prefix('people')->group(function () {
        Route::get('/', [DiscoveryController::class, 'index'])->name('people.index');
        Route::get('{userId}', [DiscoveryController::class, 'show'])->name('people.show');
    });

    // Likes - Like interactions
    Route::prefix('likes')->group(function () {
        Route::get('/', [LikeController::class, 'index'])->name('likes.index');
        Route::post('{userId}', [LikeController::class, 'store'])->name('likes.store');
        Route::delete('{userId}', [LikeController::class, 'destroy'])->name('likes.destroy');
    });

    // Dislikes - Dislike/pass interactions
    Route::prefix('dislikes')->group(function () {
        Route::post('{userId}', [DislikeController::class, 'store'])->name('dislikes.store');
    });

    // Profile - User profile management
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('profile.show');
        Route::put('/', [ProfileController::class, 'update'])->name('profile.update');
    });

    // Pictures - Picture management
    Route::prefix('pictures')->group(function () {
        Route::get('/', [PictureController::class, 'index'])->name('pictures.index');
        Route::post('/', [PictureController::class, 'store'])->name('pictures.store');
        Route::delete('{pictureId}', [PictureController::class, 'destroy'])->name('pictures.destroy');
        Route::patch('{pictureId}/primary', [PictureController::class, 'setPrimary'])->name('pictures.setPrimary');
        Route::post('reorder', [PictureController::class, 'reorder'])->name('pictures.reorder');
    });
});
