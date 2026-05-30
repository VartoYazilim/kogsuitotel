<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

/**
 * Selamlama widget — sadece greeting + tarih + welcome mesajı.
 * Metric'ler (pending/arrivals/departures/revenue) ReservationStatsOverview'da;
 * burada tekrar gösterirsek duplicate olur (sahip 2026-05-25 itirazı:
 * "üstte 3 metric, altta 4 stat = dağınık"). Bkz: reference-admin-ui-layout-disiplini.
 */
class WelcomeWidget extends Widget
{
    protected string $view = 'filament.widgets.welcome';

    protected static ?int $sort = -10;

    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $user = auth()->user();
        $now = now();

        return [
            'userName' => $user->name ?? 'Misafir',
            'greeting' => $this->greetingByHour($now->hour),
            'todayDate' => $now->translatedFormat('d F Y, l'),
        ];
    }

    protected function greetingByHour(int $hour): string
    {
        return match (true) {
            $hour < 6 => 'İyi geceler',
            $hour < 12 => 'Günaydın',
            $hour < 18 => 'İyi günler',
            default => 'İyi akşamlar',
        };
    }
}
