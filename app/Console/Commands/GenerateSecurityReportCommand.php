<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SecurityMonitoringService;
use Illuminate\Support\Facades\Storage;

class GenerateSecurityReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'monitoring:security-report {--hours=24 : Number of hours to analyze} {--output= : Output file path}';

    /**
     * The console command description.
     */
    protected $description = 'Generate a security monitoring report for the specified time period';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hours = $this->option('hours');
        $outputPath = $this->option('output');

        $this->info("Generating security monitoring report for the last {$hours} hours...");

        $report = SecurityMonitoringService::generateSecurityReport($hours);

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
        $this->line('Security Monitoring Report');
        $this->line('=========================');
        $this->line("Period: {$report['period_hours']} hours");
        $this->line("Generated: {$report['generated_at']}");
        $this->line('');

        // Statistiques
        $this->line('Statistics:');
        $this->line('-----------');
        $this->line("Total Events: {$report['summary']['total_events']}");
        $this->line("Events Today: {$report['summary']['events_today']}");
        $this->line("Events This Hour: {$report['summary']['events_this_hour']}");
        $this->line("Failed Logins: {$report['summary']['failed_logins']}");
        $this->line("Suspicious Activities: {$report['summary']['suspicious_activities']}");
        $this->line("Blocked IPs: {$report['summary']['blocked_ips']}");
        $this->line('');

        // Menaces
        if (!empty($report['threats']['high_risk_ips'])) {
            $this->line('High Risk IPs:');
            $this->line('--------------');
            foreach ($report['threats']['high_risk_ips'] as $ip => $count) {
                $this->line("  {$ip}: {$count} attempts");
            }
            $this->line('');
        }

        if (!empty($report['threats']['brute_force_attempts'])) {
            $this->line('Brute Force Attempts:');
            $this->line('---------------------');
            foreach ($report['threats']['brute_force_attempts'] as $ip => $count) {
                $this->line("  {$ip}: {$count} attempts");
            }
            $this->line('');
        }

        if (!empty($report['threats']['suspicious_patterns'])) {
            $this->line('Suspicious Patterns:');
            $this->line('--------------------');
            foreach ($report['threats']['suspicious_patterns'] as $ip => $count) {
                $this->line("  {$ip}: {$count} patterns");
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

        // Événements récents
        if (!empty($report['recent_events'])) {
            $this->line('Recent Events:');
            $this->line('--------------');
            foreach ($report['recent_events'] as $event) {
                $this->line("  [{$event['timestamp']}] {$event['level']}: {$event['event']}");
                if ($event['ip_address']) {
                    $this->line("    IP: {$event['ip_address']}");
                }
            }
        }
    }
}
