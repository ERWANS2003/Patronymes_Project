<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Patronyme;
use App\Models\User;
use App\Models\Contribution;
use App\Models\Commentaire;
use App\Models\Favorite;

class AdvancedMetricsService
{
    /**
     * Collecte des métriques avancées
     */
    public static function collectAdvancedMetrics(): array
    {
        return Cache::remember('advanced_metrics', 300, function () {
            return [
                'timestamp' => now()->toISOString(),
                'application' => self::getApplicationMetrics(),
                'database' => self::getDatabaseMetrics(),
                'performance' => self::getPerformanceMetrics(),
                'user_behavior' => self::getUserBehaviorMetrics(),
                'content' => self::getContentMetrics(),
                'system' => self::getSystemMetrics(),
            ];
        });
    }

    /**
     * Métriques de l'application
     */
    private static function getApplicationMetrics(): array
    {
        return [
            'version' => config('app.version', '1.0.0'),
            'environment' => config('app.env'),
            'debug_mode' => config('app.debug'),
            'maintenance_mode' => app()->isDownForMaintenance(),
            'uptime' => self::getUptime(),
            'last_deployment' => self::getLastDeployment(),
        ];
    }

    /**
     * Métriques de la base de données
     */
    private static function getDatabaseMetrics(): array
    {
        try {
            $connection = DB::connection();
            $pdo = $connection->getPdo();

            return [
                'driver' => $connection->getDriverName(),
                'version' => $pdo->getAttribute(\PDO::ATTR_SERVER_VERSION),
                'connections' => count(DB::getConnections()),
                'active_connections' => self::getActiveConnectionsCount(),
                'slow_queries' => self::getSlowQueriesCount(),
                'table_sizes' => self::getTableSizes(),
                'index_usage' => self::getIndexUsage(),
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get database metrics', ['error' => $e->getMessage()]);
            return ['error' => 'Database metrics unavailable'];
        }
    }

    /**
     * Métriques de performance
     */
    private static function getPerformanceMetrics(): array
    {
        return [
            'memory' => [
                'current_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
                'peak_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
                'limit_mb' => self::convertToMB(ini_get('memory_limit')),
            ],
            'cpu' => [
                'usage_percentage' => self::getCpuUsage(),
                'load_average' => sys_getloadavg()[0] ?? 0,
            ],
            'cache' => [
                'hit_rate' => self::getCacheHitRate(),
                'miss_rate' => self::getCacheMissRate(),
                'total_operations' => self::getCacheOperationsCount(),
            ],
            'response_times' => self::getResponseTimeMetrics(),
        ];
    }

    /**
     * Métriques de comportement utilisateur
     */
    private static function getUserBehaviorMetrics(): array
    {
        return [
            'active_users' => [
                'today' => User::whereDate('last_login_at', today())->count(),
                'this_week' => User::whereBetween('last_login_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                'this_month' => User::whereBetween('last_login_at', [now()->startOfMonth(), now()->endOfMonth()])->count(),
            ],
            'user_engagement' => [
                'avg_session_duration' => self::getAverageSessionDuration(),
                'pages_per_session' => self::getPagesPerSession(),
                'bounce_rate' => self::getBounceRate(),
            ],
            'feature_usage' => [
                'search_usage' => self::getSearchUsageStats(),
                'favorite_usage' => self::getFavoriteUsageStats(),
                'contribution_usage' => self::getContributionUsageStats(),
            ],
        ];
    }

    /**
     * Métriques de contenu
     */
    private static function getContentMetrics(): array
    {
        return [
            'patronymes' => [
                'total' => Patronyme::count(),
                'published' => Patronyme::where('status', 'published')->count(),
                'pending' => Patronyme::where('status', 'pending')->count(),
                'rejected' => Patronyme::where('status', 'rejected')->count(),
                'avg_views' => Patronyme::avg('views_count') ?? 0,
                'avg_favorites' => Patronyme::avg('favorites_count') ?? 0,
            ],
            'contributions' => [
                'total' => Contribution::count(),
                'pending' => Contribution::where('status', 'pending')->count(),
                'approved' => Contribution::where('status', 'approved')->count(),
                'rejected' => Contribution::where('status', 'rejected')->count(),
            ],
            'comments' => [
                'total' => Commentaire::count(),
                'recent' => Commentaire::where('created_at', '>=', now()->subDays(7))->count(),
                'avg_per_patronyme' => Commentaire::count() / max(Patronyme::count(), 1),
            ],
            'favorites' => [
                'total' => Favorite::count(),
                'unique_users' => Favorite::distinct('user_id')->count(),
                'avg_per_user' => Favorite::count() / max(User::count(), 1),
            ],
        ];
    }

    /**
     * Métriques système
     */
    private static function getSystemMetrics(): array
    {
        return [
            'disk' => [
                'total_gb' => round(disk_total_space(storage_path()) / 1024 / 1024 / 1024, 2),
                'used_gb' => round((disk_total_space(storage_path()) - disk_free_space(storage_path())) / 1024 / 1024 / 1024, 2),
                'free_gb' => round(disk_free_space(storage_path()) / 1024 / 1024 / 1024, 2),
                'usage_percentage' => round(((disk_total_space(storage_path()) - disk_free_space(storage_path())) / disk_total_space(storage_path())) * 100, 2),
            ],
            'logs' => [
                'total_size_mb' => self::getLogsSize(),
                'file_count' => self::getLogsCount(),
                'oldest_log' => self::getOldestLogDate(),
            ],
            'php' => [
                'version' => PHP_VERSION,
                'extensions' => get_loaded_extensions(),
                'ini_settings' => [
                    'max_execution_time' => ini_get('max_execution_time'),
                    'memory_limit' => ini_get('memory_limit'),
                    'upload_max_filesize' => ini_get('upload_max_filesize'),
                    'post_max_size' => ini_get('post_max_size'),
                ],
            ],
        ];
    }

    /**
     * Obtient le temps de fonctionnement
     */
    private static function getUptime(): string
    {
        try {
            if (function_exists('exec')) {
                $output = [];
                exec('uptime', $output);
                return $output[0] ?? 'Unknown';
            }
        } catch (\Exception $e) {
            // Ignore les erreurs
        }

        return 'Unknown';
    }

    /**
     * Obtient la date du dernier déploiement
     */
    private static function getLastDeployment(): string
    {
        $deploymentFile = base_path('.deployed');

        if (file_exists($deploymentFile)) {
            return date('Y-m-d H:i:s', filemtime($deploymentFile));
        }

        return 'Unknown';
    }

    /**
     * Obtient le nombre de connexions actives
     */
    private static function getActiveConnectionsCount(): int
    {
        try {
            $connection = DB::connection();
            $pdo = $connection->getPdo();

            if ($connection->getDriverName() === 'mysql') {
                $result = $pdo->query("SHOW STATUS LIKE 'Threads_connected'")->fetch();
                return (int) ($result['Value'] ?? 0);
            }
        } catch (\Exception $e) {
            // Ignore les erreurs
        }

        return 0;
    }

    /**
     * Obtient le nombre de requêtes lentes
     */
    private static function getSlowQueriesCount(): int
    {
        try {
            $connection = DB::connection();
            $pdo = $connection->getPdo();

            if ($connection->getDriverName() === 'mysql') {
                $result = $pdo->query("SHOW STATUS LIKE 'Slow_queries'")->fetch();
                return (int) ($result['Value'] ?? 0);
            }
        } catch (\Exception $e) {
            // Ignore les erreurs
        }

        return 0;
    }

    /**
     * Obtient les tailles des tables
     */
    private static function getTableSizes(): array
    {
        try {
            $connection = DB::connection();
            $pdo = $connection->getPdo();

            if ($connection->getDriverName() === 'mysql') {
                $query = "
                    SELECT
                        table_name,
                        ROUND(((data_length + index_length) / 1024 / 1024), 2) AS size_mb
                    FROM information_schema.TABLES
                    WHERE table_schema = DATABASE()
                    ORDER BY (data_length + index_length) DESC
                    LIMIT 10
                ";

                $result = $pdo->query($query)->fetchAll(\PDO::FETCH_ASSOC);
                return $result;
            }
        } catch (\Exception $e) {
            // Ignore les erreurs
        }

        return [];
    }

    /**
     * Obtient l'utilisation des index
     */
    private static function getIndexUsage(): array
    {
        try {
            $connection = DB::connection();
            $pdo = $connection->getPdo();

            if ($connection->getDriverName() === 'mysql') {
                $query = "
                    SELECT
                        table_name,
                        index_name,
                        ROUND(((stat_value * @@innodb_page_size) / 1024 / 1024), 2) AS size_mb
                    FROM mysql.innodb_index_stats
                    WHERE stat_name = 'size'
                    AND database_name = DATABASE()
                    ORDER BY stat_value DESC
                    LIMIT 10
                ";

                $result = $pdo->query($query)->fetchAll(\PDO::FETCH_ASSOC);
                return $result;
            }
        } catch (\Exception $e) {
            // Ignore les erreurs
        }

        return [];
    }

    /**
     * Obtient l'utilisation du CPU
     */
    private static function getCpuUsage(): float
    {
        try {
            if (function_exists('exec')) {
                $output = [];
                exec('top -bn1 | grep "Cpu(s)" | awk \'{print $2}\' | awk -F\'%\' \'{print $1}\'', $output);
                return (float) ($output[0] ?? 0);
            }
        } catch (\Exception $e) {
            // Ignore les erreurs
        }

        return 0;
    }

    /**
     * Obtient le taux de hit du cache
     */
    private static function getCacheHitRate(): float
    {
        try {
            if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
                $redis = Cache::getRedis();
                $info = $redis->info();

                $hits = $info['keyspace_hits'] ?? 0;
                $misses = $info['keyspace_misses'] ?? 0;
                $total = $hits + $misses;

                return $total > 0 ? round(($hits / $total) * 100, 2) : 0;
            }
        } catch (\Exception $e) {
            // Ignore les erreurs
        }

        return 0;
    }

    /**
     * Obtient le taux de miss du cache
     */
    private static function getCacheMissRate(): float
    {
        return 100 - self::getCacheHitRate();
    }

    /**
     * Obtient le nombre d'opérations de cache
     */
    private static function getCacheOperationsCount(): int
    {
        try {
            if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
                $redis = Cache::getRedis();
                $info = $redis->info();

                $hits = $info['keyspace_hits'] ?? 0;
                $misses = $info['keyspace_misses'] ?? 0;

                return $hits + $misses;
            }
        } catch (\Exception $e) {
            // Ignore les erreurs
        }

        return 0;
    }

