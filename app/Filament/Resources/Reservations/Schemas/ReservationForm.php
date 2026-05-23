<?php

namespace App\Filament\Resources\Reservations\Schemas;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use App\Models\Room;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ReservationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // ─────────── 1. Konaklama Detayları (ÖNCE — oda seçilince tarih dolu günler aktif) ───────────
                Section::make('Konaklama Detayları')
                    ->description('Önce odayı seçin; uygun olmayan tarihler takvimde gri görünür.')
                    ->icon(Heroicon::OutlinedCalendarDays)
                    ->iconColor('primary')
                    ->columns(2)
                    ->components([
                        Select::make('room_id')
                            ->label('Oda')
                            ->relationship('room', 'name')
                            ->required()
                            ->live()
                            ->preload()
                            ->searchable()
                            ->prefixIcon(Heroicon::OutlinedHome)
                            ->columnSpanFull(),

                        DatePicker::make('check_in')
                            ->label('Giriş Tarihi')
                            ->required()
                            ->native(false)
                            ->displayFormat('d.m.Y')
                            ->locale('tr')
                            ->minDate(today())
                            ->prefixIcon(Heroicon::OutlinedArrowRightOnRectangle)
                            ->live()
                            ->disabledDates(fn (Get $get, ?Reservation $record): array => self::unavailableDates(
                                roomId: $get('room_id'),
                                excludeReservationId: $record?->id,
                            ))
                            ->helperText('Oda seçilince dolu günler gri görünür')
                            ->afterStateUpdated(function (Set $set, Get $get, ?string $state) {
                                if ($state && $get('check_out')) {
                                    self::recalculatePrice($get, $set);
                                }
                            }),

                        DatePicker::make('check_out')
                            ->label('Çıkış Tarihi')
                            ->required()
                            ->native(false)
                            ->displayFormat('d.m.Y')
                            ->locale('tr')
                            ->after('check_in')
                            ->prefixIcon(Heroicon::OutlinedArrowLeftOnRectangle)
                            ->live()
                            ->disabledDates(fn (Get $get, ?Reservation $record): array => self::unavailableDates(
                                roomId: $get('room_id'),
                                excludeReservationId: $record?->id,
                            ))
                            ->afterStateUpdated(function (Set $set, Get $get) {
                                self::recalculatePrice($get, $set);
                            }),

                        TextInput::make('adults')
                            ->label('Yetişkin Sayısı')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(10)
                            ->default(2)
                            ->prefixIcon(Heroicon::OutlinedUser),

                        TextInput::make('children')
                            ->label('Çocuk Sayısı')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(10)
                            ->default(0)
                            ->prefixIcon(Heroicon::OutlinedUserGroup),

                        TextInput::make('nights')
                            ->label('Gece Sayısı')
                            ->numeric()
                            ->minValue(1)
                            ->disabled()
                            ->dehydrated()
                            ->prefixIcon(Heroicon::OutlinedMoon)
                            ->helperText('Tarihten otomatik hesaplanır'),

                        TextInput::make('total_price')
                            ->label('Toplam Tutar')
                            ->required()
                            ->numeric()
                            ->step(0.01)
                            ->prefix('₺')
                            ->prefixIcon(Heroicon::OutlinedBanknotes)
                            ->helperText('Oda × Gece otomatik hesaplanır, düzenlenebilir'),

                        Select::make('status')
                            ->label('Durum')
                            ->options(ReservationStatus::class)
                            ->default(ReservationStatus::Pending)
                            ->required()
                            ->native(false)
                            ->prefixIcon(Heroicon::OutlinedCheckCircle)
                            ->columnSpanFull(),
                    ]),

                // ─────────── 2. Misafir Bilgileri ───────────
                Section::make('Misafir Bilgileri')
                    ->description('Rezervasyonu yapan kişinin iletişim bilgileri.')
                    ->icon(Heroicon::OutlinedUserCircle)
                    ->iconColor('primary')
                    ->columns(2)
                    ->components([
                        TextInput::make('guest_first_name')
                            ->label('Ad')
                            ->required()
                            ->maxLength(100)
                            ->prefixIcon(Heroicon::OutlinedUser),

                        TextInput::make('guest_last_name')
                            ->label('Soyad')
                            ->required()
                            ->maxLength(100)
                            ->prefixIcon(Heroicon::OutlinedUser),

                        TextInput::make('guest_phone')
                            ->label('Telefon')
                            ->tel()
                            ->required()
                            ->maxLength(30)
                            ->placeholder('+90 555 123 45 67')
                            ->prefixIcon(Heroicon::OutlinedPhone)
                            ->helperText('WhatsApp aksiyonu için E.164 formatı önerilir'),

                        TextInput::make('guest_email')
                            ->label('E-posta')
                            ->email()
                            ->required()
                            ->maxLength(150)
                            ->prefixIcon(Heroicon::OutlinedEnvelope),
                    ]),

                // ─────────── 3. Notlar (full-width) ───────────
                Section::make('Notlar')
                    ->icon(Heroicon::OutlinedChatBubbleBottomCenterText)
                    ->iconColor('gray')
                    ->collapsible()
                    ->columnSpanFull()
                    ->columns(2)
                    ->components([
                        Textarea::make('special_requests')
                            ->label('Özel İstekler (Misafir)')
                            ->rows(4)
                            ->maxLength(1000)
                            ->placeholder('Misafirin rezervasyon sırasında belirttiği özel talepler')
                            ->helperText('⚠️ KVKK: Misafirin yazdığı özel nitelikli veriler (sağlık/inanç) bu alanda görünebilir. İçerik 3. taraf paylaşımında dikkatli olun.'),

                        Textarea::make('admin_notes')
                            ->label('Yönetici Notları (Sadece Admin)')
                            ->rows(4)
                            ->maxLength(1000)
                            ->placeholder('Misafire görünmez — internal notlar')
                            ->helperText('⚠️ KVKK: TCKN, sağlık vb. özel nitelikli veri yazmayın. Sadece operasyonel notlar (ör. erken giriş, transfer talebi).'),
                    ]),

                // ─────────── 4. Sistem (full-width, collapsed default) ───────────
                Section::make('Sistem')
                    ->icon(Heroicon::OutlinedCog)
                    ->iconColor('gray')
                    ->collapsible()
                    ->collapsed()
                    ->columnSpanFull()
                    ->components([
                        TextInput::make('reservation_code')
                            ->label('Rezervasyon Kodu')
                            ->maxLength(20)
                            ->placeholder('KSO-YYYY-NNNN — boş bırakılırsa otomatik üretilir')
                            ->prefixIcon(Heroicon::OutlinedHashtag)
                            ->helperText('Sadece manuel düzeltme için. Yeni rezervasyonlarda boş bırakın.')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    /**
     * Belirli bir oda için aktif (confirmed/paid/completed) rezervasyonların
     * çakıştığı tüm günleri (check_in dahil, check_out hariç) array olarak döner.
     * DatePicker disabledDates için.
     */
    protected static function unavailableDates(?int $roomId, ?int $excludeReservationId = null): array
    {
        if (! $roomId) {
            return [];
        }

        $query = Reservation::query()
            ->where('room_id', $roomId)
            ->whereIn('status', [
                ReservationStatus::Confirmed,
                ReservationStatus::Paid,
                ReservationStatus::Completed,
            ])
            ->whereDate('check_out', '>=', today());

        if ($excludeReservationId) {
            $query->where('id', '!=', $excludeReservationId);
        }

        $dates = [];
        foreach ($query->get(['check_in', 'check_out']) as $r) {
            $cursor = $r->check_in->copy();
            while ($cursor->lt($r->check_out)) {
                $dates[] = $cursor->format('Y-m-d');
                $cursor->addDay();
            }
        }

        return $dates;
    }

    /** Gece sayısı + toplam tutar otomatik hesaplama. */
    protected static function recalculatePrice(Get $get, Set $set): void
    {
        $checkIn = $get('check_in');
        $checkOut = $get('check_out');
        $roomId = $get('room_id');

        if (! $checkIn || ! $checkOut || ! $roomId) {
            return;
        }

        $a = Carbon::parse($checkIn);
        $b = Carbon::parse($checkOut);
        $nights = (int) $a->diffInDays($b);

        if ($nights <= 0) {
            return;
        }

        $set('nights', $nights);

        $room = Room::find($roomId);
        if ($room) {
            $set('total_price', $room->base_price * $nights);
        }
    }
}
