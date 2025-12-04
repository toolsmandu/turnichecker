<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = [
        'site_name',
        'document_count',
        'document_label',
        'live_label',
        'hero_title',
        'hero_subtitle',
        'feature_tags',
        'faqs',
        'button_text',
        'button_link',
        'logo_path',
        'howto_title',
        'howto_steps',
        'howto_button_text',
        'howto_button_link',
        'howto_video_text',
        'howto_image_path',
        'howto_embed',
    ];

    protected $casts = [
        'feature_tags' => 'array',
        'faqs' => 'array',
        'howto_steps' => 'array',
    ];

    public function logoUrl(): ?string
    {
        if (! $this->logo_path) {
            return asset('logo.webp');
        }

        return asset('logo.webp');
    }

    public function howtoImageUrl(): ?string
    {
        if (! $this->howto_image_path) {
            return null;
        }

        return Storage::disk('public')->url($this->howto_image_path);
    }
}