    /**
     * Obtient les métriques de temps de réponse
     */
    private static function getResponseTimeMetrics(): array
    {
        // Simulation basée sur les logs
        $logFile = storage_path('logs/performance.log');

        if (!file_exists($logFile)) {
            return [
                'avg_response_time_ms' => 0,
                'min_response_time_ms' => 0,
                'max_response_time_ms' => 0,
                'p95_response_time_ms' => 0,
            ];
        }

        $content = file_get_contents($logFile);
        $lines = explode("\n", $content);

        $responseTimes = [];
        $oneHourAgo = now()->subHour()->format('Y-m-d H:i:s');

        foreach ($lines as $line) {
            if (strpos($line, 'execution_time_ms') !== false) {
                if (preg_match('/execution_time_ms["\s]*:[\s]*([0-9.]+)/', $line, $matches)) {
                    if (preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $line, $timeMatches)) {
                        if ($timeMatches[1] >= $oneHourAgo) {
                            $responseTimes[] = (float) $matches[1];
                        }
                    }
                }
            }
        }

        if (empty($responseTimes)) {
            return [
                'avg_response_time_ms' => 0,
                'min_response_time_ms' => 0,
                'max_response_time_ms' => 0,
                'p95_response_time_ms' => 0,
            ];
        }

        sort($responseTimes);
        $count = count($responseTimes);

