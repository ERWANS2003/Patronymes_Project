<?php

namespace App\Services;

use App\Models\Patronyme;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class QueryOptimizationService
{
    /**
     * Optimise les requêtes de recherche avec eager loading
     */
    public static function optimizePatronymeQuery(Builder $query, array $relations = []): Builder
    {
        $defaultRelations = [
            'region:id,name,code',
            'province:id,nom,region_id',
            'commune:id,nom,province_id',
            'groupeEthnique:id,nom',
            'ethnie:id,nom',
            'langue:id,nom',
            'modeTransmission:id,nom'
        ];

        $relationsToLoad = array_merge($defaultRelations, $relations);

        return $query->with($relationsToLoad);
    }

    /**
     * Optimise les requêtes de statistiques
     */
    public static function getOptimizedStats(): array
    {
        return [
            'total_patronymes' => Patronyme::count(),
            'total_users' => User::count(),
            'total_views' => Patronyme::sum('views_count'),
            'total_favorites' => DB::table('favorites')->count(),
            'contributions_today' => Patronyme::whereDate('created_at', today())->count(),
            'contributions_this_week' => Patronyme::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'active_users_today' => User::whereDate('last_login_at', today())->count(),
            'search_count_today' => DB::table('search_logs')->whereDate('created_at', today())->count(),
        ];
    }

    /**
     * Optimise les requêtes de recherche populaire
     */
    public static function getPopularPatronymes(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return Patronyme::select(['id', 'nom', 'views_count', 'region_id', 'groupe_ethnique_id'])
            ->with(['region:id,name', 'groupeEthnique:id,nom'])
            ->orderBy('views_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Optimise les requêtes de patronymes récents
     */
    public static function getRecentPatronymes(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return Patronyme::select(['id', 'nom', 'created_at', 'region_id', 'groupe_ethnique_id'])
            ->with(['region:id,name', 'groupeEthnique:id,nom'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Optimise les requêtes de patronymes mis en avant
     */
    public static function getFeaturedPatronymes(int $limit = 5): \Illuminate\Database\Eloquent\Collection
    {
        return Patronyme::select(['id', 'nom', 'views_count', 'region_id', 'groupe_ethnique_id'])
            ->with(['region:id,name', 'groupeEthnique:id,nom'])
            ->where('is_featured', true)
            ->orderBy('views_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Optimise les requêtes de recherche avec filtres
     */
    public static function getOptimizedSearchQuery(array $filters = []): Builder
    {
        $query = Patronyme::query();

        // Appliquer les filtres avec des index optimisés
        if (!empty($filters['search'])) {
            $searchTerm = $filters['search'];
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nom', 'ILIKE', "%{$searchTerm}%")
                  ->orWhere('signification', 'ILIKE', "%{$searchTerm}%")
                  ->orWhere('origine', 'ILIKE', "%{$searchTerm}%")
                  ->orWhere('histoire', 'ILIKE', "%{$searchTerm}%");
            });
        }

        if (!empty($filters['region_id'])) {
            $query->where('region_id', $filters['region_id']);
        }

        if (!empty($filters['groupe_ethnique_id'])) {
            $query->where('groupe_ethnique_id', $filters['groupe_ethnique_id']);
        }

        if (!empty($filters['langue_id'])) {
            $query->where('langue_id', $filters['langue_id']);
        }

        if (!empty($filters['patronyme_sexe'])) {
            $query->where('patronyme_sexe', $filters['patronyme_sexe']);
        }

        if (!empty($filters['transmission'])) {
            $query->where('transmission', $filters['transmission']);
        }

        if (!empty($filters['min_frequence'])) {
            $query->where('frequence', '>=', $filters['min_frequence']);
        }

        if (!empty($filters['max_frequence'])) {
            $query->where('frequence', '<=', $filters['max_frequence']);
        }

        // Optimiser l'ordre avec des index
        $query->orderBy('views_count', 'desc')
              ->orderBy('created_at', 'desc');

        return self::optimizePatronymeQuery($query);
    }

    /**
     * Optimise les requêtes de statistiques par région
     */
    public static function getRegionStats(): \Illuminate\Database\Eloquent\Collection
    {
        return DB::table('regions')
            ->leftJoin('patronymes', 'regions.id', '=', 'patronymes.region_id')
            ->select([
                'regions.id',
                'regions.name',
                'regions.code',
                DB::raw('COUNT(patronymes.id) as patronymes_count'),
                DB::raw('SUM(patronymes.views_count) as total_views'),
                DB::raw('AVG(patronymes.views_count) as avg_views')
            ])
            ->groupBy('regions.id', 'regions.name', 'regions.code')
            ->orderBy('patronymes_count', 'desc')
            ->get();
    }

    /**
     * Optimise les requêtes de statistiques par groupe ethnique
     */
    public static function getEthnicGroupStats(): \Illuminate\Database\Eloquent\Collection
    {
        return DB::table('groupe_ethniques')
            ->leftJoin('patronymes', 'groupe_ethniques.id', '=', 'patronymes.groupe_ethnique_id')
            ->select([
                'groupe_ethniques.id',
                'groupe_ethniques.nom',
                DB::raw('COUNT(patronymes.id) as patronymes_count'),
                DB::raw('SUM(patronymes.views_count) as total_views'),
                DB::raw('AVG(patronymes.views_count) as avg_views')
            ])
            ->groupBy('groupe_ethniques.id', 'groupe_ethniques.nom')
            ->orderBy('patronymes_count', 'desc')
            ->get();
    }

    /**
     * Optimise les requêtes de favoris par utilisateur
     */
    public static function getUserFavorites(int $userId, int $limit = 20): \Illuminate\Database\Eloquent\Collection
    {
        return DB::table('favorites')
            ->join('patronymes', 'favorites.patronyme_id', '=', 'patronymes.id')
            ->join('regions', 'patronymes.region_id', '=', 'regions.id')
            ->join('groupe_ethniques', 'patronymes.groupe_ethnique_id', '=', 'groupe_ethniques.id')
            ->select([
                'patronymes.id',
                'patronymes.nom',
                'patronymes.signification',
                'patronymes.views_count',
                'regions.name as region_name',
                'groupe_ethniques.nom as groupe_name',
                'favorites.created_at as favorited_at'
            ])
            ->where('favorites.user_id', $userId)
            ->orderBy('favorites.created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Optimise les requêtes de commentaires
     */
    public static function getPatronymeComments(int $patronymeId, int $limit = 20): \Illuminate\Database\Eloquent\Collection
    {
        return DB::table('commentaires')
            ->join('users', 'commentaires.utilisateur_id', '=', 'users.id')
            ->select([
                'commentaires.id',
                'commentaires.contenu',
                'commentaires.created_at',
                'users.name as user_name',
                'users.id as user_id'
            ])
            ->where('commentaires.patronyme_id', $patronymeId)
            ->orderBy('commentaires.created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Optimise les requêtes de recherche de suggestions
     */
    public static function getSearchSuggestions(string $query, int $limit = 15): array
    {
        $suggestions = [];

        // Recherche dans les patronymes
        $patronymes = DB::table('patronymes')
            ->select(['nom', 'signification'])
            ->where('nom', 'ILIKE', "%{$query}%")
            ->limit($limit)
            ->get();

        foreach ($patronymes as $patronyme) {
            $suggestions[] = [
                'type' => 'patronyme',
                'value' => $patronyme->nom,
                'label' => $patronyme->nom,
                'description' => $patronyme->signification
            ];
        }

        // Recherche dans les régions
        $regions = DB::table('regions')
            ->select(['name'])
            ->where('name', 'ILIKE', "%{$query}%")
            ->limit(5)
            ->get();

        foreach ($regions as $region) {
            $suggestions[] = [
                'type' => 'region',
                'value' => $region->name,
                'label' => "Région: {$region->name}",
                'description' => null
            ];
        }

        return array_slice($suggestions, 0, $limit);
    }

    /**
     * Analyse les performances des requêtes
     */
    public static function analyzeQueryPerformance(string $query): array
    {
        $startTime = microtime(true);

        $result = DB::select($query);

        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000; // en millisecondes

        return [
            'execution_time_ms' => round($executionTime, 3),
            'result_count' => count($result),
            'query' => $query,
            'timestamp' => now()->toISOString()
        ];
    }

    /**
     * Nettoie les requêtes lentes
     */
    public static function getSlowQueries(int $limit = 10): array
    {
        // Cette méthode nécessiterait l'activation du slow query log
        // Pour l'instant, on retourne un exemple
        return [
            'message' => 'Slow query log non activé',
            'suggestion' => 'Activez le slow query log dans votre configuration MySQL/PostgreSQL'
        ];
    }
}
