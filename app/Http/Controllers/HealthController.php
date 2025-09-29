<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class HealthController extends Controller
{
    public function check()
    {
        $health = [
            'status' => 'healthy',
            'timestamp' => now()->toISOString(),
            'checks' => []
        ];
        
        // Vérification de la base de données
        try {
            DB::connection()->getPdo();
            $health['checks']['database'] = [
                'status' => 'ok',
                'message' => 'Database connection successful'
            ];
        } catch (\Exception $e) {
            $health['status'] = 'unhealthy';
            $health['checks']['database'] = [
                'status' => 'error',
                'message' => 'Database connection failed: ' . $e->getMessage()
            ];
        }
        
        // Vérification du cache
        try {
            Cache::put('health_check', 'ok', 60);
            $cacheStatus = Cache::get('health_check') === 'ok';
            $health['checks']['cache'] = [
                'status' => $cacheStatus ? 'ok' : 'error',
                'message' => $cacheStatus ? 'Cache working properly' : 'Cache not working'
            ];
        } catch (\Exception $e) {
            $health['status'] = 'unhealthy';
            $health['checks']['cache'] = [
                'status' => 'error',
                'message' => 'Cache error: ' . $e->getMessage()
            ];
        }
        
        // Vérification du stockage
        try {
            Storage::disk('local')->put('health_check.txt', 'ok');
            $storageStatus = Storage::disk('local')->get('health_check.txt') === 'ok';
            Storage::disk('local')->delete('health_check.txt');
            $health['checks']['storage'] = [
                'status' => $storageStatus ? 'ok' : 'error',
                'message' => $storageStatus ? 'Storage working properly' : 'Storage not working'
            ];
        } catch (\Exception $e) {
            $health['status'] = 'unhealthy';
            $health['checks']['storage'] = [
                'status' => 'error',
                'message' => 'Storage error: ' . $e->getMessage()
            ];
        }
        
        // Statistiques système
        $health['system'] = [
            'memory_usage' => memory_get_usage(true),
            'memory_peak' => memory_get_peak_usage(true),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
        ];
        
        return response()->json($health, $health['status'] === 'healthy' ? 200 : 503);
    }
    
    public function metrics()
    {
        return response()->json([
            'patronymes_count' => \App\Models\Patronyme::count(),
            'users_count' => \App\Models\User::count(),
            'regions_count' => \App\Models\Region::count(),
            'cache_hit_rate' => $this->getCacheHitRate(),
            'memory_usage' => memory_get_usage(true),
            'uptime' => $this->getUptime(),
        ]);
    }
    
    private function getCacheHitRate()
    {
        try {
            $redis = Cache::getRedis();
            $info = $redis->info('stats');
            $hits = $info['keyspace_hits'] ?? 0;
            $misses = $info['keyspace_misses'] ?? 0;
            $total = $hits + $misses;
            
            return $total > 0 ? round(($hits / $total) * 100, 2) : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }
    
    private function getUptime()
    {
        try {
            $uptime = shell_exec('uptime');
            return trim($uptime);
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }
}
