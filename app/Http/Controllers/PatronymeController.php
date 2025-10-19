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
use App\Models\Contribution;
use App\Models\Contributeur;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Services\SearchService;
use App\Services\StatisticsService;
use App\Services\AdvancedSearchService;
use App\Http\Requests\StorePatronymeRequest;
use App\Http\Requests\UpdatePatronymeRequest;

class PatronymeController extends Controller
{
    protected $searchService;
    protected $statisticsService;
    protected $advancedSearchService;

    public function __construct(SearchService $searchService, StatisticsService $statisticsService, AdvancedSearchService $advancedSearchService)
    {
        $this->searchService = $searchService;
        $this->statisticsService = $statisticsService;
        $this->advancedSearchService = $advancedSearchService;
    }
    public function index(Request $request)
    {
        try {
            $search = trim($request->input('search'));
            $featured = $request->input('featured');

            // Check cache for frequent searches
            $cacheKey = 'search_' . md5($search . '_' . $featured);
            $patronymes = Cache::remember($cacheKey, 300, function() use ($search, $featured) {
                // Optimized search query with proper indexing
                $query = Patronyme::with(['region', 'departement', 'groupeEthnique'])
                    ->select(['id', 'nom', 'signification', 'origine', 'region_id', 'departement_id', 'groupe_ethnique_id', 'created_at', 'views_count', 'is_featured']);

                if ($search) {
                    // Enhanced search with fuzzy matching and variations
                    $searchVariations = $this->getSearchVariations($search);

                    $query->where(function($q) use ($search) {
                        // Search in multiple fields with case insensitive matching
                        $q->where('nom', 'ilike', '%' . $search . '%')
                          ->orWhere('signification', 'ilike', '%' . $search . '%')
                          ->orWhere('origine', 'ilike', '%' . $search . '%');
                    });

                    // Log search for analytics
                    $this->logSearch($search);
                }

                // Handle featured/popular patronymes
                if ($featured) {
                    $query->where('is_featured', true)
                          ->orWhere('views_count', '>', 0)
                          ->orderBy('views_count', 'desc')
                          ->orderBy('created_at', 'desc');
                } else {
                    $query->orderBy('nom');
                }

                return $query->with(['region', 'province', 'commune', 'groupeEthnique', 'langue'])->paginate(12);
            });

            // Check if this is an AJAX request for real-time search
            if (request()->ajax() && request()->has('ajax')) {
                return response()->json([
                    'patronymes' => $patronymes->items(),
                    'total' => $patronymes->total(),
                    'current_page' => $patronymes->currentPage(),
                    'last_page' => $patronymes->lastPage(),
                    'per_page' => $patronymes->perPage(),
                    'from' => $patronymes->firstItem(),
                    'to' => $patronymes->lastItem(),
                ]);
            }

            return view('patronymes.index', compact('patronymes'));

        } catch (\Exception $e) {
            Log::error('Error in PatronymeController@index', [
                'error' => $e->getMessage(),
                'search' => $request->input('search'),
                'featured' => $request->input('featured')
            ]);

            return redirect()->back()->with('error', 'Une erreur est survenue lors de la recherche.');
        }
    }

