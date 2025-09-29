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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Services\SearchService;
use App\Services\StatisticsService;

class PatronymeController extends Controller
{
    protected $searchService;
    protected $statisticsService;

    public function __construct(SearchService $searchService, StatisticsService $statisticsService)
    {
        $this->searchService = $searchService;
        $this->statisticsService = $statisticsService;
    }
    public function index(Request $request)
    {
        try {
            $filters = $request->only([
                'search', 'region_id', 'province_id', 'commune_id', 
                'groupe_ethnique_id', 'ethnie_id', 'langue_id',
                'patronyme_sexe', 'transmission', 'min_frequence', 'max_frequence'
            ]);

            // Utiliser le service de recherche optimisé
            $patronymes = $this->searchService->search($filters['search'] ?? '', $filters);
            
            // Cache des données de référence
            $regions = Cache::remember('regions_list', 3600, function () {
                return Region::orderBy('name')->get();
            });
            
            $provinces = $filters['region_id'] 
                ? Cache::remember("provinces_region_{$filters['region_id']}", 1800, function () use ($filters) {
                    return Province::where('region_id', $filters['region_id'])->orderBy('nom')->get();
                })
                : collect();
                
            $communes = $filters['province_id'] 
                ? Cache::remember("communes_province_{$filters['province_id']}", 1800, function () use ($filters) {
                    return Commune::where('province_id', $filters['province_id'])->orderBy('nom')->get();
                })
                : collect();
                
            $groupesEthniques = Cache::remember('groupes_ethniques_list', 3600, function () {
                return GroupeEthnique::orderBy('nom')->get();
            });
            
            $ethnies = Cache::remember('ethnies_list', 3600, function () {
                return \App\Models\Ethnie::orderBy('nom')->get();
            });
            
            $langues = Cache::remember('langues_list', 3600, function () {
                return \App\Models\Langue::orderBy('nom')->get();
            });

            Log::info('Patronyme search performed', [
                'filters' => $filters,
                'results_count' => $patronymes->count(),
                'user_id' => auth()->id()
            ]);

            return view('patronymes.index', compact(
                'patronymes', 'regions', 'provinces', 'communes', 'groupesEthniques', 'ethnies', 'langues'
            ))->with($filters);
            
        } catch (\Exception $e) {
            Log::error('Error in PatronymeController@index', [
                'error' => $e->getMessage(),
                'filters' => $request->all()
            ]);
            
            return redirect()->back()->with('error', 'Une erreur est survenue lors de la recherche.');
        }
    }

    /**
     * Génère des variations communes pour les patronymes burkinabés
     */
    private function getPatronymeVariations($searchTerm)
    {
        $variations = [];
        $term = strtolower(trim($searchTerm));

        // Variations communes pour les patronymes burkinabés
        $commonVariations = [
            'ouedraogo' => ['ouédraogo', 'ouedraogo', 'ouedraogo', 'wédraogo', 'wédraogo'],
            'traore' => ['traoré', 'traore', 'traoré', 'traore'],
            'sawadogo' => ['sawadogo', 'sawadogo', 'sawadogo'],
            'kabore' => ['kaboré', 'kabore', 'kaboré'],
            'zongo' => ['zongo', 'zongo'],
            'ouattara' => ['ouattara', 'ouattara', 'wattara'],
            'compore' => ['compore', 'compore', 'compore'],
            'kone' => ['koné', 'kone', 'koné'],
            'sangare' => ['sangaré', 'sangare', 'sangaré'],
            'dabire' => ['dabiré', 'dabire', 'dabiré'],
            'kabore' => ['kaboré', 'kabore', 'kaboré'],
            'ouedraogo' => ['ouédraogo', 'ouedraogo', 'wédraogo'],
        ];

        // Chercher des variations pour le terme de recherche
        foreach ($commonVariations as $base => $vars) {
            if (in_array($term, $vars) || $term === $base) {
                $variations = array_merge($variations, $vars);
                $variations = array_merge($variations, [$base]);
            }
        }

        // Variations phonétiques simples
        $phoneticVariations = [
            'ou' => ['u', 'w'],
            'é' => ['e'],
            'è' => ['e'],
            'à' => ['a'],
            'ù' => ['u'],
            'ç' => ['c'],
        ];

        foreach ($phoneticVariations as $from => $to) {
            if (strpos($term, $from) !== false) {
                foreach ($to as $replacement) {
                    $variations[] = str_replace($from, $replacement, $term);
                }
            }
        }

        return array_unique($variations);
    }

