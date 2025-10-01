<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AlertService;

class CheckAlertsCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'monitoring:check-alerts';

    /**
     * The console command description.
     */
    protected $description = 'Check system status and send alerts if necessary';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking system alerts...');

        AlertService::checkAndSendAlerts();

        $this->info('Alert check completed!');

        return 0;
    }
}
