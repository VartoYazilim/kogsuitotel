<?php

namespace App\Models;

use App\Enums\ReservationStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Reservation extends Model
{
    use HasFactory, LogsActivity;

    /**
     * KVKK m.12/3 denetim altyapısı — admin status değişimi, oda/tarih güncellemesi,
     * yönetici notu eklemesi loglanır. Properties JSON'da `old` + `attributes`
     * (eski + yeni değer) saklanır. log_name = 'reservation'.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'status', 'check_in', 'check_out', 'room_id',
                'adults', 'children', 'total_price', 'admin_notes',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('reservation');
    }

    protected $fillable = [
        'reservation_code',
        'room_id',
        'guest_first_name',
        'guest_last_name',
        'guest_phone',
        'guest_email',
        'check_in',
        'check_out',
        'adults',
        'children',
        'nights',
        'total_price',
        'special_requests',
        'status',
        'admin_notes',
    ];

    protected function casts(): array
    {
        return [
            'check_in' => 'date',
            'check_out' => 'date',
            'adults' => 'integer',
            'children' => 'integer',
            'nights' => 'integer',
            'total_price' => 'decimal:2',
            'status' => ReservationStatus::class,
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /** Misafirin tam adı — admin listeleri ve mail için pratik. */
    public function getGuestFullNameAttribute(): string
    {
        return trim($this->guest_first_name.' '.$this->guest_last_name);
    }

    /** WhatsApp link — admin panel aksiyonu için (+90 ön ekiyle E.164). */
    public function getWhatsappLinkAttribute(): string
    {
        $phone = preg_replace('/\D/', '', $this->guest_phone);

        if (str_starts_with($phone, '90')) {
            $cleanPhone = $phone;
        } elseif (str_starts_with($phone, '0')) {
            $cleanPhone = '9'.$phone;
        } else {
            $cleanPhone = '90'.$phone;
        }

        return 'https://wa.me/'.$cleanPhone;
    }

    /** Boot: reservation_code otomatik üretimi + saving event ile validasyon. */
    protected static function booted(): void
    {
        static::creating(function (Reservation $reservation) {
            if (empty($reservation->reservation_code)) {
                $reservation->reservation_code = self::generateCode();
            }

            // nights otomatik hesaplanmadıysa hesapla
            if (empty($reservation->nights) && $reservation->check_in && $reservation->check_out) {
                $reservation->nights = (int) $reservation->check_in
                    ->diffInDays($reservation->check_out);
            }
        });

        // Hem create hem update'te çalışır — her durumda invariantları korur.
        static::saving(function (Reservation $reservation) {
            self::guardCapacityNotExceeded($reservation);
            self::guardNoOverlap($reservation);
        });
    }

    /** Misafir sayısı oda kapasitesini aşmasın. */
    protected static function guardCapacityNotExceeded(Reservation $reservation): void
    {
        $room = Room::find($reservation->room_id);

        if (! $room) {
            return;
        }

        $totalGuests = (int) ($reservation->adults ?? 0) + (int) ($reservation->children ?? 0);

        if ($totalGuests > $room->capacity) {
            throw ValidationException::withMessages([
                'adults' => "Bu oda en fazla {$room->capacity} kişiliktir. Toplam misafir: {$totalGuests}.",
            ]);
        }
    }

    /**
     * Aktif statüye (Confirmed/Paid/Completed) sahip rezervasyon için
     * aynı oda + tarih aralığında başka aktif rezervasyon olmamalı.
     * Pending durumlar çakışma sayılmaz (24 saatlik hold).
     */
    protected static function guardNoOverlap(Reservation $reservation): void
    {
        $activeStatuses = [
            ReservationStatus::Confirmed,
            ReservationStatus::Paid,
            ReservationStatus::Completed,
        ];

        // Sadece aktif statu için kontrol et. Pending+iptal+gelmedi serbest.
        $status = $reservation->status instanceof ReservationStatus
            ? $reservation->status
            : ReservationStatus::tryFrom((string) $reservation->status);

        if (! in_array($status, $activeStatuses, true)) {
            return;
        }

        $overlap = static::query()
            ->where('room_id', $reservation->room_id)
            ->whereIn('status', $activeStatuses)
            ->when($reservation->id, fn ($q) => $q->where('id', '!=', $reservation->id))
            ->where(function ($q) use ($reservation) {
                $q->where('check_in', '<', $reservation->check_out)
                    ->where('check_out', '>', $reservation->check_in);
            })
            ->exists();

        if ($overlap) {
            throw ValidationException::withMessages([
                'check_in' => 'Seçilen tarihlerde bu oda başka bir aktif rezervasyonla çakışıyor.',
            ]);
        }
    }

    /**
     * KSO-YYYY-AAAAAAAA formatında benzersiz rezervasyon kodu üretir.
     *
     * Format: 4 haneli yıl + 8 karakter base32 rastgele (≈40 bit entropy).
     * Sıralı NNNN formatından geçildi (IDOR/PII enumeration koruması) —
     * saldırgan başka misafirin success sayfasını tahmin edemez.
     *
     * Eski sıralı kodları okuma backward compatibility'si yok; üretime
     * çıkmadan değişti.
     */
    protected static function generateCode(): string
    {
        $year = now()->year;
        $prefix = "KSO-{$year}-";

        do {
            // Str::random varsayılan olarak [A-Za-z0-9] üretir; uppercase'e
            // çevirip okunabilir kıl (telefonda yazılırken case karışmasın).
            // O/0, I/1 ayrımı için ileride Crockford base32 düşünülebilir.
            $code = $prefix.strtoupper(Str::random(8));
        } while (self::where('reservation_code', $code)->exists());

        return $code;
    }

    /* ───────────── Scope'lar ───────────── */

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', ReservationStatus::Pending);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', [
            ReservationStatus::Confirmed,
            ReservationStatus::Paid,
        ]);
    }

    /** Bugün giriş yapacaklar. */
    public function scopeArrivingToday(Builder $query): Builder
    {
        return $query->whereDate('check_in', today());
    }

    /** Bugün çıkış yapacaklar. */
    public function scopeDepartingToday(Builder $query): Builder
    {
        return $query->whereDate('check_out', today());
    }

    /**
     * Verilen oda + tarih aralığı için aktif (rezerve sayılan) rezervasyonları
     * bulur. Aralık çakışma kuralı:
     *   existing.check_in < new.check_out  AND  existing.check_out > new.check_in
     *
     * Otel kuralı: çıkış günü (check_out) oda yeniden müsait — yani:
     *   Önceki rez 5-8 ise, 8'de yeni rez girebilir (çakışmaz).
     *
     * Pending statusu çakışma sayılmaz (24 saatlik hold, otomatik iptal olabilir).
     * Sadece Confirmed + Paid + Completed çakışır.
     */
    public function scopeOverlapping(
        Builder $query,
        int $roomId,
        Carbon|string $checkIn,
        Carbon|string $checkOut,
        ?int $excludeId = null,
    ): Builder {
        $query
            ->where('room_id', $roomId)
            ->whereIn('status', [
                ReservationStatus::Confirmed,
                ReservationStatus::Paid,
                ReservationStatus::Completed,
            ])
            ->where(function (Builder $q) use ($checkIn, $checkOut) {
                $q->where('check_in', '<', $checkOut)
                    ->where('check_out', '>', $checkIn);
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query;
    }
}
