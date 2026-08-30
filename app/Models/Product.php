<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'sku',
        'barcode',
        'category_id',
        'sub_category_id',
        'child_category_id',
        'brand_id',
        'pcb_model',
        'voltage',
        'warranty',
        'dimensions',
        'weight',
        'chipset',
        'specifications',
        'purchase_price',
        'selling_price',
        'discount_price',
        'stock_quantity',
        'alert_threshold',
        'has_variants',
        'short_description',
        'description',
        'thumbnail',
        'datasheet_pdf',
        'is_featured',
        'is_flash_sale',
        'is_active',
        'views_count',
        'sales_count',
    ];

    protected $casts = [
        'specifications' => 'array',
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'has_variants' => 'boolean',
        'is_featured' => 'boolean',
        'is_flash_sale' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function childCategory()
    {
        return $this->belongsTo(ChildCategory::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('display_order');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function landingPages()
    {
        return $this->hasMany(LandingPage::class);
    }

    // Scopes
    public function scopeActive(Builder $query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured(Builder $query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeFlashSale(Builder $query)
    {
        return $query->where('is_flash_sale', true);
    }

    public function scopeLowStock(Builder $query)
    {
        return $query->where(function ($q) {
            $q->where('has_variants', false)
                ->whereColumn('stock_quantity', '<=', 'alert_threshold');
        })->orWhereHas('variants', function ($q) {
            $q->whereColumn('stock_quantity', '<=', 'alert_threshold');
        });
    }

    // Accessors
    public function getEffectivePriceAttribute()
    {
        return ($this->discount_price && $this->discount_price > 0 && $this->discount_price < $this->selling_price)
            ? $this->discount_price
            : $this->selling_price;
    }

    public function getDiscountPercentageAttribute(): int
    {
        if ($this->discount_price && $this->selling_price > $this->discount_price && $this->selling_price > 0) {
            return (int) round((($this->selling_price - $this->discount_price) / $this->selling_price) * 100);
        }

        return 0;
    }

    public function getThumbnailAttribute($value): string
    {
        if (empty($value)) {
            return 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=600&auto=format&fit=crop&q=80';
        }

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }

        return asset(ltrim($value, '/'));
    }

    public function getTotalStockAttribute()
    {
        if ($this->has_variants) {
            return $this->variants()->sum('stock_quantity');
        }

        return $this->stock_quantity;
    }
}
