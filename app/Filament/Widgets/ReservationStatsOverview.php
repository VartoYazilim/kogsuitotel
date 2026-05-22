<?php

namespace App\Filament\Widgets;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ReservationStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $pendingCount = Reservation::where('status', ReservationStatus::Pending)->count();

        $arrivingToday = Reservation::whereDate('check_in', today())
            ->whereIn('status', [ReservationStatus::Confirmed, ReservationStatus::Paid])
            ->count();

        $departingToday = Reservation::whereDate('check_out', today())
            ->whereIn('status', [ReservationStatus::Paid, ReservationStatus::Confirmed])
            ->count();

        $monthlyRevenue = Reservation::whereIn('status', [
            ReservationStatus::Paid,
            ReservationStatus::Completed,
        ])
            ->whereYear('check_in', now()->year)
            ->whereMonth('check_in', now()->month)
            ->sum('total_price');

        return [
            Stat::make('Bekleyen Rezervasyon', $pendingCount)
                ->description('Onay veya ödeme bekliyor')
                ->descriptionIcon('heroicon-o-clock', 'before')
                ->color($pendingCount > 0 ? 'warning' : 'gray'),

            Stat::make('Bugün Giriş', $arrivingToday)
                ->description('Check-in yapacak misafirler')
                ->descriptionIcon('heroicon-o-arrow-right-on-rectangle', 'before')
                ->color('success'),

            Stat::make('Bugün Çıkış', $departingToday)
                ->description('Check-out yapacak misafirler')
                ->descriptionIcon('heroicon-o-arrow-left-on-rectangle', 'before')
                ->color('info'),

            Stat::make('Bu Ayın Geliri', '₺'.number_format((float) $monthlyRevenue, 2, ',', '.'))
                ->description(now()->translatedFormat('F Y').' · ödenmiş + tamamlanmış')
                ->descriptionIcon('heroicon-o-banknotes', 'before')
                ->color('primary'),
        ];
    }
}
