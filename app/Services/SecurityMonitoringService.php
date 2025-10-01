<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class SecurityMonitoringService
{
    /**
     * Surveille les événements de sécurité
     */
    public static function monitorSecurity(): array
    {
        $securityStats = self::getSecurityStatistics();
        $recentEvents = self::getRecentSecurityEvents();
        $threats = self::getThreats();
        $alerts = self::checkSecurityThresholds($securityStats);

        return [
            'timestamp' => now()->toISOString(),
            'statistics' => $securityStats,
            'recent_events' => $recentEvents,
            'threats' => $threats,
            'alerts' => $alerts,
        ];
    }

    /**
     * Obtient les statistiques de sécurité
     */
    private static function getSecurityStatistics(): array
    {
        $logFile = storage_path('logs/security.log');

        if (!file_exists($logFile)) {
            return [
                'total_events' => 0,
                'events_today' => 0,
                'events_this_hour' => 0,
                'failed_logins' => 0,
                'suspicious_activities' => 0,
                'blocked_ips' => 0,
            ];
        }

        $content = file_get_contents($logFile);
        $lines = explode("\n", $content);

        $totalEvents = 0;
        $eventsToday = 0;
        $eventsThisHour = 0;
        $failedLogins = 0;
        $suspiciousActivities = 0;
        $blockedIps = 0;

        $today = now()->format('Y-m-d');
        $oneHourAgo = now()->subHour()->format('Y-m-d H:i:s');

        foreach ($lines as $line) {
            if (strpos($line, 'SECURITY') !== false) {
                $totalEvents++;

                // Extraire la date de la ligne de log
                if (preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $line, $matches)) {
                    $logDate = $matches[1];

                    if (strpos($logDate, $today) === 0) {
                        $eventsToday++;
                    }

                    if ($logDate >= $oneHourAgo) {
                        $eventsThisHour++;
                    }
                }

                // Compter les types d'événements
                if (strpos($line, 'authentication_failed') !== false) {
                    $failedLogins++;
                }

                if (strpos($line, 'suspicious_activity') !== false) {
                    $suspiciousActivities++;
                }

                if (strpos($line, 'blocked_ip') !== false) {
                    $blockedIps++;
                }
            }
        }

        return [
            'total_events' => $totalEvents,
            'events_today' => $eventsToday,
            'events_this_hour' => $eventsThisHour,
            'failed_logins' => $failedLogins,
            'suspicious_activities' => $suspiciousActivities,
            'blocked_ips' => $blockedIps,
        ];
    }

    /**
     * Obtient les événements de sécurité récents
     */
    private static function getRecentSecurityEvents(): array
    {
        $logFile = storage_path('logs/security.log');

        if (!file_exists($logFile)) {
            return [];
        }

        $content = file_get_contents($logFile);
        $lines = explode("\n", $content);

        $recentEvents = [];
        $oneHourAgo = now()->subHour()->format('Y-m-d H:i:s');

        foreach ($lines as $line) {
            if (strpos($line, 'SECURITY') !== false) {
                if (preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $line, $matches)) {
                    if ($matches[1] >= $oneHourAgo) {
                        $recentEvents[] = [
                            'timestamp' => $matches[1],
                            'event' => self::extractSecurityEvent($line),
                            'level' => self::extractSecurityLevel($line),
                            'ip_address' => self::extractIpAddress($line),
                        ];
                    }
                }
            }
        }

        // Limiter à 10 événements récents
        return array_slice($recentEvents, -10);
    }

    /**
     * Obtient les menaces détectées
     */
    private static function getThreats(): array
    {
        $logFile = storage_path('logs/security.log');

        if (!file_exists($logFile)) {
            return [
                'high_risk_ips' => [],
                'brute_force_attempts' => [],
                'suspicious_patterns' => [],
            ];
        }

        $content = file_get_contents($logFile);
        $lines = explode("\n", $content);

        $highRiskIps = [];
        $bruteForceAttempts = [];
        $suspiciousPatterns = [];

        $oneHourAgo = now()->subHour()->format('Y-m-d H:i:s');

        foreach ($lines as $line) {
            if (strpos($line, 'SECURITY') !== false) {
                if (preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $line, $matches)) {
                    if ($matches[1] >= $oneHourAgo) {
                        $ip = self::extractIpAddress($line);

                        if ($ip) {
                            // Détecter les IPs à haut risque
                            if (strpos($line, 'authentication_failed') !== false) {
                                $highRiskIps[$ip] = ($highRiskIps[$ip] ?? 0) + 1;
                            }

                            // Détecter les tentatives de force brute
                            if (strpos($line, 'brute_force') !== false) {
                                $bruteForceAttempts[$ip] = ($bruteForceAttempts[$ip] ?? 0) + 1;
                            }

                            // Détecter les patterns suspects
                            if (strpos($line, 'suspicious_pattern') !== false) {
                                $suspiciousPatterns[$ip] = ($suspiciousPatterns[$ip] ?? 0) + 1;
                            }
                        }
                    }
                }
            }
        }

        // Trier par nombre d'occurrences
        arsort($highRiskIps);
        arsort($bruteForceAttempts);
        arsort($suspiciousPatterns);

        return [
            'high_risk_ips' => array_slice($highRiskIps, 0, 10, true),
            'brute_force_attempts' => array_slice($bruteForceAttempts, 0, 10, true),
            'suspicious_patterns' => array_slice($suspiciousPatterns, 0, 10, true),
        ];
    }

    /**
     * Vérifie les seuils de sécurité
     */
    private static function checkSecurityThresholds(array $securityStats): array
    {
        $alerts = [];

        // Vérifier les tentatives de connexion échouées
        if ($securityStats['failed_logins'] > 10) {
            $alerts[] = [
                'type' => 'failed_logins_high',
                'message' => "Nombre élevé de tentatives de connexion échouées: {$securityStats['failed_logins']}",
                'level' => 'warning',
                'value' => $securityStats['failed_logins'],
                'threshold' => 10,
            ];
        }

        // Vérifier les activités suspectes
        if ($securityStats['suspicious_activities'] > 5) {
            $alerts[] = [
                'type' => 'suspicious_activities_high',
                'message' => "Nombre élevé d'activités suspectes: {$securityStats['suspicious_activities']}",
                'level' => 'critical',
                'value' => $securityStats['suspicious_activities'],
                'threshold' => 5,
            ];
        }

        // Vérifier les IPs bloquées
        if ($securityStats['blocked_ips'] > 0) {
            $alerts[] = [
                'type' => 'blocked_ips',
                'message' => "IPs bloquées: {$securityStats['blocked_ips']}",
                'level' => 'info',
                'value' => $securityStats['blocked_ips'],
                'threshold' => 0,
            ];
        }

        return $alerts;
    }

    /**
     * Extrait l'événement de sécurité d'une ligne de log
     */
    private static function extractSecurityEvent(string $line): string
    {
        if (preg_match('/SECURITY.*?(\w+):\s*(.+)/', $line, $matches)) {
            return trim($matches[2]);
        }

        return 'Événement de sécurité non disponible';
    }

    /**
     * Extrait le niveau de sécurité d'une ligne de log
     */
    private static function extractSecurityLevel(string $line): string
    {
        if (strpos($line, 'CRITICAL') !== false) {
            return 'critical';
        } elseif (strpos($line, 'WARNING') !== false) {
            return 'warning';
        } elseif (strpos($line, 'INFO') !== false) {
            return 'info';
        }

        return 'unknown';
    }

    /**
     * Extrait l'adresse IP d'une ligne de log
     */
    private static function extractIpAddress(string $line): ?string
    {
        if (preg_match('/ip_address["\s]*:[\s]*([0-9.]+)/', $line, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Génère un rapport de sécurité
     */
    public static function generateSecurityReport(int $hours = 24): array
    {
        $securityStats = self::getSecurityStatistics();
        $recentEvents = self::getRecentSecurityEvents();
        $threats = self::getThreats();
        $alerts = self::checkSecurityThresholds($securityStats);

        return [
            'period_hours' => $hours,
            'generated_at' => now()->toISOString(),
            'summary' => $securityStats,
            'recent_events' => $recentEvents,
            'threats' => $threats,
            'alerts' => $alerts,
            'recommendations' => self::generateSecurityRecommendations($securityStats, $alerts),
        ];
    }

    /**
     * Génère des recommandations de sécurité
     */
    private static function generateSecurityRecommendations(array $securityStats, array $alerts): array
    {
        $recommendations = [];

        if ($securityStats['failed_logins'] > 10) {
            $recommendations[] = 'Nombre élevé de tentatives de connexion échouées. Considérez l\'implémentation d\'un système de verrouillage de compte.';
        }

        if ($securityStats['suspicious_activities'] > 5) {
            $recommendations[] = 'Activités suspectes détectées. Surveillez l\'application de près et considérez des mesures de sécurité supplémentaires.';
        }

        if ($securityStats['blocked_ips'] > 0) {
            $recommendations[] = 'Des IPs ont été bloquées. Vérifiez les logs pour identifier les sources des attaques.';
        }

        if (empty($recommendations)) {
            $recommendations[] = 'Aucun problème de sécurité critique détecté. Continuez la surveillance.';
        }

        return $recommendations;
    }

    /**
     * Nettoie les anciens logs de sécurité
     */
    public static function cleanupSecurityLogs(int $daysToKeep = 90): void
    {
        $logPath = storage_path('logs');
        $cutoffDate = now()->subDays($daysToKeep);

        $files = glob($logPath . '/security*.log');

        foreach ($files as $file) {
            if (filemtime($file) < $cutoffDate->timestamp) {
                unlink($file);
                Log::info("Deleted old security log file: " . basename($file));
            }
        }
    }
}
