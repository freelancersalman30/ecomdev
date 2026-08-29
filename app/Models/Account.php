<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'account_type',
        'account_number',
        'bank_name',
        'branch_name',
        'opening_balance',
        'current_balance',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class)->latest('transaction_date');
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
}
