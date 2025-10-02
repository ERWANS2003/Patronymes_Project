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
use App\Http\Requests\StorePatronymeRequest;
use App\Http\Requests\UpdatePatronymeRequest;

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
        $startTime = microtime(true);

        try {
            $filters = $request->only([
                'search', 'region_id', 'province_id', 'commune_id',
                'groupe_ethnique_id', 'ethnie_id', 'langue_id',
                'patronyme_sexe', 'transmission', 'min_frequence', 'max_frequence',
                'featured', 'sort'
            ]);

            // Utiliser le service de recherche optimisé
            $patronymes = $this->searchService->search($filters['search'] ?? '', $filters);

            // Cache des données de référence avec TTL optimisé
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

            // Log de la recherche avec temps de réponse
            $responseTime = round((microtime(true) - $startTime) * 1000, 3);

            if (!empty($filters['search'])) {
                $this->searchService->logSearch(
                    $filters['search'],
                    $patronymes->total(),
                    auth()->id()
                );
            }

            Log::info('Patronyme search performed', [
                'filters' => $filters,
                'results_count' => $patronymes->total(),
                'response_time_ms' => $responseTime,
                'user_id' => auth()->id()
            ]);

            return view('patronymes.index', compact(
                'patronymes', 'regions', 'provinces', 'communes', 'groupesEthniques', 'ethnies', 'langues'
            ))->with($filters);

        } catch (\Exception $e) {
            Log::error('Error in PatronymeController@index', [
                'error' => $e->getMessage(),
                'filters' => $request->all(),
                'response_time_ms' => round((microtime(true) - $startTime) * 1000, 3)
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

        if (strlen($query) < 1) {
            return response()->json([]);
        }

        try {
            // Utiliser le service de suggestions amélioré
            $suggestions = $this->searchService->getAdvancedSuggestions($query, 15);

            return response()->json($suggestions);

        } catch (\Exception $e) {
            Log::error('Error in getSearchSuggestions', [
                'error' => $e->getMessage(),
                'query' => $query
            ]);

            return response()->json([]);
        }
    }

    /**
     * Obtenir les patronymes populaires
     */
    public function getPopularPatronymes()
    {
        try {
            $popular = Cache::remember('popular_patronymes_list', 1800, function () {
                return Patronyme::orderBy('views_count', 'desc')
                    ->orderBy('frequence', 'desc')
                    ->limit(20)
                    ->get(['nom', 'signification', 'views_count']);
            });

            return response()->json($popular);
        } catch (\Exception $e) {
            Log::error('Error in getPopularPatronymes', [
                'error' => $e->getMessage()
            ]);

            return response()->json([]);
        }
    }

    /**
     * Obtenir les patronymes par lettre
     */
    public function getPatronymesByLetter($letter)
    {
        try {
            $patronymes = Cache::remember("patronymes_letter_{$letter}", 3600, function () use ($letter) {
                return Patronyme::where('nom', 'LIKE', "{$letter}%")
                    ->orderBy('nom')
                    ->get(['nom', 'signification', 'views_count']);
            });

            return response()->json($patronymes);
        } catch (\Exception $e) {
            Log::error('Error in getPatronymesByLetter', [
                'error' => $e->getMessage(),
                'letter' => $letter
            ]);

            return response()->json([]);
        }
    }




    public function create()
    {
        $regions = Region::orderBy('name')->get();
        $groupesEthniques = GroupeEthnique::orderBy('nom')->get();
        $ethnies = \App\Models\Ethnie::orderBy('nom')->get();
        $langues = \App\Models\Langue::orderBy('nom')->get();
        $modesTransmission = \App\Models\ModeTransmission::orderBy('nom')->get();

        return view('patronymes.create', compact('regions', 'groupesEthniques', 'ethnies', 'langues', 'modesTransmission'));
    }

    public function store(StorePatronymeRequest $request)
    {
        try {
            $patronyme = Patronyme::create($request->validated());

            Log::info('Patronyme created', [
                'patronyme_id' => $patronyme->id,
                'nom' => $patronyme->nom,
                'user_id' => auth()->id()
            ]);

            return redirect()->route('patronymes.show', $patronyme)
                ->with('success', 'Patronyme créé avec succès.');
        } catch (\Exception $e) {
            Log::error('Error creating patronyme', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'request_data' => $request->all()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la création du patronyme.');
        }
    }

    public function edit(Patronyme $patronyme)
    {
        $regions = Region::orderBy('name')->get();
        $provinces = $patronyme->region_id
            ? Province::where('region_id', $patronyme->region_id)->orderBy('nom')->get()
            : collect();
        $communes = $patronyme->province_id
            ? Commune::where('province_id', $patronyme->province_id)->orderBy('nom')->get()
            : collect();
        $groupesEthniques = GroupeEthnique::orderBy('nom')->get();
        $ethnies = \App\Models\Ethnie::orderBy('nom')->get();
        $langues = \App\Models\Langue::orderBy('nom')->get();
        $modesTransmission = \App\Models\ModeTransmission::orderBy('nom')->get();

        return view('patronymes.edit', compact('patronyme', 'regions', 'provinces', 'communes', 'groupesEthniques', 'ethnies', 'langues', 'modesTransmission'));
    }

    public function update(UpdatePatronymeRequest $request, Patronyme $patronyme)
    {
        try {
            $patronyme->update($request->validated());

            Log::info('Patronyme updated', [
                'patronyme_id' => $patronyme->id,
                'nom' => $patronyme->nom,
                'user_id' => auth()->id()
            ]);

            return redirect()->route('patronymes.show', $patronyme)
                ->with('success', 'Patronyme mis à jour avec succès.');
        } catch (\Exception $e) {
            Log::error('Error updating patronyme', [
                'error' => $e->getMessage(),
                'patronyme_id' => $patronyme->id,
                'user_id' => auth()->id(),
                'request_data' => $request->all()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la mise à jour du patronyme.');
        }
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

    /**
     * Partager un patronyme
     */
    public function share(Patronyme $patronyme)
    {
        $patronyme->load(['region', 'province', 'commune', 'groupeEthnique', 'ethnie', 'langue']);

        return response()->json([
            'patronyme' => $patronyme,
            'share_url' => route('patronymes.show', $patronyme),
            'share_text' => "Découvrez l'origine du patronyme {$patronyme->nom} sur Patronymes BF"
        ]);
    }

    /**
     * Exporter les patronymes
     */
    public function export($format = 'json')
    {
        $patronymes = Patronyme::with(['region', 'province', 'commune', 'groupeEthnique', 'ethnie', 'langue'])
                              ->orderBy('nom')
                              ->get();

        switch ($format) {
            case 'csv':
                $filename = 'patronymes-' . date('Y-m-d') . '.csv';
                $headers = [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                ];

                $callback = function() use ($patronymes) {
                    $file = fopen('php://output', 'w');

                    // En-têtes CSV
                    fputcsv($file, [
                        'Nom', 'Signification', 'Origine', 'Histoire', 'Totem',
                        'Région', 'Province', 'Commune', 'Groupe ethnique', 'Ethnie', 'Langue',
                        'Fréquence', 'Vues', 'Date création'
                    ]);

                    // Données
                    foreach ($patronymes as $patronyme) {
                        fputcsv($file, [
                            $patronyme->nom,
                            $patronyme->signification,
                            $patronyme->origine,
                            $patronyme->histoire,
                            $patronyme->totem,
                            $patronyme->region ? $patronyme->region->name : '',
                            $patronyme->province ? $patronyme->province->nom : '',
                            $patronyme->commune ? $patronyme->commune->nom : '',
                            $patronyme->groupeEthnique ? $patronyme->groupeEthnique->nom : '',
                            $patronyme->ethnie ? $patronyme->ethnie->nom : '',
                            $patronyme->langue ? $patronyme->langue->nom : '',
                            $patronyme->frequence,
                            $patronyme->views_count,
                            $patronyme->created_at->format('Y-m-d H:i:s')
                        ]);
                    }

                    fclose($file);
                };

                return response()->stream($callback, 200, $headers);

            case 'json':
            default:
                $filename = 'patronymes-' . date('Y-m-d') . '.json';
                return response()->json($patronymes)
                               ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
        }
    }
}
