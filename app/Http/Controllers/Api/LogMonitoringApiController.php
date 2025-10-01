<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\LogMonitoringService;
use Illuminate\Http\Request;

class LogMonitoringApiController extends Controller
{
    /**
     * Obtient les métriques de surveillance des logs
     */
    public function index()
    {
        $monitoring = LogMonitoringService::monitorLogs();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient les statistiques des logs
     */
    public function statistics()
    {
        $monitoring = LogMonitoringService::monitorLogs();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring['statistics'],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient les logs récents
     */
    public function recent()
    {
        $monitoring = LogMonitoringService::monitorLogs();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring['recent_logs'],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient les tendances des logs
     */
    public function trends()
    {
        $monitoring = LogMonitoringService::monitorLogs();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring['trends'],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient les alertes des logs
     */
    public function alerts()
    {
        $monitoring = LogMonitoringService::monitorLogs();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring['alerts'],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Génère un rapport des logs
     */
    public function report(Request $request)
    {
        $hours = $request->get('hours', 24);
        $report = LogMonitoringService::generateLogReport($hours);

        return response()->json([
            'status' => 'success',
            'data' => $report,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Nettoie les anciens logs
     */
    public function cleanup(Request $request)
    {
        $daysToKeep = $request->get('days', 30);
        LogMonitoringService::cleanupLogs($daysToKeep);

        return response()->json([
            'message' => "Logs older than {$daysToKeep} days have been cleaned up",
            'cleaned_at' => now()->toISOString()
        ]);
    }

    /**
     * Exporte les métriques des logs
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'json');
        $hours = $request->get('hours', 24);

        $report = LogMonitoringService::generateLogReport($hours);

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
        $filename = 'log_monitoring_' . now()->format('Y-m-d_H-i-s') . '.csv';

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

            // Logs récents
            foreach ($report['recent_logs'] as $log) {
                fputcsv($file, [
                    'recent_log',
                    $log['message'],
                    $log['timestamp']
                ]);
            }

            // Tendances
            foreach ($report['trends'] as $trendType => $trends) {
                if (is_array($trends)) {
                    foreach ($trends as $trend => $value) {
                        fputcsv($file, [
                            "trends.{$trendType}.{$trend}",
                            $value,
                            $report['generated_at']
                        ]);
                    }
                } else {
                    fputcsv($file, [
                        "trends.{$trendType}",
                        $trends,
                        $report['generated_at']
                    ]);
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
