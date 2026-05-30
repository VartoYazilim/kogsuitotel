<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Sosyal medya bağlantısı — admin'den CRUD edilir, public footer + contact
 * sayfasında ikon olarak render olur. Schema.org Organization.sameAs aynı
 * kaynaktan beslenir.
 *
 * platform: icon registry'sindeki key (instagram, facebook, x, youtube, ...).
 * Custom platform'lar için 'link' kullanılır (generic globe ikon).
 *
 * @property int $id
 * @property string $platform
 * @property string $label
 * @property string $url
 * @property int $sort_order
 * @property bool $is_active
 */
class SocialLink extends Model
{
    use HasFactory, LogsActivity;

    /**
     * Desteklenen ikon registry — admin Select'inde seçilir.
     * Yeni platform eklemek için partials/social-icons.blade.php @switch'ine
     * SVG path ekle + buradaki dict'e key/label ekle.
     */
    public const PLATFORMS = [
        'instagram' => 'Instagram',
        'facebook' => 'Facebook',
        'x' => 'X (Twitter)',
        'youtube' => 'YouTube',
        'tiktok' => 'TikTok',
        'linkedin' => 'LinkedIn',
        'tripadvisor' => 'Tripadvisor',
        'whatsapp' => 'WhatsApp',
        'telegram' => 'Telegram',
        'mappin' => 'Google Maps / Konum',
        'globe' => 'Web Sitesi',
        'link' => 'Diğer (Genel Link)',
    ];

    protected $fillable = [
        'platform',
        'label',
        'url',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['platform', 'label', 'url', 'sort_order', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('social_link');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
