<?php

namespace App\Services;

use App\Models\Patronyme;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class SearchService
{
    public function search($query, $filters = [])
    {
        $cacheKey = 'search_' . md5($query . serialize($filters));

        return Cache::remember($cacheKey, 300, function () use ($query, $filters) {
            $search = Patronyme::query();

            if (!empty($query)) {
                $search->where(function ($q) use ($query) {
                    // Recherche optimisée avec priorité sur le début du nom
                    $q->where('nom', 'LIKE', "{$query}%") // Commence par la recherche
                      ->orWhere('nom', 'LIKE', "%{$query}%") // Contient la recherche
                      ->orWhere('signification', 'LIKE', "%{$query}%")
                      ->orWhere('origine', 'LIKE', "%{$query}%")
                      ->orWhere('histoire', 'LIKE', "%{$query}%")
                      ->orWhere('totem', 'LIKE', "%{$query}%")
                      ->orWhere('justification_totem', 'LIKE', "%{$query}%")
                      ->orWhere('parents_plaisanterie', 'LIKE', "%{$query}%");
                });
            }

            // Appliquer tous les filtres disponibles
            $search->advancedSearch($filters);

            return $search->with(['region', 'province', 'commune', 'groupeEthnique', 'ethnie', 'langue'])
                         ->orderByRaw("CASE WHEN nom LIKE ? THEN 1 ELSE 2 END", ["{$query}%"])
                         ->orderBy('views_count', 'desc')
                         ->orderBy('created_at', 'desc')
                         ->paginate(15);
        });
    }

    public function getSuggestions($query, $limit = 5)
    {
        $cacheKey = 'suggestions_' . md5($query);

        return Cache::remember($cacheKey, 600, function () use ($query, $limit) {
            return Patronyme::where('nom', 'LIKE', "%{$query}%")
                           ->orWhere('signification', 'LIKE', "%{$query}%")
                           ->limit($limit)
                           ->pluck('nom')
                           ->toArray();
        });
    }

    public function getPopularSearches($limit = 10)
    {
        return Cache::remember('popular_searches', 3600, function () use ($limit) {
            return Patronyme::orderBy('views_count', 'desc')
                           ->limit($limit)
                           ->pluck('nom')
                           ->toArray();
        });
    }

    public function getAdvancedSuggestions($query, $limit = 15)
    {
        $cacheKey = 'advanced_suggestions_' . md5($query);

        return Cache::remember($cacheKey, 300, function () use ($query, $limit) {
            $suggestions = [];

            // Recherche exacte d'abord (commence par)
            $exactMatches = Patronyme::where('nom', 'LIKE', "{$query}%")
                ->select('nom', 'signification')
                ->limit(5)
                ->get();

            foreach ($exactMatches as $patronyme) {
                $suggestions[] = [
                    'type' => 'patronyme',
                    'value' => $patronyme->nom,
                    'label' => $patronyme->nom,
                    'description' => $patronyme->signification,
                    'priority' => 1
                ];
            }

            // Recherche phonétique pour les patronymes burkinabés
            $phoneticMatches = $this->getPhoneticMatches($query, 5);
            foreach ($phoneticMatches as $patronyme) {
                $suggestions[] = [
                    'type' => 'patronyme',
                    'value' => $patronyme->nom,
                    'label' => $patronyme->nom,
                    'description' => $patronyme->signification,
                    'priority' => 2
                ];
            }

            // Recherche fuzzy (similarité)
            if (count($suggestions) < $limit) {
                $fuzzyMatches = $this->getFuzzyMatches($query, $limit - count($suggestions));
                foreach ($fuzzyMatches as $patronyme) {
                    $suggestions[] = [
                        'type' => 'patronyme',
                        'value' => $patronyme->nom,
                        'label' => $patronyme->nom,
                        'description' => $patronyme->signification,
                        'priority' => 3
                    ];
                }
            }

            // Recherche dans les régions
            $regions = DB::table('regions')
                ->where('name', 'LIKE', "%{$query}%")
                ->limit(3)
                ->get();

            foreach ($regions as $region) {
                $suggestions[] = [
                    'type' => 'region',
                    'value' => $region->name,
                    'label' => "Région: {$region->name}",
                    'description' => null,
                    'priority' => 4
                ];
            }

            // Trier par priorité
            usort($suggestions, function($a, $b) {
                return $a['priority'] <=> $b['priority'];
            });

            return array_slice($suggestions, 0, $limit);
        });
    }

    /**
     * Recherche phonétique pour les patronymes burkinabés
     */
    private function getPhoneticMatches($query, $limit = 5)
    {
        $query = strtolower(trim($query));

        // Variations phonétiques communes
        $phoneticVariations = [
            'ou' => ['u', 'w'],
            'é' => ['e'],
            'è' => ['e'],
            'à' => ['a'],
            'ù' => ['u'],
            'ç' => ['c'],
            'e' => ['é', 'è'],
            'a' => ['à'],
            'u' => ['ou', 'ù'],
            'c' => ['ç']
        ];

        $variations = [$query];
        foreach ($phoneticVariations as $from => $to) {
            if (strpos($query, $from) !== false) {
                foreach ($to as $replacement) {
                    $variations[] = str_replace($from, $replacement, $query);
                }
            }
        }

        $results = collect();
        foreach ($variations as $variation) {
            $matches = Patronyme::where('nom', 'LIKE', "{$variation}%")
                ->orWhere('nom', 'LIKE', "%{$variation}%")
                ->select('nom', 'signification')
                ->limit($limit)
                ->get();
            $results = $results->merge($matches);
        }

        return $results->unique('nom')->take($limit);
    }

    /**
     * Recherche fuzzy avec similarité
     */
    private function getFuzzyMatches($query, $limit = 5)
    {
        $allPatronymes = Patronyme::select('nom', 'signification')->get();
        $similarNames = [];

        foreach ($allPatronymes as $patronyme) {
            $similarity = $this->calculateSimilarity($query, $patronyme->nom);
            if ($similarity > 0.6) { // Seuil de similarité
                $similarNames[] = [
                    'patronyme' => $patronyme,
                    'similarity' => $similarity
                ];
            }
        }

        // Trier par similarité
        usort($similarNames, function($a, $b) {
            return $b['similarity'] <=> $a['similarity'];
        });

        return collect(array_slice($similarNames, 0, $limit))->pluck('patronyme');
    }

    /**
     * Calcul de similarité entre deux chaînes
     */
    private function calculateSimilarity($str1, $str2)
    {
        $str1 = strtolower($str1);
        $str2 = strtolower($str2);

        $distance = levenshtein($str1, $str2);
        $maxLength = max(strlen($str1), strlen($str2));

        return $maxLength > 0 ? 1 - ($distance / $maxLength) : 0;
    }

    public function getSearchAnalytics()
    {
        return Cache::remember('search_analytics', 1800, function () {
            return [
                'total_searches' => DB::table('search_logs')->count(),
                'popular_queries' => DB::table('search_logs')
                    ->select('query', DB::raw('count(*) as count'))
                    ->groupBy('query')
                    ->orderBy('count', 'desc')
                    ->limit(10)
                    ->get(),
                'search_trends' => DB::table('search_logs')
                    ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
                    ->where('created_at', '>=', now()->subDays(30))
                    ->groupBy('date')
                    ->orderBy('date', 'desc')
                    ->get()
            ];
        });
    }

    public function logSearch($query, $resultsCount, $userId = null)
    {
        DB::table('search_logs')->insert([
            'query' => $query,
            'results_count' => $resultsCount,
            'user_id' => $userId,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
