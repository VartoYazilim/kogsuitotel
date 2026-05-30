<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Hukuki sayfa — KVKK / Gizlilik / Çerez Politikası.
 * Admin RichEditor ile içeriği düzenleyebilir. Sahibin avukat onayı sonrası
 * güncellenmesi önemli; Activity log'da değişim trail'i tutulur (hukuki kanıt).
 *
 * Slug sabit: 'kvkk', 'privacy', 'cookie-policy' — public route mapping.
 * Singleton pattern (3 sabit kayıt, create/delete yok, sadece edit).
 *
 * @property int $id
 * @property string $slug
 * @property string $title
 * @property string|null $content_html
 * @property Carbon|null $last_reviewed_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class LegalPage extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'slug',
        'title',
        'content_html',
        'last_reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'last_reviewed_at' => 'date',
        ];
    }

    /**
     * Hukuki metin değişimleri activity log'a yazılır. Sahip veya avukat
     * "şu tarihte X eklendi/çıkarıldı" sorgulayabilir — KVKK m.12/3 denetim trail.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'content_html', 'last_reviewed_at'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('legal_page');
    }

    public static function forSlug(string $slug): ?self
    {
        return self::where('slug', $slug)->first();
    }
}
