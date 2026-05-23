<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'is_admin'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    /**
     * Filament panel erişim kontrolü.
     * Sadece `is_admin = true` olanlar /kog-yonetim panel'ine girebilir.
     * Başka panel id'ler eklenirse buraya whitelist olarak yansıyacak.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $panel->getId() === 'kog' && $this->is_admin === true;
    }
}
