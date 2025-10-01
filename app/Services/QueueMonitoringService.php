<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\DB;

class QueueMonitoringService
{
    /**
     * Surveille les files d'attente
     */
    public static function monitorQueues(): array
    {
        $queueStats = self::getQueueStatistics();
        $queueJobs = self::getQueueJobs();
        $queuePerformance = self::getQueuePerformance();
        $alerts = self::checkQueueThresholds($queueStats);

        return [
            'timestamp' => now()->toISOString(),
            'statistics' => $queueStats,
            'jobs' => $queueJobs,
            'performance' => $queuePerformance,
            'alerts' => $alerts,
        ];
    }

    /**
     * Obtient les statistiques des files d'attente
     */
    private static function getQueueStatistics(): array
    {
        $driver = config('queue.default');
        $stats = [
            'driver' => $driver,
            'enabled' => true,
        ];

        try {
            if ($driver === 'redis') {
                $stats = array_merge($stats, self::getRedisQueueStatistics());
            } elseif ($driver === 'database') {
                $stats = array_merge($stats, self::getDatabaseQueueStatistics());
            } else {
                $stats = array_merge($stats, self::getSyncQueueStatistics());
            }
        } catch (\Exception $e) {
            Log::error('Failed to get queue statistics', ['error' => $e->getMessage()]);
            $stats['error'] = 'Queue statistics unavailable';
        }

        return $stats;
    }

    /**
     * Obtient les statistiques Redis pour les files d'attente
     */
    private static function getRedisQueueStatistics(): array
    {
        try {
            $redis = \Illuminate\Support\Facades\Redis::connection();
            $queues = config('queue.connections.redis.queue', 'default');

            $stats = [
                'queues' => [],
            ];

            if (is_string($queues)) {
                $queues = [$queues];
            }

            foreach ($queues as $queue) {
                $queueKey = "queues:{$queue}";
                $delayedKey = "queues:{$queue}:delayed";
                $reservedKey = "queues:{$queue}:reserved";
                $failedKey = "queues:{$queue}:failed";

                $stats['queues'][$queue] = [
                    'pending' => $redis->llen($queueKey),
                    'delayed' => $redis->zcard($delayedKey),
                    'reserved' => $redis->zcard($reservedKey),
                    'failed' => $redis->llen($failedKey),
                ];
            }

            return $stats;
        } catch (\Exception $e) {
            Log::error('Failed to get Redis queue statistics', ['error' => $e->getMessage()]);
            return ['error' => 'Redis queue statistics unavailable'];
        }
    }

    /**
     * Obtient les statistiques de base de données pour les files d'attente
     */
    private static function getDatabaseQueueStatistics(): array
    {
        try {
            $stats = [
                'queues' => [],
            ];

            // Statistiques des jobs en attente
            $pendingJobs = DB::table('jobs')
                ->select('queue', DB::raw('count(*) as count'))
                ->groupBy('queue')
                ->get();

            foreach ($pendingJobs as $job) {
                $stats['queues'][$job->queue] = [
                    'pending' => $job->count,
                    'delayed' => 0,
                    'reserved' => 0,
                    'failed' => 0,
                ];
            }

            // Statistiques des jobs échoués
            $failedJobs = DB::table('failed_jobs')
                ->select('queue', DB::raw('count(*) as count'))
                ->groupBy('queue')
                ->get();

            foreach ($failedJobs as $job) {
                if (!isset($stats['queues'][$job->queue])) {
                    $stats['queues'][$job->queue] = [
                        'pending' => 0,
                        'delayed' => 0,
                        'reserved' => 0,
                        'failed' => 0,
                    ];
                }
                $stats['queues'][$job->queue]['failed'] = $job->count;
            }

            return $stats;
        } catch (\Exception $e) {
            Log::error('Failed to get database queue statistics', ['error' => $e->getMessage()]);
            return ['error' => 'Database queue statistics unavailable'];
        }
    }

    /**
     * Obtient les statistiques des files d'attente synchrones
     */
    private static function getSyncQueueStatistics(): array
    {
        return [
            'queues' => [
                'default' => [
                    'pending' => 0,
                    'delayed' => 0,
                    'reserved' => 0,
                    'failed' => 0,
                ],
            ],
            'note' => 'Sync driver processes jobs immediately',
        ];
    }

