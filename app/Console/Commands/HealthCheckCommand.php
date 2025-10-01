<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MonitoringService;

class HealthCheckCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'monitoring:health-check {--json : Output in JSON format}';

    /**
     * The console command description.
     */
    protected $description = 'Perform a health check of the application';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Performing health check...');

        $health = MonitoringService::healthCheck();

        if ($this->option('json')) {
            $this->line(json_encode($health, JSON_PRETTY_PRINT));
        } else {
            $this->displayHealthStatus($health);
        }

        // Retourner le code de sortie approprié
        return $health['status'] === 'healthy' ? 0 : 1;
    }

    /**
     * Affiche le statut de santé de manière lisible
     */
    private function displayHealthStatus(array $health): void
    {
        $status = $health['status'];
        $statusColor = $status === 'healthy' ? 'green' : ($status === 'warning' ? 'yellow' : 'red');

        $this->line('');
        $this->line("Application Status: <fg={$statusColor}>{$status}</>");
        $this->line("Timestamp: {$health['timestamp']}");
        $this->line('');

        $this->line('Health Checks:');
        foreach ($health['checks'] as $check => $result) {
            $checkStatus = $result['status'];
            $checkColor = $checkStatus === 'ok' ? 'green' : ($checkStatus === 'warning' ? 'yellow' : 'red');

            $this->line("  {$check}: <fg={$checkColor}>{$checkStatus}</>");

            if (isset($result['response_time_ms'])) {
                $this->line("    Response time: {$result['response_time_ms']}ms");
            }

            if (isset($result['error'])) {
                $this->line("    Error: {$result['error']}");
            }

            if (isset($result['usage_percentage'])) {
                $this->line("    Usage: {$result['usage_percentage']}%");
            }
        }
    }
}
