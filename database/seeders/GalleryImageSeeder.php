<?php

namespace Database\Seeders;

use App\Models\GalleryImage;
use Illuminate\Database\Seeder;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;

class GalleryImageSeeder extends Seeder
{
    /**
     * Demo galeri asset kaynak dizini: `database/demo-images/gallery/`.
     * Seeder run sırasında dosyalar `storage/app/public/gallery/` altına
     * idempotent kopyalanır. Filament FileUpload `disk('public')` ile preview
     * + edit/replace çalışır. Sahibin yüklediği görseller `Storage::exists`
     * defansı ile korunur.
     */
    public function run(): void
    {
        $images = [
            ['category' => 'exterior', 'path' => 'gallery/exterior-1.webp', 'demo_source' => 'gallery/exterior-1.webp', 'alt_text' => 'Koğ Suit Otel dış cephe ve giriş'],
            ['category' => 'exterior', 'path' => 'gallery/exterior-2.webp', 'demo_source' => 'gallery/exterior-2.webp', 'alt_text' => 'Otel binası ve doğal çevre'],
            ['category' => 'lobby', 'path' => 'gallery/lobby-1.webp', 'demo_source' => 'gallery/lobby-1.webp', 'alt_text' => 'Sıcak karşılama alanı ve lobi'],
            ['category' => 'rooms', 'path' => 'gallery/rooms-1.webp', 'demo_source' => 'gallery/rooms-1.webp', 'alt_text' => 'Modern ve konforlu oda iç mekanı'],
            ['category' => 'view', 'path' => 'gallery/view-1.webp', 'demo_source' => 'gallery/view-1.webp', 'alt_text' => 'Otelden geniş dağ manzarası'],
            ['category' => 'view', 'path' => 'gallery/view-2.webp', 'demo_source' => 'gallery/view-2.webp', 'alt_text' => 'Gün batımında Varto manzarası'],
        ];

        foreach ($images as $i => $image) {
            $this->copyDemoAsset($image['demo_source'], $image['path']);

            unset($image['demo_source']);

            GalleryImage::firstOrCreate(
                ['path' => $image['path']],
                array_merge($image, ['sort_order' => ($i + 1) * 10]),
            );
        }

        $this->command->info(count($images).' galeri görseli hazırlandı (demo asset storage\'a kopyalandı).');
    }

    /**
     * Demo asset dosyasını storage'a kopyalar (idempotent).
     * Bkz: RoomSeeder::copyDemoAsset (aynı pattern).
     */
    private function copyDemoAsset(string $sourceRelative, string $destStorageRelative): void
    {
        $sourceAbsolute = database_path('demo-images/'.$sourceRelative);

        if (! file_exists($sourceAbsolute)) {
            return;
        }

        if (Storage::disk('public')->exists($destStorageRelative)) {
            return;
        }

        Storage::disk('public')->putFileAs(
            dirname($destStorageRelative),
            new File($sourceAbsolute),
            basename($destStorageRelative),
        );
    }
}
