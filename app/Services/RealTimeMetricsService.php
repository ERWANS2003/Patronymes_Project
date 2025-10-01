<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class RealTimeMetricsService
{
    /**
     * Collecte les métriques en temps réel
     */
    public static function collectRealTimeMetrics(): array
    {
        return [
            'timestamp' => now()->toISOString(),
            'online_users' => self::getOnlineUsersCount(),
            'active_sessions' => self::getActiveSessionsCount(),
            'current_requests' => self::getCurrentRequestsCount(),
            'database_connections' => self::getDatabaseConnectionsCount(),
            'cache_hit_rate' => self::getCacheHitRate(),
            'memory_usage' => self::getMemoryUsage(),
            'cpu_usage' => self::getCpuUsage(),
            'disk_io' => self::getDiskIO(),
            'network_io' => self::getNetworkIO(),
        ];
    }

    /**
     * Obtient le nombre d'utilisateurs en ligne
     */
    private static function getOnlineUsersCount(): int
    {
        // Utilise Redis pour tracker les utilisateurs en ligne
        if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
            try {
                $redis = Cache::getRedis();
                $keys = $redis->keys('user_online:*');
                return count($keys);
            } catch (\Exception $e) {
                // Fallback: compter les sessions actives
                return self::getActiveSessionsCount();
            }
        }

        // Fallback: utiliser les sessions Laravel
        return self::getActiveSessionsCount();
    }

    /**
     * Obtient le nombre de sessions actives
     */
    private static function getActiveSessionsCount(): int
    {
        try {
            $sessionPath = storage_path('framework/sessions');
            if (is_dir($sessionPath)) {
                $files = glob($sessionPath . '/*');
                $activeSessions = 0;

                foreach ($files as $file) {
                    if (is_file($file) && (time() - filemtime($file)) < 3600) { // Sessions actives dans la dernière heure
                        $activeSessions++;
                    }
                }

                return $activeSessions;
            }
        } catch (\Exception $e) {
            // Ignore les erreurs
        }

        return 0;
    }

    /**
     * Obtient le nombre de requêtes en cours
     */
    private static function getCurrentRequestsCount(): int
    {
        // Simulation basée sur les logs récents
        $logFile = storage_path('logs/laravel.log');
        if (file_exists($logFile)) {
            $content = file_get_contents($logFile);
            $lines = explode("\n", $content);

            $recentRequests = 0;
            $oneMinuteAgo = now()->subMinute()->format('Y-m-d H:i:s');

            foreach ($lines as $line) {
                if (strpos($line, 'GET') !== false || strpos($line, 'POST') !== false) {
                    if (preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $line, $matches)) {
                        if ($matches[1] >= $oneMinuteAgo) {
                            $recentRequests++;
                        }
                    }
                }
            }

            return $recentRequests;
        }

        return 0;
    }

    /**
     * Obtient le nombre de connexions à la base de données
     */
    private static function getDatabaseConnectionsCount(): int
    {
        try {
            $connections = DB::getConnections();
            return count($connections);
        } catch (\Exception $e) {
            return 0;
        }
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
     * Obtient l'utilisation de la mémoire
     */
    private static function getMemoryUsage(): array
    {
        return [
            'current_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
            'peak_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
            'limit_mb' => self::convertToMB(ini_get('memory_limit')),
            'percentage' => round((memory_get_usage(true) / self::convertToMB(ini_get('memory_limit'))) * 100, 2)
        ];
    }

    /**
     * Obtient l'utilisation du CPU (simulation)
     */
    private static function getCpuUsage(): array
    {
        // Simulation basée sur le temps d'exécution
        $startTime = microtime(true);
        usleep(1000); // 1ms
        $endTime = microtime(true);

        $executionTime = ($endTime - $startTime) * 1000; // en ms

        return [
            'usage_percentage' => min(100, round($executionTime * 10, 2)), // Simulation
            'load_average' => sys_getloadavg()[0] ?? 0,
            'processes' => self::getProcessCount()
        ];
    }

    /**
     * Obtient les statistiques d'I/O disque
     */
    private static function getDiskIO(): array
    {
        $logSize = self::getLogSize();
        $storageSize = self::getStorageSize();

        return [
            'log_size_mb' => $logSize,
            'storage_size_mb' => $storageSize,
            'total_size_mb' => $logSize + $storageSize,
            'free_space_gb' => round(disk_free_space(storage_path()) / 1024 / 1024 / 1024, 2)
        ];
    }

    /**
     * Obtient les statistiques d'I/O réseau (simulation)
     */
    private static function getNetworkIO(): array
    {
        return [
            'requests_per_minute' => self::getRequestsPerMinute(),
            'bandwidth_usage_mb' => self::getBandwidthUsage(),
            'active_connections' => self::getActiveConnections()
        ];
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

    /**
     * Obtient le nombre de processus
     */
    private static function getProcessCount(): int
    {
        try {
            if (function_exists('exec')) {
                $output = [];
                exec('ps aux | wc -l', $output);
                return (int) ($output[0] ?? 0) - 1; // -1 pour la ligne d'en-tête
            }
        } catch (\Exception $e) {
            // Ignore les erreurs
        }

        return 0;
    }

    /**
     * Obtient la taille des logs
     */
    private static function getLogSize(): float
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
     * Obtient la taille du stockage
     */
    private static function getStorageSize(): float
    {
        $storagePath = storage_path('app');
        $totalSize = 0;

        if (is_dir($storagePath)) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($storagePath)
            );

            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $totalSize += $file->getSize();
                }
            }
        }

        return round($totalSize / 1024 / 1024, 2);
    }

    /**
     * Obtient le nombre de requêtes par minute
     */
    private static function getRequestsPerMinute(): int
    {
        $logFile = storage_path('logs/laravel.log');
        if (file_exists($logFile)) {
            $content = file_get_contents($logFile);
            $lines = explode("\n", $content);

            $requests = 0;
            $oneMinuteAgo = now()->subMinute()->format('Y-m-d H:i:s');

            foreach ($lines as $line) {
                if (strpos($line, 'GET') !== false || strpos($line, 'POST') !== false ||
                    strpos($line, 'PUT') !== false || strpos($line, 'DELETE') !== false) {
                    if (preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $line, $matches)) {
                        if ($matches[1] >= $oneMinuteAgo) {
                            $requests++;
                        }
                    }
                }
            }

            return $requests;
        }

        return 0;
    }

    /**
     * Obtient l'utilisation de la bande passante (simulation)
     */
    private static function getBandwidthUsage(): float
    {
        // Simulation basée sur la taille des logs
        $logSize = self::getLogSize();
        return round($logSize * 0.1, 2); // Simulation
    }

    /**
     * Obtient le nombre de connexions actives (simulation)
     */
    private static function getActiveConnections(): int
    {
        return self::getActiveSessionsCount();
    }
}
