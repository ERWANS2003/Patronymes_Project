<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\QueueMonitoringService;
use Illuminate\Http\Request;

class QueueMonitoringApiController extends Controller
{
    /**
     * Obtient les métriques de surveillance des files d'attente
     */
    public function index()
    {
        $monitoring = QueueMonitoringService::monitorQueues();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient les statistiques des files d'attente
     */
    public function statistics()
    {
        $monitoring = QueueMonitoringService::monitorQueues();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring['statistics'],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient les jobs des files d'attente
     */
    public function jobs()
    {
        $monitoring = QueueMonitoringService::monitorQueues();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring['jobs'],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient les performances des files d'attente
     */
    public function performance()
    {
        $monitoring = QueueMonitoringService::monitorQueues();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring['performance'],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient les alertes des files d'attente
     */
    public function alerts()
    {
        $monitoring = QueueMonitoringService::monitorQueues();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring['alerts'],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Génère un rapport des files d'attente
     */
    public function report(Request $request)
    {
        $hours = $request->get('hours', 24);
        $report = QueueMonitoringService::generateQueueReport($hours);

        return response()->json([
            'status' => 'success',
            'data' => $report,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Nettoie les jobs échoués
     */
    public function cleanup()
    {
        QueueMonitoringService::cleanupFailedJobs();

        return response()->json([
            'message' => 'Failed jobs cleaned up successfully',
            'cleaned_at' => now()->toISOString()
        ]);
    }

    /**
     * Exporte les métriques des files d'attente
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'json');
        $hours = $request->get('hours', 24);

        $report = QueueMonitoringService::generateQueueReport($hours);

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
        $filename = 'queue_monitoring_' . now()->format('Y-m-d_H-i-s') . '.csv';

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
                        if (is_array($subValue)) {
                            foreach ($subValue as $subSubMetric => $subSubValue) {
                                fputcsv($file, [
                                    "summary.{$metric}.{$subMetric}.{$subSubMetric}",
                                    $subSubValue,
                                    $report['generated_at']
                                ]);
                            }
                        } else {
                            fputcsv($file, [
                                "summary.{$metric}.{$subMetric}",
                                $subValue,
                                $report['generated_at']
                            ]);
                        }
                    }
                } else {
                    fputcsv($file, [
                        "summary.{$metric}",
                        $value,
                        $report['generated_at']
                    ]);
                }
            }

            // Jobs
            if (isset($report['jobs']) && is_array($report['jobs'])) {
                foreach ($report['jobs'] as $job) {
                    if (is_array($job)) {
                        foreach ($job as $key => $value) {
                            fputcsv($file, [
                                "job.{$key}",
                                is_array($value) ? json_encode($value) : $value,
                                $report['generated_at']
                            ]);
                        }
                    }
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
