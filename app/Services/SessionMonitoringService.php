<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class SessionMonitoringService
{
    /**
     * Surveille les sessions
     */
    public static function monitorSessions(): array
    {
        $sessionStats = self::getSessionStatistics();
        $activeSessions = self::getActiveSessions();
        $sessionTrends = self::getSessionTrends();
        $alerts = self::checkSessionThresholds($sessionStats);

        return [
            'timestamp' => now()->toISOString(),
            'statistics' => $sessionStats,
            'active_sessions' => $activeSessions,
            'trends' => $sessionTrends,
            'alerts' => $alerts,
        ];
    }

    /**
     * Obtient les statistiques des sessions
     */
    private static function getSessionStatistics(): array
    {
        $sessionPath = storage_path('framework/sessions');

        if (!is_dir($sessionPath)) {
            return [
                'total_sessions' => 0,
                'active_sessions' => 0,
                'expired_sessions' => 0,
                'session_files' => [],
                'total_size_mb' => 0,
                'oldest_session' => null,
                'newest_session' => null,
            ];
        }

        $files = glob($sessionPath . '/*');
        $totalSessions = 0;
        $activeSessions = 0;
        $expiredSessions = 0;
        $totalSize = 0;
        $sessionFiles = [];
        $oldestSession = null;
        $newestSession = null;

        $now = time();
        $sessionLifetime = config('session.lifetime', 120) * 60; // en secondes

        foreach ($files as $file) {
            if (is_file($file)) {
                $fileSize = filesize($file);
                $totalSize += $fileSize;
                $modified = filemtime($file);

                $sessionFiles[] = [
                    'name' => basename($file),
                    'size_mb' => round($fileSize / 1024 / 1024, 2),
                    'modified' => date('Y-m-d H:i:s', $modified),
                    'age_minutes' => round(($now - $modified) / 60, 2),
                ];

                if ($oldestSession === null || $modified < $oldestSession) {
                    $oldestSession = date('Y-m-d H:i:s', $modified);
                }

                if ($newestSession === null || $modified > $newestSession) {
                    $newestSession = date('Y-m-d H:i:s', $modified);
                }

                $totalSessions++;

                // Vérifier si la session est active ou expirée
                if (($now - $modified) < $sessionLifetime) {
                    $activeSessions++;
                } else {
                    $expiredSessions++;
                }
            }
        }

        return [
            'total_sessions' => $totalSessions,
            'active_sessions' => $activeSessions,
            'expired_sessions' => $expiredSessions,
            'session_files' => $sessionFiles,
            'total_size_mb' => round($totalSize / 1024 / 1024, 2),
            'oldest_session' => $oldestSession,
            'newest_session' => $newestSession,
            'session_lifetime_minutes' => $sessionLifetime / 60,
        ];
    }

    /**
     * Obtient les sessions actives
     */
    private static function getActiveSessions(): array
    {
        $sessionPath = storage_path('framework/sessions');

        if (!is_dir($sessionPath)) {
            return [];
        }

        $files = glob($sessionPath . '/*');
        $activeSessions = [];
        $now = time();
        $sessionLifetime = config('session.lifetime', 120) * 60; // en secondes

        foreach ($files as $file) {
            if (is_file($file)) {
                $modified = filemtime($file);

                if (($now - $modified) < $sessionLifetime) {
                    $content = file_get_contents($file);
                    $sessionData = self::parseSessionData($content);

                    $activeSessions[] = [
                        'id' => basename($file),
                        'size_mb' => round(filesize($file) / 1024 / 1024, 2),
                        'modified' => date('Y-m-d H:i:s', $modified),
                        'age_minutes' => round(($now - $modified) / 60, 2),
                        'user_id' => $sessionData['user_id'] ?? null,
                        'ip_address' => $sessionData['ip_address'] ?? null,
                        'user_agent' => $sessionData['user_agent'] ?? null,
                        'last_activity' => $sessionData['last_activity'] ?? null,
                    ];
                }
            }
        }

        // Trier par dernière activité
        usort($activeSessions, function($a, $b) {
            return strtotime($b['modified']) - strtotime($a['modified']);
        });

        return array_slice($activeSessions, 0, 50); // Limiter à 50 sessions
    }

    /**
     * Parse les données de session
     */
    private static function parseSessionData(string $content): array
    {
        $data = [];

        try {
            // Décoder les données de session
            $decoded = unserialize($content);

            if (is_array($decoded)) {
                $data['user_id'] = $decoded['login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d'] ?? null;
                $data['ip_address'] = $decoded['_token'] ?? null;
                $data['user_agent'] = $decoded['_previous'] ?? null;
                $data['last_activity'] = $decoded['_flash'] ?? null;
            }
        } catch (\Exception $e) {
            // Ignore les erreurs de parsing
        }

        return $data;
    }

    /**
     * Obtient les tendances des sessions
     */
    private static function getSessionTrends(): array
    {
        $sessionPath = storage_path('framework/sessions');

        if (!is_dir($sessionPath)) {
            return [
                'hourly' => [],
                'daily' => [],
                'trend' => 'stable',
            ];
        }

        $files = glob($sessionPath . '/*');
        $hourlySessions = [];
        $dailySessions = [];

        // Initialiser les tableaux pour les dernières 24 heures
        for ($i = 23; $i >= 0; $i--) {
            $hour = now()->subHours($i)->format('Y-m-d H');
            $hourlySessions[$hour] = 0;
        }

        // Initialiser les tableaux pour les 7 derniers jours
        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i)->format('Y-m-d');
            $dailySessions[$day] = 0;
        }

        foreach ($files as $file) {
            if (is_file($file)) {
                $modified = filemtime($file);
                $date = date('Y-m-d', $modified);
                $hour = date('Y-m-d H', $modified);

                if (isset($hourlySessions[$hour])) {
                    $hourlySessions[$hour]++;
                }

                if (isset($dailySessions[$date])) {
                    $dailySessions[$date]++;
                }
            }
        }

        // Calculer la tendance
        $trend = self::calculateSessionTrend($hourlySessions);

        return [
            'hourly' => $hourlySessions,
            'daily' => $dailySessions,
            'trend' => $trend,
        ];
    }

    /**
     * Calcule la tendance des sessions
     */
    private static function calculateSessionTrend(array $hourlySessions): string
    {
        $values = array_values($hourlySessions);
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
     * Vérifie les seuils des sessions
     */
    private static function checkSessionThresholds(array $sessionStats): array
    {
        $alerts = [];

        // Vérifier le nombre de sessions actives
        if ($sessionStats['active_sessions'] > 1000) {
            $alerts[] = [
                'type' => 'high_active_sessions',
                'message' => "Nombre élevé de sessions actives: {$sessionStats['active_sessions']}",
                'level' => 'warning',
                'value' => $sessionStats['active_sessions'],
                'threshold' => 1000,
            ];
        }

        // Vérifier le nombre de sessions expirées
        if ($sessionStats['expired_sessions'] > 10000) {
            $alerts[] = [
                'type' => 'high_expired_sessions',
                'message' => "Nombre élevé de sessions expirées: {$sessionStats['expired_sessions']}",
                'level' => 'info',
                'value' => $sessionStats['expired_sessions'],
                'threshold' => 10000,
            ];
        }

        // Vérifier la taille totale des sessions
        if ($sessionStats['total_size_mb'] > 100) { // Plus de 100MB
            $alerts[] = [
                'type' => 'large_session_size',
                'message' => "Taille totale des sessions élevée: {$sessionStats['total_size_mb']}MB",
                'level' => 'warning',
                'value' => $sessionStats['total_size_mb'],
                'threshold' => 100,
            ];
        }

        // Vérifier le nombre de fichiers de sessions
        if (count($sessionStats['session_files']) > 50000) {
            $alerts[] = [
                'type' => 'too_many_session_files',
                'message' => "Nombre élevé de fichiers de sessions: " . count($sessionStats['session_files']),
                'level' => 'info',
                'value' => count($sessionStats['session_files']),
                'threshold' => 50000,
            ];
        }

        return $alerts;
    }

    /**
     * Génère un rapport des sessions
     */
    public static function generateSessionReport(int $hours = 24): array
    {
        $sessionStats = self::getSessionStatistics();
        $activeSessions = self::getActiveSessions();
        $sessionTrends = self::getSessionTrends();
        $alerts = self::checkSessionThresholds($sessionStats);

        return [
            'period_hours' => $hours,
            'generated_at' => now()->toISOString(),
            'summary' => $sessionStats,
            'active_sessions' => $activeSessions,
            'trends' => $sessionTrends,
            'alerts' => $alerts,
            'recommendations' => self::generateSessionRecommendations($sessionStats, $alerts),
        ];
    }

    /**
     * Génère des recommandations pour les sessions
     */
    private static function generateSessionRecommendations(array $sessionStats, array $alerts): array
    {
        $recommendations = [];

        // Recommandations basées sur les alertes
        foreach ($alerts as $alert) {
            switch ($alert['type']) {
                case 'high_active_sessions':
                    $recommendations[] = 'Considérez l\'augmentation de la durée de vie des sessions ou l\'optimisation de la gestion des sessions.';
                    break;
                case 'high_expired_sessions':
                    $recommendations[] = 'Nettoyez régulièrement les sessions expirées pour libérer de l\'espace disque.';
                    break;
                case 'large_session_size':
                    $recommendations[] = 'Optimisez la taille des données de session et considérez l\'utilisation d\'un stockage de sessions plus efficace.';
                    break;
                case 'too_many_session_files':
                    $recommendations[] = 'Considérez l\'utilisation d\'un stockage de sessions en base de données ou Redis.';
                    break;
            }
        }

        // Recommandations générales
        if ($sessionStats['active_sessions'] > 500) {
            $recommendations[] = 'Le nombre de sessions actives est élevé. Vérifiez la configuration des sessions.';
        }

        if ($sessionStats['expired_sessions'] > 5000) {
            $recommendations[] = 'Nombre élevé de sessions expirées. Mettez en place un nettoyage automatique.';
        }

        if (empty($recommendations)) {
            $recommendations[] = 'Aucun problème critique détecté. Continuez la surveillance.';
        }

        return $recommendations;
    }

    /**
     * Nettoie les sessions expirées
     */
    public static function cleanupExpiredSessions(): void
    {
        $sessionPath = storage_path('framework/sessions');

        if (!is_dir($sessionPath)) {
            return;
        }

        $files = glob($sessionPath . '/*');
        $now = time();
        $sessionLifetime = config('session.lifetime', 120) * 60; // en secondes
        $cleanedCount = 0;

        foreach ($files as $file) {
            if (is_file($file)) {
                $modified = filemtime($file);

                if (($now - $modified) > $sessionLifetime) {
                    unlink($file);
                    $cleanedCount++;
                }
            }
        }

        Log::info("Cleaned up {$cleanedCount} expired sessions");
    }
}
