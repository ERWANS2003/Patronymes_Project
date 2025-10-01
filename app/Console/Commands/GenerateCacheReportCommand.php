<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CacheMonitoringService;
use Illuminate\Support\Facades\Storage;

class GenerateCacheReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'monitoring:cache-report {--hours=24 : Number of hours to analyze} {--output= : Output file path}';

    /**
     * The console command description.
     */
    protected $description = 'Generate a cache monitoring report for the specified time period';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hours = $this->option('hours');
        $outputPath = $this->option('output');

        $this->info("Generating cache monitoring report for the last {$hours} hours...");

        $report = CacheMonitoringService::generateCacheReport($hours);

        if ($outputPath) {
            // Sauvegarder dans un fichier
            $jsonReport = json_encode($report, JSON_PRETTY_PRINT);
            Storage::put($outputPath, $jsonReport);
            $this->info("Report saved to: {$outputPath}");
        } else {
            // Afficher dans la console
            $this->displayReport($report);
        }

        return 0;
    }

    /**
     * Affiche le rapport dans la console
     */
    private function displayReport(array $report): void
    {
        $this->line('');
        $this->line('Cache Monitoring Report');
        $this->line('======================');
        $this->line("Period: {$report['period_hours']} hours");
        $this->line("Generated: {$report['generated_at']}");
        $this->line('');

        // Statistiques
        $this->line('Cache Statistics:');
        $this->line('-----------------');
        if (isset($report['summary']['error'])) {
            $this->line("<fg=red>Error: {$report['summary']['error']}</>");
        } else {
            $this->line("Driver: {$report['summary']['driver']}");
            $this->line("Enabled: " . ($report['summary']['enabled'] ? 'Yes' : 'No'));

            // Statistiques spécifiques au driver
            if ($report['summary']['driver'] === 'redis') {
                $this->displayRedisStats($report['summary']);
            } elseif ($report['summary']['driver'] === 'memcached') {
                $this->displayMemcachedStats($report['summary']);
            } else {
                $this->displayFileCacheStats($report['summary']);
            }
        }
        $this->line('');

        // Performances
        $this->line('Cache Performance:');
        $this->line('------------------');
        if (isset($report['performance']['error'])) {
            $this->line("<fg=red>Error: {$report['performance']['error']}</>");
        } else {
            $this->line("Write Time: {$report['performance']['write_time_ms']}ms");
            $this->line("Read Time: {$report['performance']['read_time_ms']}ms");
            $this->line("Delete Time: {$report['performance']['delete_time_ms']}ms");
            $this->line("Total Time: {$report['performance']['total_time_ms']}ms");
            $this->line("Test Successful: " . ($report['performance']['test_successful'] ? 'Yes' : 'No'));
        }
        $this->line('');

        // Clés
        $this->line('Cache Keys:');
        $this->line('-----------');
        if (isset($report['keys']['error'])) {
            $this->line("<fg=red>Error: {$report['keys']['error']}</>");
        } else {
            $this->line("Total Keys: {$report['keys']['total_keys']}");

            if (!empty($report['keys']['keys'])) {
                $this->line('Sample Keys:');
                foreach (array_slice($report['keys']['keys'], 0, 10) as $key) {
                    if (is_array($key)) {
                        $keyInfo = [];
                        foreach ($key as $k => $v) {
                            $keyInfo[] = "{$k}: {$v}";
                        }
                        $this->line("  " . implode(', ', $keyInfo));
                    } else {
                        $this->line("  {$key}");
                    }
                }
            }
        }
        $this->line('');

        // Alertes
        if (!empty($report['alerts'])) {
            $this->line('Alerts:');
            $this->line('-------');
            foreach ($report['alerts'] as $alert) {
                $color = $alert['level'] === 'critical' ? 'red' : ($alert['level'] === 'warning' ? 'yellow' : 'blue');
                $this->line("<fg={$color}>  {$alert['message']}</>");
            }
            $this->line('');
        }

        // Recommandations
        if (!empty($report['recommendations'])) {
            $this->line('Recommendations:');
            $this->line('----------------');
            foreach ($report['recommendations'] as $recommendation) {
                $this->line("  - {$recommendation}");
            }
        }
    }

    /**
     * Affiche les statistiques Redis
     */
    private function displayRedisStats(array $stats): void
    {
        $this->line('');
        $this->line('Redis Specific Statistics:');
        $this->line('--------------------------');

        if (isset($stats['redis_version'])) {
            $this->line("Redis Version: {$stats['redis_version']}");
        }
        if (isset($stats['used_memory'])) {
            $this->line("Used Memory: {$stats['used_memory']}");
        }
        if (isset($stats['used_memory_peak'])) {
            $this->line("Used Memory Peak: {$stats['used_memory_peak']}");
        }
        if (isset($stats['connected_clients'])) {
            $this->line("Connected Clients: {$stats['connected_clients']}");
        }
        if (isset($stats['total_commands_processed'])) {
            $this->line("Total Commands Processed: {$stats['total_commands_processed']}");
        }
        if (isset($stats['keyspace_hits'])) {
            $this->line("Keyspace Hits: {$stats['keyspace_hits']}");
        }
        if (isset($stats['keyspace_misses'])) {
            $this->line("Keyspace Misses: {$stats['keyspace_misses']}");
        }
        if (isset($stats['expired_keys'])) {
            $this->line("Expired Keys: {$stats['expired_keys']}");
        }
        if (isset($stats['evicted_keys'])) {
            $this->line("Evicted Keys: {$stats['evicted_keys']}");
        }
        if (isset($stats['hit_rate'])) {
            $this->line("Hit Rate: {$stats['hit_rate']}%");
        }
        if (isset($stats['miss_rate'])) {
            $this->line("Miss Rate: {$stats['miss_rate']}%");
        }
    }

    /**
     * Affiche les statistiques Memcached
     */
    private function displayMemcachedStats(array $stats): void
    {
        $this->line('');
        $this->line('Memcached Specific Statistics:');
        $this->line('------------------------------');

        if (isset($stats['version'])) {
            $this->line("Version: {$stats['version']}");
        }
        if (isset($stats['uptime'])) {
            $this->line("Uptime: {$stats['uptime']} seconds");
        }
        if (isset($stats['total_items'])) {
            $this->line("Total Items: {$stats['total_items']}");
        }
        if (isset($stats['curr_items'])) {
            $this->line("Current Items: {$stats['curr_items']}");
        }
        if (isset($stats['bytes'])) {
            $this->line("Bytes: {$stats['bytes']}");
        }
        if (isset($stats['bytes_read'])) {
            $this->line("Bytes Read: {$stats['bytes_read']}");
        }
        if (isset($stats['bytes_written'])) {
            $this->line("Bytes Written: {$stats['bytes_written']}");
        }
        if (isset($stats['get_hits'])) {
            $this->line("Get Hits: {$stats['get_hits']}");
        }
        if (isset($stats['get_misses'])) {
            $this->line("Get Misses: {$stats['get_misses']}");
        }
        if (isset($stats['hit_rate'])) {
            $this->line("Hit Rate: {$stats['hit_rate']}%");
        }
        if (isset($stats['miss_rate'])) {
            $this->line("Miss Rate: {$stats['miss_rate']}%");
        }
    }

    /**
     * Affiche les statistiques du cache de fichiers
     */
    private function displayFileCacheStats(array $stats): void
    {
        $this->line('');
        $this->line('File Cache Specific Statistics:');
        $this->line('--------------------------------');

        if (isset($stats['file_count'])) {
            $this->line("File Count: {$stats['file_count']}");
        }
        if (isset($stats['total_size_mb'])) {
            $this->line("Total Size: {$stats['total_size_mb']}MB");
        }
        if (isset($stats['cache_path'])) {
            $this->line("Cache Path: {$stats['cache_path']}");
        }
    }
}
