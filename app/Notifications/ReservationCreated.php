<?php

namespace App\Notifications;

use App\Filament\Resources\Reservations\ReservationResource;
use App\Models\Reservation;
use Filament\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReservationCreated extends Notification
{
    use Queueable;

    public function __construct(public Reservation $reservation) {}

    /** Hangi kanal(lar) — sadece database (Filament header bell ikonu). */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /** Filament notification — bell ikonu açıldığında bu görünür. */
    public function toDatabase(object $notifiable): array
    {
        $guestFullName = trim(
            $this->reservation->guest_first_name.' '.$this->reservation->guest_last_name
        );

        return FilamentNotification::make()
            ->title('Yeni Rezervasyon Talebi')
            ->icon('heroicon-o-calendar-days')
            ->iconColor('warning')
            ->body("**{$this->reservation->reservation_code}** · {$guestFullName} · {$this->reservation->room->name}")
            ->actions([
                Action::make('view')
                    ->label('Detayını Aç')
                    ->url(ReservationResource::getUrl('view', ['record' => $this->reservation]))
                    ->markAsRead(),
            ])
            ->getDatabaseMessage();
    }
}
