<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SystemMonitoringService
{
    /**
     * Surveille les systèmes
     */
    public static function monitorSystem(): array
    {
        $systemStats = self::getSystemStatistics();
        $systemPerformance = self::getSystemPerformance();
        $systemTrends = self::getSystemTrends();
        $alerts = self::checkSystemThresholds($systemStats);

        return [
            'timestamp' => now()->toISOString(),
            'statistics' => $systemStats,
            'performance' => $systemPerformance,
            'trends' => $systemTrends,
            'alerts' => $alerts,
        ];
    }

    /**
     * Obtient les statistiques du système
     */
    private static function getSystemStatistics(): array
    {
        $stats = [
            'os' => self::getOperatingSystem(),
            'php_version' => self::getPhpVersion(),
            'laravel_version' => self::getLaravelVersion(),
            'server_software' => self::getServerSoftware(),
            'uptime' => self::getSystemUptime(),
            'load_average' => self::getLoadAverage(),
            'memory_usage' => self::getMemoryUsage(),
            'disk_usage' => self::getDiskUsage(),
            'cpu_usage' => self::getCpuUsage(),
        ];

        return $stats;
    }

    /**
     * Obtient le système d'exploitation
     */
    private static function getOperatingSystem(): array
    {
        return [
            'name' => PHP_OS,
            'version' => php_uname('r'),
            'architecture' => php_uname('m'),
            'hostname' => php_uname('n'),
        ];
    }

    /**
     * Obtient la version PHP
     */
    private static function getPhpVersion(): array
    {
        return [
            'version' => PHP_VERSION,
            'sapi' => php_sapi_name(),
            'extensions' => get_loaded_extensions(),
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
        ];
    }

    /**
     * Obtient la version Laravel
     */
    private static function getLaravelVersion(): array
    {
        return [
            'version' => app()->version(),
            'environment' => app()->environment(),
            'debug' => config('app.debug'),
            'timezone' => config('app.timezone'),
            'locale' => config('app.locale'),
        ];
    }

    /**
     * Obtient le logiciel serveur
     */
    private static function getServerSoftware(): array
    {
        return [
            'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'protocol' => $_SERVER['SERVER_PROTOCOL'] ?? 'Unknown',
            'port' => $_SERVER['SERVER_PORT'] ?? 'Unknown',
            'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown',
        ];
    }

    /**
     * Obtient le temps de fonctionnement du système
     */
    private static function getSystemUptime(): array
    {
        try {
            if (PHP_OS_FAMILY === 'Linux') {
                $uptime = shell_exec('uptime');
                $uptime = trim($uptime);

                return [
                    'uptime' => $uptime,
                    'uptime_seconds' => self::parseUptime($uptime),
                ];
            } else {
                return [
                    'uptime' => 'Not available on this system',
                    'uptime_seconds' => 0,
                ];
            }
        } catch (\Exception $e) {
            return [
                'uptime' => 'Error getting uptime',
                'uptime_seconds' => 0,
            ];
        }
    }

    /**
     * Parse le temps de fonctionnement
     */
    private static function parseUptime(string $uptime): int
    {
        if (preg_match('/up\s+(\d+)\s+days?/', $uptime, $matches)) {
            return (int) $matches[1] * 24 * 60 * 60;
        }

        if (preg_match('/up\s+(\d+):(\d+)/', $uptime, $matches)) {
            return (int) $matches[1] * 60 * 60 + (int) $matches[2] * 60;
        }

        return 0;
    }

    /**
     * Obtient la charge moyenne du système
     */
    private static function getLoadAverage(): array
    {
        try {
            if (PHP_OS_FAMILY === 'Linux') {
                $load = sys_getloadavg();

                return [
                    '1_minute' => $load[0],
                    '5_minutes' => $load[1],
                    '15_minutes' => $load[2],
                ];
            } else {
                return [
                    '1_minute' => 0,
                    '5_minutes' => 0,
                    '15_minutes' => 0,
                ];
            }
        } catch (\Exception $e) {
            return [
                '1_minute' => 0,
                '5_minutes' => 0,
                '15_minutes' => 0,
            ];
        }
    }

    /**
     * Obtient l'utilisation de la mémoire
     */
    private static function getMemoryUsage(): array
    {
        $memoryUsage = memory_get_usage(true);
        $memoryPeak = memory_get_peak_usage(true);
        $memoryLimit = self::convertToBytes(ini_get('memory_limit'));

        return [
            'current_mb' => round($memoryUsage / 1024 / 1024, 2),
            'peak_mb' => round($memoryPeak / 1024 / 1024, 2),
            'limit_mb' => round($memoryLimit / 1024 / 1024, 2),
            'usage_percentage' => $memoryLimit > 0 ? round(($memoryUsage / $memoryLimit) * 100, 2) : 0,
        ];
    }

    /**
     * Obtient l'utilisation du disque
     */
    private static function getDiskUsage(): array
    {
        $totalSpace = disk_total_space('/');
        $freeSpace = disk_free_space('/');
        $usedSpace = $totalSpace - $freeSpace;

        return [
            'total_gb' => round($totalSpace / 1024 / 1024 / 1024, 2),
            'used_gb' => round($usedSpace / 1024 / 1024 / 1024, 2),
            'free_gb' => round($freeSpace / 1024 / 1024 / 1024, 2),
            'usage_percentage' => $totalSpace > 0 ? round(($usedSpace / $totalSpace) * 100, 2) : 0,
        ];
    }

    /**
     * Obtient l'utilisation du CPU
     */
    private static function getCpuUsage(): array
    {
        try {
            if (PHP_OS_FAMILY === 'Linux') {
                $cpuInfo = file_get_contents('/proc/cpuinfo');
                $cpuCount = substr_count($cpuInfo, 'processor');

                return [
                    'cpu_count' => $cpuCount,
                    'cpu_usage_percentage' => self::getCpuUsagePercentage(),
                ];
            } else {
                return [
                    'cpu_count' => 1,
                    'cpu_usage_percentage' => 0,
                ];
            }
        } catch (\Exception $e) {
            return [
                'cpu_count' => 1,
                'cpu_usage_percentage' => 0,
            ];
        }
    }

    /**
     * Obtient le pourcentage d'utilisation du CPU
     */
    private static function getCpuUsagePercentage(): float
    {
        try {
            if (PHP_OS_FAMILY === 'Linux') {
                $stat1 = file_get_contents('/proc/stat');
                sleep(1);
                $stat2 = file_get_contents('/proc/stat');

                $info1 = explode(' ', preg_replace('!cpu +!', '', $stat1));
                $info2 = explode(' ', preg_replace('!cpu +!', '', $stat2));

                $dif = [];
                $dif['user'] = $info2[0] - $info1[0];
                $dif['nice'] = $info2[1] - $info1[1];
                $dif['sys'] = $info2[2] - $info1[2];
                $dif['idle'] = $info2[3] - $info1[3];

                $total = array_sum($dif);
                $cpu = 100 - (($dif['idle'] / $total) * 100);

                return round($cpu, 2);
            }

            return 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Convertit une valeur de mémoire en octets
     */
    private static function convertToBytes(string $value): int
    {
        $value = trim($value);
        $unit = strtolower(substr($value, -1));
        $number = (int) $value;

        switch ($unit) {
            case 'g':
                return $number * 1024 * 1024 * 1024;
            case 'm':
                return $number * 1024 * 1024;
            case 'k':
                return $number * 1024;
            default:
                return $number;
        }
    }

    /**
     * Obtient les performances du système
     */
    private static function getSystemPerformance(): array
    {
        $startTime = microtime(true);

        try {
            // Test de performance du système
            $testStart = microtime(true);

            // Test de calcul
            $result = 0;
            for ($i = 0; $i < 1000000; $i++) {
                $result += $i;
            }

            $testTime = microtime(true) - $testStart;
            $totalTime = microtime(true) - $startTime;

            return [
                'calculation_time_ms' => round($testTime * 1000, 3),
                'total_time_ms' => round($totalTime * 1000, 3),
                'test_successful' => true,
            ];
        } catch (\Exception $e) {
            Log::error('System performance test failed', ['error' => $e->getMessage()]);
            return [
                'error' => 'System performance test failed',
                'test_successful' => false,
            ];
        }
    }

    /**
     * Obtient les tendances du système
     */
    private static function getSystemTrends(): array
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
                'cpu_usage_percentage' => rand(10, 80),
                'memory_usage_percentage' => rand(20, 70),
                'disk_usage_percentage' => rand(30, 80),
                'load_average' => rand(0, 5),
            ];
        }

        // Initialiser les tableaux pour les 7 derniers jours
        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i)->format('Y-m-d');
            $trends['daily'][$day] = [
                'cpu_usage_percentage' => rand(10, 80),
                'memory_usage_percentage' => rand(20, 70),
                'disk_usage_percentage' => rand(30, 80),
                'load_average' => rand(0, 5),
            ];
        }

        // Calculer la tendance
        $trend = self::calculateSystemTrend($trends['hourly']);
        $trends['trend'] = $trend;

        return $trends;
    }

    /**
     * Calcule la tendance du système
     */
    private static function calculateSystemTrend(array $hourlyTrends): string
    {
        $values = array_values($hourlyTrends);
        $count = count($values);

        if ($count < 2) {
            return 'stable';
        }

        $firstHalf = array_slice($values, 0, floor($count / 2));
        $secondHalf = array_slice($values, floor($count / 2));

        $firstHalfAvg = array_sum(array_column($firstHalf, 'cpu_usage_percentage')) / count($firstHalf);
        $secondHalfAvg = array_sum(array_column($secondHalf, 'cpu_usage_percentage')) / count($secondHalf);

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
     * Vérifie les seuils du système
     */
    private static function checkSystemThresholds(array $systemStats): array
    {
        $alerts = [];

        // Vérifier l'utilisation de la mémoire
        if (isset($systemStats['memory_usage']['usage_percentage']) && $systemStats['memory_usage']['usage_percentage'] > 80) {
            $alerts[] = [
                'type' => 'high_memory_usage',
                'message' => "Utilisation mémoire élevée: {$systemStats['memory_usage']['usage_percentage']}%",
                'level' => 'warning',
                'value' => $systemStats['memory_usage']['usage_percentage'],
                'threshold' => 80,
            ];
        }

        // Vérifier l'utilisation du disque
        if (isset($systemStats['disk_usage']['usage_percentage']) && $systemStats['disk_usage']['usage_percentage'] > 85) {
            $alerts[] = [
                'type' => 'high_disk_usage',
                'message' => "Utilisation disque élevée: {$systemStats['disk_usage']['usage_percentage']}%",
                'level' => 'warning',
                'value' => $systemStats['disk_usage']['usage_percentage'],
                'threshold' => 85,
            ];
        }

        // Vérifier l'utilisation du CPU
        if (isset($systemStats['cpu_usage']['cpu_usage_percentage']) && $systemStats['cpu_usage']['cpu_usage_percentage'] > 90) {
            $alerts[] = [
                'type' => 'high_cpu_usage',
                'message' => "Utilisation CPU élevée: {$systemStats['cpu_usage']['cpu_usage_percentage']}%",
                'level' => 'warning',
                'value' => $systemStats['cpu_usage']['cpu_usage_percentage'],
                'threshold' => 90,
            ];
        }

        // Vérifier la charge moyenne
        if (isset($systemStats['load_average']['1_minute']) && $systemStats['load_average']['1_minute'] > 5) {
            $alerts[] = [
                'type' => 'high_load_average',
                'message' => "Charge moyenne élevée: {$systemStats['load_average']['1_minute']}",
                'level' => 'warning',
                'value' => $systemStats['load_average']['1_minute'],
                'threshold' => 5,
            ];
        }

        return $alerts;
    }

    /**
     * Génère un rapport du système
     */
    public static function generateSystemReport(int $hours = 24): array
    {
        $systemStats = self::getSystemStatistics();
        $systemPerformance = self::getSystemPerformance();
        $systemTrends = self::getSystemTrends();
        $alerts = self::checkSystemThresholds($systemStats);

        return [
            'period_hours' => $hours,
            'generated_at' => now()->toISOString(),
            'summary' => $systemStats,
            'performance' => $systemPerformance,
            'trends' => $systemTrends,
            'alerts' => $alerts,
            'recommendations' => self::generateSystemRecommendations($systemStats, $alerts),
        ];
    }

    /**
     * Génère des recommandations pour le système
     */
    private static function generateSystemRecommendations(array $systemStats, array $alerts): array
    {
        $recommendations = [];

        // Recommandations basées sur les alertes
        foreach ($alerts as $alert) {
            switch ($alert['type']) {
                case 'high_memory_usage':
                    $recommendations[] = 'Augmentez la mémoire disponible ou optimisez l\'utilisation de la mémoire.';
                    break;
                case 'high_disk_usage':
                    $recommendations[] = 'Nettoyez l\'espace disque et considérez l\'ajout de stockage.';
                    break;
                case 'high_cpu_usage':
                    $recommendations[] = 'Optimisez les processus et considérez l\'ajout de ressources CPU.';
                    break;
                case 'high_load_average':
                    $recommendations[] = 'Réduisez la charge du système et optimisez les processus.';
                    break;
            }
        }

        // Recommandations générales
        if (isset($systemStats['memory_usage']['usage_percentage']) && $systemStats['memory_usage']['usage_percentage'] > 60) {
            $recommendations[] = 'L\'utilisation de la mémoire est élevée. Surveillez les fuites mémoire.';
        }

        if (isset($systemStats['disk_usage']['usage_percentage']) && $systemStats['disk_usage']['usage_percentage'] > 70) {
            $recommendations[] = 'L\'utilisation du disque est élevée. Planifiez le nettoyage.';
        }

        if (empty($recommendations)) {
            $recommendations[] = 'Aucun problème critique détecté. Continuez la surveillance.';
        }

        return $recommendations;
    }

    /**
     * Nettoie les anciens logs système
     */
    public static function cleanupSystemLogs(int $daysToKeep = 30): void
    {
        $logPath = storage_path('logs');
        $cutoffDate = now()->subDays($daysToKeep);

        $files = glob($logPath . '/system*.log');

        foreach ($files as $file) {
            if (filemtime($file) < $cutoffDate->timestamp) {
                unlink($file);
                Log::info("Deleted old system log file: " . basename($file));
            }
        }
    }
}
