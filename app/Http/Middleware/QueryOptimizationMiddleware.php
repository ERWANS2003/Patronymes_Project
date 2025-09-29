<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QueryOptimizationMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Activer le logging des requêtes en mode debug
        if (config('app.debug')) {
            DB::enableQueryLog();
        }
        
        $response = $next($request);
        
        // Analyser les requêtes en mode debug
        if (config('app.debug')) {
            $queries = DB::getQueryLog();
            $this->analyzeQueries($queries);
        }
        
        return $response;
    }
    
    private function analyzeQueries($queries)
    {
        $slowQueries = [];
        $duplicateQueries = [];
        $queryCounts = [];
        
        foreach ($queries as $query) {
            $sql = $query['query'];
            $time = $query['time'];
            
            // Détecter les requêtes lentes (> 100ms)
            if ($time > 100) {
                $slowQueries[] = [
                    'sql' => $sql,
                    'time' => $time,
                    'bindings' => $query['bindings']
                ];
            }
            
            // Compter les requêtes similaires
            $normalizedSql = $this->normalizeSql($sql);
            $queryCounts[$normalizedSql] = ($queryCounts[$normalizedSql] ?? 0) + 1;
        }
        
        // Détecter les requêtes dupliquées
        foreach ($queryCounts as $sql => $count) {
            if ($count > 1) {
                $duplicateQueries[] = [
                    'sql' => $sql,
                    'count' => $count
                ];
            }
        }
        
        // Logger les problèmes détectés
        if (!empty($slowQueries)) {
            \Log::warning('Slow queries detected', $slowQueries);
        }
        
        if (!empty($duplicateQueries)) {
            \Log::warning('Duplicate queries detected', $duplicateQueries);
        }
    }
    
    private function normalizeSql($sql)
    {
        // Normaliser les requêtes pour détecter les doublons
        return preg_replace('/\b\d+\b/', '?', $sql);
    }
}
