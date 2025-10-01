<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PerformanceMonitoringService;
use Illuminate\Http\Request;

class PerformanceMonitoringApiController extends Controller
{
    /**
     * Obtient les métriques de surveillance des performances
     */
    public function index()
    {
        $monitoring = PerformanceMonitoringService::monitorPerformance();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient les statistiques de performance
     */
    public function statistics()
    {
        $monitoring = PerformanceMonitoringService::monitorPerformance();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring['statistics'],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient les statistiques de temps de réponse
     */
    public function responseTimes()
    {
        $monitoring = PerformanceMonitoringService::monitorPerformance();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring['response_times'],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient les statistiques d'utilisation de la mémoire
     */
    public function memoryUsage()
    {
        $monitoring = PerformanceMonitoringService::monitorPerformance();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring['memory_usage'],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient les alertes de performance
     */
    public function alerts()
    {
        $monitoring = PerformanceMonitoringService::monitorPerformance();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring['alerts'],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Génère un rapport de performance
     */
    public function report(Request $request)
    {
        $hours = $request->get('hours', 24);
        $report = PerformanceMonitoringService::generatePerformanceReport($hours);

        return response()->json([
            'status' => 'success',
            'data' => $report,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Nettoie les anciens logs de performance
     */
    public function cleanup(Request $request)
    {
        $daysToKeep = $request->get('days', 30);
        PerformanceMonitoringService::cleanupPerformanceLogs($daysToKeep);

        return response()->json([
            'message' => "Performance logs older than {$daysToKeep} days have been cleaned up",
            'cleaned_at' => now()->toISOString()
        ]);
    }

    /**
     * Exporte les métriques de performance
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'json');
        $hours = $request->get('hours', 24);

        $report = PerformanceMonitoringService::generatePerformanceReport($hours);

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
        $filename = 'performance_monitoring_' . now()->format('Y-m-d_H-i-s') . '.csv';

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
                fputcsv($file, [
                    "summary.{$metric}",
                    $value,
                    $report['generated_at']
                ]);
            }

            // Temps de réponse
            foreach ($report['response_times'] as $period => $times) {
                if (is_array($times)) {
                    foreach ($times as $time => $value) {
                        fputcsv($file, [
                            "response_times.{$period}.{$time}",
                            $value,
                            $report['generated_at']
                        ]);
                    }
                } else {
                    fputcsv($file, [
                        "response_times.{$period}",
                        $times,
                        $report['generated_at']
                    ]);
                }
            }

            // Utilisation de la mémoire
            foreach ($report['memory_usage'] as $metric => $value) {
                fputcsv($file, [
                    "memory_usage.{$metric}",
                    $value,
                    $report['generated_at']
                ]);
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
