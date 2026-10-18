<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

#[Fillable(['key', 'value'])]
class Setting extends Model
{
    public const PLATFORM_NAME = 'platform_name';

    public const SUPPORT_EMAIL = 'support_email';

    public const DEFAULT_PHARMACY_STATUS = 'default_pharmacy_status';

    protected const CACHE_KEY = 'settings.values';

    /**
     * Every setting as a key => value map, memoised in the cache store.
     *
     * @return array<string, string|null>
     */
    public static function values(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, fn (): array => static::query()->pluck('value', 'key')->all());
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return static::values()[$key] ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);

        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Whether a newly created pharmacy should start active.
     */
    public static function defaultPharmacyIsActive(): bool
    {
        return static::get(self::DEFAULT_PHARMACY_STATUS, 'active') === 'active';
    }
}
