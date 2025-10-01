<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ApplicationMonitoringService;
use Illuminate\Support\Facades\Storage;

class GenerateApplicationReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'monitoring:application-report {--hours=24 : Number of hours to analyze} {--output= : Output file path}';

    /**
     * The console command description.
     */
    protected $description = 'Generate an application monitoring report for the specified time period';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hours = $this->option('hours');
        $outputPath = $this->option('output');

        $this->info("Generating application monitoring report for the last {$hours} hours...");

        $report = ApplicationMonitoringService::generateApplicationReport($hours);

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
        $this->line('Application Monitoring Report');
        $this->line('============================');
        $this->line("Period: {$report['period_hours']} hours");
        $this->line("Generated: {$report['generated_at']}");
        $this->line('');

        // Statistiques
        $this->line('Application Statistics:');
        $this->line('------------------------');
        $this->line("App Name: {$report['summary']['app_name']}");
        $this->line("App Version: {$report['summary']['app_version']}");
        $this->line("App Environment: {$report['summary']['app_environment']}");
        $this->line("App Debug: " . ($report['summary']['app_debug'] ? 'Yes' : 'No'));
        $this->line("App Timezone: {$report['summary']['app_timezone']}");
        $this->line("App Locale: {$report['summary']['app_locale']}");
        $this->line("App URL: {$report['summary']['app_url']}");
        $this->line("App Key: {$report['summary']['app_key']}");
        $this->line("App Cipher: {$report['summary']['app_cipher']}");
        $this->line("App Providers: {$report['summary']['app_providers']}");
        $this->line("App Aliases: {$report['summary']['app_aliases']}");
        $this->line("App Middleware: {$report['summary']['app_middleware']}");
        $this->line("App Guards: {$report['summary']['app_guards']}");
        $this->line("App Providers Loaded: {$report['summary']['app_providers_loaded']}");
        $this->line("App Services Registered: {$report['summary']['app_services_registered']}");
        $this->line('');

        // Performances
        $this->line('Application Performance:');
        $this->line('------------------------');
        if (isset($report['performance']['error'])) {
            $this->line("<fg=red>Error: {$report['performance']['error']}</>");
        } else {
            $this->line("Database Time: {$report['performance']['database_time_ms']}ms");
            $this->line("Cache Time: {$report['performance']['cache_time_ms']}ms");
            $this->line("Session Time: {$report['performance']['session_time_ms']}ms");
            $this->line("Total Time: {$report['performance']['total_time_ms']}ms");
            $this->line("Test Successful: " . ($report['performance']['test_successful'] ? 'Yes' : 'No'));
        }
        $this->line('');

        // Tendances
        $this->line('Application Trends:');
        $this->line('-------------------');
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
            $this->line('Application Performance by Hour:');
            $this->line('--------------------------------');
            foreach ($report['trends']['hourly'] as $hour => $performance) {
                $this->line("  {$hour}:");
                $this->line("    Requests: {$performance['requests']}");
                $this->line("    Response Time: {$performance['response_time_ms']}ms");
                $this->line("    Memory Usage: {$performance['memory_usage_mb']}MB");
                $this->line("    CPU Usage: {$performance['cpu_usage_percentage']}%");
            }
            $this->line('');
        }

        // Tendances par jour
        if (!empty($report['trends']['daily'])) {
            $this->line('Application Performance by Day:');
            $this->line('-------------------------------');
            foreach ($report['trends']['daily'] as $day => $performance) {
                $this->line("  {$day}:");
                $this->line("    Requests: {$performance['requests']}");
                $this->line("    Response Time: {$performance['response_time_ms']}ms");
                $this->line("    Memory Usage: {$performance['memory_usage_mb']}MB");
                $this->line("    CPU Usage: {$performance['cpu_usage_percentage']}%");
            }
        }
    }
}
