<?php

namespace App\Filament\Widgets;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use Filament\Widgets\Widget;

class WelcomeWidget extends Widget
{
    protected string $view = 'filament.widgets.welcome';

    protected static ?int $sort = -10;

    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $user = auth()->user();
        $today = today();
        $now = now();

        return [
            'userName' => $user?->name ?? 'Misafir',
            'greeting' => $this->greetingByHour($now->hour),
            'todayDate' => $now->translatedFormat('d F Y, l'),
            'pendingCount' => Reservation::where('status', ReservationStatus::Pending)->count(),
            'todayArrivals' => Reservation::whereDate('check_in', $today)
                ->whereIn('status', [ReservationStatus::Confirmed, ReservationStatus::Paid])
                ->count(),
            'todayDepartures' => Reservation::whereDate('check_out', $today)
                ->whereIn('status', [ReservationStatus::Paid, ReservationStatus::Completed])
                ->count(),
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
