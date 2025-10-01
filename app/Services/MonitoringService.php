<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class MonitoringService
{
    /**
     * Log des métriques de performance
     */
    public static function logPerformanceMetrics(string $operation, float $executionTime, array $context = []): void
    {
        $metrics = [
            'operation' => $operation,
            'execution_time_ms' => round($executionTime * 1000, 3),
            'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
            'peak_memory_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
            'timestamp' => now()->toISOString(),
            'context' => $context
        ];

        Log::channel('performance')->info('Performance metrics', $metrics);

        // Alerte si le temps d'exécution est trop long
        if ($executionTime > 2.0) { // Plus de 2 secondes
            Log::channel('performance')->warning('Slow operation detected', $metrics);
        }
    }

    /**
     * Log des erreurs avec contexte
     */
    public static function logError(\Throwable $exception, array $context = []): void
    {
        $errorData = [
            'exception' => get_class($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'timestamp' => now()->toISOString(),
            'context' => $context
        ];

        Log::channel('errors')->error('Application error', $errorData);
    }

    /**
     * Log des activités utilisateur
     */
    public static function logUserActivity(string $action, array $data = []): void
    {
        $activityData = [
            'action' => $action,
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'timestamp' => now()->toISOString(),
            'data' => $data
        ];

        Log::channel('activity')->info('User activity', $activityData);
    }

    /**
     * Log des requêtes de base de données lentes
     */
    public static function logSlowQuery(string $query, float $executionTime, array $bindings = []): void
    {
        $queryData = [
            'query' => $query,
            'bindings' => $bindings,
            'execution_time_ms' => round($executionTime * 1000, 3),
            'timestamp' => now()->toISOString()
        ];

        Log::channel('queries')->warning('Slow query detected', $queryData);
    }

    /**
     * Log des tentatives de sécurité
     */
    public static function logSecurityEvent(string $event, array $data = []): void
    {
        $securityData = [
            'event' => $event,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'user_id' => auth()->id(),
            'timestamp' => now()->toISOString(),
            'data' => $data
        ];

        Log::channel('security')->warning('Security event', $securityData);
    }

    /**
     * Collecte des métriques système
     */
    public static function collectSystemMetrics(): array
    {
        return [
            'timestamp' => now()->toISOString(),
            'memory' => [
                'usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
                'peak_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
                'limit_mb' => ini_get('memory_limit')
            ],
            'database' => [
                'connections' => DB::getConnections(),
                'query_count' => DB::getQueryLog() ? count(DB::getQueryLog()) : 0
            ],
            'cache' => [
                'driver' => config('cache.default'),
                'stats' => self::getCacheStats()
            ],
            'storage' => [
                'disk_usage' => self::getDiskUsage(),
                'log_size' => self::getLogSize()
            ]
        ];
    }

    /**
     * Surveille la santé de l'application
     */
    public static function healthCheck(): array
    {
        $startTime = microtime(true);

        $health = [
            'status' => 'healthy',
            'timestamp' => now()->toISOString(),
            'checks' => []
        ];

        // Vérification de la base de données
        try {
            DB::connection()->getPdo();
            $health['checks']['database'] = [
                'status' => 'ok',
                'response_time_ms' => round((microtime(true) - $startTime) * 1000, 3)
            ];
        } catch (\Exception $e) {
            $health['checks']['database'] = [
                'status' => 'error',
                'error' => $e->getMessage()
            ];
            $health['status'] = 'unhealthy';
        }

        // Vérification du cache
        try {
            Cache::put('health_check', 'ok', 60);
            $cacheStatus = Cache::get('health_check');
            $health['checks']['cache'] = [
                'status' => $cacheStatus === 'ok' ? 'ok' : 'error'
            ];
        } catch (\Exception $e) {
            $health['checks']['cache'] = [
                'status' => 'error',
                'error' => $e->getMessage()
            ];
        }

        // Vérification de l'espace disque
        $diskUsage = self::getDiskUsage();
        $health['checks']['storage'] = [
            'status' => $diskUsage['percentage'] > 90 ? 'warning' : 'ok',
            'usage_percentage' => $diskUsage['percentage']
        ];

        if ($diskUsage['percentage'] > 95) {
            $health['status'] = 'unhealthy';
        }

        return $health;
    }

    /**
     * Génère un rapport de performance
     */
    public static function generatePerformanceReport(int $hours = 24): array
    {
        $report = [
            'period_hours' => $hours,
            'generated_at' => now()->toISOString(),
            'summary' => [],
            'recommendations' => []
        ];

        // Analyser les logs de performance
        $logFile = storage_path('logs/performance.log');
        if (file_exists($logFile)) {
            $report['summary']['log_analysis'] = self::analyzePerformanceLogs($logFile, $hours);
        }

        // Analyser les requêtes lentes
        $report['summary']['slow_queries'] = self::analyzeSlowQueries($hours);

        // Recommandations
        $report['recommendations'] = self::generateRecommendations($report['summary']);

        return $report;
    }

    /**
     * Nettoie les anciens logs
     */
    public static function cleanupLogs(int $daysToKeep = 30): void
    {
        $logPath = storage_path('logs');
        $cutoffDate = now()->subDays($daysToKeep);

        $files = glob($logPath . '/*.log');

        foreach ($files as $file) {
            if (filemtime($file) < $cutoffDate->timestamp) {
                unlink($file);
                Log::info("Deleted old log file: " . basename($file));
            }
        }
    }

    /**
     * Obtient les statistiques du cache
     */
    private static function getCacheStats(): array
    {
        try {
            if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
                $redis = Cache::getRedis();
                $info = $redis->info();

                return [
                    'used_memory' => $info['used_memory_human'] ?? 'N/A',
                    'connected_clients' => $info['connected_clients'] ?? 'N/A',
                    'keyspace_hits' => $info['keyspace_hits'] ?? 'N/A',
                    'keyspace_misses' => $info['keyspace_misses'] ?? 'N/A'
                ];
            }
        } catch (\Exception $e) {
            // Ignore les erreurs de cache
        }

        return ['driver' => config('cache.default'), 'stats' => 'Not available'];
    }

    /**
     * Obtient l'utilisation du disque
     */
    private static function getDiskUsage(): array
    {
        $totalBytes = disk_total_space(storage_path());
        $freeBytes = disk_free_space(storage_path());
        $usedBytes = $totalBytes - $freeBytes;

        return [
            'total_gb' => round($totalBytes / 1024 / 1024 / 1024, 2),
            'used_gb' => round($usedBytes / 1024 / 1024 / 1024, 2),
            'free_gb' => round($freeBytes / 1024 / 1024 / 1024, 2),
            'percentage' => round(($usedBytes / $totalBytes) * 100, 2)
        ];
    }

    /**
     * Obtient la taille des logs
     */
    private static function getLogSize(): array
    {
        $logPath = storage_path('logs');
        $totalSize = 0;
        $fileCount = 0;

        if (is_dir($logPath)) {
            $files = glob($logPath . '/*.log');
            $fileCount = count($files);

            foreach ($files as $file) {
                $totalSize += filesize($file);
            }
        }

        return [
            'total_size_mb' => round($totalSize / 1024 / 1024, 2),
            'file_count' => $fileCount
        ];
    }

    /**
     * Analyse les logs de performance
     */
    private static function analyzePerformanceLogs(string $logFile, int $hours): array
    {
        // Implémentation simplifiée - dans une vraie app, on utiliserait un parser de logs plus sophistiqué
        return [
            'message' => 'Log analysis not implemented',
            'suggestion' => 'Implement log parsing for detailed analysis'
        ];
    }

    /**
     * Analyse les requêtes lentes
     */
    private static function analyzeSlowQueries(int $hours): array
    {
        // Implémentation simplifiée
        return [
            'message' => 'Slow query analysis not implemented',
            'suggestion' => 'Enable slow query log in database configuration'
        ];
    }

    /**
     * Génère des recommandations
     */
    private static function generateRecommendations(array $summary): array
    {
        $recommendations = [];

        // Recommandations basées sur l'analyse
        if (isset($summary['log_analysis'])) {
            $recommendations[] = 'Implement detailed log analysis for better performance monitoring';
        }

        if (isset($summary['slow_queries'])) {
            $recommendations[] = 'Enable slow query logging to identify performance bottlenecks';
        }

        $recommendations[] = 'Set up automated log rotation to prevent disk space issues';
        $recommendations[] = 'Implement real-time monitoring dashboard';
        $recommendations[] = 'Set up alerts for critical performance metrics';

        return $recommendations;
    }
}
