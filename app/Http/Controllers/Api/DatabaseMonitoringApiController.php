<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DatabaseMonitoringService;
use Illuminate\Http\Request;

class DatabaseMonitoringApiController extends Controller
{
    /**
     * Obtient les métriques de surveillance de la base de données
     */
    public function index()
    {
        $monitoring = DatabaseMonitoringService::monitorDatabase();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient les statistiques de la base de données
     */
    public function statistics()
    {
        $monitoring = DatabaseMonitoringService::monitorDatabase();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring['statistics'],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient les requêtes lentes
     */
    public function slowQueries()
    {
        $monitoring = DatabaseMonitoringService::monitorDatabase();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring['slow_queries'],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient les statistiques de connexion
     */
    public function connections()
    {
        $monitoring = DatabaseMonitoringService::monitorDatabase();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring['connections'],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient les alertes de base de données
     */
    public function alerts()
    {
        $monitoring = DatabaseMonitoringService::monitorDatabase();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring['alerts'],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Génère un rapport de base de données
     */
    public function report(Request $request)
    {
        $hours = $request->get('hours', 24);
        $report = DatabaseMonitoringService::generateDatabaseReport($hours);

        return response()->json([
            'status' => 'success',
            'data' => $report,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Nettoie les anciens logs de base de données
     */
    public function cleanup(Request $request)
    {
        $daysToKeep = $request->get('days', 30);
        DatabaseMonitoringService::cleanupDatabaseLogs($daysToKeep);

        return response()->json([
            'message' => "Database logs older than {$daysToKeep} days have been cleaned up",
            'cleaned_at' => now()->toISOString()
        ]);
    }

    /**
     * Exporte les métriques de base de données
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'json');
        $hours = $request->get('hours', 24);

        $report = DatabaseMonitoringService::generateDatabaseReport($hours);

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
        $filename = 'database_monitoring_' . now()->format('Y-m-d_H-i-s') . '.csv';

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

            // Requêtes lentes
            if (isset($report['slow_queries']) && is_array($report['slow_queries'])) {
                foreach ($report['slow_queries'] as $query) {
                    if (is_array($query)) {
                        foreach ($query as $key => $value) {
                            fputcsv($file, [
                                "slow_query.{$key}",
                                $value,
                                $report['generated_at']
                            ]);
                        }
                    }
                }
            }

            // Connexions
            foreach ($report['connections'] as $metric => $value) {
                fputcsv($file, [
                    "connection.{$metric}",
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
