<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Departement extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'region_id'];

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function patronymes(): HasMany
    {
        return $this->hasMany(Patronyme::class);
    }
}
