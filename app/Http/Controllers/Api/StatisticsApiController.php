<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\StatisticsService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class StatisticsApiController extends Controller
{
    protected $statisticsService;

    public function __construct(StatisticsService $statisticsService)
    {
        $this->statisticsService = $statisticsService;
    }

    /**
     * Get overview statistics
     */
    public function overview(): JsonResponse
    {
        try {
            $stats = $this->statisticsService->getDashboardStats();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_patronymes' => $stats['total_patronymes'],
                    'total_users' => $stats['total_users'],
                    'total_regions' => $stats['total_regions'],
                    'total_groupes' => $stats['total_groupes'],
                    'total_views' => $stats['total_views'],
                    'total_favorites' => $stats['total_favorites'],
                    'contributions_today' => $stats['contributions_today'],
                    'contributions_this_week' => $stats['contributions_this_week'],
                    'active_users_today' => $stats['active_users_today'],
                    'search_count_today' => $stats['search_count_today'],
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('API Error in StatisticsApiController@overview', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la récupération des statistiques.',
                'error' => app()->environment('local') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get advanced analytics
     */
    public function advanced(): JsonResponse
    {
        try {
            $analytics = $this->statisticsService->getAdvancedAnalytics();

            return response()->json([
                'success' => true,
                'data' => $analytics
            ]);

        } catch (\Exception $e) {
            Log::error('API Error in StatisticsApiController@advanced', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la récupération des analyses avancées.',
                'error' => app()->environment('local') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get real-time statistics
     */
    public function realtime(): JsonResponse
    {
        try {
            $realtimeStats = $this->statisticsService->getRealTimeStats();

            return response()->json([
                'success' => true,
                'data' => $realtimeStats,
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('API Error in StatisticsApiController@realtime', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la récupération des statistiques en temps réel.',
                'error' => app()->environment('local') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get admin analytics (admin only)
     */
    public function adminAnalytics(): JsonResponse
    {
        try {
            $analytics = $this->statisticsService->getAdvancedAnalytics();
            $realtimeStats = $this->statisticsService->getRealTimeStats();
            $searchAnalytics = $this->statisticsService->getSearchAnalytics();

            return response()->json([
                'success' => true,
                'data' => [
                    'advanced_analytics' => $analytics,
                    'realtime_stats' => $realtimeStats,
                    'search_analytics' => $searchAnalytics,
                    'cache_stats' => \App\Services\CacheService::getStats(),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('API Error in StatisticsApiController@adminAnalytics', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la récupération des analyses administrateur.',
                'error' => app()->environment('local') ? $e->getMessage() : null
            ], 500);
        }
    }
}
