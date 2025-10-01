<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Mail\AlertNotification;

class AlertService
{
    /**
     * Envoie une alerte critique
     */
    public static function sendCriticalAlert(string $message, array $context = []): void
    {
        Log::channel('security')->critical('CRITICAL ALERT: ' . $message, $context);

        // Notifier les administrateurs par email
        $admins = User::whereHas('roles', function($query) {
            $query->where('name', 'admin');
        })->get();

        foreach ($admins as $admin) {
            try {
                Mail::to($admin->email)->send(new AlertNotification(
                    'Alerte Critique',
                    $message,
                    $context,
                    'critical'
                ));
            } catch (\Exception $e) {
                Log::error('Failed to send critical alert email', [
                    'admin_id' => $admin->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Envoie une alerte de performance
     */
    public static function sendPerformanceAlert(string $message, array $context = []): void
    {
        Log::channel('performance')->warning('PERFORMANCE ALERT: ' . $message, $context);

        // Seulement si c'est vraiment critique
        if (isset($context['execution_time_ms']) && $context['execution_time_ms'] > 5000) {
            self::sendCriticalAlert("Performance dégradée: {$message}", $context);
        }
    }

    /**
     * Envoie une alerte de sécurité
     */
    public static function sendSecurityAlert(string $message, array $context = []): void
    {
        Log::channel('security')->warning('SECURITY ALERT: ' . $message, $context);

        // Toujours notifier les admins pour les alertes de sécurité
        self::sendCriticalAlert("Alerte de sécurité: {$message}", $context);
    }

    /**
     * Envoie une alerte de stockage
     */
    public static function sendStorageAlert(string $message, array $context = []): void
    {
        Log::channel('errors')->warning('STORAGE ALERT: ' . $message, $context);

        if (isset($context['usage_percentage']) && $context['usage_percentage'] > 90) {
            self::sendCriticalAlert("Espace disque critique: {$message}", $context);
        }
    }

    /**
     * Vérifie et envoie des alertes automatiques
     */
    public static function checkAndSendAlerts(): void
    {
        // Vérifier l'espace disque
        $diskUsage = self::getDiskUsage();
        if ($diskUsage['percentage'] > 85) {
            self::sendStorageAlert("Espace disque faible", $diskUsage);
        }

        // Vérifier la mémoire
        $memoryUsage = memory_get_usage(true) / 1024 / 1024; // MB
        $memoryLimit = ini_get('memory_limit');
        $memoryLimitMB = self::convertToMB($memoryLimit);

        if ($memoryUsage > ($memoryLimitMB * 0.9)) {
            self::sendPerformanceAlert("Utilisation mémoire élevée", [
                'usage_mb' => $memoryUsage,
                'limit_mb' => $memoryLimitMB,
                'percentage' => round(($memoryUsage / $memoryLimitMB) * 100, 2)
            ]);
        }

        // Vérifier les erreurs récentes
        $recentErrors = self::getRecentErrorCount();
        if ($recentErrors > 10) {
            self::sendCriticalAlert("Nombre élevé d'erreurs récentes", [
                'error_count' => $recentErrors,
                'timeframe' => 'last hour'
            ]);
        }
    }

    /**
     * Obtient l'utilisation du disque
     */
    private static function getDiskUsage(): array
    {
        $totalBytes = disk_total_space(storage_path());
        $freeBytes = disk_free_space(storage_path());
        $usedBytes = $totalBytes - $freeBytes;

        return [
            'total_gb' => round($totalBytes / 1024 / 1024 / 1024, 2),
            'used_gb' => round($usedBytes / 1024 / 1024 / 1024, 2),
            'free_gb' => round($freeBytes / 1024 / 1024 / 1024, 2),
            'percentage' => round(($usedBytes / $totalBytes) * 100, 2)
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
     * Obtient le nombre d'erreurs récentes
     */
    private static function getRecentErrorCount(): int
    {
        $logFile = storage_path('logs/laravel.log');

        if (!file_exists($logFile)) {
            return 0;
        }

        $content = file_get_contents($logFile);
        $lines = explode("\n", $content);

        $recentErrors = 0;
        $oneHourAgo = now()->subHour()->format('Y-m-d H:i:s');

        foreach ($lines as $line) {
            if (strpos($line, 'ERROR') !== false || strpos($line, 'CRITICAL') !== false) {
                // Extraire la date de la ligne de log
                if (preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $line, $matches)) {
                    if ($matches[1] >= $oneHourAgo) {
                        $recentErrors++;
                    }
                }
            }
        }

        return $recentErrors;
    }
}
