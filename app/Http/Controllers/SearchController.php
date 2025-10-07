<?php

namespace App\Http\Controllers;

use App\Services\AdvancedSearchService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SearchController extends Controller
{
    protected $searchService;

    public function __construct(AdvancedSearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * Get search suggestions
     */
    public function suggestions(Request $request): JsonResponse
    {
        $query = $request->input('q', '');
        $limit = $request->input('limit', 10);

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $suggestions = $this->searchService->getSuggestions($query, $limit);

        return response()->json($suggestions);
    }

    /**
     * Get popular searches
     */
    public function popular(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 10);
        $popular = $this->searchService->getPopularSearches($limit);

        return response()->json($popular);
    }

    /**
     * Get search filters
     */
    public function filters(): JsonResponse
    {
        $filters = $this->searchService->getSearchFilters();

        return response()->json($filters);
    }

    /**
     * Get search statistics
     */
    public function stats(): JsonResponse
    {
        $stats = $this->searchService->getSearchStats();

        return response()->json($stats);
    }

    /**
     * Perform advanced search
     */
    public function advanced(Request $request): JsonResponse
    {
        $criteria = $request->only([
            'search', 'region_id', 'groupe_ethnique_id', 'langue_id',
            'sort_by', 'per_page'
        ]);

        $results = $this->searchService->search($criteria);

        return response()->json([
            'data' => $results->items(),
            'pagination' => [
                'current_page' => $results->currentPage(),
                'last_page' => $results->lastPage(),
                'per_page' => $results->perPage(),
                'total' => $results->total(),
                'has_more' => $results->hasMorePages(),
            ]
        ]);
    }
}
