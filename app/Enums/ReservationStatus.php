<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ReservationStatus: string implements HasColor, HasIcon, HasLabel
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Paid = 'paid';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case NoShow = 'no_show';

    public function getLabel(): string
    {
        return match ($this) {
            self::Pending => 'Bekliyor',
            self::Confirmed => 'Onaylandı',
            self::Paid => 'Ödendi',
            self::Completed => 'Tamamlandı',
            self::Cancelled => 'İptal',
            self::NoShow => 'Gelmedi',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Pending => 'gray',
            self::Confirmed => 'warning',
            self::Paid => 'success',
            self::Completed => 'info',
            self::Cancelled => 'danger',
            self::NoShow => 'gray',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Pending => 'heroicon-o-clock',
            self::Confirmed => 'heroicon-o-check-circle',
            self::Paid => 'heroicon-o-banknotes',
            self::Completed => 'heroicon-o-flag',
            self::Cancelled => 'heroicon-o-x-circle',
            self::NoShow => 'heroicon-o-user-minus',
        };
    }
}
