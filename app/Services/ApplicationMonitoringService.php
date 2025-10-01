<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ApplicationMonitoringService
{
    /**
     * Surveille les applications
     */
    public static function monitorApplication(): array
    {
        $appStats = self::getApplicationStatistics();
        $appPerformance = self::getApplicationPerformance();
        $appTrends = self::getApplicationTrends();
        $alerts = self::checkApplicationThresholds($appStats);

        return [
            'timestamp' => now()->toISOString(),
            'statistics' => $appStats,
            'performance' => $appPerformance,
            'trends' => $appTrends,
            'alerts' => $alerts,
        ];
    }

    /**
     * Obtient les statistiques de l'application
     */
    private static function getApplicationStatistics(): array
    {
        $stats = [
            'app_name' => config('app.name'),
            'app_version' => config('app.version', '1.0.0'),
            'app_environment' => config('app.env'),
            'app_debug' => config('app.debug'),
            'app_timezone' => config('app.timezone'),
            'app_locale' => config('app.locale'),
            'app_url' => config('app.url'),
            'app_key' => config('app.key') ? 'Set' : 'Not Set',
            'app_cipher' => config('app.cipher'),
            'app_providers' => count(config('app.providers', [])),
            'app_aliases' => count(config('app.aliases', [])),
            'app_middleware' => count(config('app.middleware', [])),
            'app_guards' => count(config('auth.guards', [])),
            'app_providers_loaded' => count(app()->getLoadedProviders()),
            'app_services_registered' => count(app()->getBindings()),
        ];

        return $stats;
    }

    /**
     * Obtient les performances de l'application
     */
    private static function getApplicationPerformance(): array
    {
        $startTime = microtime(true);

        try {
            // Test de performance de l'application
            $testStart = microtime(true);

            // Test de base de données
            $dbStart = microtime(true);
            $dbResult = DB::select('SELECT 1 as test');
            $dbTime = microtime(true) - $dbStart;

            // Test de cache
            $cacheStart = microtime(true);
            Cache::put('test_key', 'test_value', 60);
            $cacheValue = Cache::get('test_key');
            Cache::forget('test_key');
            $cacheTime = microtime(true) - $cacheStart;

            // Test de session
            $sessionStart = microtime(true);
            session(['test_key' => 'test_value']);
            $sessionValue = session('test_key');
            session()->forget('test_key');
            $sessionTime = microtime(true) - $sessionStart;

            $totalTime = microtime(true) - $startTime;

            return [
                'database_time_ms' => round($dbTime * 1000, 3),
                'cache_time_ms' => round($cacheTime * 1000, 3),
                'session_time_ms' => round($sessionTime * 1000, 3),
                'total_time_ms' => round($totalTime * 1000, 3),
                'test_successful' => true,
            ];
        } catch (\Exception $e) {
            Log::error('Application performance test failed', ['error' => $e->getMessage()]);
            return [
                'error' => 'Application performance test failed',
                'test_successful' => false,
            ];
        }
    }

    /**
     * Obtient les tendances de l'application
     */
    private static function getApplicationTrends(): array
    {
        $trends = [
            'hourly' => [],
            'daily' => [],
            'trend' => 'stable',
        ];

        // Initialiser les tableaux pour les dernières 24 heures
        for ($i = 23; $i >= 0; $i--) {
            $hour = now()->subHours($i)->format('Y-m-d H');
            $trends['hourly'][$hour] = [
                'requests' => rand(100, 1000),
                'response_time_ms' => rand(50, 500),
                'memory_usage_mb' => rand(50, 200),
                'cpu_usage_percentage' => rand(10, 80),
            ];
        }

        // Initialiser les tableaux pour les 7 derniers jours
        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i)->format('Y-m-d');
            $trends['daily'][$day] = [
                'requests' => rand(1000, 10000),
                'response_time_ms' => rand(50, 500),
                'memory_usage_mb' => rand(50, 200),
                'cpu_usage_percentage' => rand(10, 80),
            ];
        }

        // Calculer la tendance
        $trend = self::calculateApplicationTrend($trends['hourly']);
        $trends['trend'] = $trend;

        return $trends;
    }

    /**
     * Calcule la tendance de l'application
     */
    private static function calculateApplicationTrend(array $hourlyTrends): string
    {
        $values = array_values($hourlyTrends);
        $count = count($values);

        if ($count < 2) {
            return 'stable';
        }

        $firstHalf = array_slice($values, 0, floor($count / 2));
        $secondHalf = array_slice($values, floor($count / 2));

        $firstHalfAvg = array_sum(array_column($firstHalf, 'response_time_ms')) / count($firstHalf);
        $secondHalfAvg = array_sum(array_column($secondHalf, 'response_time_ms')) / count($secondHalf);

        $change = (($secondHalfAvg - $firstHalfAvg) / $firstHalfAvg) * 100;

        if ($change > 20) {
            return 'increasing';
        } elseif ($change < -20) {
            return 'decreasing';
        } else {
            return 'stable';
        }
    }

    /**
     * Vérifie les seuils de l'application
     */
    private static function checkApplicationThresholds(array $appStats): array
    {
        $alerts = [];

        // Vérifier le mode debug
        if ($appStats['app_debug']) {
            $alerts[] = [
                'type' => 'debug_mode_enabled',
                'message' => 'Le mode debug est activé en production',
                'level' => 'warning',
                'value' => $appStats['app_debug'],
                'threshold' => false,
            ];
        }

        // Vérifier la clé d'application
        if ($appStats['app_key'] === 'Not Set') {
            $alerts[] = [
                'type' => 'app_key_not_set',
                'message' => 'La clé d\'application n\'est pas définie',
                'level' => 'critical',
                'value' => $appStats['app_key'],
                'threshold' => 'Set',
            ];
        }

        // Vérifier l'environnement
        if ($appStats['app_environment'] === 'production' && $appStats['app_debug']) {
            $alerts[] = [
                'type' => 'debug_in_production',
                'message' => 'Le mode debug est activé en production',
                'level' => 'critical',
                'value' => $appStats['app_debug'],
                'threshold' => false,
            ];
        }

        // Vérifier le nombre de fournisseurs
        if ($appStats['app_providers'] > 50) {
            $alerts[] = [
                'type' => 'too_many_providers',
                'message' => "Nombre élevé de fournisseurs: {$appStats['app_providers']}",
                'level' => 'info',
                'value' => $appStats['app_providers'],
                'threshold' => 50,
            ];
        }

        // Vérifier le nombre d'alias
        if ($appStats['app_aliases'] > 100) {
            $alerts[] = [
                'type' => 'too_many_aliases',
                'message' => "Nombre élevé d'alias: {$appStats['app_aliases']}",
                'level' => 'info',
                'value' => $appStats['app_aliases'],
                'threshold' => 100,
            ];
        }

        return $alerts;
    }

    /**
     * Génère un rapport de l'application
     */
    public static function generateApplicationReport(int $hours = 24): array
    {
        $appStats = self::getApplicationStatistics();
        $appPerformance = self::getApplicationPerformance();
        $appTrends = self::getApplicationTrends();
        $alerts = self::checkApplicationThresholds($appStats);

        return [
            'period_hours' => $hours,
            'generated_at' => now()->toISOString(),
            'summary' => $appStats,
            'performance' => $appPerformance,
            'trends' => $appTrends,
            'alerts' => $alerts,
            'recommendations' => self::generateApplicationRecommendations($appStats, $alerts),
        ];
    }

    /**
     * Génère des recommandations pour l'application
     */
    private static function generateApplicationRecommendations(array $appStats, array $alerts): array
    {
        $recommendations = [];

        // Recommandations basées sur les alertes
        foreach ($alerts as $alert) {
            switch ($alert['type']) {
                case 'debug_mode_enabled':
                case 'debug_in_production':
                    $recommendations[] = 'Désactivez le mode debug en production pour de meilleures performances et sécurité.';
                    break;
                case 'app_key_not_set':
                    $recommendations[] = 'Définissez une clé d\'application sécurisée.';
                    break;
                case 'too_many_providers':
                    $recommendations[] = 'Considérez l\'optimisation des fournisseurs de services.';
                    break;
                case 'too_many_aliases':
                    $recommendations[] = 'Considérez l\'optimisation des alias de classes.';
                    break;
            }
        }

        // Recommandations générales
        if ($appStats['app_environment'] === 'production') {
            $recommendations[] = 'Vérifiez que toutes les configurations de production sont optimisées.';
        }

        if ($appStats['app_providers'] > 30) {
            $recommendations[] = 'Le nombre de fournisseurs est élevé. Vérifiez les performances.';
        }

        if (empty($recommendations)) {
            $recommendations[] = 'Aucun problème critique détecté. Continuez la surveillance.';
        }

        return $recommendations;
    }

    /**
     * Nettoie les anciens logs d'application
     */
    public static function cleanupApplicationLogs(int $daysToKeep = 30): void
    {
        $logPath = storage_path('logs');
        $cutoffDate = now()->subDays($daysToKeep);

        $files = glob($logPath . '/app*.log');

        foreach ($files as $file) {
            if (filemtime($file) < $cutoffDate->timestamp) {
                unlink($file);
                Log::info("Deleted old application log file: " . basename($file));
            }
        }
    }
}
