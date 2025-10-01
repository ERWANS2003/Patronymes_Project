<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SystemMonitoringService;
use Illuminate\Support\Facades\Storage;

class GenerateSystemReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'monitoring:system-report {--hours=24 : Number of hours to analyze} {--output= : Output file path}';

    /**
     * The console command description.
     */
    protected $description = 'Generate a system monitoring report for the specified time period';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hours = $this->option('hours');
        $outputPath = $this->option('output');

        $this->info("Generating system monitoring report for the last {$hours} hours...");

        $report = SystemMonitoringService::generateSystemReport($hours);

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
        $this->line('System Monitoring Report');
        $this->line('========================');
        $this->line("Period: {$report['period_hours']} hours");
        $this->line("Generated: {$report['generated_at']}");
        $this->line('');

        // Statistiques
        $this->line('System Statistics:');
        $this->line('------------------');

        // Système d'exploitation
        $this->line('Operating System:');
        $this->line("  Name: {$report['summary']['os']['name']}");
        $this->line("  Version: {$report['summary']['os']['version']}");
        $this->line("  Architecture: {$report['summary']['os']['architecture']}");
        $this->line("  Hostname: {$report['summary']['os']['hostname']}");
        $this->line('');

        // Version PHP
        $this->line('PHP Version:');
        $this->line("  Version: {$report['summary']['php_version']['version']}");
        $this->line("  SAPI: {$report['summary']['php_version']['sapi']}");
        $this->line("  Memory Limit: {$report['summary']['php_version']['memory_limit']}");
        $this->line("  Max Execution Time: {$report['summary']['php_version']['max_execution_time']}");
        $this->line("  Upload Max Filesize: {$report['summary']['php_version']['upload_max_filesize']}");
        $this->line("  Post Max Size: {$report['summary']['php_version']['post_max_size']}");
        $this->line('');

        // Version Laravel
        $this->line('Laravel Version:');
        $this->line("  Version: {$report['summary']['laravel_version']['version']}");
        $this->line("  Environment: {$report['summary']['laravel_version']['environment']}");
        $this->line("  Debug: " . ($report['summary']['laravel_version']['debug'] ? 'Yes' : 'No'));
        $this->line("  Timezone: {$report['summary']['laravel_version']['timezone']}");
        $this->line("  Locale: {$report['summary']['laravel_version']['locale']}");
        $this->line('');

        // Logiciel serveur
        $this->line('Server Software:');
        $this->line("  Server: {$report['summary']['server_software']['server']}");
        $this->line("  Protocol: {$report['summary']['server_software']['protocol']}");
        $this->line("  Port: {$report['summary']['server_software']['port']}");
        $this->line("  Document Root: {$report['summary']['server_software']['document_root']}");
        $this->line('');

        // Temps de fonctionnement
        $this->line('System Uptime:');
        $this->line("  Uptime: {$report['summary']['uptime']['uptime']}");
        $this->line("  Uptime Seconds: {$report['summary']['uptime']['uptime_seconds']}");
        $this->line('');

        // Charge moyenne
        $this->line('Load Average:');
        $this->line("  1 Minute: {$report['summary']['load_average']['1_minute']}");
        $this->line("  5 Minutes: {$report['summary']['load_average']['5_minutes']}");
        $this->line("  15 Minutes: {$report['summary']['load_average']['15_minutes']}");
        $this->line('');

        // Utilisation de la mémoire
        $this->line('Memory Usage:');
        $this->line("  Current: {$report['summary']['memory_usage']['current_mb']}MB");
        $this->line("  Peak: {$report['summary']['memory_usage']['peak_mb']}MB");
        $this->line("  Limit: {$report['summary']['memory_usage']['limit_mb']}MB");
        $this->line("  Usage: {$report['summary']['memory_usage']['usage_percentage']}%");
        $this->line('');

        // Utilisation du disque
        $this->line('Disk Usage:');
        $this->line("  Total: {$report['summary']['disk_usage']['total_gb']}GB");
        $this->line("  Used: {$report['summary']['disk_usage']['used_gb']}GB");
        $this->line("  Free: {$report['summary']['disk_usage']['free_gb']}GB");
        $this->line("  Usage: {$report['summary']['disk_usage']['usage_percentage']}%");
        $this->line('');

        // Utilisation du CPU
        $this->line('CPU Usage:');
        $this->line("  CPU Count: {$report['summary']['cpu_usage']['cpu_count']}");
        $this->line("  CPU Usage: {$report['summary']['cpu_usage']['cpu_usage_percentage']}%");
        $this->line('');

        // Performances
        $this->line('System Performance:');
        $this->line('--------------------');
        if (isset($report['performance']['error'])) {
            $this->line("<fg=red>Error: {$report['performance']['error']}</>");
        } else {
            $this->line("Calculation Time: {$report['performance']['calculation_time_ms']}ms");
            $this->line("Total Time: {$report['performance']['total_time_ms']}ms");
            $this->line("Test Successful: " . ($report['performance']['test_successful'] ? 'Yes' : 'No'));
        }
        $this->line('');

        // Tendances
        $this->line('System Trends:');
        $this->line('--------------');
        $this->line("Trend: {$report['trends']['trend']}");
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
            $this->line('');
        }

        // Tendances par heure
        if (!empty($report['trends']['hourly'])) {
            $this->line('System Performance by Hour:');
            $this->line('---------------------------');
            foreach ($report['trends']['hourly'] as $hour => $performance) {
                $this->line("  {$hour}:");
                $this->line("    CPU Usage: {$performance['cpu_usage_percentage']}%");
                $this->line("    Memory Usage: {$performance['memory_usage_percentage']}%");
                $this->line("    Disk Usage: {$performance['disk_usage_percentage']}%");
                $this->line("    Load Average: {$performance['load_average']}");
            }
            $this->line('');
        }

        // Tendances par jour
        if (!empty($report['trends']['daily'])) {
            $this->line('System Performance by Day:');
            $this->line('--------------------------');
            foreach ($report['trends']['daily'] as $day => $performance) {
                $this->line("  {$day}:");
                $this->line("    CPU Usage: {$performance['cpu_usage_percentage']}%");
                $this->line("    Memory Usage: {$performance['memory_usage_percentage']}%");
                $this->line("    Disk Usage: {$performance['disk_usage_percentage']}%");
                $this->line("    Load Average: {$performance['load_average']}");
            }
        }
    }
}
