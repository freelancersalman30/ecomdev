<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'color_id',
        'size_id',
        'variant_name',
        'sku',
        'barcode',
        'purchase_price',
        'selling_price',
        'discount_price',
        'stock_quantity',
        'alert_threshold',
        'image',
        'is_active',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function size()
    {
        return $this->belongsTo(Size::class);
    }

    public function getEffectivePriceAttribute()
    {
        return ($this->discount_price && $this->discount_price > 0 && $this->discount_price < $this->selling_price)
            ? $this->discount_price
            : $this->selling_price;
    }
}
