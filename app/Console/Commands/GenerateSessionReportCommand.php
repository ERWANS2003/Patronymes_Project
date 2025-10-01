<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SessionMonitoringService;
use Illuminate\Support\Facades\Storage;

class GenerateSessionReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'monitoring:session-report {--hours=24 : Number of hours to analyze} {--output= : Output file path}';

    /**
     * The console command description.
     */
    protected $description = 'Generate a session monitoring report for the specified time period';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hours = $this->option('hours');
        $outputPath = $this->option('output');

        $this->info("Generating session monitoring report for the last {$hours} hours...");

        $report = SessionMonitoringService::generateSessionReport($hours);

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
        $this->line('Session Monitoring Report');
        $this->line('========================');
        $this->line("Period: {$report['period_hours']} hours");
        $this->line("Generated: {$report['generated_at']}");
        $this->line('');

        // Statistiques
        $this->line('Session Statistics:');
        $this->line('-------------------');
        $this->line("Total Sessions: {$report['summary']['total_sessions']}");
        $this->line("Active Sessions: {$report['summary']['active_sessions']}");
        $this->line("Expired Sessions: {$report['summary']['expired_sessions']}");
        $this->line("Total Size: {$report['summary']['total_size_mb']}MB");
        $this->line("Session Lifetime: {$report['summary']['session_lifetime_minutes']} minutes");
        $this->line("Oldest Session: {$report['summary']['oldest_session']}");
        $this->line("Newest Session: {$report['summary']['newest_session']}");
        $this->line('');

        // Fichiers de sessions
        if (!empty($report['summary']['session_files'])) {
            $this->line('Session Files:');
            $this->line('--------------');
            foreach (array_slice($report['summary']['session_files'], 0, 10) as $file) {
                $this->line("  {$file['name']}: {$file['size_mb']}MB (Age: {$file['age_minutes']} minutes)");
            }
            if (count($report['summary']['session_files']) > 10) {
                $this->line("  ... and " . (count($report['summary']['session_files']) - 10) . " more files");
            }
            $this->line('');
        }

        // Tendances
        $this->line('Session Trends:');
        $this->line('---------------');
        $this->line("Trend: {$report['trends']['trend']}");
        $this->line('');

        // Sessions actives
        if (!empty($report['active_sessions'])) {
            $this->line('Active Sessions:');
            $this->line('----------------');
            foreach (array_slice($report['active_sessions'], 0, 10) as $session) {
                $this->line("  ID: {$session['id']}");
                $this->line("    Size: {$session['size_mb']}MB");
                $this->line("    Age: {$session['age_minutes']} minutes");
                $this->line("    Modified: {$session['modified']}");
                if ($session['user_id']) {
                    $this->line("    User ID: {$session['user_id']}");
                }
                if ($session['ip_address']) {
                    $this->line("    IP Address: {$session['ip_address']}");
                }
                $this->line('    ---');
            }
            if (count($report['active_sessions']) > 10) {
                $this->line("  ... and " . (count($report['active_sessions']) - 10) . " more active sessions");
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
            $this->line('Sessions by Hour:');
            $this->line('-----------------');
            foreach ($report['trends']['hourly'] as $hour => $count) {
                $this->line("  {$hour}: {$count} sessions");
            }
            $this->line('');
        }

        // Tendances par jour
        if (!empty($report['trends']['daily'])) {
            $this->line('Sessions by Day:');
            $this->line('----------------');
            foreach ($report['trends']['daily'] as $day => $count) {
                $this->line("  {$day}: {$count} sessions");
            }
        }
    }
}
