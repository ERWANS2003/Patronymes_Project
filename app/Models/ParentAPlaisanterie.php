<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParentAPlaisanterie extends Model
{
    use HasFactory;

    protected $fillable = [
        'ethnie_1_id',
        'ethnie_2_id',
        'description'
    ];

    public function ethnie1()
    {
        return $this->belongsTo(Ethnie::class, 'ethnie_1_id');
    }

    public function ethnie2()
    {
        return $this->belongsTo(Ethnie::class, 'ethnie_2_id');
    }
}
