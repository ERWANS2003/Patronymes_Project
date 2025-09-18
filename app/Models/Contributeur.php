<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contributeur extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'contact', 'utilisateur_id'];

    public function utilisateur()
    {
        return $this->belongsTo(User::class);
    }

    public function contributions()
    {
        return $this->hasMany(Contribution::class);
    }
}
