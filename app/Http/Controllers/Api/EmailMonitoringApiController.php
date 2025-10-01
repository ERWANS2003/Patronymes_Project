<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\EmailMonitoringService;
use Illuminate\Http\Request;

class EmailMonitoringApiController extends Controller
{
    /**
     * Obtient les métriques de surveillance des emails
     */
    public function index()
    {
        $monitoring = EmailMonitoringService::monitorEmails();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient les statistiques des emails
     */
    public function statistics()
    {
        $monitoring = EmailMonitoringService::monitorEmails();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring['statistics'],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient les performances des emails
     */
    public function performance()
    {
        $monitoring = EmailMonitoringService::monitorEmails();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring['performance'],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient les tendances des emails
     */
    public function trends()
    {
        $monitoring = EmailMonitoringService::monitorEmails();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring['trends'],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient les alertes des emails
     */
    public function alerts()
    {
        $monitoring = EmailMonitoringService::monitorEmails();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring['alerts'],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Génère un rapport des emails
     */
    public function report(Request $request)
    {
        $hours = $request->get('hours', 24);
        $report = EmailMonitoringService::generateEmailReport($hours);

        return response()->json([
            'status' => 'success',
            'data' => $report,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Nettoie les anciens logs d'emails
     */
    public function cleanup(Request $request)
    {
        $daysToKeep = $request->get('days', 30);
        EmailMonitoringService::cleanupEmailLogs($daysToKeep);

        return response()->json([
            'message' => "Email logs older than {$daysToKeep} days have been cleaned up",
            'cleaned_at' => now()->toISOString()
        ]);
    }

    /**
     * Exporte les métriques des emails
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'json');
        $hours = $request->get('hours', 24);

        $report = EmailMonitoringService::generateEmailReport($hours);

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
        $filename = 'email_monitoring_' . now()->format('Y-m-d_H-i-s') . '.csv';

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
