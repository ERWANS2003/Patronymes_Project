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
})->name('welcome');

Route::get('/welcome', function () {
    return view('welcome');
})->name('welcome');

Route::get('/dashboard', function () {
    $statisticsService = app(\App\Services\StatisticsService::class);
    $stats = $statisticsService->getDashboardStats();

    // Extraire les données spécifiques pour la vue
    $recentPatronymes = $stats['recent_patronymes'] ?? collect();
    $popularPatronymes = $stats['most_viewed'] ?? collect();

    return view('dashboard', compact('stats', 'recentPatronymes', 'popularPatronymes'));
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profil', function () {
        return view('profile.info', ['user' => auth()->user()]);
    })->name('profile.info');

    // Route pour profile.show (Jetstream)
    Route::get('/user/profile', function () {
        return view('profile.show');
    })->name('profile.show');
});

// Routes pour les patronymes avec permissions
// Routes publiques (lecture seule)
Route::get('patronymes', [PatronymeController::class, 'index'])->name('patronymes.index');

// Routes protégées (contribution) - DOIT être avant les routes avec paramètres
Route::middleware(['auth', \App\Http\Middleware\CanContributeMiddleware::class])->group(function () {
    Route::get('patronymes/create', [PatronymeController::class, 'create'])->name('patronymes.create');
    Route::post('patronymes', [PatronymeController::class, 'store'])
        ->middleware('rate.limit:10,1')
        ->name('patronymes.store');
    Route::get('patronymes/{patronyme}/edit', [PatronymeController::class, 'edit'])->name('patronymes.edit');
    Route::put('patronymes/{patronyme}', [PatronymeController::class, 'update'])
        ->middleware('rate.limit:20,1')
        ->name('patronymes.update');
    Route::delete('patronymes/{patronyme}', [PatronymeController::class, 'destroy'])
        ->middleware('rate.limit:5,1')
        ->name('patronymes.destroy');
});

// Route publique avec paramètre (DOIT être après les routes spécifiques)
Route::get('patronymes/{patronyme}', [PatronymeController::class, 'show'])->name('patronymes.show');

// Routes pour les commentaires
Route::resource('commentaires', \App\Http\Controllers\CommentaireController::class)->only(['store', 'destroy']);

// Routes AJAX pour les listes dépendantes
Route::get('get-provinces', [PatronymeController::class, 'getProvinces'])->name('get.provinces');
Route::get('get-communes', [PatronymeController::class, 'getCommunes'])->name('get.communes');

// Routes API pour les listes dépendantes
Route::get('api/regions/{region}/provinces', function ($region) {
    $provinces = \App\Models\Province::where('region_id', $region)->get();
    return response()->json($provinces);
});

Route::get('api/provinces/{province}/communes', function ($province) {
    $communes = \App\Models\Commune::where('province_id', $province)->get();
    return response()->json($communes);
});

// Route pour les suggestions de recherche avec rate limiting
Route::get('search-suggestions', [PatronymeController::class, 'getSearchSuggestions'])
    ->middleware('rate.limit:30,1')
    ->name('search.suggestions');

// Routes pour les recherches optimisées
Route::get('api/popular-patronymes', [PatronymeController::class, 'getPopularPatronymes'])
    ->name('api.popular.patronymes');

Route::get('api/patronymes/letter/{letter}', [PatronymeController::class, 'getPatronymesByLetter'])
    ->name('api.patronymes.letter');

// Minimal API docs route
Route::get('/docs', function () {
    return view('api.docs');
});

// Admin area
Route::middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::get('/import', [ImportExportController::class, 'showImportForm'])->name('import');
    Route::post('/import', [ImportExportController::class, 'import'])->name('import.run');
    Route::get('/export', [ImportExportController::class, 'export'])->name('export');

    // Routes de santé et monitoring
    Route::get('/health', [\App\Http\Controllers\HealthController::class, 'check'])->name('health.check');
    Route::get('/metrics', [\App\Http\Controllers\HealthController::class, 'metrics'])->name('metrics');
    Route::get('/statistics', [StatisticsController::class, 'index'])->name('statistics');

    // Gestion des rôles (seuls les admins)
    Route::middleware([\App\Http\Middleware\CanManageRolesMiddleware::class])->group(function () {
        Route::get('/roles', [\App\Http\Controllers\RoleManagementController::class, 'index'])->name('roles');
        Route::put('/roles/{user}', [\App\Http\Controllers\RoleManagementController::class, 'updateRole'])->name('roles.update');
        Route::post('/roles/{user}/toggle-contribution', [\App\Http\Controllers\RoleManagementController::class, 'toggleContribution'])->name('roles.toggle-contribution');
    });

    // Gestion des utilisateurs (seuls les admins)
    Route::middleware([\App\Http\Middleware\CanManageRolesMiddleware::class])->group(function () {
        Route::resource('users', \App\Http\Controllers\AdminUserController::class);
    });

    // Routes de monitoring
    Route::prefix('monitoring')->name('monitoring.')->group(function () {
        Route::get('/', [App\Http\Controllers\MonitoringController::class, 'dashboard'])->name('dashboard');
        Route::get('/dashboard', [App\Http\Controllers\MonitoringController::class, 'dashboard'])->name('dashboard');
        Route::get('/health', [App\Http\Controllers\MonitoringController::class, 'health'])->name('health');
        Route::get('/metrics', [App\Http\Controllers\MonitoringController::class, 'metrics'])->name('metrics');
        Route::get('/performance-report', [App\Http\Controllers\MonitoringController::class, 'performanceReport'])->name('performance-report');
        Route::post('/cleanup-logs', [App\Http\Controllers\MonitoringController::class, 'cleanupLogs'])->name('cleanup-logs');
        Route::get('/logs', [App\Http\Controllers\MonitoringController::class, 'logs'])->name('logs');
        Route::get('/export', [App\Http\Controllers\MonitoringController::class, 'exportMetrics'])->name('export');
        Route::get('/application', function () {
            return view('monitoring.application');
        })->name('application');
    });
});

// Favorites routes
Route::middleware(['auth'])->group(function () {
    Route::post('/patronymes/{patronyme}/favorite', [FavoriteController::class, 'toggle'])->name('patronymes.favorite.toggle');
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');

    // Contributions routes (for contributors)
    Route::middleware([\App\Http\Middleware\CanContributeMiddleware::class])->group(function () {
        Route::get('/contributions', [\App\Http\Controllers\UserContributionsController::class, 'index'])->name('contributions.index');
    });
});

// Statistics routes
Route::get('/statistics', [StatisticsController::class, 'index'])->name('statistics.index');
Route::get('/api/statistics', [StatisticsController::class, 'api'])->name('statistics.api');

require __DIR__.'/auth.php';
