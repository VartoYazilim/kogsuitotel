<?php

namespace Tests\Unit;

use App\Models\Reservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationCodeGeneratorTest extends TestCase
{
    use RefreshDatabase;

    public function test_kod_kso_yyyy_random_formatinda_olusur(): void
    {
        // 2026-05 — IDOR/PII enumeration koruması: sıralı NNNN format yerine
        // 8 char rastgele [A-Z0-9] (≈40 bit entropy). Saldırgan başkasının
        // success URL'ini tahmin edemez.
        $reservation = Reservation::factory()->create();

        $this->assertMatchesRegularExpression(
            '/^KSO-\d{4}-[A-Z0-9]{8}$/',
            $reservation->reservation_code,
            'Format: KSO-YYYY-AAAAAAAA (8 char base32)'
        );
        $this->assertStringContainsString((string) now()->year, $reservation->reservation_code);
    }

    public function test_ardisik_kodlar_unique_ama_tahmin_edilemez(): void
    {
        // Sıralı (NNNN → NNNN+1) DEĞİL artık — unique olmaları yeterli,
        // ardışıklık özelliği KALDIRILDI (IDOR koruması).
        $codes = collect(range(1, 10))->map(
            fn () => Reservation::factory()->create()->reservation_code
        );

        $this->assertSame(10, $codes->unique()->count(), '10 kodun hepsi farklı olmalı');
    }

    public function test_acikca_verilen_kod_overwrite_edilmez(): void
    {
        // Eski formatlı kod manuel verilirse korunur (admin manuel ekleme,
        // backward import vs. için).
        $customCode = 'KSO-2026-LEGACY01';

        $reservation = Reservation::factory()->create([
            'reservation_code' => $customCode,
        ]);

        $this->assertSame($customCode, $reservation->reservation_code);
    }
}
