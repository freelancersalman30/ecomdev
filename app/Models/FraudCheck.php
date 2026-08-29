<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FraudCheck extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone',
        'ip_address',
        'risk_level',
        'courier_success_rate',
        'total_parcels',
        'delivered_parcels',
        'cancelled_parcels',
        'notes',
        'is_blacklisted',
    ];

    protected $casts = [
        'courier_success_rate' => 'decimal:2',
        'is_blacklisted' => 'boolean',
    ];
}
