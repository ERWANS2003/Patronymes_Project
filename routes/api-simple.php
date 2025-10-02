<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PatronymeApiController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StatisticsApiController;

// API simplifiée pour les fonctionnalités essentielles
Route::prefix('simple')->name('api.simple.')->group(function () {

    // Authentification simple
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::post('login', [AuthController::class, 'login'])->name('login');
        Route::post('register', [AuthController::class, 'register'])->name('register');
        Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum')->name('logout');
        Route::get('user', [AuthController::class, 'user'])->middleware('auth:sanctum')->name('user');
    });

    // Patronymes essentiels
    Route::prefix('patronymes')->name('patronymes.')->group(function () {
        Route::get('/', [PatronymeApiController::class, 'index'])->name('index');
        Route::get('/{patronyme}', [PatronymeApiController::class, 'show'])->name('show');
        Route::get('/popular', [PatronymeApiController::class, 'popular'])->name('popular');
        Route::get('/recent', [PatronymeApiController::class, 'recent'])->name('recent');
        Route::get('/suggestions', [PatronymeApiController::class, 'suggestions'])->name('suggestions');

        // CRUD protégé
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/', [PatronymeApiController::class, 'store'])->name('store');
            Route::put('/{patronyme}', [PatronymeApiController::class, 'update'])->name('update');
            Route::delete('/{patronyme}', [PatronymeApiController::class, 'destroy'])->name('destroy');
            Route::post('/{patronyme}/favorite', [PatronymeApiController::class, 'toggleFavorite'])->name('favorite');
        });
    });

    // Statistiques essentielles
    Route::prefix('statistics')->name('statistics.')->group(function () {
        Route::get('overview', [StatisticsApiController::class, 'overview'])->name('overview');
        Route::get('advanced', [StatisticsApiController::class, 'advanced'])->middleware('auth:sanctum')->name('advanced');
    });
});
