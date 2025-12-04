<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Submission extends Model
{
    protected $fillable = [
        'user_id',
        'user_pack_id',
        'submission_number',
        'original_name',
        'file_path',
        'status',
        'error_note',
        'similarity_report_path',
        'ai_report_path',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pack(): BelongsTo
    {
        return $this->belongsTo(UserPack::class, 'user_pack_id');
    }
}
