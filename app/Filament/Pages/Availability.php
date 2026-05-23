<?php

namespace App\Filament\Pages;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use App\Models\Room;
use BackedEnum;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use UnitEnum;

/**
 * InteractsWithForms trait `$form` property'sini magic `__get` ile expose eder.
 * Larastan static analiz bunu görmez — PHPDoc ile tip beyan ediyoruz.
 *
 * @property-read \Filament\Schemas\Schema $form
 */
class Availability extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.availability';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static string|UnitEnum|null $navigationGroup = 'Operasyon';

    protected static ?int $navigationSort = 20;

    /**
     * Filament HasForms statePath — DatePicker'lar `data.checkIn` / `data.checkOut`
     * altında bind olur. Public site Flatpickr ile UX tutarli (Filament DatePicker
     * Flatpickr tabanli, TR locale + Olive Sanctuary palette + mobile-responsive).
     */
    public ?array $data = [];

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
        $this->form->fill([
            'checkIn' => today()->format('Y-m-d'),
            'checkOut' => today()->addDays(7)->format('Y-m-d'),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                DatePicker::make('checkIn')
                    ->label('Giriş Tarihi')
                    ->native(false)
                    ->displayFormat('d.m.Y')
                    ->locale('tr')
                    ->prefixIcon(Heroicon::OutlinedArrowRightOnRectangle)
                    ->minDate(today()->subYear())
                    ->required()
                    ->live(),

                DatePicker::make('checkOut')
                    ->label('Çıkış Tarihi')
                    ->native(false)
                    ->displayFormat('d.m.Y')
                    ->locale('tr')
                    ->prefixIcon(Heroicon::OutlinedArrowLeftOnRectangle)
                    ->after('checkIn')
                    ->required()
                    ->live(),

                Placeholder::make('nights_display')
                    ->label('Konaklama')
                    ->content(fn (): HtmlString => new HtmlString(
                        '<span class="text-2xl font-bold text-primary-700 dark:text-primary-300">'
                        .$this->getNightsCount()
                        .'</span> <span class="text-sm text-gray-500 dark:text-gray-400">gece</span>'
                    )),
            ])
            ->statePath('data');
    }

    /**
     * Form'dan checkIn alır, Y-m-d string veya Carbon olabilir (Filament DatePicker
     * Y-m-d döner) — Carbon::parse() ikisini de handle eder.
     */
    protected function getCheckInDate(): ?Carbon
    {
        $value = $this->data['checkIn'] ?? null;

        return $value ? Carbon::parse($value)->startOfDay() : null;
    }

    protected function getCheckOutDate(): ?Carbon
    {
        $value = $this->data['checkOut'] ?? null;

        return $value ? Carbon::parse($value)->startOfDay() : null;
    }

    public function getNightsCount(): int
    {
        $checkIn = $this->getCheckInDate();
        $checkOut = $this->getCheckOutDate();

        if (! $checkIn || ! $checkOut || $checkOut->lte($checkIn)) {
            return 0;
        }

        return (int) $checkIn->diffInDays($checkOut);
    }

    /**
     * Seçilen tarih aralığında her odanın durumu — view tarafından çağrılır.
     */
    public function getRoomsStatus(): Collection
    {
        $checkInDate = $this->getCheckInDate();
        $checkOutDate = $this->getCheckOutDate();
        $nights = $this->getNightsCount();

        if (! $checkInDate || ! $checkOutDate || $nights === 0) {
            return collect();
        }

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
}
