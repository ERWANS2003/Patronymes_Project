<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\NetworkMonitoringService;
use Illuminate\Http\Request;

class NetworkMonitoringApiController extends Controller
{
    /**
     * Obtient les métriques de surveillance des réseaux
     */
    public function index()
    {
        $monitoring = NetworkMonitoringService::monitorNetwork();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient les statistiques du réseau
     */
    public function statistics()
    {
        $monitoring = NetworkMonitoringService::monitorNetwork();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring['statistics'],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient les performances du réseau
     */
    public function performance()
    {
        $monitoring = NetworkMonitoringService::monitorNetwork();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring['performance'],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient les tendances du réseau
     */
    public function trends()
    {
        $monitoring = NetworkMonitoringService::monitorNetwork();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring['trends'],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient les alertes du réseau
     */
    public function alerts()
    {
        $monitoring = NetworkMonitoringService::monitorNetwork();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring['alerts'],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Génère un rapport du réseau
     */
    public function report(Request $request)
    {
        $hours = $request->get('hours', 24);
        $report = NetworkMonitoringService::generateNetworkReport($hours);

        return response()->json([
            'status' => 'success',
            'data' => $report,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Nettoie les anciens logs de réseau
     */
    public function cleanup(Request $request)
    {
        $daysToKeep = $request->get('days', 30);
        NetworkMonitoringService::cleanupNetworkLogs($daysToKeep);

        return response()->json([
            'message' => "Network logs older than {$daysToKeep} days have been cleaned up",
            'cleaned_at' => now()->toISOString()
        ]);
    }

    /**
     * Exporte les métriques du réseau
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'json');
        $hours = $request->get('hours', 24);

        $report = NetworkMonitoringService::generateNetworkReport($hours);

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
        $filename = 'network_monitoring_' . now()->format('Y-m-d_H-i-s') . '.csv';

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
                                    is_array($subSubValue) ? json_encode($subSubValue) : $subSubValue,
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