    /**
     * Obtient les jobs des files d'attente
     */
    private static function getQueueJobs(): array
    {
        $driver = config('queue.default');
        $jobs = [];

        try {
            if ($driver === 'redis') {
                $jobs = self::getRedisQueueJobs();
            } elseif ($driver === 'database') {
                $jobs = self::getDatabaseQueueJobs();
            } else {
                $jobs = ['note' => 'No jobs available for sync driver'];
            }
        } catch (\Exception $e) {
            Log::error('Failed to get queue jobs', ['error' => $e->getMessage()]);
            $jobs = ['error' => 'Queue jobs unavailable'];
        }

        return $jobs;
    }

    /**
     * Obtient les jobs Redis
     */
    private static function getRedisQueueJobs(): array
    {
        try {
            $redis = \Illuminate\Support\Facades\Redis::connection();
            $queues = config('queue.connections.redis.queue', 'default');

            if (is_string($queues)) {
                $queues = [$queues];
            }

            $jobs = [];

            foreach ($queues as $queue) {
                $queueKey = "queues:{$queue}";
                $delayedKey = "queues:{$queue}:delayed";
                $reservedKey = "queues:{$queue}:reserved";
                $failedKey = "queues:{$queue}:failed";

                // Jobs en attente
                $pendingJobs = $redis->lrange($queueKey, 0, 9);
                foreach ($pendingJobs as $job) {
                    $jobs[] = [
                        'queue' => $queue,
                        'status' => 'pending',
                        'payload' => json_decode($job, true),
                    ];
                }

                // Jobs différés
                $delayedJobs = $redis->zrange($delayedKey, 0, 9, 'WITHSCORES');
                foreach ($delayedJobs as $job => $score) {
                    $jobs[] = [
                        'queue' => $queue,
                        'status' => 'delayed',
                        'payload' => json_decode($job, true),
                        'delay_until' => date('Y-m-d H:i:s', $score),
                    ];
                }

                // Jobs réservés
                $reservedJobs = $redis->zrange($reservedKey, 0, 9, 'WITHSCORES');
                foreach ($reservedJobs as $job => $score) {
                    $jobs[] = [
                        'queue' => $queue,
                        'status' => 'reserved',
                        'payload' => json_decode($job, true),
                        'reserved_at' => date('Y-m-d H:i:s', $score),
                    ];
                }

                // Jobs échoués
                $failedJobs = $redis->lrange($failedKey, 0, 9);
                foreach ($failedJobs as $job) {
                    $jobs[] = [
                        'queue' => $queue,
                        'status' => 'failed',
                        'payload' => json_decode($job, true),
                    ];
                }
            }

            return array_slice($jobs, 0, 50); // Limiter à 50 jobs
        } catch (\Exception $e) {
            Log::error('Failed to get Redis queue jobs', ['error' => $e->getMessage()]);
            return ['error' => 'Redis queue jobs unavailable'];
        }
    }

    /**
     * Obtient les jobs de base de données
     */
    private static function getDatabaseQueueJobs(): array
    {
        try {
            $jobs = [];

            // Jobs en attente
            $pendingJobs = DB::table('jobs')
                ->orderBy('id', 'desc')
                ->limit(20)
                ->get();

            foreach ($pendingJobs as $job) {
                $jobs[] = [
                    'id' => $job->id,
                    'queue' => $job->queue,
                    'status' => 'pending',
                    'payload' => json_decode($job->payload, true),
                    'attempts' => $job->attempts,
                    'created_at' => $job->created_at,
                ];
            }

            // Jobs échoués
            $failedJobs = DB::table('failed_jobs')
                ->orderBy('id', 'desc')
                ->limit(20)
                ->get();

            foreach ($failedJobs as $job) {
                $jobs[] = [
                    'id' => $job->id,
                    'queue' => $job->queue,
                    'status' => 'failed',
                    'payload' => json_decode($job->payload, true),
                    'exception' => $job->exception,
                    'failed_at' => $job->failed_at,
                ];
            }

            return $jobs;
        } catch (\Exception $e) {
            Log::error('Failed to get database queue jobs', ['error' => $e->getMessage()]);
            return ['error' => 'Database queue jobs unavailable'];
        }
    }

    /**
     * Obtient les performances des files d'attente
     */
    private static function getQueuePerformance(): array
    {
        $startTime = microtime(true);

        try {
            // Test de performance des files d'attente
            $testJob = new \App\Jobs\TestQueueJob();

            // Test d'ajout à la file d'attente
            $addStart = microtime(true);
            Queue::push($testJob);
            $addTime = microtime(true) - $addStart;

            $totalTime = microtime(true) - $startTime;

            return [
                'add_time_ms' => round($addTime * 1000, 3),
                'total_time_ms' => round($totalTime * 1000, 3),
                'test_successful' => true,
            ];
        } catch (\Exception $e) {
            Log::error('Queue performance test failed', ['error' => $e->getMessage()]);
            return [
                'error' => 'Queue performance test failed',
                'test_successful' => false,
            ];
        }
    }

