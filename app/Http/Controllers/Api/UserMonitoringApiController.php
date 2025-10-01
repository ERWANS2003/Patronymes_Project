<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UserMonitoringService;
use Illuminate\Http\Request;

class UserMonitoringApiController extends Controller
{
    /**
     * Obtient les métriques de surveillance des utilisateurs
     */
    public function index()
    {
        $monitoring = UserMonitoringService::monitorUsers();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient les statistiques des utilisateurs
     */
    public function statistics()
    {
        $monitoring = UserMonitoringService::monitorUsers();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring['statistics'],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient l'activité des utilisateurs
     */
    public function activity()
    {
        $monitoring = UserMonitoringService::monitorUsers();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring['activity'],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient l'engagement des utilisateurs
     */
    public function engagement()
    {
        $monitoring = UserMonitoringService::monitorUsers();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring['engagement'],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtient les alertes des utilisateurs
     */
    public function alerts()
    {
        $monitoring = UserMonitoringService::monitorUsers();

        return response()->json([
            'status' => 'success',
            'data' => $monitoring['alerts'],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Génère un rapport des utilisateurs
     */
    public function report(Request $request)
    {
        $hours = $request->get('hours', 24);
        $report = UserMonitoringService::generateUserReport($hours);

        return response()->json([
            'status' => 'success',
            'data' => $report,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Nettoie les anciens logs d'utilisateurs
     */
    public function cleanup(Request $request)
    {
        $daysToKeep = $request->get('days', 30);
        UserMonitoringService::cleanupUserLogs($daysToKeep);

        return response()->json([
            'message' => "User logs older than {$daysToKeep} days have been cleaned up",
            'cleaned_at' => now()->toISOString()
        ]);
    }

    /**
     * Exporte les métriques des utilisateurs
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'json');
        $hours = $request->get('hours', 24);

        $report = UserMonitoringService::generateUserReport($hours);

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
        $filename = 'user_monitoring_' . now()->format('Y-m-d_H-i-s') . '.csv';

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

            // Activité
            foreach ($report['activity'] as $activityType => $activities) {
                if (is_array($activities)) {
                    foreach ($activities as $activity) {
                        if (is_array($activity)) {
                            foreach ($activity as $key => $value) {
                                fputcsv($file, [
                                    "activity.{$activityType}.{$key}",
                                    $value,
                                    $report['generated_at']
                                ]);
                            }
                        } else {
                            fputcsv($file, [
                                "activity.{$activityType}",
                                $activity,
                                $report['generated_at']
                            ]);
                        }
                    }
                }
            }

            // Engagement
            foreach ($report['engagement'] as $engagementType => $engagements) {
                if (is_array($engagements)) {
                    foreach ($engagements as $engagement) {
                        if (is_array($engagement)) {
                            foreach ($engagement as $key => $value) {
                                fputcsv($file, [
                                    "engagement.{$engagementType}.{$key}",
                                    $value,
                                    $report['generated_at']
                                ]);
                            }
                        } else {
                            fputcsv($file, [
                                "engagement.{$engagementType}",
                                $engagement,
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
