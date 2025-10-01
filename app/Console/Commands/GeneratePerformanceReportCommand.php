<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PerformanceMonitoringService;
use Illuminate\Support\Facades\Storage;

class GeneratePerformanceReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'monitoring:performance-report {--hours=24 : Number of hours to analyze} {--output= : Output file path}';

    /**
     * The console command description.
     */
    protected $description = 'Generate a performance monitoring report for the specified time period';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hours = $this->option('hours');
        $outputPath = $this->option('output');

        $this->info("Generating performance monitoring report for the last {$hours} hours...");

        $report = PerformanceMonitoringService::generatePerformanceReport($hours);

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
        $this->line('Performance Monitoring Report');
        $this->line('============================');
        $this->line("Period: {$report['period_hours']} hours");
        $this->line("Generated: {$report['generated_at']}");
        $this->line('');

        // Statistiques
        $this->line('Performance Statistics:');
        $this->line('------------------------');
        $this->line("Total Requests: {$report['summary']['total_requests']}");
        $this->line("Requests Today: {$report['summary']['requests_today']}");
        $this->line("Requests This Hour: {$report['summary']['requests_this_hour']}");
        $this->line("Average Response Time: {$report['summary']['avg_response_time_ms']}ms");
        $this->line("Max Response Time: {$report['summary']['max_response_time_ms']}ms");
        $this->line("Min Response Time: {$report['summary']['min_response_time_ms']}ms");
        $this->line("P95 Response Time: {$report['summary']['p95_response_time_ms']}ms");
        $this->line("P99 Response Time: {$report['summary']['p99_response_time_ms']}ms");
        $this->line('');

        // Temps de réponse
        $this->line('Response Time Trends:');
        $this->line('---------------------');
        $this->line("Trend: {$report['response_times']['trend']}");
        $this->line('');

        // Utilisation de la mémoire
        $this->line('Memory Usage:');
        $this->line('-------------');
        $this->line("Current Memory: {$report['memory_usage']['current_memory_mb']}MB");
        $this->line("Peak Memory: {$report['memory_usage']['peak_memory_mb']}MB");
        $this->line("Memory Limit: {$report['memory_usage']['memory_limit_mb']}MB");
        $this->line("Memory Usage: {$report['memory_usage']['memory_usage_percentage']}%");
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

        // Temps de réponse par heure
        if (!empty($report['response_times']['hourly'])) {
            $this->line('Response Times by Hour:');
            $this->line('------------------------');
            foreach ($report['response_times']['hourly'] as $hour => $time) {
                $this->line("  {$hour}: {$time}ms");
            }
            $this->line('');
        }

        // Temps de réponse par jour
        if (!empty($report['response_times']['daily'])) {
            $this->line('Response Times by Day:');
            $this->line('----------------------');
            foreach ($report['response_times']['daily'] as $day => $time) {
                $this->line("  {$day}: {$time}ms");
            }
        }
    }
}
