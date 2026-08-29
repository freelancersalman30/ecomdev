<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider',
        'type',
        'title',
        'credentials',
        'is_sandbox',
        'is_active',
    ];

    protected $casts = [
        'credentials' => 'array',
        'is_sandbox' => 'boolean',
        'is_active' => 'boolean',
    ];
}
