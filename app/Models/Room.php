<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Room extends Model
{
    use HasFactory, LogsActivity;

    /**
     * Oda fiyatı, kapasite, durum, açıklama değişimleri loglanır.
     * Admin yanlış fiyat girerse geriye dönüp doğru değeri görebiliriz.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'description', 'capacity', 'base_price', 'features', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('room');
    }

    protected $fillable = [
        'name',
        'slug',
        'description',
        'capacity',
        'base_price',
        'features',
        'cover_image',
        'gallery',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
            'base_price' => 'decimal:2',
            'features' => 'array',
            'gallery' => 'array',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Cover image için public URL döner — kaynağa göre prefix otomatik.
     *
     * Pattern'lar:
     * - `images/demo/rooms/X.jpg` → demo (git'te public/images altında)
     * - `rooms/covers/X.jpg`     → Filament admin upload (storage/app/public)
     * - `/images/X.jpg`          → leading slash, absolute public
     * - `http(s)://...`          → external (CDN, S3)
     *
     * Blade'lerde: `{{ $room->cover_image_url }}` — tüm pattern'leri handle eder.
     */
    public function getCoverImageUrlAttribute(): ?string
    {
        $path = $this->cover_image;

        if (! $path) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (str_starts_with($path, '/')) {
            return asset(ltrim($path, '/'));
        }

        if (str_starts_with($path, 'images/')) {
            return asset($path);
        }

        // Default: Filament FileUpload (storage/app/public altına yazar)
        return asset('storage/'.$path);
    }

    /** Yeni oda eklenirken slug yoksa name'den üret. */
    protected static function booted(): void
    {
        static::creating(function (Room $room) {
            if (empty($room->slug)) {
                $room->slug = Str::slug($room->name);
            }
        });
    }
}
