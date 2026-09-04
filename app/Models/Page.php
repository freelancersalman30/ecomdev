<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Page extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'slug',
        'content',
        'placement',
        'is_active',
        'sort_order',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Automatically generate unique slug when setting title if slug is empty.
     */
    public static function boot(): void
    {
        parent::boot();

        static::creating(function ($page) {
            if (empty($page->slug)) {
                $page->slug = Str::slug($page->title);
            }
        });
    }

    /**
     * Scope for active published pages.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for pages that should appear in header/top menu.
     */
    public function scopeHeader(Builder $query): Builder
    {
        return $query->active()
            ->whereIn('placement', ['header', 'both'])
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc');
    }

    /**
     * Scope for pages that should appear in footer.
     */
    public function scopeFooter(Builder $query): Builder
    {
        return $query->active()
            ->whereIn('placement', ['footer', 'both'])
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc');
    }

    /**
     * Get placement human label.
     */
    public function getPlacementLabelAttribute(): string
    {
        return match ($this->placement) {
            'header' => 'Top Menu',
            'footer' => 'Footer',
            'both' => 'Top Menu & Footer',
            'none' => 'Unlisted (Direct Link Only)',
            default => ucfirst($this->placement),
        };
    }
}
