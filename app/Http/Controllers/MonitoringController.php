<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MonitoringService;
use App\Services\StatisticsService;
use App\Services\CacheService;

class MonitoringController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    /**
     * Affiche le tableau de bord de monitoring
     */
    public function dashboard()
    {
        $systemMetrics = MonitoringService::collectSystemMetrics();
        $healthCheck = MonitoringService::healthCheck();
        $performanceReport = MonitoringService::generatePerformanceReport(24);

        return view('admin.monitoring.dashboard', compact(
            'systemMetrics',
            'healthCheck',
            'performanceReport'
        ));
    }

    /**
     * Endpoint API pour la santé de l'application
     */
    public function health()
    {
        $health = MonitoringService::healthCheck();

        return response()->json($health, $health['status'] === 'healthy' ? 200 : 503);
    }

    /**
     * Endpoint API pour les métriques système
     */
    public function metrics()
    {
        $metrics = MonitoringService::collectSystemMetrics();

        return response()->json($metrics);
    }

    /**
     * Endpoint API pour le rapport de performance
     */
    public function performanceReport(Request $request)
    {
        $hours = $request->get('hours', 24);
        $report = MonitoringService::generatePerformanceReport($hours);

        return response()->json($report);
    }

    /**
     * Nettoie les anciens logs
     */
    public function cleanupLogs(Request $request)
    {
        $daysToKeep = $request->get('days', 30);
        MonitoringService::cleanupLogs($daysToKeep);

        return response()->json([
            'message' => "Logs older than {$daysToKeep} days have been cleaned up",
            'cleaned_at' => now()->toISOString()
        ]);
    }

    /**
     * Affiche les logs en temps réel
     */
    public function logs(Request $request)
    {
        $logType = $request->get('type', 'laravel');
        $lines = $request->get('lines', 100);

        $logFile = storage_path("logs/{$logType}.log");

        if (!file_exists($logFile)) {
            return response()->json(['error' => 'Log file not found'], 404);
        }

        $logs = $this->getLastLines($logFile, $lines);

        return response()->json([
            'log_type' => $logType,
            'lines_count' => count($logs),
            'logs' => $logs
        ]);
    }

    /**
     * Exporte les métriques
     */
    public function exportMetrics(Request $request)
    {
        $format = $request->get('format', 'json');
        $hours = $request->get('hours', 24);

        $data = [
            'exported_at' => now()->toISOString(),
            'period_hours' => $hours,
            'system_metrics' => MonitoringService::collectSystemMetrics(),
            'health_check' => MonitoringService::healthCheck(),
            'performance_report' => MonitoringService::generatePerformanceReport($hours)
        ];

        if ($format === 'csv') {
            return $this->exportToCsv($data);
        }

        return response()->json($data);
    }

    /**
     * Obtient les dernières lignes d'un fichier
     */
    private function getLastLines(string $file, int $lines): array
    {
        $fileContent = file($file);
        return array_slice($fileContent, -$lines);
    }

    /**
     * Exporte les données en CSV
     */
    private function exportToCsv(array $data): \Illuminate\Http\Response
    {
        $filename = 'monitoring_metrics_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');

            // En-têtes
            fputcsv($file, ['Metric', 'Value', 'Timestamp']);

            // Données système
            foreach ($data['system_metrics'] as $category => $metrics) {
                if (is_array($metrics)) {
                    foreach ($metrics as $key => $value) {
                        fputcsv($file, [
                            "system.{$category}.{$key}",
                            is_array($value) ? json_encode($value) : $value,
                            $data['exported_at']
                        ]);
                    }
                } else {
                    fputcsv($file, [
                        "system.{$category}",
                        $metrics,
                        $data['exported_at']
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
