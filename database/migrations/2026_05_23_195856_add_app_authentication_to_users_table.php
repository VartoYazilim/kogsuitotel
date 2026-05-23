<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Filament 4 native MFA (App authentication / Google Authenticator) için
     * iki ek kolon:
     *   - app_authentication_secret: TOTP secret (Filament otomatik encrypt eder)
     *   - app_authentication_recovery_codes: Recovery code array (encrypted JSON)
     *
     * Her ikisi de nullable — kullanıcı 2FA setup yapmadan da girebilir,
     * setup tamamlandıktan sonra bu alanlar dolar.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('app_authentication_secret')->nullable();
            $table->text('app_authentication_recovery_codes')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['app_authentication_secret', 'app_authentication_recovery_codes']);
        });
    }
};
