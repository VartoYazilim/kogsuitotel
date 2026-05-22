<?php

namespace App\Filament\Resources\Reservations\Tables;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ReservationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('check_in', 'desc')
            ->columns([
                TextColumn::make('reservation_code')
                    ->label('Kod')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Kod kopyalandı')
                    ->weight('semibold'),

                TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->sortable(),

                TextColumn::make('room.name')
                    ->label('Oda')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('guest_full_name')
                    ->label('Misafir')
                    ->getStateUsing(fn (Reservation $record) => $record->guest_full_name)
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query
                            ->where('guest_first_name', 'like', "%{$search}%")
                            ->orWhere('guest_last_name', 'like', "%{$search}%");
                    }),

                TextColumn::make('guest_phone')
                    ->label('Telefon')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('check_in')
                    ->label('Giriş')
                    ->date('d.m.Y')
                    ->sortable(),

                TextColumn::make('check_out')
                    ->label('Çıkış')
                    ->date('d.m.Y')
                    ->sortable(),

                TextColumn::make('nights')
                    ->label('Gece')
                    ->numeric()
                    ->alignCenter()
                    ->toggleable(),

                TextColumn::make('total_price')
                    ->label('Tutar')
                    ->money('TRY')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Oluşturuldu')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Durum')
                    ->options(collect(ReservationStatus::cases())
                        ->mapWithKeys(fn ($s) => [$s->value => $s->getLabel()])
                        ->all())
                    ->multiple(),

                SelectFilter::make('room_id')
                    ->label('Oda')
                    ->relationship('room', 'name')
                    ->multiple()
                    ->preload(),

                Filter::make('check_in_range')
                    ->label('Giriş Tarihi Aralığı')
                    ->schema([
                        \Filament\Forms\Components\DatePicker::make('from')
                            ->label('Başlangıç')
                            ->native(false)
                            ->displayFormat('d.m.Y'),
                        \Filament\Forms\Components\DatePicker::make('until')
                            ->label('Bitiş')
                            ->native(false)
                            ->displayFormat('d.m.Y'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'] ?? null, fn ($q, $d) => $q->whereDate('check_in', '>=', $d))
                            ->when($data['until'] ?? null, fn ($q, $d) => $q->whereDate('check_in', '<=', $d));
                    }),
            ])
            ->recordActions([
                Action::make('whatsapp')
                    ->label('WhatsApp')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->url(fn (Reservation $record) => $record->whatsapp_link)
                    ->openUrlInNewTab(),

                Action::make('mark_paid')
                    ->label('Ödendi')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Rezervasyonu ödendi olarak işaretle?')
                    ->modalDescription(fn (Reservation $record) => "{$record->reservation_code} kodu ödendi olarak güncellenecek.")
                    ->visible(fn (Reservation $record) => in_array($record->status, [ReservationStatus::Pending, ReservationStatus::Confirmed], true))
                    ->action(function (Reservation $record) {
                        $record->update(['status' => ReservationStatus::Paid]);
                    }),

                Action::make('mark_confirmed')
                    ->label('Onayla')
                    ->icon('heroicon-o-check-circle')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn (Reservation $record) => $record->status === ReservationStatus::Pending)
                    ->action(function (Reservation $record) {
                        $record->update(['status' => ReservationStatus::Confirmed]);
                    }),

                Action::make('cancel')
                    ->label('İptal')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Rezervasyonu iptal et?')
                    ->visible(fn (Reservation $record) => ! in_array($record->status, [ReservationStatus::Cancelled, ReservationStatus::Completed], true))
                    ->action(function (Reservation $record) {
                        $record->update(['status' => ReservationStatus::Cancelled]);
                    }),

                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
