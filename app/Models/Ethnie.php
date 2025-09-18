<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ethnie extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'groupe_ethnique_id'];

    public function groupeEthnique()
    {
        return $this->belongsTo(GroupeEthnique::class);
    }

    public function patronymes()
    {
        return $this->hasMany(Patronyme::class);
    }

    public function parentsAPlaisanterie()
    {
        return $this->hasMany(ParentAPlaisanterie::class, 'ethnie_1_id')
                    ->orWhere('ethnie_2_id', $this->id);
    }
}
