<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ErrorMonitoringService;
use Illuminate\Support\Facades\Storage;

class GenerateErrorReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'monitoring:error-report {--hours=24 : Number of hours to analyze} {--output= : Output file path}';

    /**
     * The console command description.
     */
    protected $description = 'Generate an error monitoring report for the specified time period';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hours = $this->option('hours');
        $outputPath = $this->option('output');

        $this->info("Generating error monitoring report for the last {$hours} hours...");

        $report = ErrorMonitoringService::generateErrorReport($hours);

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
        $this->line('Error Monitoring Report');
        $this->line('======================');
        $this->line("Period: {$report['period_hours']} hours");
        $this->line("Generated: {$report['generated_at']}");
        $this->line('');

        // Statistiques
        $this->line('Statistics:');
        $this->line('-----------');
        $this->line("Total Errors: {$report['summary']['total_errors']}");
        $this->line("Errors Today: {$report['summary']['errors_today']}");
        $this->line("Errors This Hour: {$report['summary']['errors_this_hour']}");
        $this->line("Error Rate: {$report['summary']['error_rate']}%");
        $this->line('');

        // Erreurs les plus communes
        if (!empty($report['summary']['most_common_errors'])) {
            $this->line('Most Common Errors:');
            $this->line('-------------------');
            foreach ($report['summary']['most_common_errors'] as $error => $count) {
                $this->line("  {$error}: {$count}");
            }
            $this->line('');
        }

        // Tendances
        $this->line('Trends:');
        $this->line('-------');
        $this->line("Error Trend: {$report['trends']['trend']}");
        $this->line('');

        // Alertes
        if (!empty($report['alerts'])) {
            $this->line('Alerts:');
            $this->line('-------');
            foreach ($report['alerts'] as $alert) {
                $color = $alert['level'] === 'critical' ? 'red' : 'yellow';
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

        // Erreurs récentes
        if (!empty($report['recent_errors'])) {
            $this->line('Recent Errors:');
            $this->line('--------------');
            foreach ($report['recent_errors'] as $error) {
                $this->line("  [{$error['timestamp']}] {$error['level']}: {$error['message']}");
            }
        }
    }
}
