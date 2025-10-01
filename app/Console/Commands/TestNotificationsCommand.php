<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NotificationService;

class TestNotificationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'monitoring:test-notifications {--channel= : Test specific channel (email, slack, webhook)}';

    /**
     * The console command description.
     */
    protected $description = 'Test monitoring notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $channel = $this->option('channel');

        $this->info('Testing monitoring notifications...');

        if ($channel) {
            $this->testSpecificChannel($channel);
        } else {
            $this->testAllChannels();
        }

        return 0;
    }

    /**
     * Teste un canal spécifique
     */
    private function testSpecificChannel(string $channel): void
    {
        $this->info("Testing {$channel} notifications...");

        $results = [];

        switch ($channel) {
            case 'email':
                $results['email'] = NotificationService::sendEmailNotification(
                    'Test de Notification Email',
                    'Ceci est un test de notification par email.',
                    ['test' => true, 'channel' => 'email'],
                    'info'
                );
                break;

            case 'slack':
                $results['slack'] = NotificationService::sendSlackNotification(
                    'Test de Notification Slack',
                    'Ceci est un test de notification Slack.',
                    ['test' => true, 'channel' => 'slack'],
                    'info'
                );
                break;

            case 'webhook':
                $results['webhook'] = NotificationService::sendWebhookNotification(
                    'Test de Notification Webhook',
                    'Ceci est un test de notification webhook.',
                    ['test' => true, 'channel' => 'webhook'],
                    'info'
                );
                break;

            default:
                $this->error("Unknown channel: {$channel}");
                return;
        }

        $this->displayResults($results);
    }

    /**
     * Teste tous les canaux
     */
    private function testAllChannels(): void
    {
        $this->info('Testing all notification channels...');

        $results = NotificationService::testNotifications();

        $this->displayResults($results);
    }

    /**
     * Affiche les résultats des tests
     */
    private function displayResults(array $results): void
    {
        $this->line('');
        $this->line('Test Results:');
        $this->line('=============');

        foreach ($results as $channel => $success) {
            $status = $success ? 'SUCCESS' : 'FAILED';
            $color = $success ? 'green' : 'red';

            $this->line("<fg={$color}>{$channel}: {$status}</>");
        }

        $this->line('');

        if (in_array(false, $results)) {
            $this->warn('Some notifications failed. Check the logs for more details.');
        } else {
            $this->info('All notifications sent successfully!');
        }
    }
}
