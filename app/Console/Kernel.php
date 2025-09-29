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
        $schedule->command('log:clear')
                 ->daily()
                 ->when(function () {
                     return \Storage::exists('logs/laravel.log') &&
                            \Storage::lastModified('logs/laravel.log') < now()->subDays(30)->timestamp;
                 });

        // Préchargement du cache toutes les 6 heures
        $schedule->call(function () {
            app(\App\Services\CacheService::class)->warmUp();
        })->everySixHours();

        // Génération de statistiques quotidiennes
        $schedule->call(function () {
            \App\Models\Patronyme::whereDate('created_at', today())->count();
        })->daily();
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
