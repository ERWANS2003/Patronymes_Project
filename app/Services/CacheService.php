<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class CacheService
{
    const CACHE_PREFIX = 'patronymes_app:';
    const DEFAULT_TTL = 3600; // 1 heure
    const SHORT_TTL = 300;    // 5 minutes
    const LONG_TTL = 86400;   // 24 heures

    /**
     * Cache avec TTL intelligent basé sur le type de données
     */
    public static function remember($key, $callback, $type = 'default')
    {
        $ttl = self::getTTL($type);
        $fullKey = self::CACHE_PREFIX . $key;

        return Cache::remember($fullKey, $ttl, $callback);
    }

    /**
     * Cache avec TTL personnalisé
     */
    public static function rememberWithTTL($key, $callback, $ttl)
    {
        $fullKey = self::CACHE_PREFIX . $key;
        return Cache::remember($fullKey, $ttl, $callback);
    }

    /**
     * Mise en cache avec tags pour invalidation groupée
     */
    public static function rememberWithTags($key, $callback, $tags = [], $ttl = null)
    {
        $fullKey = self::CACHE_PREFIX . $key;
        $ttl = $ttl ?? self::DEFAULT_TTL;

        if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
            return Cache::tags($tags)->remember($fullKey, $ttl, $callback);
        }

        return Cache::remember($fullKey, $ttl, $callback);
    }

    /**
     * Invalidation de cache par pattern
     */
    public static function forgetPattern($pattern)
    {
        $fullPattern = self::CACHE_PREFIX . $pattern;

        if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
            $keys = Redis::keys($fullPattern);
            if (!empty($keys)) {
                Redis::del($keys);
            }
        } else {
            // Pour les autres stores, on ne peut pas faire de pattern matching
            // On utilise une approche avec des tags ou des clés connues
            self::forgetKnownKeys($pattern);
        }
    }

    /**
     * Invalidation de cache par tags
     */
    public static function forgetByTags($tags)
    {
        if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
            Cache::tags($tags)->flush();
        }
    }

    /**
     * Warm up du cache avec les données fréquemment utilisées
     */
    public static function warmUp()
    {
        $warmUpTasks = [
            'regions_list' => function () {
                return \App\Models\Region::orderBy('name')->get();
            },
            'groupes_ethniques_list' => function () {
                return \App\Models\GroupeEthnique::orderBy('nom')->get();
            },
            'langues_list' => function () {
                return \App\Models\Langue::orderBy('nom')->get();
            },
            'ethnies_list' => function () {
                return \App\Models\Ethnie::orderBy('nom')->get();
            },
            'popular_patronymes_10' => function () {
                return \App\Models\Patronyme::orderBy('views_count', 'desc')->limit(10)->get();
            },
            'recent_patronymes_10' => function () {
                return \App\Models\Patronyme::orderBy('created_at', 'desc')->limit(10)->get();
            },
            'featured_patronymes_5' => function () {
                return \App\Models\Patronyme::where('is_featured', true)->limit(5)->get();
            }
        ];

        foreach ($warmUpTasks as $key => $callback) {
            self::remember($key, $callback, 'long');
        }
    }

    /**
     * Statistiques du cache
     */
    public static function getStats()
    {
        if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
            $info = Redis::info();
            return [
                'used_memory' => $info['used_memory_human'] ?? 'N/A',
                'connected_clients' => $info['connected_clients'] ?? 'N/A',
                'total_commands_processed' => $info['total_commands_processed'] ?? 'N/A',
                'keyspace_hits' => $info['keyspace_hits'] ?? 'N/A',
                'keyspace_misses' => $info['keyspace_misses'] ?? 'N/A',
                'hit_rate' => isset($info['keyspace_hits'], $info['keyspace_misses'])
                    ? round($info['keyspace_hits'] / ($info['keyspace_hits'] + $info['keyspace_misses']) * 100, 2)
                    : 'N/A'
            ];
        }

        return ['driver' => 'file', 'stats' => 'Not available for file cache'];
    }

    /**
     * Nettoyage du cache
     */
    public static function clear()
    {
        if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
            $keys = Redis::keys(self::CACHE_PREFIX . '*');
            if (!empty($keys)) {
                Redis::del($keys);
            }
        } else {
            Cache::flush();
        }
    }

    /**
     * Détermine le TTL basé sur le type de données
     */
    private static function getTTL($type)
    {
        return match($type) {
            'short' => self::SHORT_TTL,
            'long' => self::LONG_TTL,
            'search' => 300,      // 5 minutes pour les recherches
            'suggestions' => 600, // 10 minutes pour les suggestions
            'statistics' => 1800, // 30 minutes pour les statistiques
            'reference' => 3600,  // 1 heure pour les données de référence
            default => self::DEFAULT_TTL
        };
    }

    /**
     * Invalidation de clés connues (fallback pour les stores non-Redis)
     */
    private static function forgetKnownKeys($pattern)
    {
        $knownKeys = [
            'regions_list',
            'groupes_ethniques_list',
            'langues_list',
            'ethnies_list',
            'popular_patronymes_*',
            'recent_patronymes_*',
            'featured_patronymes_*',
            'search_*',
            'suggestions_*',
            'statistics_*'
        ];

        foreach ($knownKeys as $key) {
            if (fnmatch($pattern, $key)) {
                Cache::forget(self::CACHE_PREFIX . $key);
            }
        }
    }

    /**
     * Cache conditionnel basé sur l'environnement
     */
    public static function rememberIf($condition, $key, $callback, $type = 'default')
    {
        if ($condition && !app()->environment('testing')) {
            return self::remember($key, $callback, $type);
        }

        return $callback();
    }
}