    /**
     * Get search suggestions for autocomplete
     */
    public function suggestions(Request $request)
    {
        $query = $request->input('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        // Cache suggestions for better performance
        $cacheKey = 'suggestions_' . md5($query);
        $suggestions = Cache::remember($cacheKey, 180, function() use ($query) {
            return Patronyme::select('nom', 'signification', 'views_count')
                ->whereRaw('LOWER(nom) LIKE ?', [strtolower($query) . '%'])
                ->orderBy('views_count', 'desc')
                ->orderBy('nom')
                ->limit(10)
                ->get()
                ->map(function($patronyme) {
                    return [
                        'value' => $patronyme->nom,
                        'label' => $patronyme->nom,
                        'description' => $patronyme->signification ? Str::limit($patronyme->signification, 50) : null,
                        'type' => 'Patronyme',
                        'popularity' => $patronyme->views_count
                    ];
                });
        });

        return response()->json($suggestions);
    }

    /**
     * Generate search variations for better matching
     */
    private function getSearchVariations($searchTerm)
    {
        $variations = [];
        $searchTerm = strtolower(trim($searchTerm));

        // Common character substitutions for Burkinabé names
        $substitutions = [
            'é' => ['e', 'è', 'ê'],
            'è' => ['e', 'é', 'ê'],
            'ê' => ['e', 'é', 'è'],
            'ô' => ['o', 'ö'],
            'ö' => ['o', 'ô'],
            'ü' => ['u', 'ou'],
            'ou' => ['u', 'ü'],
            'c' => ['k', 'q'],
            'k' => ['c', 'q'],
            'q' => ['c', 'k'],
        ];

        // Generate variations
        foreach ($substitutions as $original => $replacements) {
            if (strpos($searchTerm, $original) !== false) {
                foreach ($replacements as $replacement) {
                    $variations[] = str_replace($original, $replacement, $searchTerm);
                }
            }
        }

        // Add common prefixes/suffixes for Burkinabé names
        $commonPrefixes = ['ou', 'oua', 'wa'];
        $commonSuffixes = ['ou', 'oua', 'wa', 'ga', 'ba'];

        foreach ($commonPrefixes as $prefix) {
            if (strpos($searchTerm, $prefix) !== 0) {
                $variations[] = $prefix . $searchTerm;
            }
        }

        foreach ($commonSuffixes as $suffix) {
            if (substr($searchTerm, -strlen($suffix)) !== $suffix) {
                $variations[] = $searchTerm . $suffix;
            }
        }

        return array_unique($variations);
    }

    /**
     * Log search queries for analytics and optimization
     */
    private function logSearch($searchTerm)
    {
        try {
            // Log search directly to search_logs table
            \DB::table('search_logs')->insert([
                'query' => $searchTerm,
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'results_count' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Invalider le cache des statistiques utilisateur pour les recherches
            if (auth()->check()) {
                $this->statisticsService->clearUserActivityCache(auth()->id());
            }
        } catch (\Exception $e) {
            Log::warning('Failed to log search', ['error' => $e->getMessage()]);
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
        $regions = Region::orderBy('nom')->get();
        $groupesEthniques = GroupeEthnique::orderBy('nom')->get();
        $ethnies = \App\Models\Ethnie::orderBy('nom')->get();
        $langues = \App\Models\Langue::orderBy('nom')->get();
        $modesTransmission = \App\Models\ModeTransmission::orderBy('type')->get();

        return view('patronymes.create', compact('regions', 'groupesEthniques', 'ethnies', 'langues', 'modesTransmission'));
    }

    public function store(StorePatronymeRequest $request)
    {
        try {
            $patronyme = Patronyme::create($request->validated());

            // Invalider le cache des statistiques utilisateur pour les contributions
            if (auth()->check()) {
                $this->statisticsService->clearUserActivityCache(auth()->id());
            }

            // Enregistrer une contribution liée à l'utilisateur authentifié
            if (auth()->check()) {
                $user = auth()->user();
                $contributeur = Contributeur::firstOrCreate(
                    ['utilisateur_id' => $user->id],
                    [
                        'nom' => $user->name ?? 'Contributeur',
                        'contact' => $user->email ?? null,
                    ]
                );

                Contribution::create([
                    'contenu' => 'Création du patronyme',
                    'patronyme_id' => $patronyme->id,
                    'contributeur_id' => $contributeur->id,
                    'date_contribution' => now(),
                    'statut' => \App\Models\Contribution::STATUT_APPROUVE,
                ]);
            }

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
        $regions = Region::orderBy('nom')->get();
        $provinces = $patronyme->region_id
            ? Province::where('region_id', $patronyme->region_id)->orderBy('nom')->get()
            : collect();
        $communes = $patronyme->province_id
            ? Commune::where('province_id', $patronyme->province_id)->orderBy('nom')->get()
            : collect();
        $groupesEthniques = GroupeEthnique::orderBy('nom')->get();
        $ethnies = \App\Models\Ethnie::orderBy('nom')->get();
        $langues = \App\Models\Langue::orderBy('nom')->get();
        $modesTransmission = \App\Models\ModeTransmission::orderBy('type')->get();

        return view('patronymes.edit', compact('patronyme', 'regions', 'provinces', 'communes', 'groupesEthniques', 'ethnies', 'langues', 'modesTransmission'));
    }

    public function update(UpdatePatronymeRequest $request, Patronyme $patronyme)
    {
        try {
            $patronyme->update($request->validated());

            // Invalider le cache des statistiques utilisateur pour les contributions
            if (auth()->check()) {
                $this->statisticsService->clearUserActivityCache(auth()->id());
            }

            // Enregistrer une contribution liée à l'utilisateur authentifié
            if (auth()->check()) {
                $user = auth()->user();
                $contributeur = Contributeur::firstOrCreate(
                    ['utilisateur_id' => $user->id],
                    [
                        'nom' => $user->name ?? 'Contributeur',
                        'contact' => $user->email ?? null,
                    ]
                );

                Contribution::create([
                    'contenu' => 'Mise à jour du patronyme',
                    'patronyme_id' => $patronyme->id,
                    'contributeur_id' => $contributeur->id,
                    'date_contribution' => now(),
                    'statut' => \App\Models\Contribution::STATUT_APPROUVE,
                ]);
            }

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
                            $patronyme->region ? $patronyme->region->nom : '',
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

    /**
     * Remove accents from a string
     */
    private function removeAccents(string $string): string
    {
        $accents = ['à', 'á', 'â', 'ã', 'ä', 'å', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'ñ', 'ç'];
        $noAccents = ['a', 'a', 'a', 'a', 'a', 'a', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'n', 'c'];

        return str_replace($accents, $noAccents, strtolower($string));
    }
}
