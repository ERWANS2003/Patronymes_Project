<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatronymeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'origine' => $this->origine,
            'signification' => $this->signification,
            'histoire' => $this->histoire,
            'transmission' => $this->transmission,
            'patronyme_sexe' => $this->patronyme_sexe,
            'totem' => $this->totem,
            'justification_totem' => $this->justification_totem,
            'parents_plaisanterie' => $this->parents_plaisanterie,
            'frequence' => $this->frequence,
            'views_count' => $this->views_count,
            'is_featured' => $this->is_featured,
            'is_popular' => $this->is_popular,
            'search_score' => $this->search_score,
            'full_location' => $this->full_location,

            // Relations
            'region' => $this->whenLoaded('region', function () {
                return [
                    'id' => $this->region->id,
                    'name' => $this->region->name,
                    'code' => $this->region->code,
                ];
            }),

            'province' => $this->whenLoaded('province', function () {
                return [
                    'id' => $this->province->id,
                    'nom' => $this->province->nom,
                ];
            }),

            'commune' => $this->whenLoaded('commune', function () {
                return [
                    'id' => $this->commune->id,
                    'nom' => $this->commune->nom,
                ];
            }),

            'groupe_ethnique' => $this->whenLoaded('groupeEthnique', function () {
                return [
                    'id' => $this->groupeEthnique->id,
                    'nom' => $this->groupeEthnique->nom,
                ];
            }),

            'ethnie' => $this->whenLoaded('ethnie', function () {
                return [
                    'id' => $this->ethnie->id,
                    'nom' => $this->ethnie->nom,
                ];
            }),

            'langue' => $this->whenLoaded('langue', function () {
                return [
                    'id' => $this->langue->id,
                    'nom' => $this->langue->nom,
                ];
            }),

            'mode_transmission' => $this->whenLoaded('modeTransmission', function () {
                return [
                    'id' => $this->modeTransmission->id,
                    'nom' => $this->modeTransmission->nom,
                ];
            }),

            // Statistiques
            'favorites_count' => $this->when($this->relationLoaded('favorites'), function () {
                return $this->favorites->count();
            }),

            'comments_count' => $this->when($this->relationLoaded('commentaires'), function () {
                return $this->commentaires->count();
            }),

            // Commentaires (si demandés)
            'commentaires' => $this->when($this->relationLoaded('commentaires'), function () {
                return $this->commentaires->map(function ($commentaire) {
                    return [
                        'id' => $commentaire->id,
                        'contenu' => $commentaire->contenu,
                        'created_at' => $commentaire->created_at,
                        'utilisateur' => $commentaire->utilisateur ? [
                            'id' => $commentaire->utilisateur->id,
                            'name' => $commentaire->utilisateur->name,
                        ] : null,
                    ];
                });
            }),

            // Informations sur l'enquêté
            'enquete' => [
                'nom' => $this->enquete_nom,
                'age' => $this->enquete_age,
                'sexe' => $this->enquete_sexe,
                'fonction' => $this->enquete_fonction,
                'contact' => $this->enquete_contact,
            ],

            // Timestamps
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // URLs
            'urls' => [
                'show' => route('patronymes.show', $this->id),
                'api_show' => route('api.patronymes.show', $this->id),
            ],
        ];
    }
}
