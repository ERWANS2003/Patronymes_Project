<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Sauvegarde quotidienne à 2h du matin
        $schedule->command('backup:database')
                 ->dailyAt('02:00')
                 ->withoutOverlapping();

        // Nettoyage du cache toutes les heures
        $schedule->command('cache:clear')
                 ->hourly();

        // Nettoyage des logs anciens (plus de 30 jours)
        $schedule->command('logs:cleanup --days=30')
                 ->daily()
                 ->at('03:00');

        // Préchargement du cache toutes les 6 heures
        $schedule->call(function () {
            app(\App\Services\CacheService::class)->warmUp();
        })->everySixHours();

        // Génération de statistiques quotidiennes
        $schedule->call(function () {
            \App\Models\Patronyme::whereDate('created_at', today())->count();
        })->daily();

        // Vérification de santé toutes les 5 minutes
        $schedule->command('monitoring:health-check')
                 ->everyFiveMinutes()
                 ->withoutOverlapping();

        // Vérification des alertes toutes les 15 minutes
        $schedule->command('monitoring:check-alerts')
                 ->everyFifteenMinutes()
                 ->withoutOverlapping();

        // Rapport de performance quotidien
        $schedule->command('monitoring:performance-report --hours=24 --output=reports/performance-' . date('Y-m-d') . '.json')
                 ->daily()
                 ->at('01:00');

        // Rapport de surveillance des erreurs quotidien
        $schedule->command('monitoring:error-report --hours=24 --output=reports/error-monitoring-' . date('Y-m-d') . '.json')
                 ->daily()
                 ->at('02:00');

        // Rapport de surveillance de la sécurité quotidien
        $schedule->command('monitoring:security-report --hours=24 --output=reports/security-monitoring-' . date('Y-m-d') . '.json')
                 ->daily()
                 ->at('03:00');

        // Rapport de surveillance de la base de données quotidien
        $schedule->command('monitoring:database-report --hours=24 --output=reports/database-monitoring-' . date('Y-m-d') . '.json')
                 ->daily()
                 ->at('04:00');

        // Rapport de surveillance des performances quotidien
        $schedule->command('monitoring:performance-report --hours=24 --output=reports/performance-monitoring-' . date('Y-m-d') . '.json')
                 ->daily()
                 ->at('05:00');

        // Rapport de surveillance des utilisateurs quotidien
        $schedule->command('monitoring:user-report --hours=24 --output=reports/user-monitoring-' . date('Y-m-d') . '.json')
                 ->daily()
                 ->at('06:00');

        // Rapport de surveillance des logs quotidien
        $schedule->command('monitoring:log-report --hours=24 --output=reports/log-monitoring-' . date('Y-m-d') . '.json')
                 ->daily()
                 ->at('07:00');

        // Rapport de surveillance des caches quotidien
        $schedule->command('monitoring:cache-report --hours=24 --output=reports/cache-monitoring-' . date('Y-m-d') . '.json')
                 ->daily()
                 ->at('08:00');

        // Rapport de surveillance des sessions quotidien
        $schedule->command('monitoring:session-report --hours=24 --output=reports/session-monitoring-' . date('Y-m-d') . '.json')
                 ->daily()
                 ->at('09:00');

        // Rapport de surveillance des files d'attente quotidien
        $schedule->command('monitoring:queue-report --hours=24 --output=reports/queue-monitoring-' . date('Y-m-d') . '.json')
                 ->daily()
                 ->at('10:00');

        // Rapport de surveillance des emails quotidien
        $schedule->command('monitoring:email-report --hours=24 --output=reports/email-monitoring-' . date('Y-m-d') . '.json')
                 ->daily()
                 ->at('11:00');

        // Rapport de surveillance des fichiers quotidien
        $schedule->command('monitoring:file-report --hours=24 --output=reports/file-monitoring-' . date('Y-m-d') . '.json')
                 ->daily()
                 ->at('12:00');

        // Rapport de surveillance des réseaux quotidien
        $schedule->command('monitoring:network-report --hours=24 --output=reports/network-monitoring-' . date('Y-m-d') . '.json')
                 ->daily()
                 ->at('13:00');

        // Rapport de surveillance des systèmes quotidien
        $schedule->command('monitoring:system-report --hours=24 --output=reports/system-monitoring-' . date('Y-m-d') . '.json')
                 ->daily()
                 ->at('14:00');

        // Rapport de surveillance des applications quotidien
        $schedule->command('monitoring:application-report --hours=24 --output=reports/application-monitoring-' . date('Y-m-d') . '.json')
                 ->daily()
                 ->at('15:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
