<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
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
     * Cek apakah pengguna memiliki peran Admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Cek apakah pengguna memiliki peran Kreator.
     */
    public function isKreator(): bool
    {
        return $this->role === 'kreator';
    }

    /**
     * Cek apakah pengguna memiliki peran Pembaca biasa.
     */
    public function isRegularUser(): bool
    {
        return $this->role === 'user';
    }

    /**
     * Cek apakah pengguna memiliki peran tertentu (string atau array).
     */
    public function hasRole(string|array $roles): bool
    {
        if (is_array($roles)) {
            return in_array($this->role, $roles, true);
        }

        return $this->role === $roles;
    }

    /**
     * URL Avatar pengguna dengan fallback UI Avatars.
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar && (file_exists(public_path($this->avatar)) || str_starts_with($this->avatar, 'http'))) {
            return str_starts_with($this->avatar, 'http') ? $this->avatar : asset($this->avatar);
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=dc2626&color=ffffff&bold=true';
    }

    /**
     * Relasi ke komik buatan kreator (1:N).
     */
    public function comics(): HasMany
    {
        return $this->hasMany(Comic::class, 'user_id');
    }

    /**
     * Relasi ke chapter yang dimoderasi oleh admin (1:N).
     */
    public function reviewedChapters(): HasMany
    {
        return $this->hasMany(Chapter::class, 'reviewed_by');
    }

    /**
     * Relasi ke komentar pengguna (1:N).
     */
    public function comments(): HasMany
    {
        return $this->hasMany(\App\Models\Comment::class, 'user_id');
    }

    /**
     * Relasi ke riwayat transaksi koin (1:N).
     */
    public function coinTransactions(): HasMany
    {
        return $this->hasMany(\App\Models\CoinTransaction::class, 'user_id');
    }

    /**
     * Relasi ke hak akses chapter premium (1:N).
     */
    public function chapterAccesses(): HasMany
    {
        return $this->hasMany(\App\Models\ChapterAccess::class, 'user_id');
    }
}
