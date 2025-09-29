<?php

namespace App\Http\Controllers;

use App\Models\Patronyme;
use App\Models\Region;
use App\Models\GroupeEthnique;
use App\Models\Langue;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    public function index()
    {
        $stats = [
            'total_patronymes' => Patronyme::count(),
            'total_regions' => Region::count(),
            'total_users' => User::count(),
            'total_favorites' => DB::table('favorites')->count(),
            'most_viewed' => Patronyme::orderBy('views_count', 'desc')->limit(5)->get(),
            'recent_patronymes' => Patronyme::with(['region', 'province', 'commune'])->latest()->limit(5)->get(),
            'patronymes_by_region' => Patronyme::select('regions.name', DB::raw('count(*) as count'))
                ->join('regions', 'patronymes.region_id', '=', 'regions.id')
                ->groupBy('regions.id', 'regions.name')
                ->orderBy('count', 'desc')
                ->get(),
            'patronymes_by_ethnic_group' => Patronyme::select('groupe_ethniques.nom', DB::raw('count(*) as count'))
                ->join('groupe_ethniques', 'patronymes.groupe_ethnique_id', '=', 'groupe_ethniques.id')
                ->groupBy('groupe_ethniques.id', 'groupe_ethniques.nom')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get(),
            'patronymes_by_language' => Patronyme::select('langues.nom', DB::raw('count(*) as count'))
                ->join('langues', 'patronymes.langue_id', '=', 'langues.id')
                ->groupBy('langues.id', 'langues.nom')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get(),
        ];

        return view('statistics.index', compact('stats'));
    }

    public function api()
    {
        $stats = [
            'total_patronymes' => Patronyme::count(),
            'total_regions' => Region::count(),
            'total_users' => User::count(),
            'total_favorites' => DB::table('favorites')->count(),
            'most_viewed' => Patronyme::orderBy('views_count', 'desc')->limit(10)->get(['nom', 'views_count']),
            'patronymes_by_region' => Patronyme::select('regions.name', DB::raw('count(*) as count'))
                ->join('regions', 'patronymes.region_id', '=', 'regions.id')
                ->groupBy('regions.id', 'regions.name')
                ->orderBy('count', 'desc')
                ->get(),
        ];

        return response()->json($stats);
    }
}
