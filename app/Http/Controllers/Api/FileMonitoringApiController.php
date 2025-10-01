<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FileMonitoringService;
use Illuminate\Http\Request;

class FileMonitoringApiController extends Controller
{
    /**
     * Obtient les métriques de surveillance des fichiers
     */
    public function index()
    {
        $monitoring = FileMonitoringService::monitorFiles();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient les statistiques des fichiers
     */
    public function statistics()
    {
        $monitoring = FileMonitoringService::monitorFiles();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring['statistics'],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient les permissions des fichiers
     */
    public function permissions()
    {
        $monitoring = FileMonitoringService::monitorFiles();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring['permissions'],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient les tendances des fichiers
     */
    public function trends()
    {
        $monitoring = FileMonitoringService::monitorFiles();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring['trends'],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient les alertes des fichiers
     */
    public function alerts()
    {
        $monitoring = FileMonitoringService::monitorFiles();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring['alerts'],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Génère un rapport des fichiers
     */
    public function report(Request $request)
    {
        $hours = $request->get('hours', 24);
        $report = FileMonitoringService::generateFileReport($hours);

        return response()->json([
            'status' => 'success',
            'data' => $report,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Nettoie les anciens fichiers
     */
    public function cleanup(Request $request)
    {
        $daysToKeep = $request->get('days', 30);
        FileMonitoringService::cleanupOldFiles($daysToKeep);

        return response()->json([
            'message' => "Files older than {$daysToKeep} days have been cleaned up",
            'cleaned_at' => now()->toISOString()
        ]);
    }

    /**
     * Exporte les métriques des fichiers
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'json');
        $hours = $request->get('hours', 24);

        $report = FileMonitoringService::generateFileReport($hours);

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
        $filename = 'file_monitoring_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($report) {
            $file = fopen('php://output', 'w');

            // En-têtes
            fputcsv($file, ['Metric', 'Value', 'Timestamp']);

            // Statistiques
            foreach ($report['summary'] as $directory => $stats) {
                if (is_array($stats)) {
                    foreach ($stats as $stat => $value) {
                        fputcsv($file, [
                            "summary.{$directory}.{$stat}",
                            $value,
                            $report['generated_at']
                        ]);
                    }
                }
            }

            // Permissions
            foreach ($report['permissions'] as $path => $permissions) {
                if (is_array($permissions)) {
                    foreach ($permissions as $permission => $value) {
                        fputcsv($file, [
                            "permissions.{$path}.{$permission}",
                            $value,
                            $report['generated_at']
                        ]);
                    }
                }
            }

            // Tendances
            foreach ($report['trends'] as $directory => $trends) {
                if (is_array($trends)) {
                    foreach ($trends as $trendType => $trend) {
                        if (is_array($trend)) {
                            foreach ($trend as $key => $value) {
                                fputcsv($file, [
                                    "trends.{$directory}.{$trendType}.{$key}",
                                    $value,
                                    $report['generated_at']
                                ]);
                            }
                        } else {
                            fputcsv($file, [
                                "trends.{$directory}.{$trendType}",
                                $trend,
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
