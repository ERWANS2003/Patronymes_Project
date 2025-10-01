<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CacheMonitoringService;
use Illuminate\Http\Request;

class CacheMonitoringApiController extends Controller
{
    /**
     * Obtient les métriques de surveillance des caches
     */
    public function index()
    {
        $monitoring = CacheMonitoringService::monitorCache();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient les statistiques du cache
     */
    public function statistics()
    {
        $monitoring = CacheMonitoringService::monitorCache();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring['statistics'],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient les performances du cache
     */
    public function performance()
    {
        $monitoring = CacheMonitoringService::monitorCache();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring['performance'],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient les clés du cache
     */
    public function keys()
    {
        $monitoring = CacheMonitoringService::monitorCache();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring['keys'],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient les alertes du cache
     */
    public function alerts()
    {
        $monitoring = CacheMonitoringService::monitorCache();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring['alerts'],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Génère un rapport du cache
     */
    public function report(Request $request)
    {
        $hours = $request->get('hours', 24);
        $report = CacheMonitoringService::generateCacheReport($hours);

        return response()->json([
            'status' => 'success',
            'data' => $report,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Nettoie le cache
     */
    public function clear()
    {
        CacheMonitoringService::clearCache();

        return response()->json([
            'message' => 'Cache cleared successfully',
            'cleared_at' => now()->toISOString()
        ]);
    }

    /**
     * Exporte les métriques du cache
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'json');
        $hours = $request->get('hours', 24);

        $report = CacheMonitoringService::generateCacheReport($hours);

        if ($format === 'csv') {
            return $this->exportToCsv($report);
        }

        return response()->json([
            'status' => 'success',
            'data' => $report,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Exporte les données en CSV
     */
    private function exportToCsv(array $report): \Illuminate\Http\Response
    {
        $filename = 'cache_monitoring_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($report) {
            $file = fopen('php://output', 'w');

            // En-têtes
            fputcsv($file, ['Metric', 'Value', 'Timestamp']);

            // Statistiques
            foreach ($report['summary'] as $metric => $value) {
                if (is_array($value)) {
                    foreach ($value as $subMetric => $subValue) {
                        fputcsv($file, [
                            "summary.{$metric}.{$subMetric}",
                            is_array($subValue) ? json_encode($subValue) : $subValue,
                            $report['generated_at']
                        ]);
                    }
                } else {
                    fputcsv($file, [
                        "summary.{$metric}",
                        $value,
                        $report['generated_at']
                    ]);
                }
            }

            // Performances
            foreach ($report['performance'] as $metric => $value) {
                fputcsv($file, [
                    "performance.{$metric}",
                    $value,
                    $report['generated_at']
                ]);
            }

            // Clés
            if (isset($report['keys']['keys']) && is_array($report['keys']['keys'])) {
                foreach ($report['keys']['keys'] as $key) {
                    if (is_array($key)) {
                        foreach ($key as $keyMetric => $keyValue) {
                            fputcsv($file, [
                                "keys.{$keyMetric}",
                                $keyValue,
                                $report['generated_at']
                            ]);
                        }
                    }
                }
            }

            // Alertes
            foreach ($report['alerts'] as $alert) {
                fputcsv($file, [
                    "alert.{$alert['type']}",
                    $alert['message'],
                    $report['generated_at']
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
