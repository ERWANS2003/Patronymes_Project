<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patronyme;
use App\Models\Favorite;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SyncController extends Controller
{
    /**
     * Synchroniser les patronymes depuis le mobile
     */
    public function syncPatronymes(Request $request)
    {
        try {
            $user = $request->user();
            $patronymes = $request->input('patronymes', []);
            $synced = [];
            $conflicts = [];

            DB::beginTransaction();

            foreach ($patronymes as $patronymeData) {
                try {
                    $result = $this->syncPatronyme($patronymeData, $user);
                    $synced[] = $result['patronyme'];

                    if ($result['conflict']) {
                        $conflicts[] = $result['conflict'];
                    }
                } catch (\Exception $e) {
                    Log::error('Sync patronyme failed', [
                        'patronyme' => $patronymeData,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'synced' => $synced,
                'conflicts' => $conflicts,
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Sync patronymes failed', ['error' => $e->getMessage()]);

            return response()->json([
                'error' => 'Erreur lors de la synchronisation'
            ], 500);
        }
    }

    /**
     * Synchroniser un patronyme individuel
     */
    private function syncPatronyme($data, $user)
    {
        $serverId = $data['server_id'] ?? null;
        $localUpdatedAt = Carbon::parse($data['local_updated_at'] ?? now());
        $action = $data['action'] ?? 'update';

        if ($action === 'delete') {
            if ($serverId) {
                $patronyme = Patronyme::find($serverId);
                if ($patronyme) {
                    $patronyme->delete();
                }
            }
            return ['patronyme' => null, 'conflict' => null];
        }

        if ($serverId) {
            // Mise à jour d'un patronyme existant
            $patronyme = Patronyme::find($serverId);

            if (!$patronyme) {
                // Le patronyme a été supprimé côté serveur
                return [
                    'patronyme' => null,
                    'conflict' => [
                        'type' => 'deleted_on_server',
                        'local_data' => $data,
                        'server_data' => null
                    ]
                ];
            }

            // Vérifier les conflits de version
            $serverUpdatedAt = Carbon::parse($patronyme->updated_at);

            if ($serverUpdatedAt->gt($localUpdatedAt)) {
                // Conflit : le serveur a une version plus récente
                return [
                    'patronyme' => $patronyme->fresh(),
                    'conflict' => [
                        'type' => 'server_newer',
                        'local_data' => $data,
                        'server_data' => $patronyme->toArray()
                    ]
                ];
            }

            // Mettre à jour le patronyme
            $patronyme->update($this->sanitizePatronymeData($data));

        } else {
            // Création d'un nouveau patronyme
            $patronyme = Patronyme::create($this->sanitizePatronymeData($data));
        }

        return ['patronyme' => $patronyme->fresh(), 'conflict' => null];
    }

    /**
     * Récupérer les patronymes mis à jour depuis une date
     */
    public function getUpdatedPatronymes(Request $request)
    {
        try {
            $since = $request->input('since');
            $perPage = $request->input('per_page', 50);

            $query = Patronyme::with(['region', 'province', 'commune', 'groupeEthnique'])
                ->orderBy('updated_at', 'asc');

            if ($since) {
                $query->where('updated_at', '>', Carbon::parse($since));
            }

            $patronymes = $query->paginate($perPage);

            return response()->json([
                'data' => $patronymes->items(),
                'pagination' => [
                    'current_page' => $patronymes->currentPage(),
                    'last_page' => $patronymes->lastPage(),
                    'per_page' => $patronymes->perPage(),
                    'total' => $patronymes->total(),
                ],
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('Get updated patronymes failed', ['error' => $e->getMessage()]);

            return response()->json([
                'error' => 'Erreur lors de la récupération des données'
            ], 500);
        }
    }

    /**
     * Synchroniser les favoris
     */
    public function syncFavorites(Request $request)
    {
        try {
            $user = $request->user();
            $favorites = $request->input('favorites', []);
            $synced = [];

            DB::beginTransaction();

            foreach ($favorites as $favoriteData) {
                $patronymeId = $favoriteData['patronyme_id'];
                $action = $favoriteData['action'] ?? 'create';

                if ($action === 'delete') {
                    Favorite::where('user_id', $user->id)
                        ->where('patronyme_id', $patronymeId)
                        ->delete();
                } else {
                    Favorite::updateOrCreate([
                        'user_id' => $user->id,
                        'patronyme_id' => $patronymeId
                    ], [
                        'created_at' => now()
                    ]);
                }

                $synced[] = $patronymeId;
            }

            DB::commit();

            return response()->json([
                'synced' => $synced,
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Sync favorites failed', ['error' => $e->getMessage()]);

            return response()->json([
                'error' => 'Erreur lors de la synchronisation des favoris'
            ], 500);
        }
    }

    /**
     * Récupérer le timestamp de la dernière synchronisation
     */
    public function getLastSyncTimestamp(Request $request)
    {
        try {
            $user = $request->user();

            // Récupérer le timestamp de la dernière modification
            $lastPatronymeUpdate = Patronyme::max('updated_at');
            $lastFavoriteUpdate = Favorite::where('user_id', $user->id)->max('created_at');

            $lastSync = collect([$lastPatronymeUpdate, $lastFavoriteUpdate])
                ->filter()
                ->max();

            return response()->json([
                'timestamp' => $lastSync ? Carbon::parse($lastSync)->toISOString() : null
            ]);

        } catch (\Exception $e) {
            Log::error('Get last sync timestamp failed', ['error' => $e->getMessage()]);

            return response()->json([
                'error' => 'Erreur lors de la récupération du timestamp'
            ], 500);
        }
    }

    /**
     * Résoudre un conflit
     */
    public function resolveConflict(Request $request)
    {
        try {
            $conflictId = $request->input('conflict_id');
            $resolution = $request->input('resolution'); // 'server' ou 'local'
            $data = $request->input('data');

            // Logique de résolution des conflits
            // Implementation dépendante de votre structure de conflits

            return response()->json([
                'resolved' => true,
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('Resolve conflict failed', ['error' => $e->getMessage()]);

            return response()->json([
                'error' => 'Erreur lors de la résolution du conflit'
            ], 500);
        }
    }

    /**
     * Nettoyer les données du patronyme
     */
    private function sanitizePatronymeData($data)
    {
        $allowedFields = [
            'nom', 'origine', 'signification', 'histoire', 'transmission',
            'patronyme_sexe', 'totem', 'justification_totem', 'parents_plaisanterie',
            'region_id', 'province_id', 'commune_id', 'departement_id',
            'groupe_ethnique_id', 'langue_id', 'frequence', 'views_count',
            'is_featured'
        ];

        return collect($data)->only($allowedFields)->toArray();
    }

    /**
     * Vérifier l'état de santé de l'API
     */
    public function health()
    {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toISOString(),
            'version' => '1.0.0'
        ]);
    }
}
