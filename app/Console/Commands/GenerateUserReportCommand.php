<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\UserMonitoringService;
use Illuminate\Support\Facades\Storage;

class GenerateUserReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'monitoring:user-report {--hours=24 : Number of hours to analyze} {--output= : Output file path}';

    /**
     * The console command description.
     */
    protected $description = 'Generate a user monitoring report for the specified time period';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hours = $this->option('hours');
        $outputPath = $this->option('output');

        $this->info("Generating user monitoring report for the last {$hours} hours...");

        $report = UserMonitoringService::generateUserReport($hours);

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
        $this->line('User Monitoring Report');
        $this->line('=====================');
        $this->line("Period: {$report['period_hours']} hours");
        $this->line("Generated: {$report['generated_at']}");
        $this->line('');

        // Statistiques
        $this->line('User Statistics:');
        $this->line('----------------');
        $this->line("Total Users: {$report['summary']['total_users']}");
        $this->line("Active Users Today: {$report['summary']['active_users_today']}");
        $this->line("Active Users This Week: {$report['summary']['active_users_this_week']}");
        $this->line("Active Users This Month: {$report['summary']['active_users_this_month']}");
        $this->line("New Users Today: {$report['summary']['new_users_today']}");
        $this->line("New Users This Week: {$report['summary']['new_users_this_week']}");
        $this->line("New Users This Month: {$report['summary']['new_users_this_month']}");
        $this->line('');

        // Utilisateurs par rôle
        $this->line('Users by Role:');
        $this->line('--------------');
        foreach ($report['summary']['users_by_role'] as $role => $count) {
            $this->line("  {$role}: {$count}");
        }
        $this->line('');

        // Utilisateurs par statut
        $this->line('Users by Status:');
        $this->line('----------------');
        foreach ($report['summary']['users_by_status'] as $status => $count) {
            $this->line("  {$status}: {$count}");
        }
        $this->line('');

        // Activité
        $this->line('User Activity:');
        $this->line('--------------');

        // Connexions récentes
        if (!empty($report['activity']['recent_logins'])) {
            $this->line('Recent Logins:');
            foreach ($report['activity']['recent_logins'] as $login) {
                $this->line("  {$login['name']} ({$login['email']}) - {$login['last_login_at']}");
            }
            $this->line('');
        }

        // Top contributeurs
        if (!empty($report['activity']['top_contributors'])) {
            $this->line('Top Contributors:');
            foreach ($report['activity']['top_contributors'] as $contributor) {
                $this->line("  {$contributor['name']} ({$contributor['email']}) - {$contributor['contributions_count']} contributions");
            }
            $this->line('');
        }

        // Top commentateurs
        if (!empty($report['activity']['top_commenters'])) {
            $this->line('Top Commenters:');
            foreach ($report['activity']['top_commenters'] as $commenter) {
                $this->line("  {$commenter['name']} ({$commenter['email']}) - {$commenter['commentaires_count']} comments");
            }
            $this->line('');
        }

        // Top favoriseurs
        if (!empty($report['activity']['top_favoriters'])) {
            $this->line('Top Favoriters:');
            foreach ($report['activity']['top_favoriters'] as $favoriter) {
                $this->line("  {$favoriter['name']} ({$favoriter['email']}) - {$favoriter['favorites_count']} favorites");
            }
            $this->line('');
        }

        // Distribution géographique
        if (!empty($report['activity']['user_geographic_distribution'])) {
            $this->line('Geographic Distribution:');
            foreach ($report['activity']['user_geographic_distribution'] as $country => $percentage) {
                $this->line("  {$country}: {$percentage}%");
            }
            $this->line('');
        }

        // Engagement
        $this->line('User Engagement:');
        $this->line('----------------');

        // Métriques d'engagement
        if (!empty($report['engagement']['engagement_metrics'])) {
            $this->line('Engagement Metrics:');
            foreach ($report['engagement']['engagement_metrics'] as $metric => $value) {
                $this->line("  {$metric}: {$value}");
            }
            $this->line('');
        }

        // Rétention
        if (!empty($report['engagement']['user_retention'])) {
            $this->line('User Retention:');
            foreach ($report['engagement']['user_retention'] as $period => $rate) {
                $this->line("  {$period}: {$rate}%");
            }
            $this->line('');
        }

        // Utilisation des fonctionnalités
        if (!empty($report['engagement']['feature_usage'])) {
            $this->line('Feature Usage:');
            foreach ($report['engagement']['feature_usage'] as $feature => $usage) {
                $this->line("  {$feature}: {$usage}%");
            }
            $this->line('');
        }

        // Satisfaction
        if (!empty($report['engagement']['user_satisfaction'])) {
            $this->line('User Satisfaction:');
            foreach ($report['engagement']['user_satisfaction'] as $aspect => $rating) {
                $this->line("  {$aspect}: {$rating}/5");
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
        }
    }
}
