<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\EmailMonitoringService;
use Illuminate\Support\Facades\Storage;

class GenerateEmailReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'monitoring:email-report {--hours=24 : Number of hours to analyze} {--output= : Output file path}';

    /**
     * The console command description.
     */
    protected $description = 'Generate an email monitoring report for the specified time period';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hours = $this->option('hours');
        $outputPath = $this->option('output');

        $this->info("Generating email monitoring report for the last {$hours} hours...");

        $report = EmailMonitoringService::generateEmailReport($hours);

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
        $this->line('Email Monitoring Report');
        $this->line('======================');
        $this->line("Period: {$report['period_hours']} hours");
        $this->line("Generated: {$report['generated_at']}");
        $this->line('');

        // Statistiques
        $this->line('Email Statistics:');
        $this->line('-----------------');
        if (isset($report['summary']['error'])) {
            $this->line("<fg=red>Error: {$report['summary']['error']}</>");
        } else {
            $this->line("Driver: {$report['summary']['driver']}");
            $this->line("Enabled: " . ($report['summary']['enabled'] ? 'Yes' : 'No'));

            // Statistiques spécifiques au driver
            if ($report['summary']['driver'] === 'smtp') {
                $this->displaySmtpStats($report['summary']);
            } elseif ($report['summary']['driver'] === 'mailgun') {
                $this->displayMailgunStats($report['summary']);
            } elseif ($report['summary']['driver'] === 'ses') {
                $this->displaySesStats($report['summary']);
            } else {
                $this->displayLogStats($report['summary']);
            }
        }
        $this->line('');

        // Performances
        $this->line('Email Performance:');
        $this->line('------------------');
        if (isset($report['performance']['error'])) {
            $this->line("<fg=red>Error: {$report['performance']['error']}</>");
        } else {
            $this->line("Send Time: {$report['performance']['send_time_ms']}ms");
            $this->line("Total Time: {$report['performance']['total_time_ms']}ms");
            $this->line("Test Successful: " . ($report['performance']['test_successful'] ? 'Yes' : 'No'));
        }
        $this->line('');

        // Tendances
        $this->line('Email Trends:');
        $this->line('-------------');
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
            $this->line('Emails by Hour:');
            $this->line('---------------');
            foreach ($report['trends']['hourly'] as $hour => $count) {
                $this->line("  {$hour}: {$count} emails");
            }
            $this->line('');
        }

        // Tendances par jour
        if (!empty($report['trends']['daily'])) {
            $this->line('Emails by Day:');
            $this->line('--------------');
            foreach ($report['trends']['daily'] as $day => $count) {
                $this->line("  {$day}: {$count} emails");
            }
        }
    }

    /**
     * Affiche les statistiques SMTP
     */
    private function displaySmtpStats(array $stats): void
    {
        $this->line('');
        $this->line('SMTP Specific Statistics:');
        $this->line('-------------------------');

        if (isset($stats['host'])) {
            $this->line("Host: {$stats['host']}");
        }
        if (isset($stats['port'])) {
            $this->line("Port: {$stats['port']}");
        }
        if (isset($stats['encryption'])) {
            $this->line("Encryption: {$stats['encryption']}");
        }
        if (isset($stats['username'])) {
            $this->line("Username: {$stats['username']}");
        }
        if (isset($stats['timeout'])) {
            $this->line("Timeout: {$stats['timeout']} seconds");
        }
        if (isset($stats['auth_mode'])) {
            $this->line("Auth Mode: {$stats['auth_mode']}");
        }
    }

    /**
     * Affiche les statistiques Mailgun
     */
    private function displayMailgunStats(array $stats): void
    {
        $this->line('');
        $this->line('Mailgun Specific Statistics:');
        $this->line('----------------------------');

        if (isset($stats['domain'])) {
            $this->line("Domain: {$stats['domain']}");
        }
        if (isset($stats['secret'])) {
            $this->line("Secret: {$stats['secret']}");
        }
        if (isset($stats['endpoint'])) {
            $this->line("Endpoint: {$stats['endpoint']}");
        }
        if (isset($stats['timeout'])) {
            $this->line("Timeout: {$stats['timeout']} seconds");
        }
    }

    /**
     * Affiche les statistiques SES
     */
    private function displaySesStats(array $stats): void
    {
        $this->line('');
        $this->line('SES Specific Statistics:');
        $this->line('------------------------');

        if (isset($stats['key'])) {
            $this->line("Key: {$stats['key']}");
        }
        if (isset($stats['secret'])) {
            $this->line("Secret: {$stats['secret']}");
        }
        if (isset($stats['region'])) {
            $this->line("Region: {$stats['region']}");
        }
        if (isset($stats['timeout'])) {
            $this->line("Timeout: {$stats['timeout']} seconds");
        }
    }

    /**
     * Affiche les statistiques de log
     */
    private function displayLogStats(array $stats): void
    {
        $this->line('');
        $this->line('Log Specific Statistics:');
        $this->line('------------------------');

        if (isset($stats['total_emails'])) {
            $this->line("Total Emails: {$stats['total_emails']}");
        }
        if (isset($stats['emails_today'])) {
            $this->line("Emails Today: {$stats['emails_today']}");
        }
        if (isset($stats['emails_this_hour'])) {
            $this->line("Emails This Hour: {$stats['emails_this_hour']}");
        }
        if (isset($stats['total_size_mb'])) {
            $this->line("Total Size: {$stats['total_size_mb']}MB");
        }
        if (isset($stats['log_files'])) {
            $this->line("Log Files: {$stats['log_files']}");
        }
    }
}
