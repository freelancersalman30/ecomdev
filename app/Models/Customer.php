<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Customer extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'email',
        'password',
        'remember_token',
        'avatar',
        'address',
        'city',
        'postal_code',
        'loyalty_points',
        'total_spent',
        'total_orders_count',
        'delivered_orders_count',
        'cancelled_orders_count',
        'returned_orders_count',
        'delivery_success_rate',
        'is_flagged_fraud',
        'fraud_reason',
        'notes',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'total_spent' => 'decimal:2',
        'delivery_success_rate' => 'decimal:2',
        'is_flagged_fraud' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function recalculateMetrics(): void
    {
        $orders = $this->orders()->get();
        $this->total_orders_count = $orders->count();
        $this->delivered_orders_count = $orders->where('status', 'completed')->count();
        $this->cancelled_orders_count = $orders->where('status', 'cancelled')->count();
        $this->returned_orders_count = $orders->where('status', 'returned')->count();
        $this->total_spent = $orders->where('payment_status', 'paid')->sum('grand_total');

        if ($this->total_orders_count > 0) {
            $this->delivery_success_rate = round(($this->delivered_orders_count / $this->total_orders_count) * 100, 2);
        } else {
            $this->delivery_success_rate = 100.00;
        }

        if ($this->delivery_success_rate < 50 && $this->total_orders_count >= 2) {
            $this->is_flagged_fraud = true;
            $this->fraud_reason = 'High parcel return / cancellation rate';
        }

        $this->save();
    }
}
