<?php

use App\Http\Controllers\Api\PatronymeApiController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StatisticsApiController;
use Illuminate\Support\Facades\Route;

// API Version 1
Route::prefix('v1')->name('api.v1.')->group(function () {

    // Routes publiques
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::post('login', [AuthController::class, 'login'])->name('login');
        Route::post('register', [AuthController::class, 'register'])->name('register');
    });

    // Patronymes - routes publiques
    Route::prefix('patronymes')->name('patronymes.')->group(function () {
        Route::get('/', [PatronymeApiController::class, 'index'])->name('index');
        Route::get('/popular', [PatronymeApiController::class, 'popular'])->name('popular');
        Route::get('/recent', [PatronymeApiController::class, 'recent'])->name('recent');
        Route::get('/suggestions', [PatronymeApiController::class, 'suggestions'])->name('suggestions');
        Route::get('/{patronyme}', [PatronymeApiController::class, 'show'])->name('show');
    });

    // Statistiques publiques
    Route::get('statistics/overview', [StatisticsApiController::class, 'overview'])->name('statistics.overview');

    // Routes protégées
    Route::middleware('auth:sanctum')->group(function () {

        // Authentification
        Route::prefix('auth')->name('auth.')->group(function () {
            Route::post('logout', [AuthController::class, 'logout'])->name('logout');
            Route::get('user', [AuthController::class, 'user'])->name('user');
            Route::post('refresh', [AuthController::class, 'refresh'])->name('refresh');
        });

        // Patronymes - CRUD
        Route::prefix('patronymes')->name('patronymes.')->group(function () {
            Route::post('/', [PatronymeApiController::class, 'store'])->name('store');
            Route::put('/{patronyme}', [PatronymeApiController::class, 'update'])->name('update');
            Route::delete('/{patronyme}', [PatronymeApiController::class, 'destroy'])->name('destroy');
            Route::post('/{patronyme}/favorite', [PatronymeApiController::class, 'toggleFavorite'])->name('favorite');
        });

        // Favoris
        Route::get('favorites', [PatronymeApiController::class, 'favorites'])->name('favorites');

        // Statistiques avancées
        Route::prefix('statistics')->name('statistics.')->group(function () {
            Route::get('advanced', [StatisticsApiController::class, 'advanced'])->name('advanced');
            Route::get('realtime', [StatisticsApiController::class, 'realtime'])->name('realtime');
        });

        // Administration
        Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
            Route::get('users', [AuthController::class, 'users'])->name('users');
            Route::get('analytics', [StatisticsApiController::class, 'adminAnalytics'])->name('analytics');
        });
    });
});

// Routes de compatibilité (sans versioning)
Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('register', [AuthController::class, 'register'])->name('register');
Route::get('patronymes', [PatronymeApiController::class, 'index'])->name('patronymes.index');
Route::get('patronymes/{patronyme}', [PatronymeApiController::class, 'show'])->name('patronymes.show');

// Routes protégées
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('patronymes', PatronymeApiController::class)->except(['show']);
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('user', [AuthController::class, 'user'])->name('user');

    // Monitoring simplifié (admin uniquement)
    Route::middleware(['admin'])->prefix('monitoring')->name('monitoring.')->group(function () {
        // Métriques essentielles
        Route::get('metrics', [\App\Http\Controllers\Api\RealTimeMetricsApiController::class, 'index'])->name('metrics');
        Route::get('performance', [\App\Http\Controllers\Api\PerformanceMonitoringApiController::class, 'index'])->name('performance');
        Route::get('health', [\App\Http\Controllers\Api\RealTimeMetricsApiController::class, 'health'])->name('health');

        // Rapports
        Route::get('reports', [\App\Http\Controllers\Api\AdvancedMetricsApiController::class, 'report'])->name('reports');
        Route::get('export', [\App\Http\Controllers\Api\AdvancedMetricsApiController::class, 'export'])->name('export');
    });
});

// Inclure l'API simplifiée
require __DIR__.'/api-simple.php';

// Inclure l'API mobile
require __DIR__.'/mobile.php';
