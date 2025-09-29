<?php

namespace App\Services;

use App\Models\Patronyme;
use Illuminate\Support\Facades\Cache;

class SearchService
{
    public function search($query, $filters = [])
    {
        $cacheKey = 'search_' . md5($query . serialize($filters));
        
        return Cache::remember($cacheKey, 300, function () use ($query, $filters) {
            $search = Patronyme::query();
            
            if (!empty($query)) {
                $search->where(function ($q) use ($query) {
                    $q->where('nom', 'ILIKE', "%{$query}%")
                      ->orWhere('signification', 'ILIKE', "%{$query}%")
                      ->orWhere('origine', 'ILIKE', "%{$query}%")
                      ->orWhere('histoire', 'ILIKE', "%{$query}%");
                });
            }
            
            // Filtres
            if (isset($filters['region_id'])) {
                $search->where('region_id', $filters['region_id']);
            }
            
            if (isset($filters['groupe_ethnique_id'])) {
                $search->where('groupe_ethnique_id', $filters['groupe_ethnique_id']);
            }
            
            if (isset($filters['langue_id'])) {
                $search->where('langue_id', $filters['langue_id']);
            }
            
            return $search->with(['region', 'groupeEthnique', 'langue'])
                         ->orderBy('created_at', 'desc')
                         ->paginate(15);
        });
    }
    
    public function getSuggestions($query, $limit = 5)
    {
        $cacheKey = 'suggestions_' . md5($query);
        
        return Cache::remember($cacheKey, 600, function () use ($query, $limit) {
            return Patronyme::where('nom', 'ILIKE', "%{$query}%")
                           ->orWhere('signification', 'ILIKE', "%{$query}%")
                           ->limit($limit)
                           ->pluck('nom')
                           ->toArray();
        });
    }
    
    public function getPopularSearches($limit = 10)
    {
        return Cache::remember('popular_searches', 3600, function () use ($limit) {
            // Simulation de recherches populaires
            return Patronyme::orderBy('views_count', 'desc')
                           ->limit($limit)
                           ->pluck('nom')
                           ->toArray();
        });
    }
}
