<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FileMonitoringService;
use Illuminate\Support\Facades\Storage;

class GenerateFileReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'monitoring:file-report {--hours=24 : Number of hours to analyze} {--output= : Output file path}';

    /**
     * The console command description.
     */
    protected $description = 'Generate a file monitoring report for the specified time period';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hours = $this->option('hours');
        $outputPath = $this->option('output');

        $this->info("Generating file monitoring report for the last {$hours} hours...");

        $report = FileMonitoringService::generateFileReport($hours);

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
        $this->line('File Monitoring Report');
        $this->line('=====================');
        $this->line("Period: {$report['period_hours']} hours");
        $this->line("Generated: {$report['generated_at']}");
        $this->line('');

        // Statistiques
        $this->line('File Statistics:');
        $this->line('----------------');

        foreach ($report['summary'] as $directory => $stats) {
            $this->line("Directory: {$directory}");
            $this->line("  Exists: " . ($stats['exists'] ? 'Yes' : 'No'));
            $this->line("  Total Files: {$stats['total_files']}");
            $this->line("  Total Directories: {$stats['total_directories']}");
            $this->line("  Total Size: {$stats['total_size_mb']}MB");
            $this->line("  Oldest File: {$stats['oldest_file']}");
            $this->line("  Newest File: {$stats['newest_file']}");
            $this->line('');
        }

        // Permissions
        $this->line('File Permissions:');
        $this->line('-----------------');

        foreach ($report['permissions'] as $path => $permissions) {
            $this->line("Path: {$path}");
            if (isset($permissions['exists']) && !$permissions['exists']) {
                $this->line("  Exists: No");
            } else {
                $this->line("  Path: {$permissions['path']}");
                $this->line("  Permissions: {$permissions['permissions']}");
                $this->line("  Readable: " . ($permissions['readable'] ? 'Yes' : 'No'));
                $this->line("  Writable: " . ($permissions['writable'] ? 'Yes' : 'No'));
                $this->line("  Executable: " . ($permissions['executable'] ? 'Yes' : 'No'));
            }
            $this->line('');
        }

        // Tendances
        $this->line('File Trends:');
        $this->line('------------');

        foreach ($report['trends'] as $directory => $trends) {
            $this->line("Directory: {$directory}");
            $this->line("  Trend: {$trends['trend']}");
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
        foreach ($report['trends'] as $directory => $trends) {
            if (!empty($trends['hourly'])) {
                $this->line("Files by Hour ({$directory}):");
                $this->line('----------------------------');
                foreach ($trends['hourly'] as $hour => $count) {
                    $this->line("  {$hour}: {$count} files");
                }
                $this->line('');
            }
        }

        // Tendances par jour
        foreach ($report['trends'] as $directory => $trends) {
            if (!empty($trends['daily'])) {
                $this->line("Files by Day ({$directory}):");
                $this->line('---------------------------');
                foreach ($trends['daily'] as $day => $count) {
                    $this->line("  {$day}: {$count} files");
                }
                $this->line('');
            }
        }
    }
}
