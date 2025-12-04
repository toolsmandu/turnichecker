<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function isExpired(): bool
    {
        return $this->expires_at?->isPast() ?? true;
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
