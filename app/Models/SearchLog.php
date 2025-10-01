<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SearchLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'query',
        'results_count',
        'user_id',
        'ip_address',
        'user_agent',
        'response_time',
    ];

    protected $casts = [
        'results_count' => 'integer',
        'response_time' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopePopular($query, $limit = 10)
    {
        return $query->select('query', \DB::raw('count(*) as count'))
                    ->groupBy('query')
                    ->orderBy('count', 'desc')
                    ->limit($limit);
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
