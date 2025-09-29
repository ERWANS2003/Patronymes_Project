<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatronymeController;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\ImportExportController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\StatisticsController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profil', function () {
        return view('profile.info', ['user' => auth()->user()]);
    })->name('profile.info');
});

// Routes pour les patronymes avec permissions
// Routes publiques (lecture seule)
Route::get('patronymes', [PatronymeController::class, 'index'])->name('patronymes.index');
Route::get('patronymes/{patronyme}', [PatronymeController::class, 'show'])->name('patronymes.show');

// Routes protégées (contribution)
Route::middleware(['auth', 'can.contribute'])->group(function () {
    Route::get('patronymes/create', [PatronymeController::class, 'create'])->name('patronymes.create');
    Route::post('patronymes', [PatronymeController::class, 'store'])->name('patronymes.store');
    Route::get('patronymes/{patronyme}/edit', [PatronymeController::class, 'edit'])->name('patronymes.edit');
    Route::put('patronymes/{patronyme}', [PatronymeController::class, 'update'])->name('patronymes.update');
    Route::delete('patronymes/{patronyme}', [PatronymeController::class, 'destroy'])->name('patronymes.destroy');
});

// Routes pour les commentaires
Route::resource('commentaires', \App\Http\Controllers\CommentaireController::class)->only(['store', 'destroy']);

// Routes AJAX pour les listes dépendantes
Route::get('get-provinces', [PatronymeController::class, 'getProvinces'])->name('get.provinces');
Route::get('get-communes', [PatronymeController::class, 'getCommunes'])->name('get.communes');

// Route pour les suggestions de recherche
Route::get('search-suggestions', [PatronymeController::class, 'getSearchSuggestions'])->name('search.suggestions');

// Minimal API docs route
Route::get('/docs', function () {
    return view('api.docs');
});

// Admin area
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::get('/import', [ImportExportController::class, 'showImportForm'])->name('import');
    Route::post('/import', [ImportExportController::class, 'import'])->name('import.run');
    Route::get('/export', [ImportExportController::class, 'export'])->name('export');
    Route::get('/statistics', [StatisticsController::class, 'index'])->name('statistics');

    // Gestion des rôles (seuls les admins)
    Route::middleware(['can.manage.roles'])->group(function () {
        Route::get('/roles', [\App\Http\Controllers\RoleManagementController::class, 'index'])->name('roles');
        Route::put('/roles/{user}', [\App\Http\Controllers\RoleManagementController::class, 'updateRole'])->name('roles.update');
        Route::post('/roles/{user}/toggle-contribution', [\App\Http\Controllers\RoleManagementController::class, 'toggleContribution'])->name('roles.toggle-contribution');
    });
});

// Favorites routes
Route::middleware(['auth'])->group(function () {
    Route::post('/patronymes/{patronyme}/favorite', [FavoriteController::class, 'toggle'])->name('patronymes.favorite.toggle');
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');

    // Contributions routes (for contributors)
    Route::middleware(['can.contribute'])->group(function () {
        Route::get('/contributions', [\App\Http\Controllers\UserContributionsController::class, 'index'])->name('contributions.index');
    });
});

// Statistics routes
Route::get('/statistics', [StatisticsController::class, 'index'])->name('statistics.index');
Route::get('/api/statistics', [StatisticsController::class, 'api'])->name('statistics.api');

require __DIR__.'/auth.php';
