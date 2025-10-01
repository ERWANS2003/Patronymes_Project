<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\MonitoringService;
use Illuminate\Support\Facades\Log;

class PerformanceMonitoringMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage(true);

        // Log de l'activité utilisateur
        if (auth()->check()) {
            MonitoringService::logUserActivity('request_started', [
                'route' => $request->route()?->getName(),
                'method' => $request->method(),
                'url' => $request->fullUrl()
            ]);
        }

        $response = $next($request);

        $endTime = microtime(true);
        $endMemory = memory_get_usage(true);

        $executionTime = $endTime - $startTime;
        $memoryUsed = $endMemory - $startMemory;

        // Log des métriques de performance
        MonitoringService::logPerformanceMetrics(
            'http_request',
            $executionTime,
            [
                'route' => $request->route()?->getName(),
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'status_code' => $response->getStatusCode(),
                'memory_used_mb' => round($memoryUsed / 1024 / 1024, 2),
                'user_id' => auth()->id(),
                'ip_address' => $request->ip()
            ]
        );

        // Ajouter des headers de performance
        $response->headers->set('X-Response-Time', round($executionTime * 1000, 3) . 'ms');
        $response->headers->set('X-Memory-Usage', round($memoryUsed / 1024 / 1024, 2) . 'MB');

        return $response;
    }
}
