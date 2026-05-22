<?php

namespace Tests\Unit;

use App\Models\Reservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationCodeGeneratorTest extends TestCase
{
    use RefreshDatabase;

    public function test_kod_kso_yyyy_nnnn_formatinda_olusur(): void
    {
        $reservation = Reservation::factory()->create();

        $this->assertMatchesRegularExpression('/^KSO-\d{4}-\d{4}$/', $reservation->reservation_code);
        $this->assertStringContainsString((string) now()->year, $reservation->reservation_code);
    }

    public function test_ardisik_kodlar_artar(): void
    {
        $first = Reservation::factory()->create();
        $second = Reservation::factory()->create();
        $third = Reservation::factory()->create();

        $firstNum = (int) substr($first->reservation_code, -4);
        $secondNum = (int) substr($second->reservation_code, -4);
        $thirdNum = (int) substr($third->reservation_code, -4);

        $this->assertEquals(1, $firstNum);
        $this->assertEquals(2, $secondNum);
        $this->assertEquals(3, $thirdNum);
    }

    public function test_acikca_verilen_kod_overwrite_edilmez(): void
    {
        $customCode = 'KSO-2026-9999';

        $reservation = Reservation::factory()->create([
            'reservation_code' => $customCode,
        ]);

        $this->assertEquals($customCode, $reservation->reservation_code);
    }
}
