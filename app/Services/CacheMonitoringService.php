<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class CacheMonitoringService
{
    /**
     * Surveille les caches
     */
    public static function monitorCache(): array
    {
        $cacheStats = self::getCacheStatistics();
        $cachePerformance = self::getCachePerformance();
        $cacheKeys = self::getCacheKeys();
        $alerts = self::checkCacheThresholds($cacheStats);

        return [
            'timestamp' => now()->toISOString(),
            'statistics' => $cacheStats,
            'performance' => $cachePerformance,
            'keys' => $cacheKeys,
            'alerts' => $alerts,
        ];
    }

    /**
     * Obtient les statistiques du cache
     */
    private static function getCacheStatistics(): array
    {
        $driver = config('cache.default');
        $stats = [
            'driver' => $driver,
            'enabled' => true,
        ];

        try {
            if ($driver === 'redis') {
                $stats = array_merge($stats, self::getRedisStatistics());
            } elseif ($driver === 'memcached') {
                $stats = array_merge($stats, self::getMemcachedStatistics());
            } else {
                $stats = array_merge($stats, self::getFileCacheStatistics());
            }
        } catch (\Exception $e) {
            Log::error('Failed to get cache statistics', ['error' => $e->getMessage()]);
            $stats['error'] = 'Cache statistics unavailable';
        }

        return $stats;
    }

    /**
     * Obtient les statistiques Redis
     */
    private static function getRedisStatistics(): array
    {
        try {
            $redis = Redis::connection();
            $info = $redis->info();

            return [
                'redis_version' => $info['redis_version'] ?? 'Unknown',
                'used_memory' => $info['used_memory_human'] ?? 'Unknown',
                'used_memory_peak' => $info['used_memory_peak_human'] ?? 'Unknown',
                'connected_clients' => $info['connected_clients'] ?? 0,
                'total_commands_processed' => $info['total_commands_processed'] ?? 0,
                'keyspace_hits' => $info['keyspace_hits'] ?? 0,
                'keyspace_misses' => $info['keyspace_misses'] ?? 0,
                'expired_keys' => $info['expired_keys'] ?? 0,
                'evicted_keys' => $info['evicted_keys'] ?? 0,
                'hit_rate' => self::calculateHitRate($info['keyspace_hits'] ?? 0, $info['keyspace_misses'] ?? 0),
                'miss_rate' => self::calculateMissRate($info['keyspace_hits'] ?? 0, $info['keyspace_misses'] ?? 0),
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get Redis statistics', ['error' => $e->getMessage()]);
            return ['error' => 'Redis statistics unavailable'];
        }
    }

    /**
     * Obtient les statistiques Memcached
     */
    private static function getMemcachedStatistics(): array
    {
        try {
            $memcached = new \Memcached();
            $memcached->addServer('127.0.0.1', 11211);
            $stats = $memcached->getStats();

            if (empty($stats)) {
                return ['error' => 'Memcached statistics unavailable'];
            }

            $serverStats = reset($stats);

            return [
                'version' => $serverStats['version'] ?? 'Unknown',
                'uptime' => $serverStats['uptime'] ?? 0,
                'total_items' => $serverStats['total_items'] ?? 0,
                'curr_items' => $serverStats['curr_items'] ?? 0,
                'bytes' => $serverStats['bytes'] ?? 0,
                'bytes_read' => $serverStats['bytes_read'] ?? 0,
                'bytes_written' => $serverStats['bytes_written'] ?? 0,
                'get_hits' => $serverStats['get_hits'] ?? 0,
                'get_misses' => $serverStats['get_misses'] ?? 0,
                'hit_rate' => self::calculateHitRate($serverStats['get_hits'] ?? 0, $serverStats['get_misses'] ?? 0),
                'miss_rate' => self::calculateMissRate($serverStats['get_hits'] ?? 0, $serverStats['get_misses'] ?? 0),
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get Memcached statistics', ['error' => $e->getMessage()]);
            return ['error' => 'Memcached statistics unavailable'];
        }
    }

    /**
     * Obtient les statistiques du cache de fichiers
     */
    private static function getFileCacheStatistics(): array
    {
        $cachePath = storage_path('framework/cache');

        if (!is_dir($cachePath)) {
            return ['error' => 'File cache directory not found'];
        }

        $files = glob($cachePath . '/*');
        $totalSize = 0;
        $fileCount = 0;

        foreach ($files as $file) {
            if (is_file($file)) {
                $totalSize += filesize($file);
                $fileCount++;
            }
        }

        return [
            'file_count' => $fileCount,
            'total_size_mb' => round($totalSize / 1024 / 1024, 2),
            'cache_path' => $cachePath,
        ];
    }

    /**
     * Calcule le taux de hit
     */
    private static function calculateHitRate(int $hits, int $misses): float
    {
        $total = $hits + $misses;
        return $total > 0 ? round(($hits / $total) * 100, 2) : 0;
    }

    /**
     * Calcule le taux de miss
     */
    private static function calculateMissRate(int $hits, int $misses): float
    {
        $total = $hits + $misses;
        return $total > 0 ? round(($misses / $total) * 100, 2) : 0;
    }

    /**
     * Obtient les performances du cache
     */
    private static function getCachePerformance(): array
    {
        $startTime = microtime(true);

        // Test de performance du cache
        $testKey = 'cache_performance_test_' . time();
        $testValue = 'test_value_' . time();

        try {
            // Test d'écriture
            $writeStart = microtime(true);
            Cache::put($testKey, $testValue, 60);
            $writeTime = microtime(true) - $writeStart;

            // Test de lecture
            $readStart = microtime(true);
            $retrievedValue = Cache::get($testKey);
            $readTime = microtime(true) - $readStart;

            // Test de suppression
            $deleteStart = microtime(true);
            Cache::forget($testKey);
            $deleteTime = microtime(true) - $deleteStart;

            $totalTime = microtime(true) - $startTime;

            return [
                'write_time_ms' => round($writeTime * 1000, 3),
                'read_time_ms' => round($readTime * 1000, 3),
                'delete_time_ms' => round($deleteTime * 1000, 3),
                'total_time_ms' => round($totalTime * 1000, 3),
                'test_successful' => $retrievedValue === $testValue,
            ];
        } catch (\Exception $e) {
            Log::error('Cache performance test failed', ['error' => $e->getMessage()]);
            return [
                'error' => 'Cache performance test failed',
                'test_successful' => false,
            ];
        }
    }

    /**
     * Obtient les clés du cache
     */
    private static function getCacheKeys(): array
    {
        $driver = config('cache.default');

        try {
            if ($driver === 'redis') {
                return self::getRedisKeys();
            } elseif ($driver === 'memcached') {
                return self::getMemcachedKeys();
            } else {
                return self::getFileCacheKeys();
            }
        } catch (\Exception $e) {
            Log::error('Failed to get cache keys', ['error' => $e->getMessage()]);
            return ['error' => 'Cache keys unavailable'];
        }
    }

    /**
     * Obtient les clés Redis
     */
    private static function getRedisKeys(): array
    {
        try {
            $redis = Redis::connection();
            $keys = $redis->keys('*');

            $keyStats = [];
            foreach ($keys as $key) {
                $ttl = $redis->ttl($key);
                $type = $redis->type($key);
                $size = $redis->strlen($key);

                $keyStats[] = [
                    'key' => $key,
                    'ttl' => $ttl,
                    'type' => $type,
                    'size' => $size,
                ];
            }

            return [
                'total_keys' => count($keys),
                'keys' => array_slice($keyStats, 0, 100), // Limiter à 100 clés
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get Redis keys', ['error' => $e->getMessage()]);
            return ['error' => 'Redis keys unavailable'];
        }
    }

    /**
     * Obtient les clés Memcached
     */
    private static function getMemcachedKeys(): array
    {
        try {
            $memcached = new \Memcached();
            $memcached->addServer('127.0.0.1', 11211);

            // Memcached ne fournit pas de méthode pour lister toutes les clés
            // On utilise une approche alternative
            $stats = $memcached->getStats();
            $serverStats = reset($stats);

            return [
                'total_keys' => $serverStats['curr_items'] ?? 0,
                'keys' => [], // Memcached ne permet pas de lister les clés
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get Memcached keys', ['error' => $e->getMessage()]);
            return ['error' => 'Memcached keys unavailable'];
        }
    }

    /**
     * Obtient les clés du cache de fichiers
     */
    private static function getFileCacheKeys(): array
    {
        $cachePath = storage_path('framework/cache');

        if (!is_dir($cachePath)) {
            return ['error' => 'File cache directory not found'];
        }

        $files = glob($cachePath . '/*');
        $keys = [];

        foreach ($files as $file) {
            if (is_file($file)) {
                $key = basename($file);
                $size = filesize($file);
                $modified = filemtime($file);

                $keys[] = [
                    'key' => $key,
                    'size' => $size,
                    'modified' => date('Y-m-d H:i:s', $modified),
                ];
            }
        }

        return [
            'total_keys' => count($keys),
            'keys' => array_slice($keys, 0, 100), // Limiter à 100 clés
        ];
    }

    /**
     * Vérifie les seuils du cache
     */
    private static function checkCacheThresholds(array $cacheStats): array
    {
        $alerts = [];

        if (isset($cacheStats['error'])) {
            $alerts[] = [
                'type' => 'cache_error',
                'message' => 'Erreur de cache: ' . $cacheStats['error'],
                'level' => 'critical',
                'value' => $cacheStats['error'],
                'threshold' => 'none',
            ];
            return $alerts;
        }

        // Vérifier le taux de hit
        if (isset($cacheStats['hit_rate']) && $cacheStats['hit_rate'] < 80) {
            $alerts[] = [
                'type' => 'low_hit_rate',
                'message' => "Taux de hit du cache faible: {$cacheStats['hit_rate']}%",
                'level' => 'warning',
                'value' => $cacheStats['hit_rate'],
                'threshold' => 80,
            ];
        }

        // Vérifier l'utilisation de la mémoire (Redis)
        if (isset($cacheStats['used_memory']) && $cacheStats['used_memory'] !== 'Unknown') {
            $memoryMB = self::convertMemoryToMB($cacheStats['used_memory']);
            if ($memoryMB > 1000) { // Plus de 1GB
                $alerts[] = [
                    'type' => 'high_memory_usage',
                    'message' => "Utilisation mémoire élevée: {$cacheStats['used_memory']}",
                    'level' => 'warning',
                    'value' => $memoryMB,
                    'threshold' => 1000,
                ];
            }
        }

        // Vérifier le nombre de clés
        if (isset($cacheStats['total_keys']) && $cacheStats['total_keys'] > 10000) {
            $alerts[] = [
                'type' => 'too_many_keys',
                'message' => "Nombre élevé de clés de cache: {$cacheStats['total_keys']}",
                'level' => 'info',
                'value' => $cacheStats['total_keys'],
                'threshold' => 10000,
            ];
        }

        return $alerts;
    }

    /**
     * Convertit une valeur de mémoire en MB
     */
    private static function convertMemoryToMB(string $memory): float
    {
        $memory = trim($memory);
        $unit = strtolower(substr($memory, -1));
        $number = (float) $memory;

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
     * Génère un rapport du cache
     */
    public static function generateCacheReport(int $hours = 24): array
    {
        $cacheStats = self::getCacheStatistics();
        $cachePerformance = self::getCachePerformance();
        $cacheKeys = self::getCacheKeys();
        $alerts = self::checkCacheThresholds($cacheStats);

        return [
            'period_hours' => $hours,
            'generated_at' => now()->toISOString(),
            'summary' => $cacheStats,
            'performance' => $cachePerformance,
            'keys' => $cacheKeys,
            'alerts' => $alerts,
            'recommendations' => self::generateCacheRecommendations($cacheStats, $alerts),
        ];
    }

    /**
     * Génère des recommandations pour le cache
     */
    private static function generateCacheRecommendations(array $cacheStats, array $alerts): array
    {
        $recommendations = [];

        if (isset($cacheStats['error'])) {
            $recommendations[] = 'Vérifiez la configuration du cache et les logs d\'erreur.';
            return $recommendations;
        }

        // Recommandations basées sur les alertes
        foreach ($alerts as $alert) {
            switch ($alert['type']) {
                case 'low_hit_rate':
                    $recommendations[] = 'Optimisez la stratégie de mise en cache et vérifiez les TTL.';
                    break;
                case 'high_memory_usage':
                    $recommendations[] = 'Considérez l\'augmentation de la mémoire ou l\'optimisation des clés.';
                    break;
                case 'too_many_keys':
                    $recommendations[] = 'Nettoyez les anciennes clés et optimisez la stratégie de cache.';
                    break;
            }
        }

        // Recommandations générales
        if ($cacheStats['driver'] === 'redis') {
            $recommendations[] = 'Vérifiez régulièrement les performances Redis.';
            $recommendations[] = 'Considérez l\'utilisation de Redis Cluster pour la haute disponibilité.';
        } elseif ($cacheStats['driver'] === 'memcached') {
            $recommendations[] = 'Vérifiez la configuration Memcached et les performances.';
        } else {
            $recommendations[] = 'Considérez l\'utilisation d\'un cache plus performant comme Redis.';
        }

        if (empty($recommendations)) {
            $recommendations[] = 'Aucun problème critique détecté. Continuez la surveillance.';
        }

        return $recommendations;
    }

    /**
     * Nettoie le cache
     */
    public static function clearCache(): void
    {
        try {
            Cache::flush();
            Log::info('Cache cleared successfully');
        } catch (\Exception $e) {
            Log::error('Failed to clear cache', ['error' => $e->getMessage()]);
        }
    }
}
