<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comic extends Model
{
    use HasFactory;

    protected $table = 'comics';

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'synopsis',
        'cover_image',
        'status',
        'is_hidden',
    ];

    protected function casts(): array
    {
        return [
            'is_hidden' => 'boolean',
        ];
    }

    /**
     * Relasi ke kreator pemilik komik.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke chapters milik komik ini.
     */
    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class, 'comic_id');
    }
}
