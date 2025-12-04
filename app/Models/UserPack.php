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
        $now = now(config('app.timezone'));
        return $this->expires_at
            ? $this->expires_at->lessThanOrEqualTo($now)
            : true;
    }

    public function remainingSlots(): int
    {
        // If quota_remaining has been set/adjusted, treat it as the source of truth.
        if (! is_null($this->quota_remaining)) {
            return max((int) $this->quota_remaining, 0);
        }

        // Fallback: derive from pack quota minus completed submissions.
        $completedCount = $this->completed_submissions_count
            ?? $this->submissions()->where('status', 'completed')->count();

        $calculatedFromQuota = ($this->pack?->quota ?? 0) - $completedCount;

        return max($calculatedFromQuota, 0);
    }

    public static function assignPack(int $userId, Pack $pack): self
    {
        $now = now(config('app.timezone'));

        return static::create([
            'user_id' => $userId,
            'pack_id' => $pack->id,
            'quota_remaining' => $pack->quota,
            'expires_at' => $now->copy()->addDays($pack->duration_days),
        ]);
    }
}
