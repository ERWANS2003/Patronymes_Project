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
            return [
                'total_patronymes' => Patronyme::count(),
                'total_users' => User::count(),
                'total_regions' => Region::count(),
                'total_groupes' => GroupeEthnique::count(),
                'recent_patronymes' => Patronyme::latest()->limit(5)->get(),
                'most_viewed' => Patronyme::orderBy('views_count', 'desc')->limit(5)->get(),
                'contributions_today' => Patronyme::whereDate('created_at', today())->count(),
                'contributions_this_week' => Patronyme::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
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
}
