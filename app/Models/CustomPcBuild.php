<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class CustomPcBuild extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'build_code',
        'category',
        'performance_level',
        'description',
        'thumbnail',
        'components',
        'estimated_wattage',
        'regular_price',
        'discount_type',
        'discount_amount',
        'final_price',
        'stock_status',
        'is_published',
        'is_featured',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'components' => 'array',
        'estimated_wattage' => 'integer',
        'regular_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_price' => 'decimal:2',
        'is_published' => 'boolean',
        'is_featured' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $baseSlug = Str::slug($model->title ?: 'custom-pc-build');
                $slug = $baseSlug;
                $counter = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $baseSlug.'-'.$counter++;
                }
                $model->slug = $slug;
            }

            if (empty($model->build_code)) {
                $model->build_code = 'PCB-PC-'.strtoupper(Str::random(5));
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Preset component slot categories for the Custom PC Builder
     */
    public static function getDefaultSlots(): array
    {
        return [
            [
                'key' => 'processor',
                'name' => 'Processor / CPU',
                'icon' => 'cpu',
                'category_hints' => ['processor', 'cpu', 'intel', 'amd', 'ryzen'],
                'required' => true,
                'default_wattage' => 65,
            ],
            [
                'key' => 'motherboard',
                'name' => 'Motherboard',
                'icon' => 'circuit-board',
                'category_hints' => ['motherboard', 'mainboard', 'b760', 'b650', 'z790', 'x670', 'h610', 'a520'],
                'required' => true,
                'default_wattage' => 40,
            ],
            [
                'key' => 'ram',
                'name' => 'RAM / Memory',
                'icon' => 'memory-stick',
                'category_hints' => ['ram', 'memory', 'ddr4', 'ddr5'],
                'required' => true,
                'default_wattage' => 15,
            ],
            [
                'key' => 'graphics_card',
                'name' => 'Graphics Card / GPU',
                'icon' => 'monitor-smartphone',
                'category_hints' => ['gpu', 'graphics', 'rtx', 'gtx', 'radeon', 'geforce'],
                'required' => false,
                'default_wattage' => 170,
            ],
            [
                'key' => 'storage_primary',
                'name' => 'Storage (Primary SSD)',
                'icon' => 'hard-drive',
                'category_hints' => ['ssd', 'nvme', 'm.2', 'storage'],
                'required' => true,
                'default_wattage' => 10,
            ],
            [
                'key' => 'storage_secondary',
                'name' => 'Secondary Storage (HDD / SSD)',
                'icon' => 'database',
                'category_hints' => ['hdd', 'hard disk', 'ssd', 'sata', 'storage'],
                'required' => false,
                'default_wattage' => 10,
            ],
            [
                'key' => 'power_supply',
                'name' => 'Power Supply Unit (PSU)',
                'icon' => 'zap',
                'category_hints' => ['psu', 'power supply', 'bronze', 'gold', 'watt'],
                'required' => true,
                'default_wattage' => 0,
            ],
            [
                'key' => 'casing',
                'name' => 'Casing / PC Cabinet',
                'icon' => 'box',
                'category_hints' => ['casing', 'case', 'chassis', 'cabinet', 'rgb'],
                'required' => true,
                'default_wattage' => 10,
            ],
            [
                'key' => 'cooler',
                'name' => 'CPU Cooler (Air / Liquid AIO)',
                'icon' => 'fan',
                'category_hints' => ['cooler', 'cooling', 'aio', 'liquid', 'air cooler', 'fan'],
                'required' => false,
                'default_wattage' => 20,
            ],
            [
                'key' => 'monitor',
                'name' => 'Monitor (Optional)',
                'icon' => 'monitor',
                'category_hints' => ['monitor', 'display', 'screen', 'gaming monitor'],
                'required' => false,
                'default_wattage' => 0,
            ],
            [
                'key' => 'peripherals',
                'name' => 'Keyboard & Mouse Combo (Optional)',
                'icon' => 'keyboard',
                'category_hints' => ['keyboard', 'mouse', 'combo', 'headphone', 'peripherals'],
                'required' => false,
                'default_wattage' => 5,
            ],
            [
                'key' => 'ups_accessory',
                'name' => 'UPS / Extra Accessories (Optional)',
                'icon' => 'plug-zap',
                'category_hints' => ['ups', 'accessories', 'surge', 'cable'],
                'required' => false,
                'default_wattage' => 0,
            ],
        ];
    }
}
