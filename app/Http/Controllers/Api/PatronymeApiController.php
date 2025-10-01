<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePatronymeRequest;
use App\Http\Requests\UpdatePatronymeRequest;
use App\Http\Resources\PatronymeResource;
use App\Http\Resources\PatronymeCollection;
use App\Models\Patronyme;
use App\Services\SearchService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class PatronymeApiController extends Controller
{
    protected $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only([
                'search', 'region_id', 'province_id', 'commune_id',
                'groupe_ethnique_id', 'ethnie_id', 'langue_id',
                'patronyme_sexe', 'transmission', 'min_frequence', 'max_frequence',
                'per_page', 'page', 'sort', 'order'
            ]);

            $perPage = min($filters['per_page'] ?? 15, 100); // Limite à 100 par page
            $sort = $filters['sort'] ?? 'created_at';
            $order = $filters['order'] ?? 'desc';

            $patronymes = $this->searchService->search($filters['search'] ?? '', $filters)
                ->appends($request->query())
                ->with(['region', 'province', 'commune', 'groupeEthnique', 'ethnie', 'langue', 'modeTransmission']);

            return response()->json([
                'success' => true,
                'data' => new PatronymeCollection($patronymes),
                'meta' => [
                    'total' => $patronymes->total(),
                    'per_page' => $patronymes->perPage(),
                    'current_page' => $patronymes->currentPage(),
                    'last_page' => $patronymes->lastPage(),
                    'from' => $patronymes->firstItem(),
                    'to' => $patronymes->lastItem(),
                ],
                'links' => [
                    'first' => $patronymes->url(1),
                    'last' => $patronymes->url($patronymes->lastPage()),
                    'prev' => $patronymes->previousPageUrl(),
                    'next' => $patronymes->nextPageUrl(),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('API Error in PatronymeApiController@index', [
                'error' => $e->getMessage(),
                'filters' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la récupération des patronymes.',
                'error' => app()->environment('local') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePatronymeRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $patronyme = Patronyme::create($validated);

            Log::info('API Patronyme created', [
                'patronyme_id' => $patronyme->id,
                'nom' => $patronyme->nom,
                'user_id' => auth()->id(),
                'ip_address' => $request->ip()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Patronyme créé avec succès.',
                'data' => new PatronymeResource($patronyme->load(['region', 'province', 'commune', 'groupeEthnique', 'ethnie', 'langue', 'modeTransmission']))
            ], 201);

        } catch (\Exception $e) {
            Log::error('API Error creating patronyme', [
                'error' => $e->getMessage(),
                'data' => $request->validated(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la création du patronyme.',
                'error' => app()->environment('local') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Patronyme $patronyme): JsonResponse
    {
        try {
            // Incrémenter le compteur de vues
            $patronyme->incrementViews();

            $patronyme->load([
                'region', 'province', 'commune', 'groupeEthnique', 'ethnie',
                'langue', 'modeTransmission', 'commentaires.utilisateur'
            ]);

            return response()->json([
                'success' => true,
                'data' => new PatronymeResource($patronyme)
            ]);

        } catch (\Exception $e) {
            Log::error('API Error in PatronymeApiController@show', [
                'error' => $e->getMessage(),
                'patronyme_id' => $patronyme->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la récupération du patronyme.',
                'error' => app()->environment('local') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePatronymeRequest $request, Patronyme $patronyme): JsonResponse
    {
        try {
            $validated = $request->validated();
            $patronyme->update($validated);

            Log::info('API Patronyme updated', [
                'patronyme_id' => $patronyme->id,
                'nom' => $patronyme->nom,
                'user_id' => auth()->id(),
                'ip_address' => $request->ip()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Patronyme mis à jour avec succès.',
                'data' => new PatronymeResource($patronyme->load(['region', 'province', 'commune', 'groupeEthnique', 'ethnie', 'langue', 'modeTransmission']))
            ]);

        } catch (\Exception $e) {
            Log::error('API Error updating patronyme', [
                'error' => $e->getMessage(),
                'patronyme_id' => $patronyme->id,
                'data' => $request->validated(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la mise à jour du patronyme.',
                'error' => app()->environment('local') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patronyme $patronyme): JsonResponse
    {
        try {
            $patronyme->delete();

            Log::info('API Patronyme deleted', [
                'patronyme_id' => $patronyme->id,
                'nom' => $patronyme->nom,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Patronyme supprimé avec succès.'
            ]);

        } catch (\Exception $e) {
            Log::error('API Error deleting patronyme', [
                'error' => $e->getMessage(),
                'patronyme_id' => $patronyme->id,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la suppression du patronyme.',
                'error' => app()->environment('local') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get search suggestions
     */
    public function suggestions(Request $request): JsonResponse
    {
        try {
            $query = $request->input('q', '');

            if (strlen($query) < 2) {
                return response()->json([
                    'success' => true,
                    'data' => []
                ]);
            }

            $suggestions = $this->searchService->getAdvancedSuggestions($query, 15);

            return response()->json([
                'success' => true,
                'data' => $suggestions
            ]);

        } catch (\Exception $e) {
            Log::error('API Error in suggestions', [
                'error' => $e->getMessage(),
                'query' => $request->input('q')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la récupération des suggestions.',
                'error' => app()->environment('local') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get popular patronymes
     */
    public function popular(Request $request): JsonResponse
    {
        try {
            $limit = min($request->input('limit', 10), 50);

            $patronymes = Patronyme::orderBy('views_count', 'desc')
                ->limit($limit)
                ->with(['region', 'groupeEthnique'])
                ->get();

            return response()->json([
                'success' => true,
                'data' => new PatronymeCollection($patronymes)
            ]);

        } catch (\Exception $e) {
            Log::error('API Error in popular patronymes', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la récupération des patronymes populaires.',
                'error' => app()->environment('local') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get recent patronymes
     */
    public function recent(Request $request): JsonResponse
    {
        try {
            $limit = min($request->input('limit', 10), 50);

            $patronymes = Patronyme::orderBy('created_at', 'desc')
                ->limit($limit)
                ->with(['region', 'groupeEthnique'])
                ->get();

            return response()->json([
                'success' => true,
                'data' => new PatronymeCollection($patronymes)
            ]);

        } catch (\Exception $e) {
            Log::error('API Error in recent patronymes', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la récupération des patronymes récents.',
                'error' => app()->environment('local') ? $e->getMessage() : null
            ], 500);
        }
    }
}
