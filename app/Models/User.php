<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'coin_balance',
        'avatar',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'coin_balance' => 'integer',
            'is_active' => 'boolean',
        ];
    }
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isKreator(): bool
    {
        return $this->role === 'kreator';
    }

    public function isRegularUser(): bool
    {
        return $this->role === 'user';
    }

    
    public function hasRole(string|array $roles): bool
    {
        if (is_array($roles)) {
            return in_array($this->role, $roles, true);
        }

        return $this->role === $roles;
    }

    
    public function getAvatarUrlAttribute(): string
    {
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=dc2626&color=ffffff&bold=true';
    }

    public function comics(): HasMany
    {
        return $this->hasMany(Comic::class, 'user_id');
    }

    public function reviewedChapters(): HasMany
    {
        return $this->hasMany(Chapter::class, 'reviewed_by');
    }
    
    public function comments(): HasMany
    {
        return $this->hasMany(\App\Models\Comment::class, 'user_id');
    }

    public function coinTransactions(): HasMany
    {
        return $this->hasMany(\App\Models\CoinTransaction::class, 'user_id');
    }


    public function chapterAccesses(): HasMany
    {
        return $this->hasMany(\App\Models\ChapterAccess::class, 'user_id');
    }
}
