<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class User extends Authenticatable
{
    use HasApiTokens, SoftDeletes;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'can_contribute',
        'can_manage_roles',
        'last_login_at',
        'login_count',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'can_contribute' => 'boolean',
            'can_manage_roles' => 'boolean',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'login_count' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    // Constants for user roles
    const ROLE_USER = 'user';
    const ROLE_CONTRIBUTEUR = 'contributeur';
    const ROLE_ADMIN = 'admin';

    // Relationships
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function favoritePatronymes()
    {
        return $this->belongsToMany(Patronyme::class, 'favorites');
    }

    public function patronymes()
    {
        // Relation temporaire - retourne une collection vide pour l'instant
        // TODO: Ajouter la colonne created_by à la table patronymes
        return collect([]);
    }

    public function contributions()
    {
        return $this->hasManyThrough(Contribution::class, Contributeur::class, 'utilisateur_id', 'contributeur_id');
    }

    public function commentaires()
    {
        return $this->hasMany(Commentaire::class, 'utilisateur_id');
    }

    // Helper methods
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isContributeur(): bool
    {
        return $this->role === self::ROLE_CONTRIBUTEUR;
    }

    public function isUser(): bool
    {
        return $this->role === self::ROLE_USER;
    }

    public function canContribute(): bool
    {
        return $this->can_contribute || $this->isAdmin() || $this->isContributeur();
    }

    public function canManageRoles(): bool
    {
        return $this->can_manage_roles || $this->isAdmin();
    }

    // Accessor methods
    public function getFullNameAttribute()
    {
        return $this->name;
    }

    public function getInitialsAttribute()
    {
        $names = explode(' ', $this->name);
        $initials = '';
        foreach ($names as $name) {
            $initials .= strtoupper(substr($name, 0, 1));
        }
        return $initials;
    }

    public function getActivityScoreAttribute()
    {
        $score = 0;
        
        // Score basé sur les contributions
        $score += $this->contributions()->count() * 2;
        
        // Score basé sur les commentaires
        $score += $this->commentaires()->count() * 1;
        
        // Score basé sur les favoris
        $score += $this->favorites()->count() * 0.5;
        
        // Score basé sur les connexions
        $score += $this->login_count * 0.1;
        
        return round($score, 2);
    }

    public function getIsActiveAttribute()
    {
        return $this->is_active ?? true;
    }

    // Cache methods
    public static function getCachedActiveUsers($limit = 10)
    {
        return Cache::remember("active_users_{$limit}", 3600, function () use ($limit) {
            return static::where('is_active', true)
                        ->orderBy('login_count', 'desc')
                        ->limit($limit)
                        ->get();
        });
    }

    public static function getCachedRecentUsers($limit = 10)
    {
        return Cache::remember("recent_users_{$limit}", 1800, function () use ($limit) {
            return static::orderBy('created_at', 'desc')
                        ->limit($limit)
                        ->get();
        });
    }

    // Event handlers
    protected static function booted()
    {
        static::created(function ($user) {
            Cache::forget('active_users_*');
            Cache::forget('recent_users_*');
        });

        static::updated(function ($user) {
            Cache::forget('active_users_*');
            Cache::forget('recent_users_*');
        });

        static::deleted(function ($user) {
            Cache::forget('active_users_*');
            Cache::forget('recent_users_*');
        });
    }

    // Login tracking
    public function trackLogin()
    {
        $this->update([
            'last_login_at' => now(),
            'login_count' => $this->login_count + 1,
        ]);
    }

    // Scope methods
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    public function scopeContributors($query)
    {
        return $query->where('can_contribute', true);
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', self::ROLE_ADMIN);
    }
}
