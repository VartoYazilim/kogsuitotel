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
        $password = (string) env('ADMIN_PASSWORD', '');

        // Prod-grade disiplin: hardcoded fallback yok. Geliştirici/sahip .env'de
        // güçlü, gerçek password set etmek zorunda. Aksi takdirde seed fail eder
        // ve sistem deploy edilmez. Bilinçli "changeme123" gibi varsayılan yok.
        if ($password === '' || $password === 'CHANGE_ME_BEFORE_DEPLOY') {
            throw new \RuntimeException(
                'ADMIN_PASSWORD env değişkeni boş veya placeholder. .env dosyasında '
                .'güçlü ve gerçek bir password set edin. Üretmek için: '
                .'php -r "echo base64_encode(random_bytes(24)) . PHP_EOL;"'
            );
        }

        if (strlen($password) < 12) {
            throw new \RuntimeException(
                'ADMIN_PASSWORD en az 12 karakter olmalıdır. Mevcut: '.strlen($password).' karakter.'
            );
        }

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
