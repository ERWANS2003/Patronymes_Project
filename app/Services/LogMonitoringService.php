<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class LogMonitoringService
{
    /**
     * Surveille les logs
     */
    public static function monitorLogs(): array
    {
        $logStats = self::getLogStatistics();
        $recentLogs = self::getRecentLogs();
        $logTrends = self::getLogTrends();
        $alerts = self::checkLogThresholds($logStats);

        return [
            'timestamp' => now()->toISOString(),
            'statistics' => $logStats,
            'recent_logs' => $recentLogs,
            'trends' => $logTrends,
            'alerts' => $alerts,
        ];
    }

    /**
     * Obtient les statistiques des logs
     */
    private static function getLogStatistics(): array
    {
        $logPath = storage_path('logs');

        if (!is_dir($logPath)) {
            return [
                'total_logs' => 0,
                'logs_today' => 0,
                'logs_this_hour' => 0,
                'log_files' => [],
                'total_size_mb' => 0,
                'oldest_log' => null,
                'newest_log' => null,
            ];
        }

        $files = glob($logPath . '/*.log');
        $totalLogs = 0;
        $logsToday = 0;
        $logsThisHour = 0;
        $totalSize = 0;
        $logFiles = [];
        $oldestLog = null;
        $newestLog = null;

        $today = now()->format('Y-m-d');
        $oneHourAgo = now()->subHour()->format('Y-m-d H:i:s');

        foreach ($files as $file) {
            $fileSize = filesize($file);
            $totalSize += $fileSize;

            $logFiles[] = [
                'name' => basename($file),
                'size_mb' => round($fileSize / 1024 / 1024, 2),
                'modified' => date('Y-m-d H:i:s', filemtime($file)),
            ];

            if ($oldestLog === null || filemtime($file) < $oldestLog) {
                $oldestLog = date('Y-m-d H:i:s', filemtime($file));
            }

            if ($newestLog === null || filemtime($file) > $newestLog) {
                $newestLog = date('Y-m-d H:i:s', filemtime($file));
            }

            $content = file_get_contents($file);
            $lines = explode("\n", $content);

            foreach ($lines as $line) {
                if (!empty(trim($line))) {
                    $totalLogs++;

                    // Extraire la date de la ligne de log
                    if (preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $line, $matches)) {
                        $logDate = $matches[1];

                        if (strpos($logDate, $today) === 0) {
                            $logsToday++;
                        }

                        if ($logDate >= $oneHourAgo) {
                            $logsThisHour++;
                        }
                    }
                }
            }
        }

        return [
            'total_logs' => $totalLogs,
            'logs_today' => $logsToday,
            'logs_this_hour' => $logsThisHour,
            'log_files' => $logFiles,
            'total_size_mb' => round($totalSize / 1024 / 1024, 2),
            'oldest_log' => $oldestLog,
            'newest_log' => $newestLog,
        ];
    }

    /**
     * Obtient les logs récents
     */
    private static function getRecentLogs(): array
    {
        $logPath = storage_path('logs');

        if (!is_dir($logPath)) {
            return [];
        }

        $files = glob($logPath . '/*.log');
        $recentLogs = [];
        $oneHourAgo = now()->subHour()->format('Y-m-d H:i:s');

        foreach ($files as $file) {
            $content = file_get_contents($file);
            $lines = explode("\n", $content);

            foreach ($lines as $line) {
                if (!empty(trim($line))) {
                    if (preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $line, $matches)) {
                        if ($matches[1] >= $oneHourAgo) {
                            $recentLogs[] = [
                                'timestamp' => $matches[1],
                                'level' => self::extractLogLevel($line),
                                'message' => self::extractLogMessage($line),
                                'file' => basename($file),
                            ];
                        }
                    }
                }
            }
        }

        // Trier par timestamp et limiter à 50 logs
        usort($recentLogs, function($a, $b) {
            return strtotime($b['timestamp']) - strtotime($a['timestamp']);
        });

        return array_slice($recentLogs, 0, 50);
    }

    /**
     * Obtient les tendances des logs
     */
    private static function getLogTrends(): array
    {
        $logPath = storage_path('logs');

        if (!is_dir($logPath)) {
            return [
                'hourly' => [],
                'daily' => [],
                'trend' => 'stable',
            ];
        }

        $files = glob($logPath . '/*.log');
        $hourlyLogs = [];
        $dailyLogs = [];

        // Initialiser les tableaux pour les dernières 24 heures
        for ($i = 23; $i >= 0; $i--) {
            $hour = now()->subHours($i)->format('Y-m-d H');
            $hourlyLogs[$hour] = 0;
        }

        // Initialiser les tableaux pour les 7 derniers jours
        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i)->format('Y-m-d');
            $dailyLogs[$day] = 0;
        }

        foreach ($files as $file) {
            $content = file_get_contents($file);
            $lines = explode("\n", $content);

            foreach ($lines as $line) {
                if (!empty(trim($line))) {
                    if (preg_match('/\[(\d{4}-\d{2}-\d{2}) (\d{2}):\d{2}:\d{2}\]/', $line, $matches)) {
                        $date = $matches[1];
                        $hour = $matches[2];
                        $hourKey = $date . ' ' . $hour;

                        if (isset($hourlyLogs[$hourKey])) {
                            $hourlyLogs[$hourKey]++;
                        }

                        if (isset($dailyLogs[$date])) {
                            $dailyLogs[$date]++;
                        }
                    }
                }
            }
        }

        // Calculer la tendance
        $trend = self::calculateLogTrend($hourlyLogs);

        return [
            'hourly' => $hourlyLogs,
            'daily' => $dailyLogs,
            'trend' => $trend,
        ];
    }

    /**
     * Calcule la tendance des logs
     */
    private static function calculateLogTrend(array $hourlyLogs): string
    {
        $values = array_values($hourlyLogs);
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
     * Extrait le niveau de log d'une ligne
     */
    private static function extractLogLevel(string $line): string
    {
        if (strpos($line, 'CRITICAL') !== false) {
            return 'critical';
        } elseif (strpos($line, 'ERROR') !== false) {
            return 'error';
        } elseif (strpos($line, 'WARNING') !== false) {
            return 'warning';
        } elseif (strpos($line, 'INFO') !== false) {
            return 'info';
        } elseif (strpos($line, 'DEBUG') !== false) {
            return 'debug';
        }

        return 'unknown';
    }

    /**
     * Extrait le message de log d'une ligne
     */
    private static function extractLogMessage(string $line): string
    {
        // Extraire le message après le niveau de log
        if (preg_match('/\w+.*?:\s*(.+)/', $line, $matches)) {
            return trim($matches[1]);
        }

        return 'Message de log non disponible';
    }

    /**
     * Vérifie les seuils des logs
     */
    private static function checkLogThresholds(array $logStats): array
    {
        $alerts = [];

        // Vérifier la taille totale des logs
        if ($logStats['total_size_mb'] > 1000) { // Plus de 1GB
            $alerts[] = [
                'type' => 'large_log_size',
                'message' => "Taille totale des logs élevée: {$logStats['total_size_mb']}MB",
                'level' => 'warning',
                'value' => $logStats['total_size_mb'],
                'threshold' => 1000,
            ];
        }

        // Vérifier le nombre de logs par heure
        if ($logStats['logs_this_hour'] > 10000) {
            $alerts[] = [
                'type' => 'high_log_volume',
                'message' => "Volume de logs élevé cette heure: {$logStats['logs_this_hour']}",
                'level' => 'warning',
                'value' => $logStats['logs_this_hour'],
                'threshold' => 10000,
            ];
        }

        // Vérifier le nombre de fichiers de logs
        if (count($logStats['log_files']) > 50) {
            $alerts[] = [
                'type' => 'too_many_log_files',
                'message' => "Nombre élevé de fichiers de logs: " . count($logStats['log_files']),
                'level' => 'info',
                'value' => count($logStats['log_files']),
                'threshold' => 50,
            ];
        }

        return $alerts;
    }

    /**
     * Génère un rapport des logs
     */
    public static function generateLogReport(int $hours = 24): array
    {
        $logStats = self::getLogStatistics();
        $recentLogs = self::getRecentLogs();
        $logTrends = self::getLogTrends();
        $alerts = self::checkLogThresholds($logStats);

        return [
            'period_hours' => $hours,
            'generated_at' => now()->toISOString(),
            'summary' => $logStats,
            'recent_logs' => $recentLogs,
            'trends' => $logTrends,
            'alerts' => $alerts,
            'recommendations' => self::generateLogRecommendations($logStats, $alerts),
        ];
    }

    /**
     * Génère des recommandations pour les logs
     */
    private static function generateLogRecommendations(array $logStats, array $alerts): array
    {
        $recommendations = [];

        // Recommandations basées sur les alertes
        foreach ($alerts as $alert) {
            switch ($alert['type']) {
                case 'large_log_size':
                    $recommendations[] = 'Considérez la rotation des logs et le nettoyage des anciens fichiers.';
                    break;
                case 'high_log_volume':
                    $recommendations[] = 'Vérifiez la configuration des logs et considérez l\'augmentation du niveau de log.';
                    break;
                case 'too_many_log_files':
                    $recommendations[] = 'Consolidez les logs et supprimez les anciens fichiers inutiles.';
                    break;
            }
        }

        // Recommandations générales
        if ($logStats['total_size_mb'] > 500) {
            $recommendations[] = 'La taille des logs est importante. Mettez en place une rotation automatique.';
        }

        if ($logStats['logs_this_hour'] > 5000) {
            $recommendations[] = 'Volume de logs élevé. Vérifiez la configuration et les performances.';
        }

        if (empty($recommendations)) {
            $recommendations[] = 'Aucun problème critique détecté. Continuez la surveillance.';
        }

        return $recommendations;
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
}
