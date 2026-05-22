<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Banka / Havale (sahibinden gerçek değer alınana kadar placeholder)
            'iban' => 'TR00 0000 0000 0000 0000 0000 00',
            'iban_holder' => 'Koğ Suit Otel',
            'bank_name' => '—',

            // İletişim
            'phone' => '+90 555 123 45 67',
            'whatsapp' => '+90 555 123 45 67',
            'email' => 'info@kogsuitotel.com',
            'address' => 'Varto, Muş',

            // Konaklama saatleri
            'checkin_time' => '14:00',
            'checkout_time' => '12:00',

            // Sosyal medya (opsiyonel, sonra doldurulur)
            'instagram_url' => '',
            'facebook_url' => '',
            'google_maps_url' => '',
            'tripadvisor_url' => '',
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value],
            );
        }

        $this->command->info(count($settings).' setting hazırlandı.');
    }
}
