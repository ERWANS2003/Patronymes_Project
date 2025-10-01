<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NetworkMonitoringService;
use Illuminate\Support\Facades\Storage;

class GenerateNetworkReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'monitoring:network-report {--hours=24 : Number of hours to analyze} {--output= : Output file path}';

    /**
     * The console command description.
     */
    protected $description = 'Generate a network monitoring report for the specified time period';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hours = $this->option('hours');
        $outputPath = $this->option('output');

        $this->info("Generating network monitoring report for the last {$hours} hours...");

        $report = NetworkMonitoringService::generateNetworkReport($hours);

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
        $this->line('Network Monitoring Report');
        $this->line('========================');
        $this->line("Period: {$report['period_hours']} hours");
        $this->line("Generated: {$report['generated_at']}");
        $this->line('');

        // Statistiques
        $this->line('Network Statistics:');
        $this->line('-------------------');
        $this->line("Local IP: {$report['summary']['local_ip']}");
        $this->line("Public IP: {$report['summary']['public_ip']}");
        $this->line('');

        // Serveurs DNS
        if (!empty($report['summary']['dns_servers'])) {
            $this->line('DNS Servers:');
            $this->line('-------------');
            foreach ($report['summary']['dns_servers'] as $dns) {
                if (is_array($dns)) {
                    $this->line("  Server: {$dns['server']}");
                    $this->line("  Response Time: {$dns['response_time_ms']}ms");
                } else {
                    $this->line("  {$dns}");
                }
            }
            $this->line('');
        }

        // Interfaces réseau
        if (!empty($report['summary']['network_interfaces'])) {
            $this->line('Network Interfaces:');
            $this->line('--------------------');
            foreach ($report['summary']['network_interfaces'] as $interface) {
                if (is_array($interface)) {
                    $this->line("  Name: {$interface['name']}");
                    $this->line("  Status: {$interface['status']}");
                    $this->line("  IP Address: {$interface['ip_address']}");
                    $this->line("  Subnet Mask: {$interface['subnet_mask']}");
                    $this->line("  Gateway: {$interface['gateway']}");
                }
            }
            $this->line('');
        }

        // Table de routage
        if (!empty($report['summary']['routing_table'])) {
            $this->line('Routing Table:');
            $this->line('--------------');
            foreach ($report['summary']['routing_table'] as $route) {
                if (is_array($route)) {
                    $this->line("  Destination: {$route['destination']}");
                    $this->line("  Gateway: {$route['gateway']}");
                    $this->line("  Interface: {$route['interface']}");
                    $this->line("  Metric: {$route['metric']}");
                }
            }
            $this->line('');
        }

        // Performances
        $this->line('Network Performance:');
        $this->line('--------------------');
        if (isset($report['performance']['error'])) {
            $this->line("<fg=red>Error: {$report['performance']['error']}</>");
        } else {
            $this->line("Ping Time: {$report['performance']['ping_time_ms']}ms");
            $this->line("Ping Result: " . ($report['performance']['ping_result'] ? 'Success' : 'Failed'));
            $this->line("Download Speed: {$report['performance']['download_speed_mbps']}Mbps");
            $this->line("Upload Speed: {$report['performance']['upload_speed_mbps']}Mbps");
            $this->line("Total Time: {$report['performance']['total_time_ms']}ms");
            $this->line("Test Successful: " . ($report['performance']['test_successful'] ? 'Yes' : 'No'));
        }
        $this->line('');

        // Tendances
        $this->line('Network Trends:');
        $this->line('---------------');
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
            $this->line('Network Performance by Hour:');
            $this->line('----------------------------');
            foreach ($report['trends']['hourly'] as $hour => $performance) {
                $this->line("  {$hour}:");
                $this->line("    Ping: {$performance['ping_time_ms']}ms");
                $this->line("    Download: {$performance['download_speed_mbps']}Mbps");
                $this->line("    Upload: {$performance['upload_speed_mbps']}Mbps");
            }
            $this->line('');
        }

        // Tendances par jour
        if (!empty($report['trends']['daily'])) {
            $this->line('Network Performance by Day:');
            $this->line('---------------------------');
            foreach ($report['trends']['daily'] as $day => $performance) {
                $this->line("  {$day}:");
                $this->line("    Ping: {$performance['ping_time_ms']}ms");
                $this->line("    Download: {$performance['download_speed_mbps']}Mbps");
                $this->line("    Upload: {$performance['upload_speed_mbps']}Mbps");
            }
        }
    }
}
