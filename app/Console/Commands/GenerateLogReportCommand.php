<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\LogMonitoringService;
use Illuminate\Support\Facades\Storage;

class GenerateLogReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'monitoring:log-report {--hours=24 : Number of hours to analyze} {--output= : Output file path}';

    /**
     * The console command description.
     */
    protected $description = 'Generate a log monitoring report for the specified time period';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hours = $this->option('hours');
        $outputPath = $this->option('output');

        $this->info("Generating log monitoring report for the last {$hours} hours...");

        $report = LogMonitoringService::generateLogReport($hours);

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
        $this->line('Log Monitoring Report');
        $this->line('====================');
        $this->line("Period: {$report['period_hours']} hours");
        $this->line("Generated: {$report['generated_at']}");
        $this->line('');

        // Statistiques
        $this->line('Log Statistics:');
        $this->line('---------------');
        $this->line("Total Logs: {$report['summary']['total_logs']}");
        $this->line("Logs Today: {$report['summary']['logs_today']}");
        $this->line("Logs This Hour: {$report['summary']['logs_this_hour']}");
        $this->line("Total Size: {$report['summary']['total_size_mb']}MB");
        $this->line("Oldest Log: {$report['summary']['oldest_log']}");
        $this->line("Newest Log: {$report['summary']['newest_log']}");
        $this->line('');

        // Fichiers de logs
        if (!empty($report['summary']['log_files'])) {
            $this->line('Log Files:');
            $this->line('----------');
            foreach ($report['summary']['log_files'] as $file) {
                $this->line("  {$file['name']}: {$file['size_mb']}MB (Modified: {$file['modified']})");
            }
            $this->line('');
        }

        // Tendances
        $this->line('Log Trends:');
        $this->line('-----------');
        $this->line("Trend: {$report['trends']['trend']}");
        $this->line('');

        // Logs récents
        if (!empty($report['recent_logs'])) {
            $this->line('Recent Logs:');
            $this->line('------------');
            foreach ($report['recent_logs'] as $log) {
                $color = $log['level'] === 'critical' ? 'red' : ($log['level'] === 'error' ? 'red' : ($log['level'] === 'warning' ? 'yellow' : 'blue'));
                $this->line("<fg={$color}>  [{$log['timestamp']}] {$log['level']}: {$log['message']}</>");
                $this->line("    File: {$log['file']}");
            }
            $this->line('');
        }

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
            $this->line('Logs by Hour:');
            $this->line('-------------');
            foreach ($report['trends']['hourly'] as $hour => $count) {
                $this->line("  {$hour}: {$count} logs");
            }
            $this->line('');
        }

        // Tendances par jour
        if (!empty($report['trends']['daily'])) {
            $this->line('Logs by Day:');
            $this->line('------------');
            foreach ($report['trends']['daily'] as $day => $count) {
                $this->line("  {$day}: {$count} logs");
            }
        }
    }
}
