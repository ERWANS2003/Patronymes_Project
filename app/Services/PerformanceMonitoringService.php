<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PerformanceMonitoringService
{
    /**
     * Surveille les performances
     */
    public static function monitorPerformance(): array
    {
        $performanceStats = self::getPerformanceStatistics();
        $responseTimes = self::getResponseTimeStatistics();
        $memoryUsage = self::getMemoryUsageStatistics();
        $alerts = self::checkPerformanceThresholds($performanceStats);

        return [
            'timestamp' => now()->toISOString(),
            'statistics' => $performanceStats,
            'response_times' => $responseTimes,
            'memory_usage' => $memoryUsage,
            'alerts' => $alerts,
        ];
    }

    /**
     * Obtient les statistiques de performance
     */
    private static function getPerformanceStatistics(): array
    {
        $logFile = storage_path('logs/performance.log');

        if (!file_exists($logFile)) {
            return [
                'total_requests' => 0,
                'requests_today' => 0,
                'requests_this_hour' => 0,
                'avg_response_time_ms' => 0,
                'max_response_time_ms' => 0,
                'min_response_time_ms' => 0,
                'p95_response_time_ms' => 0,
                'p99_response_time_ms' => 0,
            ];
        }

        $content = file_get_contents($logFile);
        $lines = explode("\n", $content);

        $totalRequests = 0;
        $requestsToday = 0;
        $requestsThisHour = 0;
        $responseTimes = [];

        $today = now()->format('Y-m-d');
        $oneHourAgo = now()->subHour()->format('Y-m-d H:i:s');

        foreach ($lines as $line) {
            if (strpos($line, 'execution_time_ms') !== false) {
                $totalRequests++;

                // Extraire la date de la ligne de log
                if (preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $line, $matches)) {
                    $logDate = $matches[1];

                    if (strpos($logDate, $today) === 0) {
                        $requestsToday++;
                    }

                    if ($logDate >= $oneHourAgo) {
                        $requestsThisHour++;
                    }
                }

                // Extraire le temps de réponse
                if (preg_match('/execution_time_ms["\s]*:[\s]*([0-9.]+)/', $line, $matches)) {
                    $responseTimes[] = (float) $matches[1];
                }
            }
        }

        // Calculer les statistiques de temps de réponse
        $responseTimeStats = self::calculateResponseTimeStatistics($responseTimes);

        return array_merge([
            'total_requests' => $totalRequests,
            'requests_today' => $requestsToday,
            'requests_this_hour' => $requestsThisHour,
        ], $responseTimeStats);
    }

    /**
     * Calcule les statistiques de temps de réponse
     */
    private static function calculateResponseTimeStatistics(array $responseTimes): array
    {
        if (empty($responseTimes)) {
            return [
                'avg_response_time_ms' => 0,
                'max_response_time_ms' => 0,
                'min_response_time_ms' => 0,
                'p95_response_time_ms' => 0,
                'p99_response_time_ms' => 0,
            ];
        }

        sort($responseTimes);
        $count = count($responseTimes);

        return [
            'avg_response_time_ms' => round(array_sum($responseTimes) / $count, 2),
            'max_response_time_ms' => round(max($responseTimes), 2),
            'min_response_time_ms' => round(min($responseTimes), 2),
            'p95_response_time_ms' => round($responseTimes[floor($count * 0.95)], 2),
            'p99_response_time_ms' => round($responseTimes[floor($count * 0.99)], 2),
        ];
    }

    /**
     * Obtient les statistiques de temps de réponse par période
     */
    private static function getResponseTimeStatistics(): array
    {
        $logFile = storage_path('logs/performance.log');

        if (!file_exists($logFile)) {
            return [
                'hourly' => [],
                'daily' => [],
                'trend' => 'stable',
            ];
        }

        $content = file_get_contents($logFile);
        $lines = explode("\n", $content);

        $hourlyResponseTimes = [];
        $dailyResponseTimes = [];

        // Initialiser les tableaux pour les dernières 24 heures
        for ($i = 23; $i >= 0; $i--) {
            $hour = now()->subHours($i)->format('Y-m-d H');
            $hourlyResponseTimes[$hour] = [];
        }

        // Initialiser les tableaux pour les 7 derniers jours
        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i)->format('Y-m-d');
            $dailyResponseTimes[$day] = [];
        }

        foreach ($lines as $line) {
            if (strpos($line, 'execution_time_ms') !== false) {
                if (preg_match('/\[(\d{4}-\d{2}-\d{2}) (\d{2}):\d{2}:\d{2}\]/', $line, $matches)) {
                    $date = $matches[1];
                    $hour = $matches[2];
                    $hourKey = $date . ' ' . $hour;

                    if (preg_match('/execution_time_ms["\s]*:[\s]*([0-9.]+)/', $line, $timeMatches)) {
                        $responseTime = (float) $timeMatches[1];

                        if (isset($hourlyResponseTimes[$hourKey])) {
                            $hourlyResponseTimes[$hourKey][] = $responseTime;
                        }

                        if (isset($dailyResponseTimes[$date])) {
                            $dailyResponseTimes[$date][] = $responseTime;
                        }
                    }
                }
            }
        }

        // Calculer les moyennes
        foreach ($hourlyResponseTimes as $hour => $times) {
            $hourlyResponseTimes[$hour] = !empty($times) ? round(array_sum($times) / count($times), 2) : 0;
        }

        foreach ($dailyResponseTimes as $day => $times) {
            $dailyResponseTimes[$day] = !empty($times) ? round(array_sum($times) / count($times), 2) : 0;
        }

        // Calculer la tendance
        $trend = self::calculateResponseTimeTrend($hourlyResponseTimes);

        return [
            'hourly' => $hourlyResponseTimes,
            'daily' => $dailyResponseTimes,
            'trend' => $trend,
        ];
    }

    /**
     * Calcule la tendance des temps de réponse
     */
    private static function calculateResponseTimeTrend(array $hourlyResponseTimes): string
    {
        $values = array_values($hourlyResponseTimes);
        $count = count($values);

        if ($count < 2) {
            return 'stable';
        }

        $firstHalf = array_slice($values, 0, floor($count / 2));
        $secondHalf = array_slice($values, floor($count / 2));

        $firstHalfAvg = array_sum($firstHalf) / count($firstHalf);
        $secondHalfAvg = array_sum($secondHalf) / count($secondHalf);

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
     * Obtient les statistiques d'utilisation de la mémoire
     */
    private static function getMemoryUsageStatistics(): array
    {
        $logFile = storage_path('logs/performance.log');

        if (!file_exists($logFile)) {
            return [
                'current_memory_mb' => 0,
                'peak_memory_mb' => 0,
                'memory_limit_mb' => 0,
                'memory_usage_percentage' => 0,
            ];
        }

        $content = file_get_contents($logFile);
        $lines = explode("\n", $content);

        $memoryUsages = [];
        $peakMemories = [];
        $oneHourAgo = now()->subHour()->format('Y-m-d H:i:s');

        foreach ($lines as $line) {
            if (strpos($line, 'memory_usage_mb') !== false) {
                if (preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $line, $matches)) {
                    if ($matches[1] >= $oneHourAgo) {
                        if (preg_match('/memory_usage_mb["\s]*:[\s]*([0-9.]+)/', $line, $memoryMatches)) {
                            $memoryUsages[] = (float) $memoryMatches[1];
                        }

                        if (preg_match('/peak_memory_mb["\s]*:[\s]*([0-9.]+)/', $line, $peakMatches)) {
                            $peakMemories[] = (float) $peakMatches[1];
                        }
                    }
                }
            }
        }

        $currentMemory = !empty($memoryUsages) ? round(array_sum($memoryUsages) / count($memoryUsages), 2) : 0;
        $peakMemory = !empty($peakMemories) ? round(max($peakMemories), 2) : 0;
        $memoryLimit = self::convertToMB(ini_get('memory_limit'));
        $memoryUsagePercentage = $memoryLimit > 0 ? round(($currentMemory / $memoryLimit) * 100, 2) : 0;

        return [
            'current_memory_mb' => $currentMemory,
            'peak_memory_mb' => $peakMemory,
            'memory_limit_mb' => $memoryLimit,
            'memory_usage_percentage' => $memoryUsagePercentage,
        ];
    }

    /**
     * Vérifie les seuils de performance
     */
    private static function checkPerformanceThresholds(array $performanceStats): array
    {
        $alerts = [];
        $thresholds = config('monitoring.thresholds', []);

        // Vérifier le temps de réponse moyen
        $responseTimeThreshold = $thresholds['response_time_ms'] ?? 2000;
        if ($performanceStats['avg_response_time_ms'] > $responseTimeThreshold) {
            $alerts[] = [
                'type' => 'high_response_time',
                'message' => "Temps de réponse moyen élevé: {$performanceStats['avg_response_time_ms']}ms",
                'level' => 'warning',
                'value' => $performanceStats['avg_response_time_ms'],
                'threshold' => $responseTimeThreshold,
            ];
        }

        // Vérifier le temps de réponse P95
        if ($performanceStats['p95_response_time_ms'] > ($responseTimeThreshold * 2)) {
            $alerts[] = [
                'type' => 'high_p95_response_time',
                'message' => "Temps de réponse P95 élevé: {$performanceStats['p95_response_time_ms']}ms",
                'level' => 'warning',
                'value' => $performanceStats['p95_response_time_ms'],
                'threshold' => $responseTimeThreshold * 2,
            ];
        }

        // Vérifier le temps de réponse P99
        if ($performanceStats['p99_response_time_ms'] > ($responseTimeThreshold * 3)) {
            $alerts[] = [
                'type' => 'high_p99_response_time',
                'message' => "Temps de réponse P99 élevé: {$performanceStats['p99_response_time_ms']}ms",
                'level' => 'critical',
                'value' => $performanceStats['p99_response_time_ms'],
                'threshold' => $responseTimeThreshold * 3,
            ];
        }

        // Vérifier le nombre de requêtes par heure
        if ($performanceStats['requests_this_hour'] > 1000) {
            $alerts[] = [
                'type' => 'high_request_volume',
                'message' => "Volume de requêtes élevé cette heure: {$performanceStats['requests_this_hour']}",
                'level' => 'info',
                'value' => $performanceStats['requests_this_hour'],
                'threshold' => 1000,
            ];
        }

        return $alerts;
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
     * Génère un rapport de performance
     */
    public static function generatePerformanceReport(int $hours = 24): array
    {
        $performanceStats = self::getPerformanceStatistics();
        $responseTimes = self::getResponseTimeStatistics();
        $memoryUsage = self::getMemoryUsageStatistics();
        $alerts = self::checkPerformanceThresholds($performanceStats);

        return [
            'period_hours' => $hours,
            'generated_at' => now()->toISOString(),
            'summary' => $performanceStats,
            'response_times' => $responseTimes,
            'memory_usage' => $memoryUsage,
            'alerts' => $alerts,
            'recommendations' => self::generatePerformanceRecommendations($performanceStats, $alerts),
        ];
    }

    /**
     * Génère des recommandations de performance
     */
    private static function generatePerformanceRecommendations(array $performanceStats, array $alerts): array
    {
        $recommendations = [];

        // Recommandations basées sur les alertes
        foreach ($alerts as $alert) {
            switch ($alert['type']) {
                case 'high_response_time':
                case 'high_p95_response_time':
                case 'high_p99_response_time':
                    $recommendations[] = 'Optimisez les requêtes de base de données et considérez l\'utilisation du cache.';
                    break;
                case 'high_request_volume':
                    $recommendations[] = 'Considérez la mise en place d\'un système de mise en cache et d\'optimisation des requêtes.';
                    break;
            }
        }

        // Recommandations générales
        if ($performanceStats['avg_response_time_ms'] > 1000) {
            $recommendations[] = 'Le temps de réponse moyen est élevé. Vérifiez les performances de la base de données.';
        }

        if ($performanceStats['requests_this_hour'] > 500) {
            $recommendations[] = 'Volume de requêtes élevé. Considérez l\'implémentation d\'un système de cache.';
        }

        if (empty($recommendations)) {
            $recommendations[] = 'Aucun problème de performance critique détecté. Continuez la surveillance.';
        }

        return $recommendations;
    }

    /**
     * Nettoie les anciens logs de performance
     */
    public static function cleanupPerformanceLogs(int $daysToKeep = 30): void
    {
        $logPath = storage_path('logs');
        $cutoffDate = now()->subDays($daysToKeep);

        $files = glob($logPath . '/performance*.log');

        foreach ($files as $file) {
            if (filemtime($file) < $cutoffDate->timestamp) {
                unlink($file);
                Log::info("Deleted old performance log file: " . basename($file));
            }
        }
    }
}
