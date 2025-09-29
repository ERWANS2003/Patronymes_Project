<?php
namespace App\Http\Controllers;

use App\Models\Patronyme;
use App\Models\Region;
use App\Models\Departement;
use App\Models\GroupeEthnique;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use App\Models\Province;
use App\Models\Commune;

class PatronymeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $regionId = $request->input('region_id');
        $provinceId = $request->input('province_id');
        $communeId = $request->input('commune_id');
        $groupeEthniqueId = $request->input('groupe_ethnique_id');
        $ethnieId = $request->input('ethnie_id');
        $langueId = $request->input('langue_id');

        $query = Patronyme::with(['region', 'province', 'commune', 'groupeEthnique', 'ethnie', 'langue', 'modeTransmission']);

        if ($search) {
            $query->where('nom', 'like', '%' . $search . '%');
        }
        if ($regionId) {
            $query->where('region_id', $regionId);
        }
        if ($provinceId) {
            $query->where('province_id', $provinceId);
        }
        if ($communeId) {
            $query->where('commune_id', $communeId);
        }
        if (Schema::hasColumn('patronymes', 'groupe_ethnique_id') && $groupeEthniqueId) {
            $query->where('groupe_ethnique_id', $groupeEthniqueId);
        }
        if (Schema::hasColumn('patronymes', 'ethnie_id') && $ethnieId) {
            $query->where('ethnie_id', $ethnieId);
        }
        if (Schema::hasColumn('patronymes', 'langue_id') && $langueId) {
            $query->where('langue_id', $langueId);
        }

        $patronymes = $query->paginate(10)->withQueryString();

        $regions = Region::orderBy('name')->get();
        $provinces = $regionId ? Province::where('region_id', $regionId)->orderBy('nom')->get() : collect();
        $communes = $provinceId ? Commune::where('province_id', $provinceId)->orderBy('nom')->get() : collect();
        $groupesEthniques = class_exists(\App\Models\GroupeEthnique::class)
            ? GroupeEthnique::orderBy('nom')->get()
            : collect();
        $ethnies = class_exists(\App\Models\Ethnie::class)
            ? \App\Models\Ethnie::orderBy('nom')->get()
            : collect();
        $langues = class_exists(\App\Models\Langue::class)
            ? \App\Models\Langue::orderBy('nom')->get()
            : collect();

        return view('patronymes.index', compact(
            'patronymes', 'regions', 'provinces', 'communes', 'groupesEthniques', 'ethnies', 'langues',
            'search', 'regionId', 'provinceId', 'communeId', 'groupeEthniqueId', 'ethnieId', 'langueId'
        ));
    }

    public function create()
    {
        $regions = Region::orderBy('name')->get();
        $departements = Departement::orderBy('name')->get();
        return view('patronymes.create', compact('regions', 'departements'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'region_id' => 'nullable|exists:regions,id',
            'departement_id' => 'nullable|exists:departements,id',
            'frequence' => 'nullable|integer|min:0',
            'origine' => 'nullable|string',
            'signification' => 'nullable|string',
            'histoire' => 'nullable|string',
        ]);

        Patronyme::create($validated);

        return redirect()->route('patronymes.index')->with('success', 'Patronyme ajouté avec succès.');
    }

    public function edit(Patronyme $patronyme)
    {
        $regions = Region::orderBy('name')->get();
        $departements = Departement::orderBy('name')->get();
        return view('patronymes.edit', compact('patronyme', 'regions', 'departements'));
    }

    public function update(Request $request, Patronyme $patronyme)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'region_id' => 'nullable|exists:regions,id',
            'departement_id' => 'nullable|exists:departements,id',
            'frequence' => 'nullable|integer|min:0',
            'origine' => 'nullable|string',
            'signification' => 'nullable|string',
            'histoire' => 'nullable|string',
        ]);

        $patronyme->update($validated);

        return redirect()->route('patronymes.index')->with('success', 'Patronyme mis à jour avec succès.');
    }

    public function show(Patronyme $patronyme)
    {
        // Increment view count
        $patronyme->incrementViews();

        return view('patronymes.show', compact('patronyme'));
    }

    public function destroy(Patronyme $patronyme)
    {
        $patronyme->delete();
        return redirect()->route('patronymes.index')->with('success', 'Patronyme supprimé.');
    }

    // AJAX
    public function getProvinces(Request $request)
    {
        $provinces = Province::where('region_id', $request->region_id)->orderBy('nom')->get();
        return response()->json($provinces);
    }

    public function getCommunes(Request $request)
    {
        $communes = Commune::where('province_id', $request->province_id)->orderBy('nom')->get();
        return response()->json($communes);
    }
}
