<?php

namespace App\Http\Controllers;

use App\Models\Commentaire;
use Illuminate\Http\Request;

class CommentaireController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'contenu' => 'required|string|max:1000',
            'patronyme_id' => 'required|exists:patronymes,id',
        ]);

        $validated['utilisateur_id'] = auth()->id();
        $validated['date_commentaire'] = now();

        Commentaire::create($validated);

        return redirect()->back()->with('success', 'Commentaire ajouté avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Commentaire $commentaire)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Commentaire $commentaire)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Commentaire $commentaire)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Commentaire $commentaire)
    {
        // Vérifier que l'utilisateur peut supprimer ce commentaire
        if (auth()->id() !== $commentaire->utilisateur_id && !auth()->user()->isAdmin()) {
            abort(403, 'Vous ne pouvez pas supprimer ce commentaire.');
        }

        $commentaire->delete();

        return redirect()->back()->with('success', 'Commentaire supprimé avec succès.');
    }
}
