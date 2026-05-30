<?php

namespace Database\Seeders;

use App\Models\SocialLink;
use Illuminate\Database\Seeder;

/**
 * SocialLink default seed — boş başlar (sahip kendi platformlarını ekleyecek).
 * 2026_05_25_150001 migration mevcut Setting key'lerini (instagram_url vb.)
 * otomatik taşır; bu seeder fresh install içindir.
 */
class SocialLinkSeeder extends Seeder
{
    public function run(): void
    {
        // No default — sahip admin'den platformları kendisi ekler.
        // Eski demo data kalıntısı bırakmamak için intentional empty.
        $this->command->info('SocialLink seeder: boş (sahip admin\'den ekler).');
    }
}
