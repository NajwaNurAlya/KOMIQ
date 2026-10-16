<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'coin_balance',
        'avatar',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
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
            'coin_balance' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Cek apakah user memiliki peran admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Cek apakah user memiliki peran kreator.
     */
    public function isKreator(): bool
    {
        return $this->role === 'kreator';
    }

    /**
     * Cek apakah user memiliki peran pembaca biasa.
     */
    public function isRegularUser(): bool
    {
        return $this->role === 'user';
    }

    /**
     * Relasi ke komik milik kreator ini.
     */
    public function comics(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Comic::class, 'user_id');
    }

    /**
     * Relasi ke chapter yang telah diperiksa oleh admin ini.
     */
    public function reviewedChapters(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Chapter::class, 'reviewed_by');
    }
}
