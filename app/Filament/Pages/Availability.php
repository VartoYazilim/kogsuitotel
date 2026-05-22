<?php

namespace App\Filament\Pages;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use App\Models\Room;
use BackedEnum;
use Carbon\Carbon;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use UnitEnum;

class Availability extends Page
{
    protected string $view = 'filament.pages.availability';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static string|UnitEnum|null $navigationGroup = 'Operasyon';

    protected static ?int $navigationSort = 20;

    public string $checkIn = '';

    public string $checkOut = '';

    public function getTitle(): string
    {
        return 'Oda Müsaitlik Sorgusu';
    }

    public function getHeading(): string
    {
        return 'Müsaitlik';
    }

    public static function getNavigationLabel(): string
    {
        return 'Müsaitlik';
    }

    public function mount(): void
    {
        $this->checkIn = today()->format('Y-m-d');
        $this->checkOut = today()->addDays(7)->format('Y-m-d');
    }

    /**
     * Seçilen tarih aralığında her odanın durumu — view tarafından çağrılır.
     */
    public function getRoomsStatus(): Collection
    {
        if (! $this->checkIn || ! $this->checkOut || $this->checkOut <= $this->checkIn) {
            return collect();
        }

        $checkInDate = Carbon::parse($this->checkIn)->startOfDay();
        $checkOutDate = Carbon::parse($this->checkOut)->startOfDay();
        $nights = (int) $checkInDate->diffInDays($checkOutDate);

        return Room::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(function (Room $room) use ($checkInDate, $checkOutDate, $nights) {
                $overlapping = Reservation::query()
                    ->where('room_id', $room->id)
                    ->whereIn('status', [
                        ReservationStatus::Confirmed,
                        ReservationStatus::Paid,
                        ReservationStatus::Completed,
                    ])
                    ->where('check_in', '<', $checkOutDate)
                    ->where('check_out', '>', $checkInDate)
                    ->orderBy('check_in')
                    ->get();

                $pending = Reservation::query()
                    ->where('room_id', $room->id)
                    ->where('status', ReservationStatus::Pending)
                    ->where('check_in', '<', $checkOutDate)
                    ->where('check_out', '>', $checkInDate)
                    ->orderBy('check_in')
                    ->get();

                return [
                    'room' => $room,
                    'is_available' => $overlapping->isEmpty(),
                    'overlapping' => $overlapping,
                    'pending' => $pending,
                    'has_pending_warning' => $pending->isNotEmpty(),
                    'total_price' => $room->base_price * $nights,
                    'nights' => $nights,
                ];
            });
    }

    public function getNightsCount(): int
    {
        if (! $this->checkIn || ! $this->checkOut || $this->checkOut <= $this->checkIn) {
            return 0;
        }

        return (int) Carbon::parse($this->checkIn)->diffInDays(Carbon::parse($this->checkOut));
    }
}
