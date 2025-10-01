<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MonitoringService;

class CleanupLogsCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'logs:cleanup {--days=30 : Number of days to keep logs}';

    /**
     * The console command description.
     */
    protected $description = 'Clean up old log files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $daysToKeep = $this->option('days');

        $this->info("Cleaning up logs older than {$daysToKeep} days...");

        MonitoringService::cleanupLogs($daysToKeep);

        $this->info('Log cleanup completed successfully!');

        return 0;
    }
}
