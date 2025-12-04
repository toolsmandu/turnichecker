<?php

namespace App\Models;

use App\Models\Submission;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserPack extends Model
{
    protected $fillable = [
        'user_id',
        'pack_id',
        'quota_remaining',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function pack(): BelongsTo
    {
        return $this->belongsTo(Pack::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class, 'user_pack_id');
    }

    public function isExpired(): bool
    {
        return $this->expires_at?->isPast() ?? true;
    }

    public function remainingSlots(): int
    {
        // Prefer eager-loaded count when available to avoid extra queries.
        $completedCount = $this->completed_submissions_count
            ?? $this->submissions()->where('status', 'completed')->count();

        $calculatedFromQuota = ($this->pack?->quota ?? 0) - $completedCount;
        $calculatedFromQuota = max($calculatedFromQuota, 0);

        return max((int) ($this->quota_remaining ?? 0), $calculatedFromQuota);
    }

    public static function assignPack(int $userId, Pack $pack): self
    {
        return static::create([
            'user_id' => $userId,
            'pack_id' => $pack->id,
            'quota_remaining' => $pack->quota,
            'expires_at' => now()->addDays($pack->duration_days),
        ]);
    }
}
