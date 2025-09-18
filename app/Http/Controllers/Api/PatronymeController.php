<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patronyme;
use App\Models\Region;
use App\Models\Departement;
use Illuminate\Http\Request;

class PatronymeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $regionId = $request->input('region_id');
        $departementId = $request->input('departement_id');
        $perPage = $request->input('per_page', 20);

        $patronymes = Patronyme::query()
            ->when($search, fn($query) => $query->search($search))
            ->when($regionId, fn($query) => $query->byRegion($regionId))
            ->when($departementId, fn($query) => $query->byDepartement($departementId))
            ->with(['region', 'departement'])
            ->orderBy('nom')
            ->paginate($perPage);

        return response()->json($patronymes);
    }

    public function store(Request $request)
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

        $patronyme = Patronyme::create($validated);

        return response()->json($patronyme, 201);
    }

    public function show(Patronyme $patronyme)
    {
        $patronyme->load(['region', 'departement']);
        return response()->json($patronyme);
    }

    public function update(Request $request, Patronyme $patronyme)
    {
        $validated = $request->validate([
            'nom' => 'sometimes|required|string|max:255',
            'origine' => 'nullable|string',
            'signification' => 'nullable|string',
            'histoire' => 'nullable|string',
            'region_id' => 'nullable|exists:regions,id',
            'departement_id' => 'nullable|exists:departements,id',
            'frequence' => 'nullable|integer|min:0',
        ]);

        $patronyme->update($validated);

        return response()->json($patronyme);
    }

    public function destroy(Patronyme $patronyme)
    {
        $patronyme->delete();

        return response()->json(null, 204);
    }

    public function search(Request $request)
    {
        $search = $request->input('q');

        if (!$search) {
            return response()->json([]);
        }

        $patronymes = Patronyme::search($search)
            ->with(['region', 'departement'])
            ->limit(10)
            ->get();

        return response()->json($patronymes);
    }

    public function regions()
    {
        $regions = Region::orderBy('name')->get();
        return response()->json($regions);
    }

    public function departements(Request $request)
    {
        $regionId = $request->input('region_id');

        $departements = Departement::when($regionId, fn($query) => $query->where('region_id', $regionId))
            ->orderBy('name')
            ->get();

        return response()->json($departements);
    }
}
