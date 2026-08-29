<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourierConsignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'courier_name',
        'consignment_id',
        'tracking_code',
        'cod_amount',
        'delivery_fee',
        'status',
        'response_payload',
    ];

    protected $casts = [
        'cod_amount' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'response_payload' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
