<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'image',
        'icon',
        'description',
        'is_featured',
        'is_active',
        'display_order',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function subCategories()
    {
        return $this->hasMany(SubCategory::class)->orderBy('display_order');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
