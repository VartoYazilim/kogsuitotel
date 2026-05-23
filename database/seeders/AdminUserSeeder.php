<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = (string) env('ADMIN_EMAIL', 'admin@kogsuitotel.com');
        $name = (string) env('ADMIN_NAME', 'Otel Yönetici');
        $password = (string) env('ADMIN_PASSWORD', 'changeme123');

        // `is_admin` User model'inde fillable değil (mass assignment koruması).
        // Seeder context'inde explicit olarak unguarded yazıyoruz.
        Model::unguarded(function () use ($email, $name, $password): void {
            User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => Hash::make($password),
                    'is_admin' => true,
                    'email_verified_at' => now(),
                ],
            );
        });

        $this->command->info("Admin kullanıcı hazır: {$email}");
    }
}
