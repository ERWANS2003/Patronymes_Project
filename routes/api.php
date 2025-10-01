<?php

use App\Http\Controllers\Api\PatronymeApiController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StatisticsApiController;
use Illuminate\Support\Facades\Route;

// Versioning de l'API
Route::prefix('v1')->group(function () {

    // Routes publiques
    Route::post('auth/login', [AuthController::class, 'login']);
    Route::post('auth/register', [AuthController::class, 'register']);

    // Patronymes - routes publiques
    Route::get('patronymes', [PatronymeApiController::class, 'index']);
    Route::get('patronymes/popular', [PatronymeApiController::class, 'popular']);
    Route::get('patronymes/recent', [PatronymeApiController::class, 'recent']);
    Route::get('patronymes/suggestions', [PatronymeApiController::class, 'suggestions']);
    Route::get('patronymes/{patronyme}', [PatronymeApiController::class, 'show']);

    // Statistiques publiques
    Route::get('statistics/overview', [StatisticsApiController::class, 'overview']);

    // Routes protégées
    Route::middleware('auth:sanctum')->group(function () {

        // Authentification
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('auth/user', [AuthController::class, 'user']);
        Route::post('auth/refresh', [AuthController::class, 'refresh']);

        // Patronymes - CRUD
        Route::post('patronymes', [PatronymeApiController::class, 'store']);
        Route::put('patronymes/{patronyme}', [PatronymeApiController::class, 'update']);
        Route::delete('patronymes/{patronyme}', [PatronymeApiController::class, 'destroy']);

        // Favoris
        Route::post('patronymes/{patronyme}/favorite', [PatronymeApiController::class, 'toggleFavorite']);
        Route::get('favorites', [PatronymeApiController::class, 'favorites']);

        // Statistiques avancées
        Route::get('statistics/advanced', [StatisticsApiController::class, 'advanced']);
        Route::get('statistics/realtime', [StatisticsApiController::class, 'realtime']);

        // Administration
        Route::middleware('admin')->group(function () {
            Route::get('admin/users', [AuthController::class, 'users']);
            Route::get('admin/analytics', [StatisticsApiController::class, 'adminAnalytics']);
        });
    });
});

