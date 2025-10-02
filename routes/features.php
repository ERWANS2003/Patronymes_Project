<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatronymeController;

// Routes des nouvelles fonctionnalités
Route::prefix('features')->name('features.')->group(function () {

    // Recherche avancée
    Route::prefix('search')->name('search.')->group(function () {
        Route::get('suggestions', [PatronymeController::class, 'getSearchSuggestions'])
            ->middleware('rate.limit:30,1')
            ->name('suggestions');
        Route::get('popular', [PatronymeController::class, 'getPopularPatronymes'])
            ->name('popular');
        Route::get('by-letter/{letter}', [PatronymeController::class, 'getPatronymesByLetter'])
            ->name('by-letter');
    });

    // Partage et export
    Route::prefix('share')->name('share.')->group(function () {
        Route::get('patronyme/{patronyme}', [PatronymeController::class, 'share'])->name('patronyme');
        Route::get('export/{format}', [PatronymeController::class, 'export'])->name('export');
    });

    // Statistiques avancées
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('trends', [PatronymeController::class, 'getTrends'])->name('trends');
        Route::get('popular-by-region', [PatronymeController::class, 'getPopularByRegion'])->name('popular-by-region');
        Route::get('search-analytics', [PatronymeController::class, 'getSearchAnalytics'])->name('search');
    });
});
