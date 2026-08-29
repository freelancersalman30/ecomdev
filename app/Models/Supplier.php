<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'company',
        'phone',
        'email',
        'address',
        'opening_balance',
        'total_purchased',
        'total_paid',
        'current_due',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'total_purchased' => 'decimal:2',
        'total_paid' => 'decimal:2',
        'current_due' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function payments()
    {
        return $this->hasMany(SupplierPayment::class);
    }

    public function recalculateDue(): void
    {
        $purchased = $this->purchases()->sum('grand_total');
        $paid = $this->payments()->sum('amount');
        $this->total_purchased = $purchased;
        $this->total_paid = $paid;
        $this->current_due = ($this->opening_balance + $purchased) - $paid;
        $this->save();
    }
}
