<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Reservations\ReservationResource;
use App\Models\Reservation;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestReservations extends TableWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Son Rezervasyonlar')
            ->description('En son 5 rezervasyon talebi.')
            ->query(
                Reservation::query()
                    ->with('room')
                    ->latest()
                    ->limit(5)
            )
            ->paginated(false)
            ->columns([
                TextColumn::make('reservation_code')
                    ->label('Kod')
                    ->weight('semibold'),

                TextColumn::make('status')
                    ->label('Durum')
                    ->badge(),

                TextColumn::make('room.name')
                    ->label('Oda'),

                TextColumn::make('guest_first_name')
                    ->label('Misafir')
                    ->formatStateUsing(fn (Reservation $record) => $record->guest_full_name),

                TextColumn::make('check_in')
                    ->label('Giriş')
                    ->date('d.m.Y'),

                TextColumn::make('check_out')
                    ->label('Çıkış')
                    ->date('d.m.Y'),

                TextColumn::make('total_price')
                    ->label('Tutar')
                    ->money('TRY'),

                TextColumn::make('created_at')
                    ->label('Talep Zamanı')
                    ->since(),
            ])
            ->recordActions([
                Action::make('view')
                    ->label('Detay')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Reservation $record) => ReservationResource::getUrl('view', ['record' => $record])),
            ]);
    }
}
