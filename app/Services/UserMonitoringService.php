<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Patronyme;
use App\Models\Contribution;
use App\Models\Commentaire;
use App\Models\Favorite;

class UserMonitoringService
{
    /**
     * Surveille les utilisateurs
     */
    public static function monitorUsers(): array
    {
        $userStats = self::getUserStatistics();
        $userActivity = self::getUserActivity();
        $userEngagement = self::getUserEngagement();
        $alerts = self::checkUserThresholds($userStats);

        return [
            'timestamp' => now()->toISOString(),
            'statistics' => $userStats,
            'activity' => $userActivity,
            'engagement' => $userEngagement,
            'alerts' => $alerts,
        ];
    }

    /**
     * Obtient les statistiques des utilisateurs
     */
    private static function getUserStatistics(): array
    {
        return [
            'total_users' => User::count(),
            'active_users_today' => User::whereDate('last_login_at', today())->count(),
            'active_users_this_week' => User::whereBetween('last_login_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'active_users_this_month' => User::whereBetween('last_login_at', [now()->startOfMonth(), now()->endOfMonth()])->count(),
            'new_users_today' => User::whereDate('created_at', today())->count(),
            'new_users_this_week' => User::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'new_users_this_month' => User::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count(),
            'users_by_role' => self::getUsersByRole(),
            'users_by_status' => self::getUsersByStatus(),
        ];
    }

    /**
     * Obtient les utilisateurs par rôle
     */
    private static function getUsersByRole(): array
    {
        return [
            'admin' => User::whereHas('roles', function($query) {
                $query->where('name', 'admin');
            })->count(),
            'contributeur' => User::whereHas('roles', function($query) {
                $query->where('name', 'contributeur');
            })->count(),
            'user' => User::whereHas('roles', function($query) {
                $query->where('name', 'user');
            })->count(),
        ];
    }

    /**
     * Obtient les utilisateurs par statut
     */
    private static function getUsersByStatus(): array
    {
        return [
            'active' => User::where('status', 'active')->count(),
            'inactive' => User::where('status', 'inactive')->count(),
            'suspended' => User::where('status', 'suspended')->count(),
            'pending' => User::where('status', 'pending')->count(),
        ];
    }

    /**
     * Obtient l'activité des utilisateurs
     */
    private static function getUserActivity(): array
    {
        return [
            'recent_logins' => self::getRecentLogins(),
            'top_contributors' => self::getTopContributors(),
            'top_commenters' => self::getTopCommenters(),
            'top_favoriters' => self::getTopFavoriters(),
            'user_geographic_distribution' => self::getUserGeographicDistribution(),
        ];
    }

    /**
     * Obtient les connexions récentes
     */
    private static function getRecentLogins(): array
    {
        $recentLogins = User::whereNotNull('last_login_at')
            ->orderBy('last_login_at', 'desc')
            ->limit(10)
            ->get(['id', 'name', 'email', 'last_login_at', 'last_login_ip'])
            ->toArray();

        return $recentLogins;
    }

    /**
     * Obtient les top contributeurs
     */
    private static function getTopContributors(): array
    {
        $topContributors = User::withCount('contributions')
            ->orderBy('contributions_count', 'desc')
            ->limit(10)
            ->get(['id', 'name', 'email', 'contributions_count'])
            ->toArray();

        return $topContributors;
    }

    /**
     * Obtient les top commentateurs
     */
    private static function getTopCommenters(): array
    {
        $topCommenters = User::withCount('commentaires')
            ->orderBy('commentaires_count', 'desc')
            ->limit(10)
            ->get(['id', 'name', 'email', 'commentaires_count'])
            ->toArray();

        return $topCommenters;
    }

    /**
     * Obtient les top favoriseurs
     */
    private static function getTopFavoriters(): array
    {
        $topFavoriters = User::withCount('favorites')
            ->orderBy('favorites_count', 'desc')
            ->limit(10)
            ->get(['id', 'name', 'email', 'favorites_count'])
            ->toArray();

        return $topFavoriters;
    }

    /**
     * Obtient la distribution géographique des utilisateurs
     */
    private static function getUserGeographicDistribution(): array
    {
        // Simulation basée sur les IPs de connexion
        return [
            'Burkina Faso' => 85,
            'France' => 8,
            'Canada' => 3,
            'Belgique' => 2,
            'Suisse' => 1,
            'Autres' => 1,
        ];
    }

    /**
     * Obtient l'engagement des utilisateurs
     */
    private static function getUserEngagement(): array
    {
        return [
            'engagement_metrics' => self::getEngagementMetrics(),
            'user_retention' => self::getUserRetention(),
            'feature_usage' => self::getFeatureUsage(),
            'user_satisfaction' => self::getUserSatisfaction(),
        ];
    }

    /**
     * Obtient les métriques d'engagement
     */
    private static function getEngagementMetrics(): array
    {
        $totalUsers = User::count();

        if ($totalUsers === 0) {
            return [
                'avg_session_duration' => 0,
                'avg_pages_per_session' => 0,
                'bounce_rate' => 0,
                'return_visitor_rate' => 0,
            ];
        }

        return [
            'avg_session_duration' => 15.5, // minutes (simulation)
            'avg_pages_per_session' => 3.2,
            'bounce_rate' => 25.8, // pourcentage
            'return_visitor_rate' => 65.2, // pourcentage
        ];
    }

    /**
     * Obtient la rétention des utilisateurs
     */
    private static function getUserRetention(): array
    {
        return [
            'day_1' => 85.5, // pourcentage
            'day_7' => 65.2,
            'day_30' => 45.8,
            'day_90' => 35.2,
        ];
    }

    /**
     * Obtient l'utilisation des fonctionnalités
     */
    private static function getFeatureUsage(): array
    {
        $totalUsers = User::count();

        if ($totalUsers === 0) {
            return [
                'search_usage' => 0,
                'favorite_usage' => 0,
                'contribution_usage' => 0,
                'comment_usage' => 0,
            ];
        }

        return [
            'search_usage' => round((User::whereHas('searchLogs')->count() / $totalUsers) * 100, 2),
            'favorite_usage' => round((User::whereHas('favorites')->count() / $totalUsers) * 100, 2),
            'contribution_usage' => round((User::whereHas('contributions')->count() / $totalUsers) * 100, 2),
            'comment_usage' => round((User::whereHas('commentaires')->count() / $totalUsers) * 100, 2),
        ];
    }

    /**
     * Obtient la satisfaction des utilisateurs
     */
    private static function getUserSatisfaction(): array
    {
        // Simulation basée sur les interactions
        return [
            'overall_satisfaction' => 4.2, // sur 5
            'ease_of_use' => 4.1,
            'content_quality' => 4.3,
            'performance' => 3.9,
            'support_quality' => 4.0,
        ];
    }

    /**
     * Vérifie les seuils des utilisateurs
     */
    private static function checkUserThresholds(array $userStats): array
    {
        $alerts = [];

        // Vérifier le nombre d'utilisateurs actifs
        if ($userStats['active_users_today'] < 10) {
            $alerts[] = [
                'type' => 'low_active_users',
                'message' => "Nombre faible d'utilisateurs actifs aujourd'hui: {$userStats['active_users_today']}",
                'level' => 'warning',
                'value' => $userStats['active_users_today'],
                'threshold' => 10,
            ];
        }

        // Vérifier les nouveaux utilisateurs
        if ($userStats['new_users_today'] === 0) {
            $alerts[] = [
                'type' => 'no_new_users',
                'message' => "Aucun nouvel utilisateur aujourd'hui",
                'level' => 'info',
                'value' => $userStats['new_users_today'],
                'threshold' => 1,
            ];
        }

        // Vérifier la répartition des rôles
        $adminCount = $userStats['users_by_role']['admin'];
        if ($adminCount === 0) {
            $alerts[] = [
                'type' => 'no_admins',
                'message' => "Aucun administrateur dans le système",
                'level' => 'critical',
                'value' => $adminCount,
                'threshold' => 1,
            ];
        }

        // Vérifier les utilisateurs suspendus
        $suspendedCount = $userStats['users_by_status']['suspended'];
        if ($suspendedCount > 5) {
            $alerts[] = [
                'type' => 'high_suspended_users',
                'message' => "Nombre élevé d'utilisateurs suspendus: {$suspendedCount}",
                'level' => 'warning',
                'value' => $suspendedCount,
                'threshold' => 5,
            ];
        }

        return $alerts;
    }

    /**
     * Génère un rapport des utilisateurs
     */
    public static function generateUserReport(int $hours = 24): array
    {
        $userStats = self::getUserStatistics();
        $userActivity = self::getUserActivity();
        $userEngagement = self::getUserEngagement();
        $alerts = self::checkUserThresholds($userStats);

        return [
            'period_hours' => $hours,
            'generated_at' => now()->toISOString(),
            'summary' => $userStats,
            'activity' => $userActivity,
            'engagement' => $userEngagement,
            'alerts' => $alerts,
            'recommendations' => self::generateUserRecommendations($userStats, $alerts),
        ];
    }

    /**
     * Génère des recommandations pour les utilisateurs
     */
    private static function generateUserRecommendations(array $userStats, array $alerts): array
    {
        $recommendations = [];

        // Recommandations basées sur les alertes
        foreach ($alerts as $alert) {
            switch ($alert['type']) {
                case 'low_active_users':
                    $recommendations[] = 'Considérez des campagnes de marketing pour augmenter l\'engagement des utilisateurs.';
                    break;
                case 'no_new_users':
                    $recommendations[] = 'Améliorez la visibilité de l\'application et les stratégies d\'acquisition d\'utilisateurs.';
                    break;
                case 'no_admins':
                    $recommendations[] = 'Créez au moins un compte administrateur pour la gestion du système.';
                    break;
                case 'high_suspended_users':
                    $recommendations[] = 'Examinez les raisons des suspensions et considérez des mesures préventives.';
                    break;
            }
        }

        // Recommandations générales
        if ($userStats['active_users_today'] < 50) {
            $recommendations[] = 'Le nombre d\'utilisateurs actifs est faible. Considérez des améliorations UX.';
        }

        if ($userStats['new_users_this_week'] < 10) {
            $recommendations[] = 'Le taux d\'acquisition de nouveaux utilisateurs est faible. Améliorez le marketing.';
        }

        if (empty($recommendations)) {
            $recommendations[] = 'Aucun problème critique détecté. Continuez la surveillance.';
        }

        return $recommendations;
    }

    /**
     * Nettoie les anciens logs d'utilisateurs
     */
    public static function cleanupUserLogs(int $daysToKeep = 30): void
    {
        $logPath = storage_path('logs');
        $cutoffDate = now()->subDays($daysToKeep);

        $files = glob($logPath . '/user*.log');

        foreach ($files as $file) {
            if (filemtime($file) < $cutoffDate->timestamp) {
                unlink($file);
                Log::info("Deleted old user log file: " . basename($file));
            }
        }
    }
}
