<?php

namespace Database\Seeders;

use App\Models\GalleryImage;
use Illuminate\Database\Seeder;

class GalleryImageSeeder extends Seeder
{
    public function run(): void
    {
        // Demo görsel path'leri `public/images/demo/gallery/*.jpg` — Unsplash
        // kaynaklı geçici. Sahibin foto'ları geldiğinde Filament admin'den
        // GalleryImageResource ile değiştirilir.
        // Kategoriler: exterior, rooms, lobby, view (CLAUDE.md veri modeli).
        $images = [
            ['category' => 'exterior', 'path' => 'images/demo/gallery/exterior-1.jpg', 'alt_text' => 'Koğ Suit Otel dış cephe ve giriş'],
            ['category' => 'exterior', 'path' => 'images/demo/gallery/exterior-2.jpg', 'alt_text' => 'Otel binası ve doğal çevre'],
            ['category' => 'lobby', 'path' => 'images/demo/gallery/lobby-1.jpg', 'alt_text' => 'Sıcak karşılama alanı ve lobi'],
            ['category' => 'rooms', 'path' => 'images/demo/gallery/rooms-1.jpg', 'alt_text' => 'Modern ve konforlu oda iç mekanı'],
            ['category' => 'view', 'path' => 'images/demo/gallery/view-1.jpg', 'alt_text' => 'Otelden panoramik dağ manzarası'],
            ['category' => 'view', 'path' => 'images/demo/gallery/view-2.jpg', 'alt_text' => 'Gün batımında Varto manzarası'],
        ];

        foreach ($images as $i => $image) {
            GalleryImage::updateOrCreate(
                ['path' => $image['path']],
                array_merge($image, ['sort_order' => ($i + 1) * 10]),
            );
        }

        $this->command->info(count($images).' galeri görseli hazırlandı (demo).');
    }
}
