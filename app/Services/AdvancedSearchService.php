<?php

namespace App\Services;

use App\Models\Patronyme;
use App\Models\Region;
use App\Models\GroupeEthnique;
use App\Models\Langue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdvancedSearchService
{
    /**
     * Perform advanced search with multiple criteria
     */
    public function search(array $criteria): array
    {
        $searchTerm = $criteria['search'] ?? '';
        $regionId = $criteria['region_id'] ?? null;
        $groupeEthniqueId = $criteria['groupe_ethnique_id'] ?? null;
        $langueId = $criteria['langue_id'] ?? null;
        $sortBy = $criteria['sort_by'] ?? 'relevance';
        $perPage = $criteria['per_page'] ?? 12;

        $cacheKey = 'advanced_search_' . md5(serialize($criteria));

        return Cache::remember($cacheKey, 300, function() use ($searchTerm, $regionId, $groupeEthniqueId, $langueId, $sortBy, $perPage) {
            $query = Patronyme::with(['region', 'province', 'commune', 'groupeEthnique', 'langue', 'ethnie'])
                ->select([
                    'id', 'nom', 'signification', 'origine', 'histoire',
                    'region_id', 'province_id', 'commune_id', 'groupe_ethnique_id', 'langue_id', 'ethnie_id',
                    'views_count', 'is_featured', 'created_at'
                ]);

            // Apply search filters
            if ($searchTerm) {
                $query->where(function($q) use ($searchTerm) {
                    $this->applySearchTerm($q, $searchTerm);
                });
            }

            // Apply filters
            if ($regionId) {
                $query->where('region_id', $regionId);
            }

            if ($groupeEthniqueId) {
                $query->where('groupe_ethnique_id', $groupeEthniqueId);
            }

            if ($langueId) {
                $query->where('langue_id', $langueId);
            }

            // Apply sorting
            $this->applySorting($query, $sortBy);

            return $query->paginate($perPage);
        });
    }

    /**
     * Get search suggestions with intelligent ranking
     */
    public function getSuggestions(string $query, int $limit = 10): array
    {
        if (strlen($query) < 2) {
            return [];
        }

        $cacheKey = 'search_suggestions_' . md5($query);

        return Cache::remember($cacheKey, 180, function() use ($query, $limit) {
            // Normalize query for case-insensitive search
            $normalizedQuery = strtolower($query);

            // Get exact matches first - case insensitive
            $exactMatches = Patronyme::select('nom', 'signification', 'views_count')
                ->whereRaw('LOWER(nom) LIKE ?', [strtolower($query) . '%'])
                ->orderBy('views_count', 'desc')
                ->limit($limit)
                ->get();

            // Get partial matches if we don't have enough - case insensitive
            $remaining = $limit - $exactMatches->count();
            if ($remaining > 0) {
                $partialMatches = Patronyme::select('nom', 'signification', 'views_count')
                    ->whereRaw('LOWER(nom) LIKE ?', ['%' . strtolower($query) . '%'])
                    ->whereNotIn('nom', $exactMatches->pluck('nom'))
                    ->orderBy('views_count', 'desc')
                    ->limit($remaining)
                    ->get();

                $exactMatches = $exactMatches->concat($partialMatches);
            }

            return $exactMatches->map(function($patronyme) {
                return [
                    'value' => $patronyme->nom,
                    'label' => $patronyme->nom,
                    'description' => $patronyme->signification ? Str::limit($patronyme->signification, 60) : null,
                    'type' => 'Patronyme',
                    'popularity' => $patronyme->views_count
                ];
            })->toArray();
        });
    }

    /**
     * Get popular searches
     */
    public function getPopularSearches(int $limit = 10): array
    {
        $cacheKey = 'popular_searches_' . $limit;

        return Cache::remember($cacheKey, 3600, function() use ($limit) {
            // Get from search logs if available, otherwise from popular patronymes
            return Patronyme::select('nom', 'views_count')
                ->where('views_count', '>', 0)
                ->orderBy('views_count', 'desc')
                ->limit($limit)
                ->get()
                ->map(function($patronyme) {
                    return [
                        'term' => $patronyme->nom,
                        'count' => $patronyme->views_count
                    ];
                })->toArray();
        });
    }

    /**
     * Get search filters data
     */
    public function getSearchFilters(): array
    {
        $cacheKey = 'search_filters_data';

        return Cache::remember($cacheKey, 1800, function() {
            return [
                'regions' => Region::select('id', 'nom')->orderBy('nom')->get(),
                'groupe_ethniques' => GroupeEthnique::select('id', 'nom')->orderBy('nom')->get(),
                'langues' => Langue::select('id', 'nom')->orderBy('nom')->get(),
            ];
        });
    }

    /**
     * Get search statistics
     */
    public function getSearchStats(): array
    {
        $cacheKey = 'search_statistics';

        return Cache::remember($cacheKey, 3600, function() {
            return [
                'total_patronymes' => Patronyme::count(),
                'total_regions' => Region::count(),
                'total_ethnies' => GroupeEthnique::count(),
                'total_langues' => Langue::count(),
                'most_viewed' => Patronyme::orderBy('views_count', 'desc')->limit(5)->get(['nom', 'views_count']),
                'recent_additions' => Patronyme::orderBy('created_at', 'desc')->limit(5)->get(['nom', 'created_at']),
            ];
        });
    }

    /**
     * Apply search term with intelligent matching
     */
    private function applySearchTerm($query, string $searchTerm): void
    {
        $terms = explode(' ', trim($searchTerm));
        $searchVariations = $this->getSearchVariations($searchTerm);

        foreach ($terms as $term) {
            $query->where(function($q) use ($term, $searchVariations) {
                // Normalize search term (remove accents and convert to lowercase for comparison)
                $normalizedTerm = $this->normalizeSearchTerm($term);

                // Exact match (highest priority) - case insensitive
                $q->whereRaw('LOWER(nom) LIKE ?', [strtolower($term) . '%'])
                  ->orWhereRaw('LOWER(nom) LIKE ?', ['%' . strtolower($term) . '%']);

                // Search in signification and origine - case insensitive
                $q->orWhereRaw('LOWER(signification) LIKE ?', ['%' . strtolower($term) . '%'])
                  ->orWhereRaw('LOWER(origine) LIKE ?', ['%' . strtolower($term) . '%'])
                  ->orWhereRaw('LOWER(histoire) LIKE ?', ['%' . strtolower($term) . '%']);

                // Search in related models - case insensitive
                $q->orWhereHas('region', function($regionQuery) use ($term) {
                    $regionQuery->whereRaw('LOWER(nom) LIKE ?', ['%' . strtolower($term) . '%']);
                })
                ->orWhereHas('groupeEthnique', function($ethnieQuery) use ($term) {
                    $ethnieQuery->whereRaw('LOWER(nom) LIKE ?', ['%' . strtolower($term) . '%']);
                })
                ->orWhereHas('langue', function($langueQuery) use ($term) {
                    $langueQuery->whereRaw('LOWER(nom) LIKE ?', ['%' . strtolower($term) . '%']);
                });

                // Search variations for better matching - case insensitive
                foreach ($searchVariations as $variation) {
                    $q->orWhereRaw('LOWER(nom) LIKE ?', ['%' . strtolower($variation) . '%'])
                      ->orWhereRaw('LOWER(signification) LIKE ?', ['%' . strtolower($variation) . '%']);
                }
            });
        }
    }

    /**
     * Apply sorting based on criteria
     */
    private function applySorting($query, string $sortBy): void
    {
        switch ($sortBy) {
            case 'name':
                $query->orderBy('nom');
                break;
            case 'popularity':
                $query->orderBy('views_count', 'desc')->orderBy('nom');
                break;
            case 'recent':
                $query->orderBy('created_at', 'desc');
                break;
            case 'featured':
                $query->orderBy('is_featured', 'desc')->orderBy('views_count', 'desc');
                break;
            case 'relevance':
            default:
                // Relevance scoring based on search term match and popularity
                $query->orderBy('is_featured', 'desc')
                      ->orderBy('views_count', 'desc')
                      ->orderBy('nom');
                break;
        }
    }

    /**
     * Get search variations for better matching
     */
    private function getSearchVariations(string $search): array
    {
        $variations = [];
        $search = Str::lower($search);

        // Common variations for Burkina Faso names
        $commonVariations = [
            'ouédraogo' => ['ouedraogo', 'wedraogo', 'ouédraogo'],
            'traoré' => ['traore', 'traore', 'traoré'],
            'ouédraogo' => ['ouedraogo', 'wedraogo'],
            'kabré' => ['kabre', 'kabré'],
            'sawadogo' => ['sawadogo', 'sawadogo'],
            'zongo' => ['zongo'],
            'tankoano' => ['tankoano'],
            'kaboré' => ['kabore', 'kaboré'],
        ];

        if (isset($commonVariations[$search])) {
            $variations = $commonVariations[$search];
        }

        // Add phonetic variations
        $phoneticVariations = [
            'ou' => ['u', 'w'],
            'é' => ['e'],
            'è' => ['e'],
            'à' => ['a'],
            'ô' => ['o'],
        ];

        foreach ($phoneticVariations as $original => $replacements) {
            if (Str::contains($search, $original)) {
                foreach ($replacements as $replacement) {
                    $variations[] = Str::replace($original, $replacement, $search);
                }
            }
        }

        return array_unique($variations);
    }

    /**
     * Normalize search term by removing accents and converting to lowercase
     */
    private function normalizeSearchTerm(string $term): string
    {
        // Remove accents and convert to lowercase
        $accents = ['à', 'á', 'â', 'ã', 'ä', 'å', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'ñ', 'ç'];
        $noAccents = ['a', 'a', 'a', 'a', 'a', 'a', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'n', 'c'];

        $normalized = str_replace($accents, $noAccents, strtolower($term));

        return $normalized;
    }
}
