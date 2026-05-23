<?php

namespace Tests\Unit;

use App\Enums\ReservationStatus;
use Tests\TestCase;

class ReservationStatusTest extends TestCase
{
    public function test_tum_statusler_turkce_label_doner(): void
    {
        // Filament HasLabel interface — admin tablosunda + dropdown'da TR.
        // Yeni status eklenirse bu test güncellenmeli (whitelist coverage).
        $expected = [
            'Pending' => 'Bekliyor',
            'Confirmed' => 'Onaylandı',
            'Paid' => 'Ödendi',
            'Completed' => 'Tamamlandı',
            'Cancelled' => 'İptal',
            'NoShow' => 'Gelmedi',
        ];

        foreach (ReservationStatus::cases() as $status) {
            $this->assertArrayHasKey(
                $status->name,
                $expected,
                "ReservationStatus::{$status->name} için TR label test'i eksik — bu test'i güncelle."
            );
            $this->assertSame($expected[$status->name], $status->getLabel());
        }
    }

    public function test_tum_statusler_string_renk_doner(): void
    {
        // Filament HasColor interface — badge/icon renkleri için.
        // Filament 4 palette: gray, warning, success, info, danger.
        foreach (ReservationStatus::cases() as $status) {
            $color = $status->getColor();

            $this->assertIsString(
                $color,
                "Status {$status->name} string renk dönmeli (array veya null değil)."
            );
            $this->assertNotEmpty($color);
        }
    }

    public function test_tum_statusler_heroicon_formatinda_ikon_doner(): void
    {
        // Filament HasIcon interface — icon enum yerine string heroicon
        // (Filament 4'te string format hala valid).
        foreach (ReservationStatus::cases() as $status) {
            $icon = $status->getIcon();

            $this->assertIsString(
                $icon,
                "Status {$status->name} string ikon dönmeli."
            );
            $this->assertStringStartsWith(
                'heroicon-',
                $icon,
                "Status {$status->name} heroicon-* prefix bekleniyor: {$icon}"
            );
        }
    }

    public function test_status_value_string_olarak_db_de_saklanir(): void
    {
        // Enum backed string — DB'de 'pending', 'paid' vb. tutulur.
        $this->assertSame('pending', ReservationStatus::Pending->value);
        $this->assertSame('confirmed', ReservationStatus::Confirmed->value);
        $this->assertSame('paid', ReservationStatus::Paid->value);
        $this->assertSame('completed', ReservationStatus::Completed->value);
        $this->assertSame('cancelled', ReservationStatus::Cancelled->value);
        $this->assertSame('no_show', ReservationStatus::NoShow->value);
    }
}
