<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ErrorMonitoringService
{
    /**
     * Surveille les erreurs et génère des alertes
     */
    public static function monitorErrors(): array
    {
        $errorStats = self::getErrorStatistics();
        $recentErrors = self::getRecentErrors();
        $errorTrends = self::getErrorTrends();

        // Vérifier les seuils d'alerte
        $alerts = self::checkErrorThresholds($errorStats);

        return [
            'timestamp' => now()->toISOString(),
            'statistics' => $errorStats,
            'recent_errors' => $recentErrors,
            'trends' => $errorTrends,
            'alerts' => $alerts,
        ];
    }

    /**
     * Obtient les statistiques d'erreurs
     */
    private static function getErrorStatistics(): array
    {
        $logFile = storage_path('logs/laravel.log');

        if (!file_exists($logFile)) {
            return [
                'total_errors' => 0,
                'errors_today' => 0,
                'errors_this_hour' => 0,
                'error_rate' => 0,
                'most_common_errors' => [],
            ];
        }

        $content = file_get_contents($logFile);
        $lines = explode("\n", $content);

        $totalErrors = 0;
        $errorsToday = 0;
        $errorsThisHour = 0;
        $errorTypes = [];

        $today = now()->format('Y-m-d');
        $oneHourAgo = now()->subHour()->format('Y-m-d H:i:s');

        foreach ($lines as $line) {
            if (strpos($line, 'ERROR') !== false || strpos($line, 'CRITICAL') !== false) {
                $totalErrors++;

                // Extraire la date de la ligne de log
                if (preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $line, $matches)) {
                    $logDate = $matches[1];

                    if (strpos($logDate, $today) === 0) {
                        $errorsToday++;
                    }

                    if ($logDate >= $oneHourAgo) {
                        $errorsThisHour++;
                    }
                }

                // Compter les types d'erreurs
                if (preg_match('/ERROR.*?(\w+Exception|\w+Error)/', $line, $matches)) {
                    $errorType = $matches[1];
                    $errorTypes[$errorType] = ($errorTypes[$errorType] ?? 0) + 1;
                }
            }
        }

        // Calculer le taux d'erreur
        $totalRequests = self::getTotalRequests();
        $errorRate = $totalRequests > 0 ? round(($totalErrors / $totalRequests) * 100, 2) : 0;

        // Trier les erreurs les plus communes
        arsort($errorTypes);
        $mostCommonErrors = array_slice($errorTypes, 0, 5, true);

        return [
            'total_errors' => $totalErrors,
            'errors_today' => $errorsToday,
            'errors_this_hour' => $errorsThisHour,
            'error_rate' => $errorRate,
            'most_common_errors' => $mostCommonErrors,
        ];
    }

    /**
     * Obtient les erreurs récentes
     */
    private static function getRecentErrors(): array
    {
        $logFile = storage_path('logs/laravel.log');

        if (!file_exists($logFile)) {
            return [];
        }

        $content = file_get_contents($logFile);
        $lines = explode("\n", $content);

        $recentErrors = [];
        $oneHourAgo = now()->subHour()->format('Y-m-d H:i:s');

        foreach ($lines as $line) {
            if (strpos($line, 'ERROR') !== false || strpos($line, 'CRITICAL') !== false) {
                if (preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $line, $matches)) {
                    if ($matches[1] >= $oneHourAgo) {
                        $recentErrors[] = [
                            'timestamp' => $matches[1],
                            'message' => self::extractErrorMessage($line),
                            'level' => strpos($line, 'CRITICAL') !== false ? 'critical' : 'error',
                        ];
                    }
                }
            }
        }

        // Limiter à 10 erreurs récentes
        return array_slice($recentErrors, -10);
    }

    /**
     * Obtient les tendances d'erreurs
     */
    private static function getErrorTrends(): array
    {
        $logFile = storage_path('logs/laravel.log');

        if (!file_exists($logFile)) {
            return [
                'hourly' => [],
                'daily' => [],
                'trend' => 'stable',
            ];
        }

        $content = file_get_contents($logFile);
        $lines = explode("\n", $content);

        $hourlyErrors = [];
        $dailyErrors = [];

        // Initialiser les tableaux pour les dernières 24 heures
        for ($i = 23; $i >= 0; $i--) {
            $hour = now()->subHours($i)->format('Y-m-d H');
            $hourlyErrors[$hour] = 0;
        }

        // Initialiser les tableaux pour les 7 derniers jours
        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i)->format('Y-m-d');
            $dailyErrors[$day] = 0;
        }

        foreach ($lines as $line) {
            if (strpos($line, 'ERROR') !== false || strpos($line, 'CRITICAL') !== false) {
                if (preg_match('/\[(\d{4}-\d{2}-\d{2}) (\d{2}):\d{2}:\d{2}\]/', $line, $matches)) {
                    $date = $matches[1];
                    $hour = $matches[2];
                    $hourKey = $date . ' ' . $hour;

                    if (isset($hourlyErrors[$hourKey])) {
                        $hourlyErrors[$hourKey]++;
                    }

                    if (isset($dailyErrors[$date])) {
                        $dailyErrors[$date]++;
                    }
                }
            }
        }

        // Calculer la tendance
        $trend = self::calculateTrend($hourlyErrors);

        return [
            'hourly' => $hourlyErrors,
            'daily' => $dailyErrors,
            'trend' => $trend,
        ];
    }

    /**
     * Vérifie les seuils d'alerte
     */
    private static function checkErrorThresholds(array $errorStats): array
    {
        $alerts = [];
        $thresholds = config('monitoring.thresholds', []);

        // Vérifier le taux d'erreur
        $errorRateThreshold = $thresholds['error_rate_percentage'] ?? 5;
        if ($errorStats['error_rate'] > $errorRateThreshold) {
            $alerts[] = [
                'type' => 'error_rate_high',
                'message' => "Taux d'erreur élevé: {$errorStats['error_rate']}%",
                'level' => 'warning',
                'value' => $errorStats['error_rate'],
                'threshold' => $errorRateThreshold,
            ];
        }

        // Vérifier les erreurs de la dernière heure
        $errorsThisHour = $errorStats['errors_this_hour'];
        if ($errorsThisHour > 10) {
            $alerts[] = [
                'type' => 'errors_this_hour_high',
                'message' => "Nombre élevé d'erreurs cette heure: {$errorsThisHour}",
                'level' => 'warning',
                'value' => $errorsThisHour,
                'threshold' => 10,
            ];
        }

        // Vérifier les erreurs critiques
        $criticalErrors = self::getCriticalErrorsCount();
        if ($criticalErrors > 0) {
            $alerts[] = [
                'type' => 'critical_errors',
                'message' => "Erreurs critiques détectées: {$criticalErrors}",
                'level' => 'critical',
                'value' => $criticalErrors,
                'threshold' => 0,
            ];
        }

        return $alerts;
    }

    /**
     * Obtient le nombre total de requêtes
     */
    private static function getTotalRequests(): int
    {
        $logFile = storage_path('logs/laravel.log');

        if (!file_exists($logFile)) {
            return 0;
        }

        $content = file_get_contents($logFile);
        $lines = explode("\n", $content);

        $totalRequests = 0;

        foreach ($lines as $line) {
            if (strpos($line, 'GET') !== false || strpos($line, 'POST') !== false ||
                strpos($line, 'PUT') !== false || strpos($line, 'DELETE') !== false) {
                $totalRequests++;
            }
        }

        return $totalRequests;
    }

    /**
     * Extrait le message d'erreur d'une ligne de log
     */
    private static function extractErrorMessage(string $line): string
    {
        // Extraire le message d'erreur après le niveau de log
        if (preg_match('/ERROR.*?:\s*(.+)/', $line, $matches)) {
            return trim($matches[1]);
        }

        if (preg_match('/CRITICAL.*?:\s*(.+)/', $line, $matches)) {
            return trim($matches[1]);
        }

        return 'Message d\'erreur non disponible';
    }

    /**
     * Calcule la tendance des erreurs
     */
    private static function calculateTrend(array $hourlyErrors): string
    {
        $values = array_values($hourlyErrors);
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
     * Obtient le nombre d'erreurs critiques
     */
    private static function getCriticalErrorsCount(): int
    {
        $logFile = storage_path('logs/laravel.log');

        if (!file_exists($logFile)) {
            return 0;
        }

        $content = file_get_contents($logFile);
        $lines = explode("\n", $content);

        $criticalErrors = 0;
        $oneHourAgo = now()->subHour()->format('Y-m-d H:i:s');

        foreach ($lines as $line) {
            if (strpos($line, 'CRITICAL') !== false) {
                if (preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $line, $matches)) {
                    if ($matches[1] >= $oneHourAgo) {
                        $criticalErrors++;
                    }
                }
            }
        }

        return $criticalErrors;
    }

    /**
     * Nettoie les anciens logs d'erreurs
     */
    public static function cleanupErrorLogs(int $daysToKeep = 30): void
    {
        $logPath = storage_path('logs');
        $cutoffDate = now()->subDays($daysToKeep);

        $files = glob($logPath . '/*.log');

        foreach ($files as $file) {
            if (filemtime($file) < $cutoffDate->timestamp) {
                unlink($file);
                Log::info("Deleted old error log file: " . basename($file));
            }
        }
    }

    /**
     * Génère un rapport d'erreurs
     */
    public static function generateErrorReport(int $hours = 24): array
    {
        $errorStats = self::getErrorStatistics();
        $recentErrors = self::getRecentErrors();
        $errorTrends = self::getErrorTrends();
        $alerts = self::checkErrorThresholds($errorStats);

        return [
            'period_hours' => $hours,
            'generated_at' => now()->toISOString(),
            'summary' => $errorStats,
            'recent_errors' => $recentErrors,
            'trends' => $errorTrends,
            'alerts' => $alerts,
            'recommendations' => self::generateRecommendations($errorStats, $alerts),
        ];
    }

    /**
     * Génère des recommandations basées sur les erreurs
     */
    private static function generateRecommendations(array $errorStats, array $alerts): array
    {
        $recommendations = [];

        if ($errorStats['error_rate'] > 5) {
            $recommendations[] = 'Le taux d\'erreur est élevé. Vérifiez les logs pour identifier les causes.';
        }

        if ($errorStats['errors_this_hour'] > 10) {
            $recommendations[] = 'Nombre élevé d\'erreurs cette heure. Surveillez l\'application de près.';
        }

        if (!empty($errorStats['most_common_errors'])) {
            $mostCommon = array_key_first($errorStats['most_common_errors']);
            $recommendations[] = "L'erreur la plus commune est: {$mostCommon}. Considérez une correction prioritaire.";
        }

        if (empty($recommendations)) {
            $recommendations[] = 'Aucun problème critique détecté. Continuez la surveillance.';
        }

        return $recommendations;
    }
}
