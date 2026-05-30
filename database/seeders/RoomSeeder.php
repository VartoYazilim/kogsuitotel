<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;

class RoomSeeder extends Seeder
{
    /**
     * Demo asset kaynak dizini: `database/demo-images/` — git'te kalır.
     * Seeder run sırasında bu dosyalar `storage/app/public/rooms/{covers,gallery}/`
     * altına idempotent kopyalanır (yoksa). Filament FileUpload `disk('public')`
     * format'ına uyumlu, admin edit ekranında preview otomatik çalışır.
     * Sahibin gerçek foto'ları geldiğinde admin'den replace edilir; demo dosyalar
     * storage'da kalır (re-seed override etmez — `Storage::exists` defansı).
     */
    public function run(): void
    {
        $rooms = [
            [
                'name' => 'Standart Oda',
                'slug' => 'standart-oda',
                'description' => 'Kompakt ve konforlu. İş seyahati ve kısa konaklamalar için uygun; sade tasarım, çalışma masası, rahat yatak.',
                'capacity' => 2,
                'base_price' => 1500.00,
                'features' => ['Wi-Fi', 'Klima', 'Smart TV', 'Sıcak Su', 'Çalışma Masası'],
                'cover_image' => 'rooms/covers/standart.webp',
                'demo_cover_source' => 'rooms/standart.webp',
                'gallery' => [
                    'rooms/gallery/rooms-1.webp',
                    'rooms/gallery/lobby-1.webp',
                    'rooms/gallery/exterior-1.webp',
                ],
                'demo_gallery_sources' => [
                    'gallery/rooms-1.webp',
                    'gallery/lobby-1.webp',
                    'gallery/exterior-1.webp',
                ],
                'sort_order' => 10,
            ],
            [
                'name' => 'Suit Oda',
                'slug' => 'suit-oda',
                'description' => 'Standart odaya göre daha geniş alan ve oturma köşesi var. Hafta sonu için rahat bir tercih.',
                'capacity' => 2,
                'base_price' => 2000.00,
                'features' => ['Wi-Fi', 'Klima', 'Smart TV', 'Mini Bar', 'Oturma Köşesi'],
                'cover_image' => 'rooms/covers/suit.webp',
                'demo_cover_source' => 'rooms/suit.webp',
                'gallery' => [
                    'rooms/gallery/rooms-1.webp',
                    'rooms/gallery/exterior-2.webp',
                    'rooms/gallery/view-1.webp',
                ],
                'demo_gallery_sources' => [
                    'gallery/rooms-1.webp',
                    'gallery/exterior-2.webp',
                    'gallery/view-1.webp',
                ],
                'sort_order' => 20,
            ],
            [
                'name' => 'Aile Odası',
                'slug' => 'aile-odasi',
                'description' => 'Geniş yaşam alanı. Çift kişilik yatak + ek tek yatak; çocuklu aileler için uygun.',
                'capacity' => 4,
                'base_price' => 2400.00,
                'features' => ['Wi-Fi', 'Klima', 'Smart TV', 'Mini Bar', 'Çocuk Yatağı', 'Geniş Banyo'],
                'cover_image' => 'rooms/covers/aile.webp',
                'demo_cover_source' => 'rooms/aile.webp',
                'gallery' => [
                    'rooms/gallery/rooms-1.webp',
                    'rooms/gallery/view-2.webp',
                    'rooms/gallery/lobby-1.webp',
                ],
                'demo_gallery_sources' => [
                    'gallery/rooms-1.webp',
                    'gallery/view-2.webp',
                    'gallery/lobby-1.webp',
                ],
                'sort_order' => 30,
            ],
            [
                'name' => 'Deluxe Süit',
                'slug' => 'deluxe-suit',
                'description' => 'Geniş manzara, jakuzili banyo ve ayrı oturma alanı. Özel günler veya rahat bir tatil için.',
                'capacity' => 3,
                'base_price' => 3500.00,
                'features' => ['Wi-Fi', 'Klima', 'Smart TV', 'Mini Bar', 'Jakuzi', 'Geniş Manzara'],
                'cover_image' => 'rooms/covers/deluxe.webp',
                'demo_cover_source' => 'rooms/deluxe.webp',
                'gallery' => [
                    'rooms/gallery/view-1.webp',
                    'rooms/gallery/exterior-1.webp',
                    'rooms/gallery/lobby-1.webp',
                    'rooms/gallery/rooms-1.webp',
                ],
                'demo_gallery_sources' => [
                    'gallery/view-1.webp',
                    'gallery/exterior-1.webp',
                    'gallery/lobby-1.webp',
                    'gallery/rooms-1.webp',
                ],
                'sort_order' => 40,
            ],
            [
                'name' => 'Premium Süit',
                'slug' => 'premium-suit',
                'description' => 'En geniş odamız. Özel teras, şömine ve geniş yaşam alanı. Uzun konaklamalar veya özel bir tatil için.',
                'capacity' => 4,
                'base_price' => 5000.00,
                'features' => ['Wi-Fi', 'Klima', 'Smart TV', 'Mini Bar', 'Jakuzi', 'Şömine', 'Özel Teras', 'Karşılama İkramı'],
                'cover_image' => 'rooms/covers/premium.webp',
                'demo_cover_source' => 'rooms/premium.webp',
                'gallery' => [
                    'rooms/gallery/view-2.webp',
                    'rooms/gallery/exterior-2.webp',
                    'rooms/gallery/view-1.webp',
                    'rooms/gallery/lobby-1.webp',
                    'rooms/gallery/rooms-1.webp',
                ],
                'demo_gallery_sources' => [
                    'gallery/view-2.webp',
                    'gallery/exterior-2.webp',
                    'gallery/view-1.webp',
                    'gallery/lobby-1.webp',
                    'gallery/rooms-1.webp',
                ],
                'sort_order' => 50,
            ],
        ];

        foreach ($rooms as $room) {
            // Demo asset'leri storage'a kopyala — gallery için (multi)
            $this->copyDemoAsset($room['demo_cover_source'], $room['cover_image']);
            foreach ($room['demo_gallery_sources'] as $i => $source) {
                $this->copyDemoAsset($source, $room['gallery'][$i]);
            }

            unset($room['demo_cover_source'], $room['demo_gallery_sources']);

            // firstOrCreate — sadece slug yoksa yeni oda oluşturur, mevcut record'a
            // dokunmaz. Migration zaten demo path'leri yeni format'a çevirdi.
            // Sahibin gerçek foto'ları korunur.
            Room::firstOrCreate(
                ['slug' => $room['slug']],
                array_merge($room, ['is_active' => true]),
            );
        }

        $this->command->info(count($rooms).' oda hazırlandı (demo asset storage\'a kopyalandı).');
    }

    /**
     * Demo asset dosyasını storage'a kopyalar (idempotent).
     * - Source `database/demo-images/X` yoksa → skip
     * - Dest `storage/app/public/X` zaten varsa → skip (sahip override etmiş olabilir)
     * - Aksi takdirde → kopyala
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
