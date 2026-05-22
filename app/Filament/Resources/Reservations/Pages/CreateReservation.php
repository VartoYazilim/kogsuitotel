<?php

namespace App\Filament\Resources\Reservations\Pages;

use App\Filament\Resources\Reservations\ReservationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateReservation extends CreateRecord
{
    protected static string $resource = ReservationResource::class;

    /** "Oluştur & yeni oluştur" butonunu gizle — kafa karıştırıcı. */
    protected static bool $canCreateAnother = false;
}
