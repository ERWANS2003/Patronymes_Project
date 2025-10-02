<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatronymeController;
use App\Http\Controllers\ImportExportController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\StatisticsController;

// Routes publiques
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Dashboard
Route::get('/dashboard', function () {
    $statisticsService = app(\App\Services\StatisticsService::class);
    $stats = $statisticsService->getDashboardStats();

    // Extraire les données spécifiques pour la vue
    $recentPatronymes = $stats['recent_patronymes'] ?? collect();
    $popularPatronymes = $stats['most_viewed'] ?? collect();

    return view('dashboard', compact('stats', 'recentPatronymes', 'popularPatronymes'));
})->middleware(['auth'])->name('dashboard');

// Routes d'authentification et profil
Route::middleware('auth')->group(function () {
    // Profil utilisateur
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
        Route::get('/info', function () {
            return view('profile.info', ['user' => auth()->user()]);
        })->name('info');
        Route::get('/show', function () {
            return view('profile.show');
        })->name('show');
    });
});

// Routes des patronymes
Route::prefix('patronymes')->name('patronymes.')->group(function () {
    // Routes publiques
    Route::get('/', [PatronymeController::class, 'index'])->name('index');

    // Routes protégées (contribution) - DOIT être AVANT les routes avec paramètres
    Route::middleware(['auth', \App\Http\Middleware\CanContributeMiddleware::class])->group(function () {
        Route::get('/create', [PatronymeController::class, 'create'])->name('create');
        Route::post('/', [PatronymeController::class, 'store'])
            ->middleware('rate.limit:10,1')
            ->name('store');
        Route::get('/{patronyme}/edit', [PatronymeController::class, 'edit'])->name('edit');
        Route::put('/{patronyme}', [PatronymeController::class, 'update'])
            ->middleware('rate.limit:20,1')
            ->name('update');
        Route::delete('/{patronyme}', [PatronymeController::class, 'destroy'])
            ->middleware('rate.limit:5,1')
            ->name('destroy');
    });

    // Route avec paramètre - DOIT être APRÈS les routes spécifiques
    Route::get('/{patronyme}', [PatronymeController::class, 'show'])->name('show');
});

// Routes des commentaires
Route::resource('commentaires', \App\Http\Controllers\CommentaireController::class)->only(['store', 'destroy']);

// Routes AJAX et API
Route::prefix('api')->name('api.')->group(function () {
    // Listes dépendantes
    Route::get('regions/{region}/provinces', function ($region) {
        $provinces = \App\Models\Province::where('region_id', $region)->get();
        return response()->json($provinces);
    })->name('regions.provinces');

    Route::get('provinces/{province}/communes', function ($province) {
        $communes = \App\Models\Commune::where('province_id', $province)->get();
        return response()->json($communes);
    })->name('provinces.communes');

    // Recherche et suggestions
    Route::get('search-suggestions', [PatronymeController::class, 'getSearchSuggestions'])
        ->middleware('rate.limit:30,1')
        ->name('search.suggestions');

    Route::get('popular-patronymes', [PatronymeController::class, 'getPopularPatronymes'])
        ->name('popular.patronymes');

    Route::get('patronymes/letter/{letter}', [PatronymeController::class, 'getPatronymesByLetter'])
        ->name('patronymes.letter');

    // Routes AJAX pour les listes dépendantes
    Route::get('get-provinces', [PatronymeController::class, 'getProvinces'])->name('get.provinces');
    Route::get('get-communes', [PatronymeController::class, 'getCommunes'])->name('get.communes');
});

// Route pour l'application mobile
Route::get('mobile', function () {
    return view('mobile.app');
})->name('mobile.app');

// Documentation API
Route::get('/docs', function () {
    return view('api.docs');
})->name('docs');

// Zone d'administration
Route::middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Dashboard admin
        Route::get('/', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        // Import/Export
        Route::prefix('data')->name('data.')->group(function () {
            Route::get('/import', [ImportExportController::class, 'showImportForm'])->name('import');
            Route::post('/import', [ImportExportController::class, 'import'])->name('import.run');
            Route::get('/export', [ImportExportController::class, 'export'])->name('export');
        });

        // Santé et monitoring
        Route::prefix('health')->name('health.')->group(function () {
            Route::get('/', [\App\Http\Controllers\HealthController::class, 'check'])->name('check');
            Route::get('/metrics', [\App\Http\Controllers\HealthController::class, 'metrics'])->name('metrics');
        });

        // Statistiques
        Route::get('/statistics', [StatisticsController::class, 'index'])->name('statistics');

        // Gestion des rôles
        Route::middleware([\App\Http\Middleware\CanManageRolesMiddleware::class])
            ->prefix('roles')
            ->name('roles.')
            ->group(function () {
                Route::get('/', [\App\Http\Controllers\RoleManagementController::class, 'index'])->name('index');
                Route::put('/{user}', [\App\Http\Controllers\RoleManagementController::class, 'updateRole'])->name('update');
                Route::post('/{user}/toggle-contribution', [\App\Http\Controllers\RoleManagementController::class, 'toggleContribution'])->name('toggle-contribution');
            });

        // Gestion des utilisateurs
        Route::middleware([\App\Http\Middleware\CanManageRolesMiddleware::class])
            ->resource('users', \App\Http\Controllers\AdminUserController::class);

        // Monitoring (simplifié)
        Route::prefix('monitoring')->name('monitoring.')->group(function () {
            Route::get('/', [App\Http\Controllers\MonitoringController::class, 'dashboard'])->name('dashboard');
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

// Routes des favoris et contributions
Route::middleware(['auth'])->group(function () {
    // Favoris
    Route::post('/patronymes/{patronyme}/favorite', [FavoriteController::class, 'toggle'])->name('patronymes.favorite.toggle');
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');

    // Contributions (pour les contributeurs)
    Route::middleware([\App\Http\Middleware\CanContributeMiddleware::class])->group(function () {
        Route::get('/contributions', [\App\Http\Controllers\UserContributionsController::class, 'index'])->name('contributions.index');
    });
});

// Routes des statistiques
Route::prefix('statistics')->name('statistics.')->group(function () {
    Route::get('/', [StatisticsController::class, 'index'])->name('index');
    Route::get('/api', [StatisticsController::class, 'api'])->name('api');
});

// Inclure les routes d'authentification
require __DIR__.'/auth.php';

// Inclure les routes des nouvelles fonctionnalités
require __DIR__.'/features.php';
