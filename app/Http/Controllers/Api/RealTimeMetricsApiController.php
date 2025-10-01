<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RealTimeMetricsService;
use App\Services\MonitoringService;
use Illuminate\Http\Request;

class RealTimeMetricsApiController extends Controller
{
    /**
     * Obtient les métriques en temps réel
     */
    public function index()
    {
        $metrics = RealTimeMetricsService::collectRealTimeMetrics();

        return response()->json([
            'status' => 'success',
            'data' => $metrics,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient les métriques de performance
     */
    public function performance()
    {
        $metrics = RealTimeMetricsService::collectRealTimeMetrics();

        return response()->json([
            'status' => 'success',
            'data' => [
                'memory' => $metrics['memory_usage'],
                'cpu' => $metrics['cpu_usage'],
                'disk_io' => $metrics['disk_io'],
                'network_io' => $metrics['network_io'],
                'cache_hit_rate' => $metrics['cache_hit_rate']
            ],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient les métriques d'activité
     */
    public function activity()
    {
        $metrics = RealTimeMetricsService::collectRealTimeMetrics();

        return response()->json([
            'status' => 'success',
            'data' => [
                'online_users' => $metrics['online_users'],
                'active_sessions' => $metrics['active_sessions'],
                'current_requests' => $metrics['current_requests'],
                'database_connections' => $metrics['database_connections']
            ],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient l'état de santé de l'application
     */
    public function health()
    {
        $health = MonitoringService::healthCheck();

        return response()->json($health, $health['status'] === 'healthy' ? 200 : 503);
    }

    /**
     * Obtient les métriques système
     */
    public function system()
    {
        $metrics = MonitoringService::collectSystemMetrics();

        return response()->json([
            'status' => 'success',
            'data' => $metrics,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient un rapport de performance
     */
    public function performanceReport(Request $request)
    {
        $hours = $request->get('hours', 1);
        $report = MonitoringService::generatePerformanceReport($hours);

        return response()->json([
            'status' => 'success',
            'data' => $report,
            'timestamp' => now()->toISOString()
        ]);
    }
}
