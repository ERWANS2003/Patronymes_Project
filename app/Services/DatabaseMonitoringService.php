<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DatabaseMonitoringService
{
    /**
     * Surveille la base de données
     */
    public static function monitorDatabase(): array
    {
        $dbStats = self::getDatabaseStatistics();
        $slowQueries = self::getSlowQueries();
        $connectionStats = self::getConnectionStatistics();
        $alerts = self::checkDatabaseThresholds($dbStats);
        
        return [
            'timestamp' => now()->toISOString(),
            'statistics' => $dbStats,
            'slow_queries' => $slowQueries,
            'connections' => $connectionStats,
            'alerts' => $alerts,
        ];
    }

    /**
     * Obtient les statistiques de la base de données
     */
    private static function getDatabaseStatistics(): array
    {
        try {
            $connection = DB::connection();
            $pdo = $connection->getPdo();
            
            $stats = [
                'driver' => $connection->getDriverName(),
                'version' => $pdo->getAttribute(\PDO::ATTR_SERVER_VERSION),
                'host' => $connection->getConfig('host'),
                'database' => $connection->getConfig('database'),
                'charset' => $connection->getConfig('charset'),
                'collation' => $connection->getConfig('collation'),
            ];
            
            // Statistiques spécifiques au driver
            if ($connection->getDriverName() === 'mysql') {
                $stats = array_merge($stats, self::getMysqlStatistics($pdo));
            } elseif ($connection->getDriverName() === 'pgsql') {
                $stats = array_merge($stats, self::getPostgresStatistics($pdo));
            }
            
            return $stats;
        } catch (\Exception $e) {
            Log::error('Failed to get database statistics', ['error' => $e->getMessage()]);
            return ['error' => 'Database statistics unavailable'];
        }
    }

    /**
     * Obtient les statistiques MySQL
     */
    private static function getMysqlStatistics(\PDO $pdo): array
    {
        $stats = [];
        
        try {
            // Variables globales
            $globalVars = [
                'max_connections',
                'max_used_connections',
                'threads_connected',
                'threads_running',
                'slow_query_log',
                'long_query_time',
                'query_cache_size',
                'innodb_buffer_pool_size',
                'innodb_log_file_size',
            ];
            
            foreach ($globalVars as $var) {
                $result = $pdo->query("SHOW GLOBAL VARIABLES LIKE '{$var}'")->fetch();
                if ($result) {
                    $stats[$var] = $result['Value'];
                }
            }
            
            // Statuts
            $statusVars = [
                'Connections',
                'Uptime',
                'Questions',
                'Slow_queries',
                'Aborted_connects',
                'Aborted_clients',
                'Bytes_received',
                'Bytes_sent',
                'Qcache_hits',
                'Qcache_inserts',
                'Qcache_not_cached',
            ];
            
            foreach ($statusVars as $var) {
                $result = $pdo->query("SHOW GLOBAL STATUS LIKE '{$var}'")->fetch();
                if ($result) {
                    $stats[$var] = $result['Value'];
                }
            }
            
            // Taille des tables
            $result = $pdo->query("
                SELECT 
                    table_name,
                    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS size_mb,
                    table_rows
                FROM information_schema.TABLES 
                WHERE table_schema = DATABASE()
                ORDER BY (data_length + index_length) DESC
                LIMIT 10
            ")->fetchAll(\PDO::FETCH_ASSOC);
            
            $stats['table_sizes'] = $result;
            
        } catch (\Exception $e) {
            Log::error('Failed to get MySQL statistics', ['error' => $e->getMessage()]);
        }
        
        return $stats;
    }

    /**
     * Obtient les statistiques PostgreSQL
     */
    private static function getPostgresStatistics(\PDO $pdo): array
    {
        $stats = [];
        
        try {
            // Version et informations de base
            $result = $pdo->query("SELECT version()")->fetch();
            $stats['version'] = $result[0];
            
            // Statistiques de connexion
            $result = $pdo->query("
                SELECT 
                    count(*) as total_connections,
                    count(*) FILTER (WHERE state = 'active') as active_connections,
                    count(*) FILTER (WHERE state = 'idle') as idle_connections
                FROM pg_stat_activity
            ")->fetch();
            
            $stats['connections'] = $result;
            
            // Taille de la base de données
            $result = $pdo->query("
                SELECT pg_size_pretty(pg_database_size(current_database())) as database_size
            ")->fetch();
            
            $stats['database_size'] = $result['database_size'];
            
            // Statistiques des tables
            $result = $pdo->query("
                SELECT 
                    schemaname,
                    tablename,
                    pg_size_pretty(pg_total_relation_size(schemaname||'.'||tablename)) as size,
                    n_tup_ins as inserts,
                    n_tup_upd as updates,
                    n_tup_del as deletes
                FROM pg_stat_user_tables
                ORDER BY pg_total_relation_size(schemaname||'.'||tablename) DESC
                LIMIT 10
            ")->fetchAll(\PDO::FETCH_ASSOC);
            
            $stats['table_stats'] = $result;
            
        } catch (\Exception $e) {
            Log::error('Failed to get PostgreSQL statistics', ['error' => $e->getMessage()]);
        }
        
        return $stats;
    }

    /**
     * Obtient les requêtes lentes
     */
    private static function getSlowQueries(): array
    {
        try {
            $connection = DB::connection();
            $pdo = $connection->getPdo();
            
            if ($connection->getDriverName() === 'mysql') {
                // Vérifier si le slow query log est activé
                $result = $pdo->query("SHOW GLOBAL VARIABLES LIKE 'slow_query_log'")->fetch();
                if ($result && $result['Value'] === 'ON') {
                    // Analyser le slow query log
                    return self::analyzeSlowQueryLog();
                } else {
                    return ['message' => 'Slow query log is not enabled'];
                }
            }
            
            return ['message' => 'Slow query analysis not available for this database driver'];
        } catch (\Exception $e) {
            Log::error('Failed to get slow queries', ['error' => $e->getMessage()]);
            return ['error' => 'Slow query analysis failed'];
        }
    }

    /**
     * Analyse le slow query log
     */
    private static function analyzeSlowQueryLog(): array
    {
        $logFile = '/var/log/mysql/mysql-slow.log'; // Chemin par défaut
        
        if (!file_exists($logFile)) {
            return ['message' => 'Slow query log file not found'];
        }
        
        $content = file_get_contents($logFile);
        $lines = explode("\n", $content);
        
        $slowQueries = [];
        $currentQuery = null;
        
        foreach ($lines as $line) {
            if (strpos($line, 'Time:') === 0) {
                if ($currentQuery) {
                    $slowQueries[] = $currentQuery;
                }
                $currentQuery = ['time' => trim(substr($line, 5))];
            } elseif (strpos($line, 'Query_time:') === 0) {
                if ($currentQuery) {
                    $currentQuery['query_time'] = trim(substr($line, 11));
                }
            } elseif (strpos($line, 'Lock_time:') === 0) {
                if ($currentQuery) {
                    $currentQuery['lock_time'] = trim(substr($line, 10));
                }
            } elseif (strpos($line, 'Rows_sent:') === 0) {
                if ($currentQuery) {
                    $currentQuery['rows_sent'] = trim(substr($line, 10));
                }
            } elseif (strpos($line, 'Rows_examined:') === 0) {
                if ($currentQuery) {
                    $currentQuery['rows_examined'] = trim(substr($line, 14));
                }
            } elseif (strpos($line, 'SET timestamp=') === 0) {
                if ($currentQuery) {
                    $currentQuery['timestamp'] = trim(substr($line, 14));
                }
            } elseif (strpos($line, 'SELECT') === 0 || strpos($line, 'INSERT') === 0 || 
                      strpos($line, 'UPDATE') === 0 || strpos($line, 'DELETE') === 0) {
                if ($currentQuery) {
                    $currentQuery['query'] = trim($line);
                }
            }
        }
        
        if ($currentQuery) {
            $slowQueries[] = $currentQuery;
        }
        
        // Limiter à 10 requêtes lentes
        return array_slice($slowQueries, -10);
    }

    /**
     * Obtient les statistiques de connexion
     */
    private static function getConnectionStatistics(): array
    {
        try {
            $connection = DB::connection();
            $pdo = $connection->getPdo();
            
            $stats = [
                'driver' => $connection->getDriverName(),
                'host' => $connection->getConfig('host'),
                'port' => $connection->getConfig('port'),
                'database' => $connection->getConfig('database'),
                'username' => $connection->getConfig('username'),
                'charset' => $connection->getConfig('charset'),
                'collation' => $connection->getConfig('collation'),
            ];
            
            if ($connection->getDriverName() === 'mysql') {
                // Statistiques de connexion MySQL
                $result = $pdo->query("SHOW GLOBAL STATUS LIKE 'Connections'")->fetch();
                if ($result) {
                    $stats['total_connections'] = $result['Value'];
                }
                
                $result = $pdo->query("SHOW GLOBAL STATUS LIKE 'Threads_connected'")->fetch();
                if ($result) {
                    $stats['current_connections'] = $result['Value'];
                }
                
                $result = $pdo->query("SHOW GLOBAL STATUS LIKE 'Max_used_connections'")->fetch();
                if ($result) {
                    $stats['max_used_connections'] = $result['Value'];
                }
                
                $result = $pdo->query("SHOW GLOBAL VARIABLES LIKE 'max_connections'")->fetch();
                if ($result) {
                    $stats['max_connections'] = $result['Value'];
                }
            }
            
            return $stats;
        } catch (\Exception $e) {
            Log::error('Failed to get connection statistics', ['error' => $e->getMessage()]);
            return ['error' => 'Connection statistics unavailable'];
        }
    }

    /**
     * Vérifie les seuils de la base de données
     */
    private static function checkDatabaseThresholds(array $dbStats): array
    {
        $alerts = [];
        
        if (isset($dbStats['error'])) {
            $alerts[] = [
                'type' => 'database_error',
                'message' => 'Erreur de base de données: ' . $dbStats['error'],
                'level' => 'critical',
                'value' => $dbStats['error'],
                'threshold' => 'none',
            ];
            return $alerts;
        }
        
        // Vérifier les connexions
        if (isset($dbStats['current_connections']) && isset($dbStats['max_connections'])) {
            $currentConnections = (int) $dbStats['current_connections'];
            $maxConnections = (int) $dbStats['max_connections'];
            $connectionPercentage = ($currentConnections / $maxConnections) * 100;
            
            if ($connectionPercentage > 80) {
                $alerts[] = [
                    'type' => 'high_connection_usage',
                    'message' => "Utilisation élevée des connexions: {$connectionPercentage}%",
                    'level' => 'warning',
                    'value' => $connectionPercentage,
                    'threshold' => 80,
                ];
            }
        }
        
        // Vérifier les requêtes lentes
        if (isset($dbStats['Slow_queries'])) {
            $slowQueries = (int) $dbStats['Slow_queries'];
            if ($slowQueries > 100) {
                $alerts[] = [
                    'type' => 'high_slow_queries',
                    'message' => "Nombre élevé de requêtes lentes: {$slowQueries}",
                    'level' => 'warning',
                    'value' => $slowQueries,
                    'threshold' => 100,
                ];
            }
        }
        
        // Vérifier les connexions avortées
        if (isset($dbStats['Aborted_connects'])) {
            $abortedConnects = (int) $dbStats['Aborted_connects'];
            if ($abortedConnects > 10) {
                $alerts[] = [
                    'type' => 'high_aborted_connects',
                    'message' => "Nombre élevé de connexions avortées: {$abortedConnects}",
                    'level' => 'warning',
                    'value' => $abortedConnects,
                    'threshold' => 10,
                ];
            }
        }
        
        return $alerts;
    }

    /**
     * Génère un rapport de base de données
     */
    public static function generateDatabaseReport(int $hours = 24): array
    {
        $dbStats = self::getDatabaseStatistics();
        $slowQueries = self::getSlowQueries();
        $connectionStats = self::getConnectionStatistics();
        $alerts = self::checkDatabaseThresholds($dbStats);
        
        return [
            'period_hours' => $hours,
            'generated_at' => now()->toISOString(),
            'summary' => $dbStats,
            'slow_queries' => $slowQueries,
            'connections' => $connectionStats,
            'alerts' => $alerts,
            'recommendations' => self::generateDatabaseRecommendations($dbStats, $alerts),
        ];
    }

    /**
     * Génère des recommandations pour la base de données
     */
    private static function generateDatabaseRecommendations(array $dbStats, array $alerts): array
    {
        $recommendations = [];
        
        if (isset($dbStats['error'])) {
            $recommendations[] = 'Vérifiez la configuration de la base de données et les logs d\'erreur.';
            return $recommendations;
        }
        
        // Recommandations basées sur les alertes
        foreach ($alerts as $alert) {
            switch ($alert['type']) {
                case 'high_connection_usage':
                    $recommendations[] = 'Considérez l\'augmentation du nombre maximum de connexions ou l\'optimisation des requêtes.';
                    break;
                case 'high_slow_queries':
                    $recommendations[] = 'Analysez et optimisez les requêtes lentes. Activez le slow query log si ce n\'est pas déjà fait.';
                    break;
                case 'high_aborted_connects':
                    $recommendations[] = 'Vérifiez la configuration réseau et les paramètres de connexion.';
                    break;
            }
        }
        
        // Recommandations générales
        if (isset($dbStats['driver']) && $dbStats['driver'] === 'mysql') {
            $recommendations[] = 'Vérifiez régulièrement les performances de la base de données MySQL.';
            $recommendations[] = 'Considérez l\'utilisation d\'index pour améliorer les performances.';
        }
        
        if (empty($recommendations)) {
            $recommendations[] = 'Aucun problème critique détecté. Continuez la surveillance.';
        }
        
        return $recommendations;
    }

    /**
     * Nettoie les anciens logs de base de données
     */
    public static function cleanupDatabaseLogs(int $daysToKeep = 30): void
    {
        $logPath = storage_path('logs');
        $cutoffDate = now()->subDays($daysToKeep);
        
        $files = glob($logPath . '/database*.log');
        
        foreach ($files as $file) {
            if (filemtime($file) < $cutoffDate->timestamp) {
                unlink($file);
                Log::info("Deleted old database log file: " . basename($file));
            }
        }
    }
}
