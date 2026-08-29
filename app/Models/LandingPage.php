<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'product_id',
        'headline',
        'sub_headline',
        'video_url',
        'builder_blocks',
        'features_list',
        'testimonials',
        'theme_color',
        'fb_pixel_id',
        'custom_domain',
        'views_count',
        'conversions_count',
        'is_active',
    ];

    protected $casts = [
        'builder_blocks' => 'array',
        'features_list' => 'array',
        'testimonials' => 'array',
        'is_active' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
