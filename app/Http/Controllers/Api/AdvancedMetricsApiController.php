<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AdvancedMetricsService;
use Illuminate\Http\Request;

class AdvancedMetricsApiController extends Controller
{
    /**
     * Obtient toutes les métriques avancées
     */
    public function index()
    {
        $metrics = AdvancedMetricsService::collectAdvancedMetrics();

        return response()->json([
            'status' => 'success',
            'data' => $metrics,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient les métriques de l'application
     */
    public function application()
    {
        $metrics = AdvancedMetricsService::collectAdvancedMetrics();

        return response()->json([
            'status' => 'success',
            'data' => $metrics['application'],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient les métriques de la base de données
     */
    public function database()
    {
        $metrics = AdvancedMetricsService::collectAdvancedMetrics();

        return response()->json([
            'status' => 'success',
            'data' => $metrics['database'],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient les métriques de performance
     */
    public function performance()
    {
        $metrics = AdvancedMetricsService::collectAdvancedMetrics();

        return response()->json([
            'status' => 'success',
            'data' => $metrics['performance'],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient les métriques de comportement utilisateur
     */
    public function userBehavior()
    {
        $metrics = AdvancedMetricsService::collectAdvancedMetrics();

        return response()->json([
            'status' => 'success',
            'data' => $metrics['user_behavior'],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient les métriques de contenu
     */
    public function content()
    {
        $metrics = AdvancedMetricsService::collectAdvancedMetrics();

        return response()->json([
            'status' => 'success',
            'data' => $metrics['content'],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient les métriques système
     */
    public function system()
    {
        $metrics = AdvancedMetricsService::collectAdvancedMetrics();

        return response()->json([
            'status' => 'success',
            'data' => $metrics['system'],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient un rapport détaillé
     */
    public function report(Request $request)
    {
        $format = $request->get('format', 'json');
        $metrics = AdvancedMetricsService::collectAdvancedMetrics();

        if ($format === 'csv') {
            return $this->exportToCsv($metrics);
        }

        return response()->json([
            'status' => 'success',
            'data' => $metrics,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Exporte les métriques en CSV
     */
    private function exportToCsv(array $metrics): \Illuminate\Http\Response
    {
        $filename = 'advanced_metrics_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($metrics) {
            $file = fopen('php://output', 'w');

            // En-têtes
            fputcsv($file, ['Category', 'Metric', 'Value', 'Timestamp']);

            // Données
            foreach ($metrics as $category => $categoryData) {
                if (is_array($categoryData)) {
                    foreach ($categoryData as $metric => $value) {
                        if (is_array($value)) {
                            foreach ($value as $subMetric => $subValue) {
                                fputcsv($file, [
                                    $category,
                                    "{$metric}.{$subMetric}",
                                    is_array($subValue) ? json_encode($subValue) : $subValue,
                                    $metrics['timestamp']
                                ]);
                            }
                        } else {
                            fputcsv($file, [
                                $category,
                                $metric,
                                $value,
                                $metrics['timestamp']
                            ]);
                        }
                    }
                } else {
                    fputcsv($file, [
                        'general',
                        $category,
                        $categoryData,
                        $metrics['timestamp']
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
