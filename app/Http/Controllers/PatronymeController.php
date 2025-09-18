<?php

namespace App\Http\Controllers;

use App\Models\Patronyme;
use App\Models\Region;
use App\Models\Departement;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PatronymeController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $regionId = $request->input('region_id');
        $departementId = $request->input('departement_id');

        $patronymes = Patronyme::query()
            ->when($search, fn($query) => $query->search($search))
            ->when($regionId, fn($query) => $query->byRegion($regionId))
            ->when($departementId, fn($query) => $query->byDepartement($departementId))
            ->with(['region', 'departement'])
            ->orderBy('nom')
            ->paginate(20);

        $regions = Region::orderBy('name')->get();
        $departements = Departement::when($regionId, fn($query) => $query->where('region_id', $regionId))
            ->orderBy('name')
            ->get();

        return view('patronymes.index', compact('patronymes', 'regions', 'departements', 'search', 'regionId', 'departementId'));
    }

    public function create(): View
    {
        $regions = Region::orderBy('name')->get();
        $departements = Departement::orderBy('name')->get();

        return view('patronymes.create', compact('regions', 'departements'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'origine' => 'nullable|string',
            'signification' => 'nullable|string',
            'histoire' => 'nullable|string',
            'region_id' => 'nullable|exists:regions,id',
            'departement_id' => 'nullable|exists:departements,id',
            'frequence' => 'nullable|integer|min:0',
        ]);

        Patronyme::create($validated);

        return redirect()->route('patronymes.index')
            ->with('success', 'Patronyme ajouté avec succès.');
    }

    public function show(Patronyme $patronyme): View
    {
        return view('patronymes.show', compact('patronyme'));
    }

    public function edit(Patronyme $patronyme): View
    {
        $regions = Region::orderBy('name')->get();
        $departements = Departement::orderBy('name')->get();

        return view('patronymes.edit', compact('patronyme', 'regions', 'departements'));
    }

    public function update(Request $request, Patronyme $patronyme): RedirectResponse
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'origine' => 'nullable|string',
            'signification' => 'nullable|string',
            'histoire' => 'nullable|string',
            'region_id' => 'nullable|exists:regions,id',
            'departement_id' => 'nullable|exists:departements,id',
            'frequence' => 'nullable|integer|min:0',
        ]);

        $patronyme->update($validated);

        return redirect()->route('patronymes.index')
            ->with('success', 'Patronyme mis à jour avec succès.');
    }

    public function destroy(Patronyme $patronyme): RedirectResponse
    {
        $patronyme->delete();

        return redirect()->route('patronymes.index')
            ->with('success', 'Patronyme supprimé avec succès.');
    }
}
