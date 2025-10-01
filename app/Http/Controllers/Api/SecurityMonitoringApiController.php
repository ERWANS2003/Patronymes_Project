<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SecurityMonitoringService;
use Illuminate\Http\Request;

class SecurityMonitoringApiController extends Controller
{
    /**
     * Obtient les métriques de surveillance de la sécurité
     */
    public function index()
    {
        $monitoring = SecurityMonitoringService::monitorSecurity();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient les statistiques de sécurité
     */
    public function statistics()
    {
        $monitoring = SecurityMonitoringService::monitorSecurity();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring['statistics'],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient les événements de sécurité récents
     */
    public function recent()
    {
        $monitoring = SecurityMonitoringService::monitorSecurity();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring['recent_events'],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient les menaces détectées
     */
    public function threats()
    {
        $monitoring = SecurityMonitoringService::monitorSecurity();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring['threats'],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient les alertes de sécurité
     */
    public function alerts()
    {
        $monitoring = SecurityMonitoringService::monitorSecurity();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring['alerts'],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Génère un rapport de sécurité
     */
    public function report(Request $request)
    {
        $hours = $request->get('hours', 24);
        $report = SecurityMonitoringService::generateSecurityReport($hours);

        return response()->json([
            'status' => 'success',
            'data' => $report,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Nettoie les anciens logs de sécurité
     */
    public function cleanup(Request $request)
    {
        $daysToKeep = $request->get('days', 90);
        SecurityMonitoringService::cleanupSecurityLogs($daysToKeep);

        return response()->json([
            'message' => "Security logs older than {$daysToKeep} days have been cleaned up",
            'cleaned_at' => now()->toISOString()
        ]);
    }

    /**
     * Exporte les métriques de sécurité
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'json');
        $hours = $request->get('hours', 24);

        $report = SecurityMonitoringService::generateSecurityReport($hours);

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
        $filename = 'security_monitoring_' . now()->format('Y-m-d_H-i-s') . '.csv';

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

            // Événements récents
            foreach ($report['recent_events'] as $event) {
                fputcsv($file, [
                    'recent_event',
                    $event['event'],
                    $event['timestamp']
                ]);
            }

            // Menaces
            foreach ($report['threats'] as $threatType => $threats) {
                foreach ($threats as $threat => $count) {
                    fputcsv($file, [
                        "threat.{$threatType}",
                        $threat,
                        $count
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
