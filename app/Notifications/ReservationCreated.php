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

    /**
     * Filament notification — bell ikonu açıldığında bu görünür.
     *
     * PII minimizasyonu (KVKK m.4/2-d): notifications tablosunda misafir
     * adı + oda adı duplikasyonu yapılmaz. Sadece rezervasyon kodu tutulur;
     * detay görmek için admin Detayını Aç → Resource view sayfası.
     * Bu tablonun silinmesi unutulursa bile PII sızıntı yüzeyi minimal.
     */
    public function toDatabase(object $notifiable): array
    {
        return FilamentNotification::make()
            ->title('Yeni Rezervasyon Talebi')
            ->icon('heroicon-o-calendar-days')
            ->iconColor('warning')
            ->body("Rezervasyon kodu: **{$this->reservation->reservation_code}**")
            ->actions([
                Action::make('view')
                    ->label('Detayını Aç')
                    ->url(ReservationResource::getUrl('view', ['record' => $this->reservation]))
                    ->markAsRead(),
            ])
            ->getDatabaseMessage();
    }
}
