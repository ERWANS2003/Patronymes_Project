<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code'];

    public function provinces()
    {
        return $this->hasMany(Province::class);
    }

    public function patronymes()
    {
        return $this->hasMany(Patronyme::class);
    }

    public function departements()
    {
        return $this->hasMany(Departement::class);
    }
}
