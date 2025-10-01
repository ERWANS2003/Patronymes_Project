<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class EmailMonitoringService
{
    /**
     * Surveille les emails
     */
    public static function monitorEmails(): array
    {
        $emailStats = self::getEmailStatistics();
        $emailPerformance = self::getEmailPerformance();
        $emailTrends = self::getEmailTrends();
        $alerts = self::checkEmailThresholds($emailStats);

        return [
            'timestamp' => now()->toISOString(),
            'statistics' => $emailStats,
            'performance' => $emailPerformance,
            'trends' => $emailTrends,
            'alerts' => $alerts,
        ];
    }

    /**
     * Obtient les statistiques des emails
     */
    private static function getEmailStatistics(): array
    {
        $driver = config('mail.default');
        $stats = [
            'driver' => $driver,
            'enabled' => true,
        ];

        try {
            if ($driver === 'smtp') {
                $stats = array_merge($stats, self::getSmtpStatistics());
            } elseif ($driver === 'mailgun') {
                $stats = array_merge($stats, self::getMailgunStatistics());
            } elseif ($driver === 'ses') {
                $stats = array_merge($stats, self::getSesStatistics());
            } else {
                $stats = array_merge($stats, self::getLogStatistics());
            }
        } catch (\Exception $e) {
            Log::error('Failed to get email statistics', ['error' => $e->getMessage()]);
            $stats['error'] = 'Email statistics unavailable';
        }

        return $stats;
    }

    /**
     * Obtient les statistiques SMTP
     */
    private static function getSmtpStatistics(): array
    {
        $config = config('mail.mailers.smtp');

        return [
            'host' => $config['host'] ?? 'Unknown',
            'port' => $config['port'] ?? 'Unknown',
            'encryption' => $config['encryption'] ?? 'None',
            'username' => $config['username'] ?? 'Unknown',
            'timeout' => $config['timeout'] ?? 30,
            'auth_mode' => $config['auth_mode'] ?? 'Unknown',
        ];
    }

    /**
     * Obtient les statistiques Mailgun
     */
    private static function getMailgunStatistics(): array
    {
        $config = config('services.mailgun');

        return [
            'domain' => $config['domain'] ?? 'Unknown',
            'secret' => $config['secret'] ? 'Set' : 'Not Set',
            'endpoint' => $config['endpoint'] ?? 'Unknown',
            'timeout' => $config['timeout'] ?? 30,
        ];
    }

    /**
     * Obtient les statistiques SES
     */
    private static function getSesStatistics(): array
    {
        $config = config('services.ses');

        return [
            'key' => $config['key'] ? 'Set' : 'Not Set',
            'secret' => $config['secret'] ? 'Set' : 'Not Set',
            'region' => $config['region'] ?? 'Unknown',
            'timeout' => $config['timeout'] ?? 30,
        ];
    }

    /**
     * Obtient les statistiques de log
     */
    private static function getLogStatistics(): array
    {
        $logPath = storage_path('logs');
        $logFiles = glob($logPath . '/mail*.log');

        $totalEmails = 0;
        $emailsToday = 0;
        $emailsThisHour = 0;
        $totalSize = 0;

        $today = now()->format('Y-m-d');
        $oneHourAgo = now()->subHour()->format('Y-m-d H:i:s');

        foreach ($logFiles as $file) {
            $totalSize += filesize($file);
            $content = file_get_contents($file);
            $lines = explode("\n", $content);

            foreach ($lines as $line) {
                if (strpos($line, 'Message-ID:') !== false) {
                    $totalEmails++;

                    if (preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $line, $matches)) {
                        $logDate = $matches[1];

                        if (strpos($logDate, $today) === 0) {
                            $emailsToday++;
                        }

                        if ($logDate >= $oneHourAgo) {
                            $emailsThisHour++;
                        }
                    }
                }
            }
        }

        return [
            'total_emails' => $totalEmails,
            'emails_today' => $emailsToday,
            'emails_this_hour' => $emailsThisHour,
            'total_size_mb' => round($totalSize / 1024 / 1024, 2),
            'log_files' => count($logFiles),
        ];
    }

    /**
     * Obtient les performances des emails
     */
    private static function getEmailPerformance(): array
    {
        $startTime = microtime(true);

        try {
            // Test de performance des emails
            $testEmail = new \App\Mail\TestEmail();

            // Test d'envoi d'email
            $sendStart = microtime(true);
            Mail::to('test@example.com')->send($testEmail);
            $sendTime = microtime(true) - $sendStart;

            $totalTime = microtime(true) - $startTime;

            return [
                'send_time_ms' => round($sendTime * 1000, 3),
                'total_time_ms' => round($totalTime * 1000, 3),
                'test_successful' => true,
            ];
        } catch (\Exception $e) {
            Log::error('Email performance test failed', ['error' => $e->getMessage()]);
            return [
                'error' => 'Email performance test failed',
                'test_successful' => false,
            ];
        }
    }

    /**
     * Obtient les tendances des emails
     */
    private static function getEmailTrends(): array
    {
        $logPath = storage_path('logs');
        $logFiles = glob($logPath . '/mail*.log');

        $hourlyEmails = [];
        $dailyEmails = [];

        // Initialiser les tableaux pour les dernières 24 heures
        for ($i = 23; $i >= 0; $i--) {
            $hour = now()->subHours($i)->format('Y-m-d H');
            $hourlyEmails[$hour] = 0;
        }

        // Initialiser les tableaux pour les 7 derniers jours
        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i)->format('Y-m-d');
            $dailyEmails[$day] = 0;
        }

        foreach ($logFiles as $file) {
            $content = file_get_contents($file);
            $lines = explode("\n", $content);

            foreach ($lines as $line) {
                if (strpos($line, 'Message-ID:') !== false) {
                    if (preg_match('/\[(\d{4}-\d{2}-\d{2}) (\d{2}):\d{2}:\d{2}\]/', $line, $matches)) {
                        $date = $matches[1];
                        $hour = $matches[2];
                        $hourKey = $date . ' ' . $hour;

                        if (isset($hourlyEmails[$hourKey])) {
                            $hourlyEmails[$hourKey]++;
                        }

                        if (isset($dailyEmails[$date])) {
                            $dailyEmails[$date]++;
                        }
                    }
                }
            }
        }

        // Calculer la tendance
        $trend = self::calculateEmailTrend($hourlyEmails);

        return [
            'hourly' => $hourlyEmails,
            'daily' => $dailyEmails,
            'trend' => $trend,
        ];
    }

    /**
     * Calcule la tendance des emails
     */
    private static function calculateEmailTrend(array $hourlyEmails): string
    {
        $values = array_values($hourlyEmails);
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
     * Vérifie les seuils des emails
     */
    private static function checkEmailThresholds(array $emailStats): array
    {
        $alerts = [];

        if (isset($emailStats['error'])) {
            $alerts[] = [
                'type' => 'email_error',
                'message' => 'Erreur d\'email: ' . $emailStats['error'],
                'level' => 'critical',
                'value' => $emailStats['error'],
                'threshold' => 'none',
            ];
            return $alerts;
        }

        // Vérifier le nombre d'emails par heure
        if (isset($emailStats['emails_this_hour']) && $emailStats['emails_this_hour'] > 1000) {
            $alerts[] = [
                'type' => 'high_email_volume',
                'message' => "Volume d'emails élevé cette heure: {$emailStats['emails_this_hour']}",
                'level' => 'warning',
                'value' => $emailStats['emails_this_hour'],
                'threshold' => 1000,
            ];
        }

        // Vérifier la taille totale des logs d'emails
        if (isset($emailStats['total_size_mb']) && $emailStats['total_size_mb'] > 100) { // Plus de 100MB
            $alerts[] = [
                'type' => 'large_email_log_size',
                'message' => "Taille totale des logs d'emails élevée: {$emailStats['total_size_mb']}MB",
                'level' => 'warning',
                'value' => $emailStats['total_size_mb'],
                'threshold' => 100,
            ];
        }

        // Vérifier le nombre de fichiers de logs d'emails
        if (isset($emailStats['log_files']) && $emailStats['log_files'] > 50) {
            $alerts[] = [
                'type' => 'too_many_email_log_files',
                'message' => "Nombre élevé de fichiers de logs d'emails: {$emailStats['log_files']}",
                'level' => 'info',
                'value' => $emailStats['log_files'],
                'threshold' => 50,
            ];
        }

        return $alerts;
    }

    /**
     * Génère un rapport des emails
     */
    public static function generateEmailReport(int $hours = 24): array
    {
        $emailStats = self::getEmailStatistics();
        $emailPerformance = self::getEmailPerformance();
        $emailTrends = self::getEmailTrends();
        $alerts = self::checkEmailThresholds($emailStats);

        return [
            'period_hours' => $hours,
            'generated_at' => now()->toISOString(),
            'summary' => $emailStats,
            'performance' => $emailPerformance,
            'trends' => $emailTrends,
            'alerts' => $alerts,
            'recommendations' => self::generateEmailRecommendations($emailStats, $alerts),
        ];
    }

    /**
     * Génère des recommandations pour les emails
     */
    private static function generateEmailRecommendations(array $emailStats, array $alerts): array
    {
        $recommendations = [];

        if (isset($emailStats['error'])) {
            $recommendations[] = 'Vérifiez la configuration des emails et les logs d\'erreur.';
            return $recommendations;
        }

        // Recommandations basées sur les alertes
        foreach ($alerts as $alert) {
            switch ($alert['type']) {
                case 'high_email_volume':
                    $recommendations[] = 'Considérez l\'utilisation d\'une file d\'attente pour les emails en masse.';
                    break;
                case 'large_email_log_size':
                    $recommendations[] = 'Nettoyez régulièrement les logs d\'emails et considérez la rotation des logs.';
                    break;
                case 'too_many_email_log_files':
                    $recommendations[] = 'Consolidez les logs d\'emails et supprimez les anciens fichiers.';
                    break;
            }
        }

        // Recommandations générales
        if ($emailStats['driver'] === 'log') {
            $recommendations[] = 'Considérez l\'utilisation d\'un service d\'email réel pour la production.';
        }

        if (isset($emailStats['emails_this_hour']) && $emailStats['emails_this_hour'] > 500) {
            $recommendations[] = 'Volume d\'emails élevé. Vérifiez la configuration et les performances.';
        }

        if (empty($recommendations)) {
            $recommendations[] = 'Aucun problème critique détecté. Continuez la surveillance.';
        }

        return $recommendations;
    }

    /**
     * Nettoie les anciens logs d'emails
     */
    public static function cleanupEmailLogs(int $daysToKeep = 30): void
    {
        $logPath = storage_path('logs');
        $cutoffDate = now()->subDays($daysToKeep);

        $files = glob($logPath . '/mail*.log');

        foreach ($files as $file) {
            if (filemtime($file) < $cutoffDate->timestamp) {
                unlink($file);
                Log::info("Deleted old email log file: " . basename($file));
            }
        }
    }
}