    /**
     * Recherche par similarité phonétique et orthographique
     */
    private function getSimilarNames($searchTerm, $limit = 5)
    {
        $term = strtolower(trim($searchTerm));
        $allPatronymes = Patronyme::select('nom')->distinct()->get();
        $similarNames = [];

        foreach ($allPatronymes as $patronyme) {
            $name = strtolower($patronyme->nom);

            // Distance de Levenshtein pour la similarité
            $distance = levenshtein($term, $name);
            $maxLength = max(strlen($term), strlen($name));
            $similarity = 1 - ($distance / $maxLength);

            // Si la similarité est suffisante (plus de 70%)
            if ($similarity > 0.7) {
                $similarNames[] = [
                    'name' => $patronyme->nom,
                    'similarity' => $similarity
                ];
            }
        }

        // Trier par similarité décroissante
        usort($similarNames, function($a, $b) {
            return $b['similarity'] <=> $a['similarity'];
        });

        return array_slice($similarNames, 0, $limit);
    }

    /**
     * Retourne des suggestions de recherche en temps réel
     */
    public function getSearchSuggestions(Request $request)
    {
        $query = $request->input('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $suggestions = [];

        // Recherche dans les noms de patronymes
        $patronymes = Patronyme::where('nom', 'like', '%' . $query . '%')
            ->select('nom')
            ->distinct()
            ->limit(10)
            ->get();

        foreach ($patronymes as $patronyme) {
            $suggestions[] = [
                'type' => 'patronyme',
                'value' => $patronyme->nom,
                'label' => $patronyme->nom
            ];
        }

        // Recherche dans les régions
        $regions = Region::where('name', 'like', '%' . $query . '%')
            ->select('name')
            ->distinct()
            ->limit(5)
            ->get();

        foreach ($regions as $region) {
            $suggestions[] = [
                'type' => 'region',
                'value' => $region->name,
                'label' => 'Région: ' . $region->name
            ];
        }

        // Recherche dans les groupes ethniques
        $groupes = GroupeEthnique::where('nom', 'like', '%' . $query . '%')
            ->select('nom')
            ->distinct()
            ->limit(5)
            ->get();

        foreach ($groupes as $groupe) {
            $suggestions[] = [
                'type' => 'groupe',
                'value' => $groupe->nom,
                'label' => 'Groupe: ' . $groupe->nom
            ];
        }

        // Recherche par similarité si peu de résultats
        if (count($suggestions) < 5) {
            $similarNames = $this->getSimilarNames($query, 5);
            foreach ($similarNames as $similar) {
                $suggestions[] = [
                    'type' => 'similar',
                    'value' => $similar['name'],
                    'label' => 'Similaire: ' . $similar['name'] . ' (' . round($similar['similarity'] * 100) . '%)'
                ];
            }
        }

        return response()->json(array_slice($suggestions, 0, 15));
    }

    public function create()
    {
        $regions = Region::orderBy('name')->get();
        $groupesEthniques = GroupeEthnique::orderBy('nom')->get();
        $langues = \App\Models\Langue::orderBy('nom')->get();
        return view('patronymes.create', compact('regions', 'groupesEthniques', 'langues'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                // Informations sur l'enquêté
                'enquete_nom' => 'required|string|max:255',
                'enquete_age' => 'nullable|integer|min:1|max:120',
                'enquete_sexe' => 'nullable|in:M,F',
                'enquete_fonction' => 'nullable|string|max:255',
                'enquete_contact' => 'nullable|string|max:255',

                // Informations sur le patronyme
                'nom' => 'required|string|max:255|unique:patronymes,nom',
                'groupe_ethnique_id' => 'nullable|exists:groupe_ethniques,id',
                'origine' => 'nullable|string',
                'signification' => 'nullable|string',
                'histoire' => 'nullable|string',
                'langue_id' => 'nullable|exists:langues,id',
                'transmission' => 'nullable|in:pere,mere,autre',
                'patronyme_sexe' => 'nullable|in:M,F,mixte',
                'totem' => 'nullable|string|max:255',
                'justification_totem' => 'nullable|string',
                'parents_plaisanterie' => 'nullable|string',

                // Localisation
                'region_id' => 'nullable|exists:regions,id',
                'province_id' => 'nullable|exists:provinces,id',
                'commune_id' => 'nullable|exists:communes,id',
            ]);

            $patronyme = Patronyme::create($validated);

            // Log de la création
            Log::info('Patronyme created', [
                'patronyme_id' => $patronyme->id,
                'nom' => $patronyme->nom,
                'user_id' => auth()->id()
            ]);

            // Nettoyer le cache
            Cache::forget('popular_patronymes_*');
            Cache::forget('recent_patronymes_*');
            Cache::forget('featured_patronymes_*');

            return redirect()->route('patronymes.index')->with('success', 'Patronyme ajouté avec succès.');
            
        } catch (\Exception $e) {
            Log::error('Error creating patronyme', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);
            
            return redirect()->back()->with('error', 'Une erreur est survenue lors de la création du patronyme.');
        }
    }

