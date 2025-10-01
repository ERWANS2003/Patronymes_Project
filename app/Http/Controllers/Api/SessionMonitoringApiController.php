<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SessionMonitoringService;
use Illuminate\Http\Request;

class SessionMonitoringApiController extends Controller
{
    /**
     * Obtient les métriques de surveillance des sessions
     */
    public function index()
    {
        $monitoring = SessionMonitoringService::monitorSessions();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient les statistiques des sessions
     */
    public function statistics()
    {
        $monitoring = SessionMonitoringService::monitorSessions();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring['statistics'],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient les sessions actives
     */
    public function active()
    {
        $monitoring = SessionMonitoringService::monitorSessions();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring['active_sessions'],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient les tendances des sessions
     */
    public function trends()
    {
        $monitoring = SessionMonitoringService::monitorSessions();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring['trends'],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient les alertes des sessions
     */
    public function alerts()
    {
        $monitoring = SessionMonitoringService::monitorSessions();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring['alerts'],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Génère un rapport des sessions
     */
    public function report(Request $request)
    {
        $hours = $request->get('hours', 24);
        $report = SessionMonitoringService::generateSessionReport($hours);

        return response()->json([
            'status' => 'success',
            'data' => $report,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Nettoie les sessions expirées
     */
    public function cleanup()
    {
        SessionMonitoringService::cleanupExpiredSessions();

        return response()->json([
            'message' => 'Expired sessions cleaned up successfully',
            'cleaned_at' => now()->toISOString()
        ]);
    }

    /**
     * Exporte les métriques des sessions
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'json');
        $hours = $request->get('hours', 24);

        $report = SessionMonitoringService::generateSessionReport($hours);

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
        $filename = 'session_monitoring_' . now()->format('Y-m-d_H-i-s') . '.csv';

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

            // Sessions actives
            foreach ($report['active_sessions'] as $session) {
                if (is_array($session)) {
                    foreach ($session as $key => $value) {
                        fputcsv($file, [
                            "active_session.{$key}",
                            $value,
                            $report['generated_at']
                        ]);
                    }
                }
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
