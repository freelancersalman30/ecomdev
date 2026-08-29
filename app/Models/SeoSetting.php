<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeoSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_image',
        'canonical_url',
        'robots_txt',
        'sitemap_auto_ping',
        'last_sitemap_generated_at',
    ];

    protected $casts = [
        'sitemap_auto_ping' => 'boolean',
        'last_sitemap_generated_at' => 'datetime',
    ];
}