    public function edit(Patronyme $patronyme)
    {
        $regions = Region::orderBy('name')->get();
        $groupesEthniques = GroupeEthnique::orderBy('nom')->get();
        $langues = \App\Models\Langue::orderBy('nom')->get();

        // Load the patronyme with its relations
        $patronyme->load(['region', 'province', 'commune', 'groupeEthnique', 'langue']);

        return view('patronymes.edit', compact('patronyme', 'regions', 'groupesEthniques', 'langues'));
    }

    public function update(Request $request, Patronyme $patronyme)
    {
        $validated = $request->validate([
            // Informations sur l'enquêté
            'enquete_nom' => 'required|string|max:255',
            'enquete_age' => 'nullable|integer|min:1|max:120',
            'enquete_sexe' => 'nullable|in:M,F',
            'enquete_fonction' => 'nullable|string|max:255',
            'enquete_contact' => 'nullable|string|max:255',

            // Informations sur le patronyme
            'nom' => 'required|string|max:255',
            'groupe_ethnique_id' => 'nullable|exists:groupe_ethniques,id',
            'origine' => 'nullable|string',
            'signification' => 'nullable|string',
            'histoire' => 'nullable|string',
            'langue_id' => 'nullable|exists:langues,id',
            'transmission' => 'nullable|in:pere,mere',
            'patronyme_sexe' => 'nullable|string',
            'totem' => 'nullable|string|max:255',
            'justification_totem' => 'nullable|string',
            'parents_plaisanterie' => 'nullable|string',

            // Localisation
            'region_id' => 'nullable|exists:regions,id',
            'province_id' => 'nullable|exists:provinces,id',
            'commune_id' => 'nullable|exists:communes,id',
        ]);

        $patronyme->update($validated);

        return redirect()->route('patronymes.index')->with('success', 'Patronyme mis à jour avec succès.');
    }

    public function show(Patronyme $patronyme)
    {
        // Increment view count
        $patronyme->incrementViews();

        // Load the patronyme with its relations including comments
        $patronyme->load(['region', 'province', 'commune', 'groupeEthnique', 'ethnie', 'langue', 'modeTransmission', 'commentaires.utilisateur']);

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
