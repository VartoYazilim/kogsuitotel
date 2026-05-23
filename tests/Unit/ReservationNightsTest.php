<?php

namespace Tests\Unit;

use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationNightsTest extends TestCase
{
    use RefreshDatabase;

    public function test_nights_check_in_check_out_arasindan_hesaplanir(): void
    {
        $reservation = Reservation::factory()->create([
            'check_in' => Carbon::parse('2026-06-01'),
            'check_out' => Carbon::parse('2026-06-04'),
            'nights' => null, // boş bırak, otomatik hesaplansın
        ]);

        $this->assertEquals(3, $reservation->fresh()->nights);
    }

    public function test_explicit_nights_overwrite_edilmez(): void
    {
        $reservation = Reservation::factory()->create([
            'check_in' => Carbon::parse('2026-06-01'),
            'check_out' => Carbon::parse('2026-06-04'),
            'nights' => 5, // bilerek farklı verildi
        ]);

        $this->assertEquals(5, $reservation->fresh()->nights);
    }

    public function test_guest_full_name_accessor(): void
    {
        $reservation = Reservation::factory()->create([
            'guest_first_name' => 'Ali',
            'guest_last_name' => 'Veli',
        ]);

        $this->assertEquals('Ali Veli', $reservation->guest_full_name);
    }

    public function test_whatsapp_link_e164_formatinda(): void
    {
        $reservation = Reservation::factory()->create([
            'guest_phone' => '0555 123 45 67',
        ]);

        $this->assertStringStartsWith('https://wa.me/9', $reservation->whatsapp_link);
        $this->assertStringContainsString('905551234567', $reservation->whatsapp_link);
    }

    public function test_whatsapp_link_90_prefix_zaten_varsa_korunur(): void
    {
        // E.164 formatında zaten +90'lı telefon — 909... gibi mükerrer
        // prefix eklenmemeli.
        $reservation = Reservation::factory()->create([
            'guest_phone' => '+90 555 123 45 67',
        ]);

        $this->assertStringContainsString('905551234567', $reservation->whatsapp_link);
        $this->assertStringNotContainsString('9905551234567', $reservation->whatsapp_link);
    }

    public function test_whatsapp_link_prefix_yoksa_90_eklenir(): void
    {
        // Bazen kullanıcı sadece 10 hane yazar (555 XXX XX XX) — 90 ön ek
        // controller/accessor seviyesinde otomatik eklenmeli.
        $reservation = Reservation::factory()->create([
            'guest_phone' => '555 123 45 67',
        ]);

        $this->assertStringContainsString('905551234567', $reservation->whatsapp_link);
    }
}
