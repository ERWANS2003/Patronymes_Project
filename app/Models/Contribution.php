<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'contenu',
        'patronyme_id',
        'contributeur_id',
        'date_contribution',
        'statut'
    ];

    protected $casts = [
        'date_contribution' => 'datetime',
    ];

    const STATUT_EN_ATTENTE = 'en_attente';
    const STATUT_APPROUVE = 'approuve';
    const STATUT_REJETE = 'rejete';

    public function patronyme()
    {
        return $this->belongsTo(Patronyme::class);
    }

    public function contributeur()
    {
        return $this->belongsTo(Contributeur::class);
    }
}
