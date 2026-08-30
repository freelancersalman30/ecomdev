<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_no',
        'customer_id',
        'user_id',
        'order_type',
        'status',
        'subtotal',
        'discount',
        'coupon_code',
        'shipping_charge',
        'tax',
        'grand_total',
        'paid_amount',
        'due_amount',
        'payment_method',
        'payment_status',
        'payment_transaction_id',
        'account_id',
        'shipping_name',
        'shipping_phone',
        'shipping_email',
        'shipping_address',
        'shipping_city',
        'shipping_zone',
        'courier_name',
        'courier_tracking_id',
        'courier_consignment_id',
        'courier_status',
        'is_fraud_suspect',
        'fraud_score',
        'fraud_reason',
        'ip_address',
        'user_agent',
        'admin_note',
        'customer_note',
        'processed_by',
        'delivered_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'shipping_charge' => 'decimal:2',
        'tax' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
        'is_fraud_suspect' => 'boolean',
        'delivered_at' => 'datetime',
    ];

    public function getOrderStatusAttribute()
    {
        return $this->status;
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusLogs()
    {
        return $this->hasMany(OrderStatusLog::class)->latest();
    }

    public function consignment()
    {
        return $this->hasOne(CourierConsignment::class);
    }

    public function courierConsignment()
    {
        return $this->hasOne(CourierConsignment::class);
    }

    // Status Scopes
    public function scopeIncomplete(Builder $query)
    {
        return $query->where('status', 'incomplete');
    }

    public function scopePending(Builder $query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessing(Builder $query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeOnTheWay(Builder $query)
    {
        return $query->where('status', 'on_the_way');
    }

    public function scopeInCourier(Builder $query)
    {
        return $query->where('status', 'in_courier');
    }

    public function scopeCompleted(Builder $query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled(Builder $query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeReturned(Builder $query)
    {
        return $query->where('status', 'returned');
    }

    // Helpers
    public function logStatusChange(string $toStatus, ?string $note = null, ?int $userId = null): void
    {
        $fromStatus = $this->status;
        $this->status = $toStatus;
        $this->save();

        $this->statusLogs()->create([
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'note' => $note,
            'changed_by' => $userId,
        ]);
    }
}
