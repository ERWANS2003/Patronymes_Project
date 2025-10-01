<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ErrorMonitoringService;
use Illuminate\Http\Request;

class ErrorMonitoringApiController extends Controller
{
    /**
     * Obtient les métriques de surveillance des erreurs
     */
    public function index()
    {
        $monitoring = ErrorMonitoringService::monitorErrors();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient les statistiques d'erreurs
     */
    public function statistics()
    {
        $monitoring = ErrorMonitoringService::monitorErrors();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring['statistics'],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient les erreurs récentes
     */
    public function recent()
    {
        $monitoring = ErrorMonitoringService::monitorErrors();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring['recent_errors'],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient les tendances d'erreurs
     */
    public function trends()
    {
        $monitoring = ErrorMonitoringService::monitorErrors();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring['trends'],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient les alertes d'erreurs
     */
    public function alerts()
    {
        $monitoring = ErrorMonitoringService::monitorErrors();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring['alerts'],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Génère un rapport d'erreurs
     */
    public function report(Request $request)
    {
        $hours = $request->get('hours', 24);
        $report = ErrorMonitoringService::generateErrorReport($hours);

        return response()->json([
            'status' => 'success',
            'data' => $report,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Nettoie les anciens logs d'erreurs
     */
    public function cleanup(Request $request)
    {
        $daysToKeep = $request->get('days', 30);
        ErrorMonitoringService::cleanupErrorLogs($daysToKeep);

        return response()->json([
            'message' => "Error logs older than {$daysToKeep} days have been cleaned up",
            'cleaned_at' => now()->toISOString()
        ]);
    }

    /**
     * Exporte les métriques d'erreurs
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'json');
        $hours = $request->get('hours', 24);

        $report = ErrorMonitoringService::generateErrorReport($hours);

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
        $filename = 'error_monitoring_' . now()->format('Y-m-d_H-i-s') . '.csv';

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
                            $subValue,
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

            // Erreurs récentes
            foreach ($report['recent_errors'] as $error) {
                fputcsv($file, [
                    'recent_error',
                    $error['message'],
                    $error['timestamp']
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