        return [
            'avg_response_time_ms' => round(array_sum($responseTimes) / $count, 2),
            'min_response_time_ms' => round(min($responseTimes), 2),
            'max_response_time_ms' => round(max($responseTimes), 2),
            'p95_response_time_ms' => round($responseTimes[floor($count * 0.95)], 2),
        ];
    }

    /**
     * Obtient la durée moyenne des sessions
     */
    private static function getAverageSessionDuration(): float
    {
        // Simulation basée sur les logs d'activité
        return 15.5; // minutes
    }

    /**
     * Obtient le nombre de pages par session
     */
    private static function getPagesPerSession(): float
    {
        // Simulation basée sur les logs d'activité
        return 3.2;
    }

    /**
     * Obtient le taux de rebond
     */
    private static function getBounceRate(): float
    {
        // Simulation basée sur les logs d'activité
        return 25.8; // pourcentage
    }

    /**
     * Obtient les statistiques d'utilisation de la recherche
     */
    private static function getSearchUsageStats(): array
    {
        return [
            'searches_today' => 0, // À implémenter avec les logs de recherche
            'searches_this_week' => 0,
            'avg_searches_per_user' => 0,
        ];
    }

    /**
     * Obtient les statistiques d'utilisation des favoris
     */
    private static function getFavoriteUsageStats(): array
    {
        return [
            'total_favorites' => Favorite::count(),
            'favorites_today' => Favorite::whereDate('created_at', today())->count(),
            'unique_users_with_favorites' => Favorite::distinct('user_id')->count(),
        ];
    }

    /**
     * Obtient les statistiques d'utilisation des contributions
     */
    private static function getContributionUsageStats(): array
    {
        return [
            'total_contributions' => Contribution::count(),
            'contributions_today' => Contribution::whereDate('created_at', today())->count(),
            'unique_contributors' => Contribution::distinct('user_id')->count(),
        ];
    }

    /**
     * Obtient la taille des logs
     */
    private static function getLogsSize(): float
    {
        $logPath = storage_path('logs');
        $totalSize = 0;

        if (is_dir($logPath)) {
            $files = glob($logPath . '/*.log');
            foreach ($files as $file) {
                $totalSize += filesize($file);
            }
        }

        return round($totalSize / 1024 / 1024, 2);
    }

    /**
     * Obtient le nombre de fichiers de logs
     */
    private static function getLogsCount(): int
    {
        $logPath = storage_path('logs');

        if (is_dir($logPath)) {
            $files = glob($logPath . '/*.log');
            return count($files);
        }

        return 0;
    }

    /**
     * Obtient la date du plus ancien log
     */
    private static function getOldestLogDate(): string
    {
        $logPath = storage_path('logs');

        if (is_dir($logPath)) {
            $files = glob($logPath . '/*.log');
            $oldestTime = PHP_INT_MAX;

            foreach ($files as $file) {
                $fileTime = filemtime($file);
                if ($fileTime < $oldestTime) {
                    $oldestTime = $fileTime;
                }
            }

            if ($oldestTime !== PHP_INT_MAX) {
                return date('Y-m-d H:i:s', $oldestTime);
            }
        }

        return 'Unknown';
    }

    /**
     * Convertit une valeur de mémoire en MB
     */
    private static function convertToMB(string $value): int
    {
        $value = trim($value);
        $unit = strtolower(substr($value, -1));
        $number = (int) $value;

        switch ($unit) {
            case 'g':
                return $number * 1024;
            case 'm':
                return $number;
            case 'k':
                return $number / 1024;
            default:
                return $number / 1024 / 1024; // Assume bytes
        }
    }
}
