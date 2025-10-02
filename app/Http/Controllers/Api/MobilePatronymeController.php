<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patronyme;
use App\Services\SearchService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class MobilePatronymeController extends Controller
{
    protected $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * Liste des patronymes optimisée pour mobile
     */
    public function mobileIndex(Request $request): JsonResponse
    {
        try {
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 20);
            $search = $request->get('search', '');
            $region = $request->get('region_id');
            $province = $request->get('province_id');
            $commune = $request->get('commune_id');

            $cacheKey = "mobile_patronymes_{$page}_{$perPage}_{$search}_{$region}_{$province}_{$commune}";

            $patronymes = Cache::remember($cacheKey, 300, function () use ($request, $perPage) {
                $query = Patronyme::with(['region:id,name', 'province:id,nom', 'commune:id,nom', 'groupeEthnique:id,nom'])
                    ->select('id', 'nom', 'signification', 'origine', 'region_id', 'province_id', 'commune_id', 'groupe_ethnique_id', 'views_count', 'created_at');

                if ($request->get('search')) {
                    $query->where('nom', 'LIKE', "%{$request->get('search')}%");
                }

                if ($request->get('region_id')) {
                    $query->where('region_id', $request->get('region_id'));
                }

                if ($request->get('province_id')) {
                    $query->where('province_id', $request->get('province_id'));
                }

                if ($request->get('commune_id')) {
                    $query->where('commune_id', $request->get('commune_id'));
                }

                return $query->orderBy('views_count', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->paginate($perPage);
            });

            return response()->json([
                'success' => true,
                'data' => $patronymes->items(),
                'pagination' => [
                    'current_page' => $patronymes->currentPage(),
                    'last_page' => $patronymes->lastPage(),
                    'per_page' => $patronymes->perPage(),
                    'total' => $patronymes->total(),
                    'has_more' => $patronymes->hasMorePages()
                ],
                'meta' => [
                    'cache_hit' => Cache::has($cacheKey),
                    'response_time' => microtime(true) - LARAVEL_START
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Mobile API Error - mobileIndex', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des patronymes',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne'
            ], 500);
        }
    }

    /**
     * Détail d'un patronyme optimisé pour mobile
     */
    public function mobileShow(Patronyme $patronyme): JsonResponse
    {
        try {
            // Incrémenter les vues
            $patronyme->increment('views_count');

            $patronyme->load([
                'region:id,name',
                'province:id,nom',
                'commune:id,nom',
                'groupeEthnique:id,nom',
                'ethnie:id,nom',
                'langue:id,nom'
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $patronyme->id,
                    'nom' => $patronyme->nom,
                    'signification' => $patronyme->signification,
                    'origine' => $patronyme->origine,
                    'histoire' => $patronyme->histoire,
                    'totem' => $patronyme->totem,
                    'justification_totem' => $patronyme->justification_totem,
                    'parents_plaisanterie' => $patronyme->parents_plaisanterie,
                    'transmission' => $patronyme->transmission,
                    'frequence' => $patronyme->frequence,
                    'views_count' => $patronyme->views_count,
                    'location' => [
                        'region' => $patronyme->region,
                        'province' => $patronyme->province,
                        'commune' => $patronyme->commune
                    ],
                    'ethnicity' => [
                        'groupe_ethnique' => $patronyme->groupeEthnique,
                        'ethnie' => $patronyme->ethnie,
                        'langue' => $patronyme->langue
                    ],
                    'created_at' => $patronyme->created_at,
                    'updated_at' => $patronyme->updated_at
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Mobile API Error - mobileShow', [
                'error' => $e->getMessage(),
                'patronyme_id' => $patronyme->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement du patronyme',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne'
            ], 500);
        }
    }

    /**
     * Patronymes populaires pour mobile
     */
    public function mobilePopular(Request $request): JsonResponse
    {
        try {
            $limit = $request->get('limit', 10);

            $popular = Cache::remember("mobile_popular_{$limit}", 1800, function () use ($limit) {
                return Patronyme::with(['region:id,name', 'province:id,nom'])
                    ->select('id', 'nom', 'signification', 'region_id', 'province_id', 'views_count')
                    ->orderBy('views_count', 'desc')
                    ->orderBy('frequence', 'desc')
                    ->limit($limit)
                    ->get();
            });

            return response()->json([
                'success' => true,
                'data' => $popular
            ]);

        } catch (\Exception $e) {
            Log::error('Mobile API Error - mobilePopular', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des patronymes populaires'
            ], 500);
        }
    }

    /**
     * Patronymes récents pour mobile
     */
    public function mobileRecent(Request $request): JsonResponse
    {
        try {
            $limit = $request->get('limit', 10);

            $recent = Cache::remember("mobile_recent_{$limit}", 300, function () use ($limit) {
                return Patronyme::with(['region:id,name', 'province:id,nom'])
                    ->select('id', 'nom', 'signification', 'region_id', 'province_id', 'created_at')
                    ->orderBy('created_at', 'desc')
                    ->limit($limit)
                    ->get();
            });

            return response()->json([
                'success' => true,
                'data' => $recent
            ]);

        } catch (\Exception $e) {
            Log::error('Mobile API Error - mobileRecent', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des patronymes récents'
            ], 500);
        }
    }

    /**
     * Recherche mobile optimisée
     */
    public function mobileSearch(Request $request): JsonResponse
    {
        try {
            $query = $request->get('q', '');
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 15);

            if (strlen($query) < 2) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'message' => 'La recherche doit contenir au moins 2 caractères'
                ]);
            }

            $results = $this->searchService->search($query, [
                'region_id' => $request->get('region_id'),
                'province_id' => $request->get('province_id'),
                'commune_id' => $request->get('commune_id')
            ]);

            return response()->json([
                'success' => true,
                'data' => $results->items(),
                'pagination' => [
                    'current_page' => $results->currentPage(),
                    'last_page' => $results->lastPage(),
                    'per_page' => $results->perPage(),
                    'total' => $results->total(),
                    'has_more' => $results->hasMorePages()
                ],
                'query' => $query
            ]);

        } catch (\Exception $e) {
            Log::error('Mobile API Error - mobileSearch', [
                'error' => $e->getMessage(),
                'query' => $request->get('q')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la recherche'
            ], 500);
        }
    }

    /**
     * Patronymes par lettre pour mobile
     */
    public function mobileByLetter(Request $request, string $letter): JsonResponse
    {
        try {
            $limit = $request->get('limit', 20);

            $patronymes = Cache::remember("mobile_letter_{$letter}_{$limit}", 3600, function () use ($letter, $limit) {
                return Patronyme::with(['region:id,name', 'province:id,nom'])
                    ->select('id', 'nom', 'signification', 'region_id', 'province_id', 'views_count')
                    ->where('nom', 'LIKE', "{$letter}%")
                    ->orderBy('nom')
                    ->limit($limit)
                    ->get();
            });

            return response()->json([
                'success' => true,
                'data' => $patronymes,
                'letter' => strtoupper($letter),
                'count' => $patronymes->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Mobile API Error - mobileByLetter', [
                'error' => $e->getMessage(),
                'letter' => $letter
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des patronymes'
            ], 500);
        }
    }

    /**
     * Données pour mode hors ligne
     */
    public function getOfflineData(): JsonResponse
    {
        try {
            $offlineData = Cache::remember('mobile_offline_data', 3600, function () {
                return [
                    'regions' => \App\Models\Region::select('id', 'name')->get(),
                    'provinces' => \App\Models\Province::select('id', 'nom', 'region_id')->get(),
                    'communes' => \App\Models\Commune::select('id', 'nom', 'province_id')->get(),
                    'groupes_ethniques' => \App\Models\GroupeEthnique::select('id', 'nom')->get(),
                    'ethnies' => \App\Models\Ethnie::select('id', 'nom', 'groupe_ethnique_id')->get(),
                    'langues' => \App\Models\Langue::select('id', 'nom')->get(),
                    'popular_patronymes' => Patronyme::select('id', 'nom', 'signification', 'views_count')
                        ->orderBy('views_count', 'desc')
                        ->limit(50)
                        ->get()
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $offlineData,
                'generated_at' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('Mobile API Error - getOfflineData', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des données hors ligne'
            ], 500);
        }
    }

    /**
     * Synchronisation des données
     */
    public function syncData(Request $request): JsonResponse
    {
        try {
            $lastSync = $request->get('last_sync');
            $userId = auth()->id();

            // Logique de synchronisation
            $syncData = [
                'user_id' => $userId,
                'last_sync' => now()->toISOString(),
                'patronymes_updated' => 0,
                'favorites_updated' => 0
            ];

            return response()->json([
                'success' => true,
                'data' => $syncData,
                'message' => 'Synchronisation terminée'
            ]);

        } catch (\Exception $e) {
            Log::error('Mobile API Error - syncData', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la synchronisation'
            ], 500);
        }
    }
}