// Routes de compatibilité (sans versioning)
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::get('patronymes', [PatronymeApiController::class, 'index']);
Route::get('patronymes/{patronyme}', [PatronymeApiController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('patronymes', PatronymeApiController::class)->except(['show']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('user', [AuthController::class, 'user']);

    // Métriques en temps réel (admin uniquement)
    Route::middleware(['admin'])->prefix('metrics')->name('metrics.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\RealTimeMetricsApiController::class, 'index'])->name('index');
        Route::get('/performance', [\App\Http\Controllers\Api\RealTimeMetricsApiController::class, 'performance'])->name('performance');
        Route::get('/activity', [\App\Http\Controllers\Api\RealTimeMetricsApiController::class, 'activity'])->name('activity');
        Route::get('/health', [\App\Http\Controllers\Api\RealTimeMetricsApiController::class, 'health'])->name('health');
        Route::get('/system', [\App\Http\Controllers\Api\RealTimeMetricsApiController::class, 'system'])->name('system');
        Route::get('/performance-report', [\App\Http\Controllers\Api\RealTimeMetricsApiController::class, 'performanceReport'])->name('performance-report');
    });

    // Métriques avancées (admin uniquement)
    Route::middleware(['admin'])->prefix('advanced-metrics')->name('advanced-metrics.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\AdvancedMetricsApiController::class, 'index'])->name('index');
        Route::get('/application', [\App\Http\Controllers\Api\AdvancedMetricsApiController::class, 'application'])->name('application');
        Route::get('/database', [\App\Http\Controllers\Api\AdvancedMetricsApiController::class, 'database'])->name('database');
        Route::get('/performance', [\App\Http\Controllers\Api\AdvancedMetricsApiController::class, 'performance'])->name('performance');
        Route::get('/user-behavior', [\App\Http\Controllers\Api\AdvancedMetricsApiController::class, 'userBehavior'])->name('user-behavior');
        Route::get('/content', [\App\Http\Controllers\Api\AdvancedMetricsApiController::class, 'content'])->name('content');
        Route::get('/system', [\App\Http\Controllers\Api\AdvancedMetricsApiController::class, 'system'])->name('system');
        Route::get('/report', [\App\Http\Controllers\Api\AdvancedMetricsApiController::class, 'report'])->name('report');
    });

    // Surveillance des erreurs (admin uniquement)
    Route::middleware(['admin'])->prefix('error-monitoring')->name('error-monitoring.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\ErrorMonitoringApiController::class, 'index'])->name('index');
        Route::get('/statistics', [\App\Http\Controllers\Api\ErrorMonitoringApiController::class, 'statistics'])->name('statistics');
        Route::get('/recent', [\App\Http\Controllers\Api\ErrorMonitoringApiController::class, 'recent'])->name('recent');
        Route::get('/trends', [\App\Http\Controllers\Api\ErrorMonitoringApiController::class, 'trends'])->name('trends');
        Route::get('/alerts', [\App\Http\Controllers\Api\ErrorMonitoringApiController::class, 'alerts'])->name('alerts');
        Route::get('/report', [\App\Http\Controllers\Api\ErrorMonitoringApiController::class, 'report'])->name('report');
        Route::post('/cleanup', [\App\Http\Controllers\Api\ErrorMonitoringApiController::class, 'cleanup'])->name('cleanup');
        Route::get('/export', [\App\Http\Controllers\Api\ErrorMonitoringApiController::class, 'export'])->name('export');
    });

    // Surveillance de la sécurité (admin uniquement)
    Route::middleware(['admin'])->prefix('security-monitoring')->name('security-monitoring.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\SecurityMonitoringApiController::class, 'index'])->name('index');
        Route::get('/statistics', [\App\Http\Controllers\Api\SecurityMonitoringApiController::class, 'statistics'])->name('statistics');
        Route::get('/recent', [\App\Http\Controllers\Api\SecurityMonitoringApiController::class, 'recent'])->name('recent');
        Route::get('/threats', [\App\Http\Controllers\Api\SecurityMonitoringApiController::class, 'threats'])->name('threats');
        Route::get('/alerts', [\App\Http\Controllers\Api\SecurityMonitoringApiController::class, 'alerts'])->name('alerts');
        Route::get('/report', [\App\Http\Controllers\Api\SecurityMonitoringApiController::class, 'report'])->name('report');
        Route::post('/cleanup', [\App\Http\Controllers\Api\SecurityMonitoringApiController::class, 'cleanup'])->name('cleanup');
        Route::get('/export', [\App\Http\Controllers\Api\SecurityMonitoringApiController::class, 'export'])->name('export');
    });

    // Surveillance de la base de données (admin uniquement)
    Route::middleware(['admin'])->prefix('database-monitoring')->name('database-monitoring.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\DatabaseMonitoringApiController::class, 'index'])->name('index');
        Route::get('/statistics', [\App\Http\Controllers\Api\DatabaseMonitoringApiController::class, 'statistics'])->name('statistics');
        Route::get('/slow-queries', [\App\Http\Controllers\Api\DatabaseMonitoringApiController::class, 'slowQueries'])->name('slow-queries');
        Route::get('/connections', [\App\Http\Controllers\Api\DatabaseMonitoringApiController::class, 'connections'])->name('connections');
        Route::get('/alerts', [\App\Http\Controllers\Api\DatabaseMonitoringApiController::class, 'alerts'])->name('alerts');
        Route::get('/report', [\App\Http\Controllers\Api\DatabaseMonitoringApiController::class, 'report'])->name('report');
        Route::post('/cleanup', [\App\Http\Controllers\Api\DatabaseMonitoringApiController::class, 'cleanup'])->name('cleanup');
        Route::get('/export', [\App\Http\Controllers\Api\DatabaseMonitoringApiController::class, 'export'])->name('export');
    });

    // Surveillance des performances (admin uniquement)
    Route::middleware(['admin'])->prefix('performance-monitoring')->name('performance-monitoring.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\PerformanceMonitoringApiController::class, 'index'])->name('index');
        Route::get('/statistics', [\App\Http\Controllers\Api\PerformanceMonitoringApiController::class, 'statistics'])->name('statistics');
        Route::get('/response-times', [\App\Http\Controllers\Api\PerformanceMonitoringApiController::class, 'responseTimes'])->name('response-times');
        Route::get('/memory-usage', [\App\Http\Controllers\Api\PerformanceMonitoringApiController::class, 'memoryUsage'])->name('memory-usage');
        Route::get('/alerts', [\App\Http\Controllers\Api\PerformanceMonitoringApiController::class, 'alerts'])->name('alerts');
        Route::get('/report', [\App\Http\Controllers\Api\PerformanceMonitoringApiController::class, 'report'])->name('report');
        Route::post('/cleanup', [\App\Http\Controllers\Api\PerformanceMonitoringApiController::class, 'cleanup'])->name('cleanup');
        Route::get('/export', [\App\Http\Controllers\Api\PerformanceMonitoringApiController::class, 'export'])->name('export');
    });

    // Surveillance des utilisateurs (admin uniquement)
    Route::middleware(['admin'])->prefix('user-monitoring')->name('user-monitoring.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\UserMonitoringApiController::class, 'index'])->name('index');
        Route::get('/statistics', [\App\Http\Controllers\Api\UserMonitoringApiController::class, 'statistics'])->name('statistics');
        Route::get('/activity', [\App\Http\Controllers\Api\UserMonitoringApiController::class, 'activity'])->name('activity');
        Route::get('/engagement', [\App\Http\Controllers\Api\UserMonitoringApiController::class, 'engagement'])->name('engagement');
        Route::get('/alerts', [\App\Http\Controllers\Api\UserMonitoringApiController::class, 'alerts'])->name('alerts');
        Route::get('/report', [\App\Http\Controllers\Api\UserMonitoringApiController::class, 'report'])->name('report');
        Route::post('/cleanup', [\App\Http\Controllers\Api\UserMonitoringApiController::class, 'cleanup'])->name('cleanup');
        Route::get('/export', [\App\Http\Controllers\Api\UserMonitoringApiController::class, 'export'])->name('export');
    });

    // Surveillance des logs (admin uniquement)
    Route::middleware(['admin'])->prefix('log-monitoring')->name('log-monitoring.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\LogMonitoringApiController::class, 'index'])->name('index');
        Route::get('/statistics', [\App\Http\Controllers\Api\LogMonitoringApiController::class, 'statistics'])->name('statistics');
        Route::get('/recent', [\App\Http\Controllers\Api\LogMonitoringApiController::class, 'recent'])->name('recent');
        Route::get('/trends', [\App\Http\Controllers\Api\LogMonitoringApiController::class, 'trends'])->name('trends');
        Route::get('/alerts', [\App\Http\Controllers\Api\LogMonitoringApiController::class, 'alerts'])->name('alerts');
        Route::get('/report', [\App\Http\Controllers\Api\LogMonitoringApiController::class, 'report'])->name('report');
        Route::post('/cleanup', [\App\Http\Controllers\Api\LogMonitoringApiController::class, 'cleanup'])->name('cleanup');
        Route::get('/export', [\App\Http\Controllers\Api\LogMonitoringApiController::class, 'export'])->name('export');
    });

    // Surveillance des caches (admin uniquement)
    Route::middleware(['admin'])->prefix('cache-monitoring')->name('cache-monitoring.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\CacheMonitoringApiController::class, 'index'])->name('index');
        Route::get('/statistics', [\App\Http\Controllers\Api\CacheMonitoringApiController::class, 'statistics'])->name('statistics');
        Route::get('/performance', [\App\Http\Controllers\Api\CacheMonitoringApiController::class, 'performance'])->name('performance');
        Route::get('/keys', [\App\Http\Controllers\Api\CacheMonitoringApiController::class, 'keys'])->name('keys');
        Route::get('/alerts', [\App\Http\Controllers\Api\CacheMonitoringApiController::class, 'alerts'])->name('alerts');
        Route::get('/report', [\App\Http\Controllers\Api\CacheMonitoringApiController::class, 'report'])->name('report');
        Route::post('/clear', [\App\Http\Controllers\Api\CacheMonitoringApiController::class, 'clear'])->name('clear');
        Route::get('/export', [\App\Http\Controllers\Api\CacheMonitoringApiController::class, 'export'])->name('export');
    });

    // Surveillance des sessions (admin uniquement)
    Route::middleware(['admin'])->prefix('session-monitoring')->name('session-monitoring.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\SessionMonitoringApiController::class, 'index'])->name('index');
        Route::get('/statistics', [\App\Http\Controllers\Api\SessionMonitoringApiController::class, 'statistics'])->name('statistics');
        Route::get('/active', [\App\Http\Controllers\Api\SessionMonitoringApiController::class, 'active'])->name('active');
        Route::get('/trends', [\App\Http\Controllers\Api\SessionMonitoringApiController::class, 'trends'])->name('trends');
        Route::get('/alerts', [\App\Http\Controllers\Api\SessionMonitoringApiController::class, 'alerts'])->name('alerts');
        Route::get('/report', [\App\Http\Controllers\Api\SessionMonitoringApiController::class, 'report'])->name('report');
        Route::post('/cleanup', [\App\Http\Controllers\Api\SessionMonitoringApiController::class, 'cleanup'])->name('cleanup');
        Route::get('/export', [\App\Http\Controllers\Api\SessionMonitoringApiController::class, 'export'])->name('export');
    });

    // Surveillance des files d'attente (admin uniquement)
    Route::middleware(['admin'])->prefix('queue-monitoring')->name('queue-monitoring.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\QueueMonitoringApiController::class, 'index'])->name('index');
        Route::get('/statistics', [\App\Http\Controllers\Api\QueueMonitoringApiController::class, 'statistics'])->name('statistics');
        Route::get('/jobs', [\App\Http\Controllers\Api\QueueMonitoringApiController::class, 'jobs'])->name('jobs');
        Route::get('/performance', [\App\Http\Controllers\Api\QueueMonitoringApiController::class, 'performance'])->name('performance');
        Route::get('/alerts', [\App\Http\Controllers\Api\QueueMonitoringApiController::class, 'alerts'])->name('alerts');
        Route::get('/report', [\App\Http\Controllers\Api\QueueMonitoringApiController::class, 'report'])->name('report');
        Route::post('/cleanup', [\App\Http\Controllers\Api\QueueMonitoringApiController::class, 'cleanup'])->name('cleanup');
        Route::get('/export', [\App\Http\Controllers\Api\QueueMonitoringApiController::class, 'export'])->name('export');
    });

    // Surveillance des emails (admin uniquement)
    Route::middleware(['admin'])->prefix('email-monitoring')->name('email-monitoring.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\EmailMonitoringApiController::class, 'index'])->name('index');
        Route::get('/statistics', [\App\Http\Controllers\Api\EmailMonitoringApiController::class, 'statistics'])->name('statistics');
        Route::get('/performance', [\App\Http\Controllers\Api\EmailMonitoringApiController::class, 'performance'])->name('performance');
        Route::get('/trends', [\App\Http\Controllers\Api\EmailMonitoringApiController::class, 'trends'])->name('trends');
        Route::get('/alerts', [\App\Http\Controllers\Api\EmailMonitoringApiController::class, 'alerts'])->name('alerts');
        Route::get('/report', [\App\Http\Controllers\Api\EmailMonitoringApiController::class, 'report'])->name('report');
        Route::post('/cleanup', [\App\Http\Controllers\Api\EmailMonitoringApiController::class, 'cleanup'])->name('cleanup');
        Route::get('/export', [\App\Http\Controllers\Api\EmailMonitoringApiController::class, 'export'])->name('export');
    });

    // Surveillance des fichiers (admin uniquement)
    Route::middleware(['admin'])->prefix('file-monitoring')->name('file-monitoring.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\FileMonitoringApiController::class, 'index'])->name('index');
        Route::get('/statistics', [\App\Http\Controllers\Api\FileMonitoringApiController::class, 'statistics'])->name('statistics');
        Route::get('/permissions', [\App\Http\Controllers\Api\FileMonitoringApiController::class, 'permissions'])->name('permissions');
        Route::get('/trends', [\App\Http\Controllers\Api\FileMonitoringApiController::class, 'trends'])->name('trends');
        Route::get('/alerts', [\App\Http\Controllers\Api\FileMonitoringApiController::class, 'alerts'])->name('alerts');
        Route::get('/report', [\App\Http\Controllers\Api\FileMonitoringApiController::class, 'report'])->name('report');
        Route::post('/cleanup', [\App\Http\Controllers\Api\FileMonitoringApiController::class, 'cleanup'])->name('cleanup');
        Route::get('/export', [\App\Http\Controllers\Api\FileMonitoringApiController::class, 'export'])->name('export');
    });

    // Surveillance des réseaux (admin uniquement)
    Route::middleware(['admin'])->prefix('network-monitoring')->name('network-monitoring.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\NetworkMonitoringApiController::class, 'index'])->name('index');
        Route::get('/statistics', [\App\Http\Controllers\Api\NetworkMonitoringApiController::class, 'statistics'])->name('statistics');
        Route::get('/performance', [\App\Http\Controllers\Api\NetworkMonitoringApiController::class, 'performance'])->name('performance');
        Route::get('/trends', [\App\Http\Controllers\Api\NetworkMonitoringApiController::class, 'trends'])->name('trends');
        Route::get('/alerts', [\App\Http\Controllers\Api\NetworkMonitoringApiController::class, 'alerts'])->name('alerts');
        Route::get('/report', [\App\Http\Controllers\Api\NetworkMonitoringApiController::class, 'report'])->name('report');
        Route::post('/cleanup', [\App\Http\Controllers\Api\NetworkMonitoringApiController::class, 'cleanup'])->name('cleanup');
        Route::get('/export', [\App\Http\Controllers\Api\NetworkMonitoringApiController::class, 'export'])->name('export');
    });

    // Surveillance des systèmes (admin uniquement)
    Route::middleware(['admin'])->prefix('system-monitoring')->name('system-monitoring.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\SystemMonitoringApiController::class, 'index'])->name('index');
        Route::get('/statistics', [\App\Http\Controllers\Api\SystemMonitoringApiController::class, 'statistics'])->name('statistics');
        Route::get('/performance', [\App\Http\Controllers\Api\SystemMonitoringApiController::class, 'performance'])->name('performance');
        Route::get('/trends', [\App\Http\Controllers\Api\SystemMonitoringApiController::class, 'trends'])->name('trends');
        Route::get('/alerts', [\App\Http\Controllers\Api\SystemMonitoringApiController::class, 'alerts'])->name('alerts');
        Route::get('/report', [\App\Http\Controllers\Api\SystemMonitoringApiController::class, 'report'])->name('report');
        Route::post('/cleanup', [\App\Http\Controllers\Api\SystemMonitoringApiController::class, 'cleanup'])->name('cleanup');
        Route::get('/export', [\App\Http\Controllers\Api\SystemMonitoringApiController::class, 'export'])->name('export');
    });

    // Surveillance des applications (admin uniquement)
    Route::middleware(['admin'])->prefix('application-monitoring')->name('application-monitoring.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\ApplicationMonitoringApiController::class, 'index'])->name('index');
        Route::get('/statistics', [\App\Http\Controllers\Api\ApplicationMonitoringApiController::class, 'statistics'])->name('statistics');
        Route::get('/performance', [\App\Http\Controllers\Api\ApplicationMonitoringApiController::class, 'performance'])->name('performance');
        Route::get('/trends', [\App\Http\Controllers\Api\ApplicationMonitoringApiController::class, 'trends'])->name('trends');
        Route::get('/alerts', [\App\Http\Controllers\Api\ApplicationMonitoringApiController::class, 'alerts'])->name('alerts');
        Route::get('/report', [\App\Http\Controllers\Api\ApplicationMonitoringApiController::class, 'report'])->name('report');
        Route::post('/cleanup', [\App\Http\Controllers\Api\ApplicationMonitoringApiController::class, 'cleanup'])->name('cleanup');
        Route::get('/export', [\App\Http\Controllers\Api\ApplicationMonitoringApiController::class, 'export'])->name('export');
    });
});
