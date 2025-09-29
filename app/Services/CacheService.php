<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class CacheService
{
    public function remember($key, $callback, $ttl = 3600)
    {
        return Cache::remember($key, $ttl, $callback);
    }

    public function forget($pattern)
    {
        $keys = Cache::getRedis()->keys("*{$pattern}*");
        if (!empty($keys)) {
            Cache::getRedis()->del($keys);
        }
    }

    public function warmUp()
    {
        // Préchargement des données fréquemment utilisées
        $this->warmUpRegions();
        $this->warmUpGroupesEthniques();
        $this->warmUpLangues();
        $this->warmUpPopularPatronymes();
    }

    private function warmUpRegions()
    {
        Cache::remember('regions_list', 3600, function () {
            return \App\Models\Region::with('provinces')->get();
        });
    }

    private function warmUpGroupesEthniques()
    {
        Cache::remember('groupes_ethniques_list', 3600, function () {
            return \App\Models\GroupeEthnique::all();
        });
    }

    private function warmUpLangues()
    {
        Cache::remember('langues_list', 3600, function () {
            return \App\Models\Langue::all();
        });
    }

    private function warmUpPopularPatronymes()
    {
        Cache::remember('popular_patronymes', 1800, function () {
            return \App\Models\Patronyme::orderBy('views_count', 'desc')
                                     ->limit(10)
                                     ->get();
        });
    }

    public function getCacheStats()
    {
        $redis = Cache::getRedis();
        return [
            'memory_usage' => $redis->info('memory')['used_memory_human'],
            'keys_count' => $redis->dbsize(),
            'hit_rate' => $this->calculateHitRate(),
        ];
    }

    private function calculateHitRate()
    {
        $redis = Cache::getRedis();
        $info = $redis->info('stats');
        $hits = $info['keyspace_hits'] ?? 0;
        $misses = $info['keyspace_misses'] ?? 0;
        $total = $hits + $misses;

        return $total > 0 ? round(($hits / $total) * 100, 2) : 0;
    }
}
