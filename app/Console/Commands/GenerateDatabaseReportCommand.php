<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DatabaseMonitoringService;
use Illuminate\Support\Facades\Storage;

class GenerateDatabaseReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'monitoring:database-report {--hours=24 : Number of hours to analyze} {--output= : Output file path}';

    /**
     * The console command description.
     */
    protected $description = 'Generate a database monitoring report for the specified time period';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hours = $this->option('hours');
        $outputPath = $this->option('output');

        $this->info("Generating database monitoring report for the last {$hours} hours...");

        $report = DatabaseMonitoringService::generateDatabaseReport($hours);

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
        $this->line('Database Monitoring Report');
        $this->line('=========================');
        $this->line("Period: {$report['period_hours']} hours");
        $this->line("Generated: {$report['generated_at']}");
        $this->line('');

        // Statistiques
        $this->line('Database Statistics:');
        $this->line('--------------------');
        if (isset($report['summary']['error'])) {
            $this->line("<fg=red>Error: {$report['summary']['error']}</>");
        } else {
            $this->line("Driver: {$report['summary']['driver']}");
            $this->line("Version: {$report['summary']['version']}");
            $this->line("Host: {$report['summary']['host']}");
            $this->line("Database: {$report['summary']['database']}");
            $this->line("Charset: {$report['summary']['charset']}");
            $this->line("Collation: {$report['summary']['collation']}");

            // Statistiques spécifiques au driver
            if ($report['summary']['driver'] === 'mysql') {
                $this->displayMysqlStats($report['summary']);
            } elseif ($report['summary']['driver'] === 'pgsql') {
                $this->displayPostgresStats($report['summary']);
            }
        }
        $this->line('');

        // Requêtes lentes
        if (isset($report['slow_queries']) && !empty($report['slow_queries'])) {
            $this->line('Slow Queries:');
            $this->line('-------------');
            if (isset($report['slow_queries']['message'])) {
                $this->line($report['slow_queries']['message']);
            } else {
                foreach ($report['slow_queries'] as $query) {
                    $this->line("Time: {$query['time']}");
                    $this->line("Query Time: {$query['query_time']}");
                    $this->line("Lock Time: {$query['lock_time']}");
                    $this->line("Rows Sent: {$query['rows_sent']}");
                    $this->line("Rows Examined: {$query['rows_examined']}");
                    $this->line("Query: {$query['query']}");
                    $this->line('---');
                }
            }
            $this->line('');
        }

        // Connexions
        $this->line('Connection Statistics:');
        $this->line('----------------------');
        foreach ($report['connections'] as $metric => $value) {
            $this->line("{$metric}: {$value}");
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

    /**
     * Affiche les statistiques MySQL
     */
    private function displayMysqlStats(array $stats): void
    {
        $this->line('');
        $this->line('MySQL Specific Statistics:');
        $this->line('--------------------------');

        if (isset($stats['max_connections'])) {
            $this->line("Max Connections: {$stats['max_connections']}");
        }
        if (isset($stats['current_connections'])) {
            $this->line("Current Connections: {$stats['current_connections']}");
        }
        if (isset($stats['max_used_connections'])) {
            $this->line("Max Used Connections: {$stats['max_used_connections']}");
        }
        if (isset($stats['slow_query_log'])) {
            $this->line("Slow Query Log: {$stats['slow_query_log']}");
        }
        if (isset($stats['long_query_time'])) {
            $this->line("Long Query Time: {$stats['long_query_time']}");
        }
        if (isset($stats['Slow_queries'])) {
            $this->line("Slow Queries: {$stats['Slow_queries']}");
        }
        if (isset($stats['Aborted_connects'])) {
            $this->line("Aborted Connects: {$stats['Aborted_connects']}");
        }
        if (isset($stats['Aborted_clients'])) {
            $this->line("Aborted Clients: {$stats['Aborted_clients']}");
        }
        if (isset($stats['Bytes_received'])) {
            $this->line("Bytes Received: {$stats['Bytes_received']}");
        }
        if (isset($stats['Bytes_sent'])) {
            $this->line("Bytes Sent: {$stats['Bytes_sent']}");
        }
        if (isset($stats['Qcache_hits'])) {
            $this->line("Query Cache Hits: {$stats['Qcache_hits']}");
        }
        if (isset($stats['Qcache_inserts'])) {
            $this->line("Query Cache Inserts: {$stats['Qcache_inserts']}");
        }
        if (isset($stats['Qcache_not_cached'])) {
            $this->line("Query Cache Not Cached: {$stats['Qcache_not_cached']}");
        }

        // Taille des tables
        if (isset($stats['table_sizes']) && !empty($stats['table_sizes'])) {
            $this->line('');
            $this->line('Table Sizes:');
            $this->line('------------');
            foreach ($stats['table_sizes'] as $table) {
                $this->line("{$table['table_name']}: {$table['size_mb']}MB ({$table['table_rows']} rows)");
            }
        }
    }

    /**
     * Affiche les statistiques PostgreSQL
     */
    private function displayPostgresStats(array $stats): void
    {
        $this->line('');
        $this->line('PostgreSQL Specific Statistics:');
        $this->line('-------------------------------');

        if (isset($stats['version'])) {
            $this->line("Version: {$stats['version']}");
        }
        if (isset($stats['connections'])) {
            $this->line("Total Connections: {$stats['connections']['total_connections']}");
            $this->line("Active Connections: {$stats['connections']['active_connections']}");
            $this->line("Idle Connections: {$stats['connections']['idle_connections']}");
        }
        if (isset($stats['database_size'])) {
            $this->line("Database Size: {$stats['database_size']}");
        }

        // Statistiques des tables
        if (isset($stats['table_stats']) && !empty($stats['table_stats'])) {
            $this->line('');
            $this->line('Table Statistics:');
            $this->line('-----------------');
            foreach ($stats['table_stats'] as $table) {
                $this->line("{$table['schemaname']}.{$table['tablename']}: {$table['size']} (Inserts: {$table['inserts']}, Updates: {$table['updates']}, Deletes: {$table['deletes']})");
            }
        }
    }
}
