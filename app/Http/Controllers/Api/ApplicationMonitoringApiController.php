<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ApplicationMonitoringService;
use Illuminate\Http\Request;

class ApplicationMonitoringApiController extends Controller
{
    /**
     * Obtient les métriques de surveillance des applications
     */
    public function index()
    {
        $monitoring = ApplicationMonitoringService::monitorApplication();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient les statistiques de l'application
     */
    public function statistics()
    {
        $monitoring = ApplicationMonitoringService::monitorApplication();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring['statistics'],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient les performances de l'application
     */
    public function performance()
    {
        $monitoring = ApplicationMonitoringService::monitorApplication();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring['performance'],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient les tendances de l'application
     */
    public function trends()
    {
        $monitoring = ApplicationMonitoringService::monitorApplication();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring['trends'],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient les alertes de l'application
     */
    public function alerts()
    {
        $monitoring = ApplicationMonitoringService::monitorApplication();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring['alerts'],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Génère un rapport de l'application
     */
    public function report(Request $request)
    {
        $hours = $request->get('hours', 24);
        $report = ApplicationMonitoringService::generateApplicationReport($hours);

        return response()->json([
            'status' => 'success',
            'data' => $report,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Nettoie les anciens logs d'application
     */
    public function cleanup(Request $request)
    {
        $daysToKeep = $request->get('days', 30);
        ApplicationMonitoringService::cleanupApplicationLogs($daysToKeep);

        return response()->json([
            'message' => "Application logs older than {$daysToKeep} days have been cleaned up",
            'cleaned_at' => now()->toISOString()
        ]);
    }

    /**
     * Exporte les métriques de l'application
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'json');
        $hours = $request->get('hours', 24);

        $report = ApplicationMonitoringService::generateApplicationReport($hours);

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
        $filename = 'application_monitoring_' . now()->format('Y-m-d_H-i-s') . '.csv';

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

            // Performances
            foreach ($report['performance'] as $metric => $value) {
                fputcsv($file, [
                    "performance.{$metric}",
                    $value,
                    $report['generated_at']
                ]);
            }

            // Tendances
            foreach ($report['trends'] as $trendType => $trends) {
                if (is_array($trends)) {
                    foreach ($trends as $trend => $value) {
                        if (is_array($value)) {
                            foreach ($value as $key => $val) {
                                fputcsv($file, [
                                    "trends.{$trendType}.{$trend}.{$key}",
                                    $val,
                                    $report['generated_at']
                                ]);
                            }
                        } else {
                            fputcsv($file, [
                                "trends.{$trendType}.{$trend}",
                                $value,
                                $report['generated_at']
                            ]);
                        }
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
