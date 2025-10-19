<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Patronyme;
use App\Services\StatisticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    protected $statisticsService;

    public function __construct(StatisticsService $statisticsService)
    {
        $this->statisticsService = $statisticsService;
    }

    public function toggle(Request $request, Patronyme $patronyme)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $favorite = Favorite::where('user_id', $user->id)
                           ->where('patronyme_id', $patronyme->id)
                           ->first();

        if ($favorite) {
            $favorite->delete();
            $isFavorited = false;
        } else {
            Favorite::create([
                'user_id' => $user->id,
                'patronyme_id' => $patronyme->id,
            ]);
            $isFavorited = true;
        }

        // Invalider le cache des statistiques utilisateur
        $this->statisticsService->clearUserActivityCache($user->id);

        return response()->json([
            'isFavorited' => $isFavorited,
            'favoritesCount' => $patronyme->favorites()->count()
        ]);
    }

    public function index()
    {
        $user = Auth::user();
        $favorites = $user->favoritePatronymes()
                         ->with(['region', 'province', 'commune', 'groupeEthnique', 'langue'])
                         ->paginate(12);

        return view('favorites.index', compact('favorites'));
    }

    public function apiIndex(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $perPage = (int) $request->get('per_page', 20);
        if ($perPage < 1) $perPage = 1;
        if ($perPage > 100) $perPage = 100;

        $favorites = $user->favoritePatronymes()
            ->with(['region:id,name', 'province:id,nom', 'commune:id,nom', 'groupeEthnique:id,nom'])
            ->select('patronymes.id', 'patronymes.nom', 'patronymes.signification', 'patronymes.region_id', 'patronymes.province_id', 'patronymes.commune_id', 'patronymes.groupe_ethnique_id', 'patronymes.views_count', 'patronymes.created_at')
            ->orderByDesc('patronymes.created_at')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $favorites->items(),
            'pagination' => [
                'current_page' => $favorites->currentPage(),
                'last_page' => $favorites->lastPage(),
                'per_page' => $favorites->perPage(),
                'total' => $favorites->total(),
                'has_more' => $favorites->hasMorePages()
            ],
            'links' => [
                'next' => $favorites->nextPageUrl(),
                'prev' => $favorites->previousPageUrl()
            ],
            'meta' => [
                'response_time' => microtime(true) - LARAVEL_START
            ]
        ]);
    }
}
