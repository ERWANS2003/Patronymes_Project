<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\QueueMonitoringService;
use Illuminate\Support\Facades\Storage;

class GenerateQueueReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'monitoring:queue-report {--hours=24 : Number of hours to analyze} {--output= : Output file path}';

    /**
     * The console command description.
     */
    protected $description = 'Generate a queue monitoring report for the specified time period';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hours = $this->option('hours');
        $outputPath = $this->option('output');

        $this->info("Generating queue monitoring report for the last {$hours} hours...");

        $report = QueueMonitoringService::generateQueueReport($hours);

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
        $this->line('Queue Monitoring Report');
        $this->line('======================');
        $this->line("Period: {$report['period_hours']} hours");
        $this->line("Generated: {$report['generated_at']}");
        $this->line('');

        // Statistiques
        $this->line('Queue Statistics:');
        $this->line('-----------------');
        if (isset($report['summary']['error'])) {
            $this->line("<fg=red>Error: {$report['summary']['error']}</>");
        } else {
            $this->line("Driver: {$report['summary']['driver']}");
            $this->line("Enabled: " . ($report['summary']['enabled'] ? 'Yes' : 'No'));

            // Statistiques des files d'attente
            if (!empty($report['summary']['queues'])) {
                $this->line('');
                $this->line('Queue Details:');
                $this->line('--------------');
                foreach ($report['summary']['queues'] as $queue => $stats) {
                    $this->line("Queue: {$queue}");
                    $this->line("  Pending: {$stats['pending']}");
                    $this->line("  Delayed: {$stats['delayed']}");
                    $this->line("  Reserved: {$stats['reserved']}");
                    $this->line("  Failed: {$stats['failed']}");
                    $this->line('');
                }
            }
        }
        $this->line('');

        // Performances
        $this->line('Queue Performance:');
        $this->line('------------------');
        if (isset($report['performance']['error'])) {
            $this->line("<fg=red>Error: {$report['performance']['error']}</>");
        } else {
            $this->line("Add Time: {$report['performance']['add_time_ms']}ms");
            $this->line("Total Time: {$report['performance']['total_time_ms']}ms");
            $this->line("Test Successful: " . ($report['performance']['test_successful'] ? 'Yes' : 'No'));
        }
        $this->line('');

        // Jobs
        $this->line('Queue Jobs:');
        $this->line('-----------');
        if (isset($report['jobs']['error'])) {
            $this->line("<fg=red>Error: {$report['jobs']['error']}</>");
        } elseif (isset($report['jobs']['note'])) {
            $this->line($report['jobs']['note']);
        } else {
            $this->line("Total Jobs: " . count($report['jobs']));
            $this->line('');

            foreach (array_slice($report['jobs'], 0, 10) as $job) {
                $this->line("Job Details:");
                $this->line("  Queue: {$job['queue']}");
                $this->line("  Status: {$job['status']}");
                if (isset($job['id'])) {
                    $this->line("  ID: {$job['id']}");
                }
                if (isset($job['attempts'])) {
                    $this->line("  Attempts: {$job['attempts']}");
                }
                if (isset($job['created_at'])) {
                    $this->line("  Created: {$job['created_at']}");
                }
                if (isset($job['failed_at'])) {
                    $this->line("  Failed: {$job['failed_at']}");
                }
                if (isset($job['delay_until'])) {
                    $this->line("  Delay Until: {$job['delay_until']}");
                }
                if (isset($job['reserved_at'])) {
                    $this->line("  Reserved: {$job['reserved_at']}");
                }
                $this->line('  ---');
            }

            if (count($report['jobs']) > 10) {
                $this->line("  ... and " . (count($report['jobs']) - 10) . " more jobs");
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
}
