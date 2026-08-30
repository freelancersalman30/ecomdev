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

    /**
     * Retrieve a specific credential key with an optional fallback.
     */
    public function getCredential(string $key, mixed $default = null): mixed
    {
        $creds = $this->credentials;
        if (is_array($creds) && array_key_exists($key, $creds)) {
            return $creds[$key];
        }

        return $default;
    }

    /**
     * Dynamically access credential keys as model properties.
     */
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        if ($value !== null) {
            return $value;
        }

        if ($key !== 'credentials') {
            $creds = parent::getAttribute('credentials');
            if (is_array($creds) && array_key_exists($key, $creds)) {
                return $creds[$key];
            }
        }

        return null;
    }
}
