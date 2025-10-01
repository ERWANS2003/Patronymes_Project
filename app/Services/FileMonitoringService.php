<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class FileMonitoringService
{
    /**
     * Surveille les fichiers
     */
    public static function monitorFiles(): array
    {
        $fileStats = self::getFileStatistics();
        $filePermissions = self::getFilePermissions();
        $fileTrends = self::getFileTrends();
        $alerts = self::checkFileThresholds($fileStats);

        return [
            'timestamp' => now()->toISOString(),
            'statistics' => $fileStats,
            'permissions' => $filePermissions,
            'trends' => $fileTrends,
            'alerts' => $alerts,
        ];
    }

    /**
     * Obtient les statistiques des fichiers
     */
    private static function getFileStatistics(): array
    {
        $storagePath = storage_path();
        $publicPath = public_path();
        $appPath = app_path();

        $stats = [
            'storage' => self::getDirectoryStats($storagePath),
            'public' => self::getDirectoryStats($publicPath),
            'app' => self::getDirectoryStats($appPath),
        ];

        return $stats;
    }

    /**
     * Obtient les statistiques d'un répertoire
     */
    private static function getDirectoryStats(string $path): array
    {
        if (!is_dir($path)) {
            return [
                'exists' => false,
                'total_files' => 0,
                'total_directories' => 0,
                'total_size_mb' => 0,
                'oldest_file' => null,
                'newest_file' => null,
            ];
        }

        $files = glob($path . '/**/*', GLOB_BRACE);
        $totalFiles = 0;
        $totalDirectories = 0;
        $totalSize = 0;
        $oldestFile = null;
        $newestFile = null;

        foreach ($files as $file) {
            if (is_file($file)) {
                $totalFiles++;
                $fileSize = filesize($file);
                $totalSize += $fileSize;
                $modified = filemtime($file);

                if ($oldestFile === null || $modified < $oldestFile) {
                    $oldestFile = $modified;
                }

                if ($newestFile === null || $modified > $newestFile) {
                    $newestFile = $modified;
                }
            } elseif (is_dir($file)) {
                $totalDirectories++;
            }
        }

        return [
            'exists' => true,
            'total_files' => $totalFiles,
            'total_directories' => $totalDirectories,
            'total_size_mb' => round($totalSize / 1024 / 1024, 2),
            'oldest_file' => $oldestFile ? date('Y-m-d H:i:s', $oldestFile) : null,
            'newest_file' => $newestFile ? date('Y-m-d H:i:s', $newestFile) : null,
        ];
    }

    /**
     * Obtient les permissions des fichiers
     */
    private static function getFilePermissions(): array
    {
        $criticalPaths = [
            'storage' => storage_path(),
            'public' => public_path(),
            'app' => app_path(),
            'config' => config_path(),
            'database' => database_path(),
            'resources' => resource_path(),
            'routes' => base_path('routes'),
        ];

        $permissions = [];

        foreach ($criticalPaths as $name => $path) {
            if (is_dir($path)) {
                $permissions[$name] = [
                    'path' => $path,
                    'permissions' => substr(sprintf('%o', fileperms($path)), -4),
                    'readable' => is_readable($path),
                    'writable' => is_writable($path),
                    'executable' => is_executable($path),
                ];
            } else {
                $permissions[$name] = [
                    'path' => $path,
                    'exists' => false,
                ];
            }
        }

        return $permissions;
    }

    /**
     * Obtient les tendances des fichiers
     */
    private static function getFileTrends(): array
    {
        $storagePath = storage_path();
        $publicPath = public_path();

        $trends = [
            'storage' => self::getDirectoryTrends($storagePath),
            'public' => self::getDirectoryTrends($publicPath),
        ];

        return $trends;
    }

    /**
     * Obtient les tendances d'un répertoire
     */
    private static function getDirectoryTrends(string $path): array
    {
        if (!is_dir($path)) {
            return [
                'hourly' => [],
                'daily' => [],
                'trend' => 'stable',
            ];
        }

        $files = glob($path . '/**/*', GLOB_BRACE);
        $hourlyFiles = [];
        $dailyFiles = [];

        // Initialiser les tableaux pour les dernières 24 heures
        for ($i = 23; $i >= 0; $i--) {
            $hour = now()->subHours($i)->format('Y-m-d H');
            $hourlyFiles[$hour] = 0;
        }

        // Initialiser les tableaux pour les 7 derniers jours
        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i)->format('Y-m-d');
            $dailyFiles[$day] = 0;
        }

        foreach ($files as $file) {
            if (is_file($file)) {
                $modified = filemtime($file);
                $date = date('Y-m-d', $modified);
                $hour = date('Y-m-d H', $modified);

                if (isset($hourlyFiles[$hour])) {
                    $hourlyFiles[$hour]++;
                }

                if (isset($dailyFiles[$date])) {
                    $dailyFiles[$date]++;
                }
            }
        }

        // Calculer la tendance
        $trend = self::calculateFileTrend($hourlyFiles);

        return [
            'hourly' => $hourlyFiles,
            'daily' => $dailyFiles,
            'trend' => $trend,
        ];
    }

    /**
     * Calcule la tendance des fichiers
     */
    private static function calculateFileTrend(array $hourlyFiles): string
    {
        $values = array_values($hourlyFiles);
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
     * Vérifie les seuils des fichiers
     */
    private static function checkFileThresholds(array $fileStats): array
    {
        $alerts = [];

        // Vérifier la taille du répertoire storage
        if (isset($fileStats['storage']['total_size_mb']) && $fileStats['storage']['total_size_mb'] > 1000) { // Plus de 1GB
            $alerts[] = [
                'type' => 'large_storage_size',
                'message' => "Taille du répertoire storage élevée: {$fileStats['storage']['total_size_mb']}MB",
                'level' => 'warning',
                'value' => $fileStats['storage']['total_size_mb'],
                'threshold' => 1000,
            ];
        }

        // Vérifier la taille du répertoire public
        if (isset($fileStats['public']['total_size_mb']) && $fileStats['public']['total_size_mb'] > 500) { // Plus de 500MB
            $alerts[] = [
                'type' => 'large_public_size',
                'message' => "Taille du répertoire public élevée: {$fileStats['public']['total_size_mb']}MB",
                'level' => 'warning',
                'value' => $fileStats['public']['total_size_mb'],
                'threshold' => 500,
            ];
        }

        // Vérifier le nombre de fichiers dans storage
        if (isset($fileStats['storage']['total_files']) && $fileStats['storage']['total_files'] > 10000) {
            $alerts[] = [
                'type' => 'too_many_storage_files',
                'message' => "Nombre élevé de fichiers dans storage: {$fileStats['storage']['total_files']}",
                'level' => 'info',
                'value' => $fileStats['storage']['total_files'],
                'threshold' => 10000,
            ];
        }

        // Vérifier le nombre de fichiers dans public
        if (isset($fileStats['public']['total_files']) && $fileStats['public']['total_files'] > 5000) {
            $alerts[] = [
                'type' => 'too_many_public_files',
                'message' => "Nombre élevé de fichiers dans public: {$fileStats['public']['total_files']}",
                'level' => 'info',
                'value' => $fileStats['public']['total_files'],
                'threshold' => 5000,
            ];
        }

        return $alerts;
    }

    /**
     * Génère un rapport des fichiers
     */
    public static function generateFileReport(int $hours = 24): array
    {
        $fileStats = self::getFileStatistics();
        $filePermissions = self::getFilePermissions();
        $fileTrends = self::getFileTrends();
        $alerts = self::checkFileThresholds($fileStats);

        return [
            'period_hours' => $hours,
            'generated_at' => now()->toISOString(),
            'summary' => $fileStats,
            'permissions' => $filePermissions,
            'trends' => $fileTrends,
            'alerts' => $alerts,
            'recommendations' => self::generateFileRecommendations($fileStats, $alerts),
        ];
    }

    /**
     * Génère des recommandations pour les fichiers
     */
    private static function generateFileRecommendations(array $fileStats, array $alerts): array
    {
        $recommendations = [];

        // Recommandations basées sur les alertes
        foreach ($alerts as $alert) {
            switch ($alert['type']) {
                case 'large_storage_size':
                    $recommendations[] = 'Nettoyez les anciens fichiers de storage et considérez l\'archivage.';
                    break;
                case 'large_public_size':
                    $recommendations[] = 'Optimisez les fichiers publics et considérez l\'utilisation d\'un CDN.';
                    break;
                case 'too_many_storage_files':
                    $recommendations[] = 'Consolidez les fichiers de storage et supprimez les anciens fichiers inutiles.';
                    break;
                case 'too_many_public_files':
                    $recommendations[] = 'Optimisez les fichiers publics et supprimez les anciens fichiers inutiles.';
                    break;
            }
        }

        // Recommandations générales
        if (isset($fileStats['storage']['total_size_mb']) && $fileStats['storage']['total_size_mb'] > 500) {
            $recommendations[] = 'La taille du répertoire storage est importante. Mettez en place un nettoyage automatique.';
        }

        if (isset($fileStats['public']['total_size_mb']) && $fileStats['public']['total_size_mb'] > 250) {
            $recommendations[] = 'La taille du répertoire public est importante. Optimisez les images et les fichiers statiques.';
        }

        if (empty($recommendations)) {
            $recommendations[] = 'Aucun problème critique détecté. Continuez la surveillance.';
        }

        return $recommendations;
    }

    /**
     * Nettoie les anciens fichiers
     */
    public static function cleanupOldFiles(int $daysToKeep = 30): void
    {
        $storagePath = storage_path();
        $publicPath = public_path();
        $cutoffDate = now()->subDays($daysToKeep);

        $cleanedCount = 0;

        // Nettoyer les fichiers de storage
        $storageFiles = glob($storagePath . '/**/*', GLOB_BRACE);
        foreach ($storageFiles as $file) {
            if (is_file($file) && filemtime($file) < $cutoffDate->timestamp) {
                unlink($file);
                $cleanedCount++;
            }
        }

        // Nettoyer les fichiers publics (sauf les fichiers critiques)
        $publicFiles = glob($publicPath . '/**/*', GLOB_BRACE);
        $criticalFiles = ['index.php', 'favicon.ico', 'robots.txt'];

        foreach ($publicFiles as $file) {
            if (is_file($file) && !in_array(basename($file), $criticalFiles) && filemtime($file) < $cutoffDate->timestamp) {
                unlink($file);
                $cleanedCount++;
            }
        }

        Log::info("Cleaned up {$cleanedCount} old files");
    }
}
