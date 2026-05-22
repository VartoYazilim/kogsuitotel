<?php

namespace App\Filament\Widgets;

use App\Enums\ReservationStatus;
use App\Filament\Resources\Reservations\ReservationResource;
use App\Models\Reservation;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class TodayActivity extends TableWidget
{
    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Bugünün Hareketleri')
            ->description('Bugün giriş veya çıkış yapacak misafirler.')
            ->query(
                Reservation::query()
                    ->with('room')
                    ->whereIn('status', [
                        ReservationStatus::Confirmed,
                        ReservationStatus::Paid,
                        ReservationStatus::Completed,
                    ])
                    ->where(function (Builder $q) {
                        $q->whereDate('check_in', today())
                            ->orWhereDate('check_out', today());
                    })
                    ->orderBy('check_in')
            )
            ->emptyStateHeading('Bugün için planlı hareket yok')
            ->emptyStateDescription('Bugün giriş veya çıkış yapacak misafir bulunmuyor.')
            ->emptyStateIcon('heroicon-o-calendar')
            ->paginated(false)
            ->columns([
                TextColumn::make('type')
                    ->label('Tür')
                    ->badge()
                    ->state(function (Reservation $record): string {
                        $isArrival = $record->check_in->isToday();
                        $isDeparture = $record->check_out->isToday();

                        if ($isArrival && $isDeparture) {
                            return 'Giriş + Çıkış';
                        }

                        return $isArrival ? 'Giriş' : 'Çıkış';
                    })
                    ->color(fn (string $state): string => match (true) {
                        str_contains($state, 'Giriş + Çıkış') => 'warning',
                        $state === 'Giriş' => 'success',
                        default => 'info',
                    })
                    ->icon(fn (string $state): string => match (true) {
                        str_contains($state, 'Giriş + Çıkış') => 'heroicon-o-arrows-right-left',
                        $state === 'Giriş' => 'heroicon-o-arrow-right-on-rectangle',
                        default => 'heroicon-o-arrow-left-on-rectangle',
                    }),

                TextColumn::make('reservation_code')
                    ->label('Kod')
                    ->weight('semibold')
                    ->copyable(),

                TextColumn::make('room.name')
                    ->label('Oda'),

                TextColumn::make('guest_first_name')
                    ->label('Misafir')
                    ->formatStateUsing(fn (Reservation $record) => $record->guest_full_name),

                TextColumn::make('guest_phone')
                    ->label('Telefon')
                    ->copyable(),

                TextColumn::make('check_in')
                    ->label('Giriş')
                    ->date('d.m.Y'),

                TextColumn::make('check_out')
                    ->label('Çıkış')
                    ->date('d.m.Y'),

                TextColumn::make('nights')
                    ->label('Gece')
                    ->alignCenter(),
            ])
            ->recordActions([
                Action::make('whatsapp')
                    ->label('WhatsApp')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->url(fn (Reservation $record) => $record->whatsapp_link)
                    ->openUrlInNewTab(),

                Action::make('view')
                    ->label('Detay')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Reservation $record) => ReservationResource::getUrl('view', ['record' => $record])),
            ]);
    }
}
