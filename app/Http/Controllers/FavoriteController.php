<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Patronyme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
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
}
