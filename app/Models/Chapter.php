<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Chapter extends Model
{
    use HasFactory;

    /**
     * Nama tabel di database.
     */
    protected $table = 'chapters';

    /**
     * Konstanta status chapter sesuai SKPL.
     */
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    /**
     * Kolom-kolom yang dapat diisi secara massal (mass assignable).
     *
     * @var list<string>
     */
    protected $fillable = [
        'comic_id',
        'chapter_number',
        'title',
        'pages',
        'is_premium',
        'coin_price',
        'status',
        'review_note',
        'reviewed_by',
        'reviewed_at',
        'published_at',
    ];

    /**
     * Type casting atribut model.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'chapter_number' => 'integer',
            'pages' => 'array',
            'is_premium' => 'boolean',
            'coin_price' => 'integer',
            'reviewed_at' => 'datetime',
            'published_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Relasi ke komik induk (comics).
     */
    public function comic(): BelongsTo
    {
        return $this->belongsTo(Comic::class, 'comic_id');
    }

    /**
     * Relasi ke Admin yang mereview chapter (users).
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Scope query untuk chapter yang berstatus approved.
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope query untuk chapter yang sedang menunggu review (pending).
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope query untuk chapter berstatus draft.
     */
    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    /**
     * Scope query untuk chapter yang ditolak (rejected).
     */
    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    /**
     * Cek apakah chapter berstatus draft.
     */
    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    /**
     * Cek apakah chapter sedang pending review.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Cek apakah chapter sudah approved.
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Cek apakah chapter ditolak (rejected).
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Cek apakah chapter dapat diedit oleh kreator (hanya draft atau rejected).
     */
    public function canBeEdited(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_REJECTED], true);
    }

    /**
     * Cek apakah chapter dapat diajukan untuk review (hanya draft atau rejected).
     */
    public function canBeSubmitted(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_REJECTED], true);
    }

    /**
     * Mendapatkan jumlah halaman pada chapter.
     */
    public function getPageCountAttribute(): int
    {
        return is_array($this->pages) ? count($this->pages) : 0;
    }
}
