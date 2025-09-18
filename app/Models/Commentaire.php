<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commentaire extends Model
{
    use HasFactory;

    protected $fillable = [
        'contenu',
        'utilisateur_id',
        'patronyme_id',
        'date_commentaire'
    ];

    protected $casts = [
        'date_commentaire' => 'datetime',
    ];

    public function patronyme()
    {
        return $this->belongsTo(Patronyme::class);
    }

    public function utilisateur()
    {
        return $this->belongsTo(User::class);
    }
}
