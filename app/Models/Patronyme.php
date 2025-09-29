<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patronyme extends Model
{
    use HasFactory;

    protected $fillable = [
        // Informations sur l'enquêté
        'enquete_nom',
        'enquete_age',
        'enquete_sexe',
        'enquete_fonction',
        'enquete_contact',

        // Informations sur le patronyme
        'nom',
        'groupe_ethnique_id',
        'origine',
        'signification',
        'histoire',
        'langue_id',
        'transmission',
        'patronyme_sexe',
        'totem',
        'justification_totem',
        'parents_plaisanterie',

        // Localisation
        'region_id',
        'province_id',
        'commune_id',

        // Champs existants
        'departement_id',
        'frequence',
        'views_count',
        'is_featured',
        'ethnie_id',
        'mode_transmission_id',
    ];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function departement()
    {
        return $this->belongsTo(Departement::class);
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function commune()
    {
        return $this->belongsTo(Commune::class);
    }

    public function groupeEthnique()
    {
        return $this->belongsTo(GroupeEthnique::class);
    }

    public function ethnie()
    {
        return $this->belongsTo(Ethnie::class);
    }

    public function langue()
    {
        return $this->belongsTo(Langue::class);
    }

    public function modeTransmission()
    {
        return $this->belongsTo(ModeTransmission::class);
    }

    public function contributions()
    {
        return $this->hasMany(Contribution::class);
    }

    public function commentaires()
    {
        return $this->hasMany(Commentaire::class);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('nom', 'like', "%{$search}%")
                    ->orWhere('signification', 'like', "%{$search}%")
                    ->orWhere('origine', 'like', "%{$search}%");
    }

    public function scopeByRegion($query, $regionId)
    {
        return $query->where('region_id', $regionId);
    }

    public function scopeByDepartement($query, $departementId)
    {
        return $query->where('departement_id', $departementId);
    }

    public function scopeByProvince($query, $provinceId)
    {
        return $query->where('province_id', $provinceId);
    }

    public function scopeByCommune($query, $communeId)
    {
        return $query->where('commune_id', $communeId);
    }

    public function scopeByGroupeEthnique($query, $groupeEthniqueId)
    {
        return $query->where('groupe_ethnique_id', $groupeEthniqueId);
    }

    public function scopeByEthnie($query, $ethnieId)
    {
        return $query->where('ethnie_id', $ethnieId);
    }

    public function scopeByLangue($query, $langueId)
    {
        return $query->where('langue_id', $langueId);
    }

    public function scopeByModeTransmission($query, $modeTransmissionId)
    {
        return $query->where('mode_transmission_id', $modeTransmissionId);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopePopular($query)
    {
        return $query->orderBy('views_count', 'desc');
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    // Favorites relationship
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function favoritedByUsers()
    {
        return $this->belongsToMany(User::class, 'favorites');
    }

    // Helper methods
    public function incrementViews()
    {
        $this->increment('views_count');
    }

    public function isFavoritedBy($userId)
    {
        return $this->favorites()->where('user_id', $userId)->exists();
    }
}
