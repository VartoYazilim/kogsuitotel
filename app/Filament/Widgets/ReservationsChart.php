<?php

namespace App\Filament\Widgets;

use App\Models\Reservation;
use Filament\Widgets\ChartWidget;

class ReservationsChart extends ChartWidget
{
    protected ?string $heading = 'Son 30 Gün Rezervasyon Trendi';

    protected ?string $description = 'Günlük yeni rezervasyon talebi sayısı.';

    protected static ?int $sort = 2;

    // Tüm satırı kaplar — 1/3 kolon "Son 30 Gün" chart için çok dar görünüyordu
    // (sahip 2026-05-25 itirazı). Chart + boş sağ alan anti-pattern'ından
    // kaçınmak için full width. Bkz: memory/reference-admin-ui-layout-disiplini.md
    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $start = now()->subDays(29)->startOfDay();
        $end = now()->endOfDay();

        // Veritabanından günlük rezervasyon sayıları
        $reservations = Reservation::query()
            ->whereBetween('created_at', [$start, $end])
            ->get()
            ->groupBy(fn ($r) => $r->created_at->format('Y-m-d'))
            ->map->count();

        $labels = [];
        $data = [];

        for ($day = $start->copy(); $day->lte($end); $day->addDay()) {
            $key = $day->format('Y-m-d');
            $labels[] = $day->format('d.m');
            $data[] = $reservations[$key] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Yeni Rezervasyon',
                    'data' => $data,
                    'borderColor' => '#6b7553',
                    'backgroundColor' => 'rgba(107, 117, 83, 0.15)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => ['precision' => 0],
                ],
            ],
            'plugins' => [
                'legend' => ['display' => false],
            ],
        ];
    }
}