    /**
     * Vérifie les seuils des files d'attente
     */
    private static function checkQueueThresholds(array $queueStats): array
    {
        $alerts = [];

        if (isset($queueStats['error'])) {
            $alerts[] = [
                'type' => 'queue_error',
                'message' => 'Erreur de file d\'attente: ' . $queueStats['error'],
                'level' => 'critical',
                'value' => $queueStats['error'],
                'threshold' => 'none',
            ];
            return $alerts;
        }

        // Vérifier le nombre de jobs en attente
        foreach ($queueStats['queues'] as $queue => $stats) {
            if ($stats['pending'] > 1000) {
                $alerts[] = [
                    'type' => 'high_pending_jobs',
                    'message' => "Nombre élevé de jobs en attente dans la file '{$queue}': {$stats['pending']}",
                    'level' => 'warning',
                    'value' => $stats['pending'],
                    'threshold' => 1000,
                ];
            }

            if ($stats['failed'] > 100) {
                $alerts[] = [
                    'type' => 'high_failed_jobs',
                    'message' => "Nombre élevé de jobs échoués dans la file '{$queue}': {$stats['failed']}",
                    'level' => 'warning',
                    'value' => $stats['failed'],
                    'threshold' => 100,
                ];
            }

            if ($stats['delayed'] > 500) {
                $alerts[] = [
                    'type' => 'high_delayed_jobs',
                    'message' => "Nombre élevé de jobs différés dans la file '{$queue}': {$stats['delayed']}",
                    'level' => 'info',
                    'value' => $stats['delayed'],
                    'threshold' => 500,
                ];
            }
        }

        return $alerts;
    }

    /**
     * Génère un rapport des files d'attente
     */
    public static function generateQueueReport(int $hours = 24): array
    {
        $queueStats = self::getQueueStatistics();
        $queueJobs = self::getQueueJobs();
        $queuePerformance = self::getQueuePerformance();
        $alerts = self::checkQueueThresholds($queueStats);

        return [
            'period_hours' => $hours,
            'generated_at' => now()->toISOString(),
            'summary' => $queueStats,
            'jobs' => $queueJobs,
            'performance' => $queuePerformance,
            'alerts' => $alerts,
            'recommendations' => self::generateQueueRecommendations($queueStats, $alerts),
        ];
    }

    /**
     * Génère des recommandations pour les files d'attente
     */
    private static function generateQueueRecommendations(array $queueStats, array $alerts): array
    {
        $recommendations = [];

        if (isset($queueStats['error'])) {
            $recommendations[] = 'Vérifiez la configuration des files d\'attente et les logs d\'erreur.';
            return $recommendations;
        }

        // Recommandations basées sur les alertes
        foreach ($alerts as $alert) {
            switch ($alert['type']) {
                case 'high_pending_jobs':
                    $recommendations[] = 'Augmentez le nombre de workers ou optimisez les jobs.';
                    break;
                case 'high_failed_jobs':
                    $recommendations[] = 'Examinez les jobs échoués et corrigez les erreurs.';
                    break;
                case 'high_delayed_jobs':
                    $recommendations[] = 'Vérifiez la logique de différation des jobs.';
                    break;
            }
        }

        // Recommandations générales
        if ($queueStats['driver'] === 'sync') {
            $recommendations[] = 'Considérez l\'utilisation d\'un driver de file d\'attente asynchrone pour de meilleures performances.';
        }

        if (empty($recommendations)) {
            $recommendations[] = 'Aucun problème critique détecté. Continuez la surveillance.';
        }

        return $recommendations;
    }

    /**
     * Nettoie les jobs échoués
     */
    public static function cleanupFailedJobs(): void
    {
        try {
            if (config('queue.default') === 'database') {
                DB::table('failed_jobs')->delete();
                Log::info('Failed jobs cleaned up successfully');
            } else {
                Log::info('Failed jobs cleanup not available for this queue driver');
            }
        } catch (\Exception $e) {
            Log::error('Failed to cleanup failed jobs', ['error' => $e->getMessage()]);
        }
    }
}
