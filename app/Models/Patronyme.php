<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patronyme extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'signification',
        'origine',
        'region_id',
        'province_id',
        'commune_id',
        'groupe_ethnique_id',
        'langue_id',
        'mode_transmission_id'
    ];

    public function region()
    {
        return $this->belongsTo(Region::class);
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

    public function scopeByGroupeEthnique($query, $groupeEthniqueId)
    {
        return $query->where('groupe_ethnique_id', $groupeEthniqueId);
    }

    public function scopeByLangue($query, $langueId)
    {
        return $query->where('langue_id', $langueId);
    }
}
