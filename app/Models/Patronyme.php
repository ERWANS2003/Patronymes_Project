<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class Patronyme extends Model
{
    use HasFactory, SoftDeletes;

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

    protected $casts = [
        'enquete_age' => 'integer',
        'frequence' => 'integer',
        'views_count' => 'integer',
        'is_featured' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $appends = [
        'full_location',
        'search_score',
        'is_popular',
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
        if (empty($search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('nom', 'ILIKE', "%{$search}%")
              ->orWhere('signification', 'ILIKE', "%{$search}%")
              ->orWhere('origine', 'ILIKE', "%{$search}%")
              ->orWhere('histoire', 'ILIKE', "%{$search}%")
              ->orWhere('totem', 'ILIKE', "%{$search}%")
              ->orWhere('justification_totem', 'ILIKE', "%{$search}%")
              ->orWhere('parents_plaisanterie', 'ILIKE', "%{$search}%");
        });
    }

    public function scopeAdvancedSearch($query, $filters)
    {
        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (!empty($filters['region_id'])) {
            $query->byRegion($filters['region_id']);
        }

        if (!empty($filters['groupe_ethnique_id'])) {
            $query->byGroupeEthnique($filters['groupe_ethnique_id']);
        }

        if (!empty($filters['langue_id'])) {
            $query->byLangue($filters['langue_id']);
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

        return $query;
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

    // Accessor methods
    public function getFullLocationAttribute()
    {
        $location = [];
        
        if ($this->region) {
            $location[] = $this->region->name;
        }
        
        if ($this->province) {
            $location[] = $this->province->nom;
        }
        
        if ($this->commune) {
            $location[] = $this->commune->nom;
        }
        
        return implode(', ', $location);
    }

    public function getSearchScoreAttribute()
    {
        $score = 0;
        
        // Score basé sur les vues
        $score += $this->views_count * 0.1;
        
        // Score basé sur la fréquence
        $score += $this->frequence * 0.05;
        
        // Score basé sur les commentaires
        $score += $this->commentaires()->count() * 0.2;
        
        // Score basé sur les favoris
        $score += $this->favorites()->count() * 0.3;
        
        return round($score, 2);
    }

    public function getIsPopularAttribute()
    {
        return $this->views_count > 100 || $this->frequence > 50;
    }

    // Cache methods
    public static function getCachedPopular($limit = 10)
    {
        return Cache::remember("popular_patronymes_{$limit}", 3600, function () use ($limit) {
            return static::popular()->limit($limit)->get();
        });
    }

    public static function getCachedRecent($limit = 10)
    {
        return Cache::remember("recent_patronymes_{$limit}", 1800, function () use ($limit) {
            return static::recent()->limit($limit)->get();
        });
    }

    public static function getCachedFeatured($limit = 5)
    {
        return Cache::remember("featured_patronymes_{$limit}", 3600, function () use ($limit) {
            return static::featured()->limit($limit)->get();
        });
    }

    // Event handlers
    protected static function booted()
    {
        static::created(function ($patronyme) {
            Cache::forget('popular_patronymes_*');
            Cache::forget('recent_patronymes_*');
            Cache::forget('featured_patronymes_*');
        });

        static::updated(function ($patronyme) {
            Cache::forget('popular_patronymes_*');
            Cache::forget('recent_patronymes_*');
            Cache::forget('featured_patronymes_*');
        });

        static::deleted(function ($patronyme) {
            Cache::forget('popular_patronymes_*');
            Cache::forget('recent_patronymes_*');
            Cache::forget('featured_patronymes_*');
        });
    }
}
