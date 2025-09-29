<?php

namespace App\Http\Controllers;

use App\Models\Patronyme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserContributionsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Pour les contributeurs, on récupère les patronymes qu'ils ont créés
        // (en supposant qu'on ajoutera une colonne created_by à la table patronymes)
        $contributions = collect([]);
        
        // Pour l'instant, on affiche tous les patronymes si l'utilisateur est contributeur
        if ($user->canContribute()) {
            $contributions = Patronyme::with(['region', 'province', 'commune', 'groupeEthnique', 'langue'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        }
        
        return view('contributions.index', compact('contributions'));
    }
}