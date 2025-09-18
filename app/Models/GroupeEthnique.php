<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupeEthnique extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'description'];

    public function ethnies()
    {
        return $this->hasMany(Ethnie::class);
    }

    public function patronymes()
    {
        return $this->hasMany(Patronyme::class);
    }
}
