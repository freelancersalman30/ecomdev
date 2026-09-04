<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'charge',
        'estimated_days',
        'min_order_for_free_delivery',
        'description',
        'is_active',
        'is_default',
        'sort_order',
    ];

    protected $casts = [
        'charge' => 'decimal:2',
        'min_order_for_free_delivery' => 'decimal:2',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Calculate effective shipping charge based on order subtotal.
     */
    public function getEffectiveCharge(float $subtotal = 0.0): float
    {
        if ($this->min_order_for_free_delivery && $subtotal >= (float) $this->min_order_for_free_delivery) {
            return 0.0;
        }

        return (float) $this->charge;
    }
}
