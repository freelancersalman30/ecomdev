<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'discount_type',
        'discount_value',
        'min_order_amount',
        'max_discount_amount',
        'usage_limit',
        'usage_per_user',
        'times_used',
        'starts_at',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function isValidForAmount(float $subtotal): bool
    {
        if (!$this->is_active) return false;
        if ($this->starts_at && Carbon::now()->lt($this->starts_at)) return false;
        if ($this->expires_at && Carbon::now()->gt($this->expires_at)) return false;
        if ($this->usage_limit && $this->times_used >= $this->usage_limit) return false;
        if ($subtotal < $this->min_order_amount) return false;

        return true;
    }

    public function calculateDiscount(float $subtotal): float
    {
        if (!$this->isValidForAmount($subtotal)) {
            return 0.00;
        }

        if ($this->discount_type === 'percentage') {
            $discount = ($subtotal * $this->discount_value) / 100;
            if ($this->max_discount_amount && $discount > $this->max_discount_amount) {
                return (float) $this->max_discount_amount;
            }
            return (float) $discount;
        }

        return min((float) $this->discount_value, $subtotal);
    }
}
