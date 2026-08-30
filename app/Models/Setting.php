<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'group',
        'key',
        'value',
    ];

    protected static ?array $runtimeCache = null;

    public static function allCached(): array
    {
        if (static::$runtimeCache === null) {
            try {
                static::$runtimeCache = Cache::remember('settings.all_mapped', 86400, function () {
                    return self::query()->pluck('value', 'key')->toArray();
                });
            } catch (\Throwable $e) {
                static::$runtimeCache = self::query()->pluck('value', 'key')->toArray();
            }
        }
        return static::$runtimeCache ?? [];
    }

    public static function get(string $key, $default = null)
    {
        $all = static::allCached();
        return array_key_exists($key, $all) ? $all[$key] : $default;
    }

    public static function set(string $key, $value, string $group = 'general'): void
    {
        self::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group]
        );
        static::$runtimeCache = null;
        Cache::forget('settings.all_mapped');
        Cache::forget("setting.{$key}");
    }
}
