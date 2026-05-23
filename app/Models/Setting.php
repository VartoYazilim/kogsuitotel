<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Setting extends Model
{
    use LogsActivity;

    protected $fillable = ['key', 'value'];

    /**
     * IBAN, telefon, e-posta vb. setting değişiklikleri loglanır.
     * Sahip yanlış IBAN girerse veya değiştirirse trail görünür.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['key', 'value'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('setting');
    }

    /** Tüm settings 5dk cache'li tek dict olarak okunur (config()->set gibi). */
    public static function get(string $key, ?string $default = null): ?string
    {
        $all = Cache::remember('settings.all', now()->addMinutes(5), function () {
            return self::query()->pluck('value', 'key')->all();
        });

        return $all[$key] ?? $default;
    }

    public static function set(string $key, ?string $value): void
    {
        self::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget('settings.all');
    }

    public static function many(array $keys): array
    {
        return collect($keys)
            ->mapWithKeys(fn ($k) => [$k => self::get($k)])
            ->all();
    }

    /** Model save'inde de cache invalidate et. */
    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('settings.all'));
        static::deleted(fn () => Cache::forget('settings.all'));
    }
}
