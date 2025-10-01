<?php

namespace App\Services;

use App\Models\Patronyme;
use App\Models\User;
use App\Models\Region;
use App\Models\GroupeEthnique;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class StatisticsService
{
    public function getDashboardStats()
    {
        return Cache::remember('dashboard_stats', 300, function () {
            $userId = auth()->id();

            return [
                'total_patronymes' => Patronyme::count(),
                'total_users' => User::count(),
                'total_regions' => Region::count(),
                'total_groupes' => GroupeEthnique::count(),
                'recent_patronymes' => Patronyme::latest()->limit(5)->get(),
                'most_viewed' => Patronyme::orderBy('views_count', 'desc')->limit(5)->get(),
                'contributions_today' => Patronyme::whereDate('created_at', today())->count(),
                'contributions_this_week' => Patronyme::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                'total_views' => Patronyme::sum('views_count'),
                'total_favorites' => DB::table('favorites')->count(),
                'active_users_today' => User::whereDate('last_login_at', today())->count(),
                'search_count_today' => DB::table('search_logs')->whereDate('created_at', today())->count(),

                // Données utilisateur spécifiques
                'my_favorites' => $userId ? DB::table('favorites')->where('user_id', $userId)->count() : 0,
                'my_searches' => $userId ? DB::table('search_logs')->where('user_id', $userId)->count() : 0,
                'my_contributions' => $userId ? Patronyme::whereHas('contributions', function ($q) use ($userId) {
                    $q->whereHas('contributeur', function ($q) use ($userId) {
                        $q->where('utilisateur_id', $userId);
                    });
                })->count() : 0,
            ];
        });
    }

    public function getRegionStats()
    {
        return Cache::remember('region_stats', 600, function () {
            return Region::withCount('patronymes')
                        ->orderBy('patronymes_count', 'desc')
                        ->get();
        });
    }

    public function getEthnicGroupStats()
    {
        return Cache::remember('ethnic_stats', 600, function () {
            return GroupeEthnique::withCount('patronymes')
                               ->orderBy('patronymes_count', 'desc')
                               ->get();
        });
    }

    public function getUserActivityStats($userId)
    {
        return Cache::remember("user_activity_{$userId}", 300, function () use ($userId) {
            return [
                'contributions' => Patronyme::whereHas('contributions', function ($q) use ($userId) {
                    $q->whereHas('contributeur', function ($q) use ($userId) {
                        $q->where('utilisateur_id', $userId);
                    });
                })->count(),
                'comments' => DB::table('commentaires')->where('utilisateur_id', $userId)->count(),
                'favorites' => DB::table('favorites')->where('user_id', $userId)->count(),
                'views_generated' => Patronyme::whereHas('contributions', function ($q) use ($userId) {
                    $q->whereHas('contributeur', function ($q) use ($userId) {
                        $q->where('utilisateur_id', $userId);
                    });
                })->sum('views_count'),
            ];
        });
    }

    public function getTrendingPatronymes($days = 7)
    {
        return Cache::remember("trending_patronymes_{$days}", 1800, function () use ($days) {
            return Patronyme::where('created_at', '>=', now()->subDays($days))
                           ->orderBy('views_count', 'desc')
                           ->limit(10)
                           ->get();
        });
    }

    public function getAdvancedAnalytics()
    {
        return Cache::remember('advanced_analytics', 1800, function () {
            return [
                'patronymes_by_month' => $this->getPatronymesByMonth(),
                'views_by_month' => $this->getViewsByMonth(),
                'top_regions' => $this->getTopRegions(),
                'top_ethnic_groups' => $this->getTopEthnicGroups(),
                'user_activity' => $this->getUserActivity(),
                'search_analytics' => $this->getSearchAnalytics(),
                'performance_metrics' => $this->getPerformanceMetrics()
            ];
        });
    }

    private function getPatronymesByMonth()
    {
        return Patronyme::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    private function getViewsByMonth()
    {
        return Patronyme::select(
                DB::raw('DATE_FORMAT(updated_at, "%Y-%m") as month'),
                DB::raw('SUM(views_count) as total_views')
            )
            ->where('updated_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    private function getTopRegions()
    {
        return Region::withCount('patronymes')
            ->orderBy('patronymes_count', 'desc')
            ->limit(10)
            ->get();
    }

    private function getTopEthnicGroups()
    {
        return GroupeEthnique::withCount('patronymes')
            ->orderBy('patronymes_count', 'desc')
            ->limit(10)
            ->get();
    }

    private function getUserActivity()
    {
        return [
            'new_users_today' => User::whereDate('created_at', today())->count(),
            'new_users_this_week' => User::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'new_users_this_month' => User::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count(),
            'active_users_today' => User::whereDate('last_login_at', today())->count(),
            'active_users_this_week' => User::whereBetween('last_login_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
        ];
    }

    private function getSearchAnalytics()
    {
        return [
            'total_searches' => DB::table('search_logs')->count(),
            'searches_today' => DB::table('search_logs')->whereDate('created_at', today())->count(),
            'searches_this_week' => DB::table('search_logs')->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'popular_queries' => DB::table('search_logs')
                ->select('query', DB::raw('COUNT(*) as count'))
                ->groupBy('query')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get(),
            'average_results_per_search' => DB::table('search_logs')->avg('results_count'),
        ];
    }

    private function getPerformanceMetrics()
    {
        return [
            'average_response_time' => DB::table('search_logs')->avg('response_time'),
            'cache_hit_rate' => $this->getCacheHitRate(),
            'database_queries_per_page' => $this->getAverageQueriesPerPage(),
        ];
    }

    private function getCacheHitRate()
    {
        // Simulation d'un taux de hit cache
        // Dans une vraie application, cela viendrait du système de cache
        return rand(85, 95);
    }

    private function getAverageQueriesPerPage()
    {
        // Simulation du nombre moyen de requêtes par page
        return rand(5, 15);
    }

    public function getRealTimeStats()
    {
        return Cache::remember('realtime_stats', 60, function () {
            return [
                'online_users' => $this->getOnlineUsersCount(),
                'current_searches' => $this->getCurrentSearchesCount(),
                'system_load' => $this->getSystemLoad(),
            ];
        });
    }

    private function getOnlineUsersCount()
    {
        // Utilisateurs connectés dans les 5 dernières minutes
        return User::where('last_login_at', '>=', now()->subMinutes(5))->count();
    }

    private function getCurrentSearchesCount()
    {
        // Recherches dans la dernière minute
        return DB::table('search_logs')->where('created_at', '>=', now()->subMinute())->count();
    }

    private function getSystemLoad()
    {
        // Simulation de la charge système
        return [
            'cpu' => rand(20, 80),
            'memory' => rand(40, 90),
            'disk' => rand(30, 70)
        ];
    }
}
