<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MobilePatronymeController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StatisticsApiController;

// API Mobile optimisée
Route::prefix('api/mobile')->name('mobile.')->group(function () {

    // Authentification mobile
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::post('login', [AuthController::class, 'mobileLogin'])->name('login');
        Route::post('register', [AuthController::class, 'mobileRegister'])->name('register');
        Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum')->name('logout');
        Route::get('user', [AuthController::class, 'user'])->middleware('auth:sanctum')->name('user');
        Route::post('refresh', [AuthController::class, 'refresh'])->middleware('auth:sanctum')->name('refresh');
    });

    // Patronymes optimisés pour mobile
    Route::prefix('patronymes')->name('patronymes.')->group(function () {
        // Routes publiques
        Route::get('/', [MobilePatronymeController::class, 'mobileIndex'])->name('index');
        Route::get('/{patronyme}', [MobilePatronymeController::class, 'mobileShow'])->name('show');
        Route::get('/popular', [MobilePatronymeController::class, 'mobilePopular'])->name('popular');
        Route::get('/recent', [MobilePatronymeController::class, 'mobileRecent'])->name('recent');
        Route::get('/search', [MobilePatronymeController::class, 'mobileSearch'])->name('search');
        Route::get('/by-letter/{letter}', [MobilePatronymeController::class, 'mobileByLetter'])->name('by-letter');

        // Routes protégées
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/', [MobilePatronymeController::class, 'store'])->name('store');
            Route::put('/{patronyme}', [MobilePatronymeController::class, 'update'])->name('update');
            Route::delete('/{patronyme}', [MobilePatronymeController::class, 'destroy'])->name('destroy');
            Route::post('/{patronyme}/favorite', [MobilePatronymeController::class, 'toggleFavorite'])->name('favorite');
            Route::get('/favorites', [MobilePatronymeController::class, 'favorites'])->name('favorites');
        });
    });

    // Statistiques mobiles
    Route::prefix('stats')->name('stats.')->group(function () {
        Route::get('overview', [StatisticsApiController::class, 'mobileOverview'])->name('overview');
        Route::get('trends', [StatisticsApiController::class, 'mobileTrends'])->name('trends');
        Route::get('popular-by-region', [StatisticsApiController::class, 'mobilePopularByRegion'])->name('popular-by-region');
    });

    // Fonctionnalités mobiles spécifiques
    Route::prefix('features')->name('features.')->group(function () {
        Route::get('offline-data', [MobilePatronymeController::class, 'getOfflineData'])->name('offline-data');
        Route::post('sync', [MobilePatronymeController::class, 'syncData'])->middleware('auth:sanctum')->name('sync');
        Route::get('search-suggestions', [MobilePatronymeController::class, 'getSearchSuggestions'])->name('search-suggestions');
        Route::post('feedback', [MobilePatronymeController::class, 'submitFeedback'])->middleware('auth:sanctum')->name('feedback');
    });

    // Notifications push
    Route::prefix('notifications')->name('notifications.')->middleware('auth:sanctum')->group(function () {
        Route::post('subscribe', [MobilePatronymeController::class, 'subscribeToNotifications'])->name('subscribe');
        Route::post('unsubscribe', [MobilePatronymeController::class, 'unsubscribeFromNotifications'])->name('unsubscribe');
        Route::get('settings', [MobilePatronymeController::class, 'getNotificationSettings'])->name('settings');
        Route::put('settings', [MobilePatronymeController::class, 'updateNotificationSettings'])->name('update-settings');
    });
});
