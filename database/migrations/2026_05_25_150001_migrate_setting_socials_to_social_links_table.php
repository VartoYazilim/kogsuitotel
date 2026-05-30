<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Mevcut Setting key'lerinden (instagram_url/facebook_url/google_maps_url/
 * tripadvisor_url) yeni social_links tablosuna dolu olanları kopyalar.
 *
 * Idempotent: aynı URL'i tekrar eklemez (where url = check).
 * Sahip-yüklediği URL'ler korunur; boş setting'ler atlanır.
 *
 * Setting row'ları SİLİNMEZ — geri uyumluluk için Settings'te kalır,
 * ama BusinessSettings UI'sından kaldırıldığı için kullanılmaz.
 */
return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $map = [
            'instagram_url' => ['platform' => 'instagram', 'label' => 'Instagram', 'sort' => 10],
            'facebook_url' => ['platform' => 'facebook', 'label' => 'Facebook', 'sort' => 20],
            'tripadvisor_url' => ['platform' => 'tripadvisor', 'label' => 'Tripadvisor', 'sort' => 30],
            'google_maps_url' => ['platform' => 'mappin', 'label' => 'Google Maps', 'sort' => 40],
        ];

        foreach ($map as $key => $meta) {
            $url = DB::table('settings')->where('key', $key)->value('value');

            if (empty($url)) {
                continue;
            }

            $existing = DB::table('social_links')
                ->where('platform', $meta['platform'])
                ->orWhere('url', $url)
                ->exists();

            if ($existing) {
                continue;
            }

            DB::table('social_links')->insert([
                'platform' => $meta['platform'],
                'label' => $meta['label'],
                'url' => $url,
                'sort_order' => $meta['sort'],
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        // No-op: ters çevirmek anlamlı değil (Settings key'leri zaten duruyor)
    }
};
