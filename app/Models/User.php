<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthentication;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthenticationRecovery;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Filament 4 MFA cast'leri Larastan'a runtime'da yansımaz; property tipleri
 * burada beyan ediyoruz.
 *
 * @property ?string $app_authentication_secret
 * @property ?array<string> $app_authentication_recovery_codes
 */
class User extends Authenticatable implements FilamentUser, HasAppAuthentication, HasAppAuthenticationRecovery
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * `is_admin` BİLİNÇLİ olarak fillable dışında — privilege escalation
     * koruması. Admin yapma işi seeder'da `Model::unguarded()` ile veya
     * admin panelde explicit `$user->is_admin = true; $user->save();` ile
     * yapılır.
     */
    protected $fillable = ['name', 'email', 'password'];

    protected $hidden = [
        'password',
        'remember_token',
        // 2FA secret + recovery codes hash'siz görünmesin (DB encrypted ama API/JSON yansıması temiz)
        'app_authentication_secret',
        'app_authentication_recovery_codes',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            // Filament Filament 4 MFA — secret string olarak DB'de encrypted,
            // recovery codes array olarak encrypted JSON.
            'app_authentication_secret' => 'encrypted',
            'app_authentication_recovery_codes' => 'encrypted:array',
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

    /* ─────────── Filament HasAppAuthentication interface ─────────── */

    public function getAppAuthenticationSecret(): ?string
    {
        return $this->app_authentication_secret;
    }

    public function saveAppAuthenticationSecret(?string $secret): void
    {
        $this->app_authentication_secret = $secret;
        $this->save();
    }

    public function getAppAuthenticationHolderName(): string
    {
        // Google Authenticator / Authy gibi uygulamalarda hesap ismi olarak görünür.
        return $this->email;
    }

    /* ─────────── HasAppAuthenticationRecovery interface ─────────── */

    /**
     * @return ?array<string>
     */
    public function getAppAuthenticationRecoveryCodes(): ?array
    {
        return $this->app_authentication_recovery_codes;
    }

    /**
     * @param  ?array<string>  $codes
     */
    public function saveAppAuthenticationRecoveryCodes(?array $codes): void
    {
        $this->app_authentication_recovery_codes = $codes;
        $this->save();
    }
}
