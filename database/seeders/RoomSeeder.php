<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        // Demo cover + gallery path'leri `public/images/demo/{rooms,gallery}/*.webp` —
        // Unsplash kaynaklı geçici görseller. Sahibin gerçek otel foto'ları
        // geldiğinde Filament admin'den FileUpload ile değiştirilir (yeni
        // path `storage/app/public/rooms/{covers,gallery}/*` formatında olur).
        // Model accessor `cover_image_url` ve `gallery_urls` her iki pattern'i
        // de handle eder.
        $rooms = [
            [
                'name' => 'Standart Oda',
                'slug' => 'standart-oda',
                'description' => 'Kompakt ve konforlu. İş seyahati ve kısa konaklamalar için uygun; sade tasarım, çalışma masası, rahat yatak.',
                'capacity' => 2,
                'base_price' => 1500.00,
                'features' => ['Wi-Fi', 'Klima', 'Smart TV', 'Sıcak Su', 'Çalışma Masası'],
                'cover_image' => 'images/demo/rooms/standart.webp',
                'gallery' => [
                    'images/demo/gallery/rooms-1.webp',
                    'images/demo/gallery/lobby-1.webp',
                    'images/demo/gallery/exterior-1.webp',
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
                'cover_image' => 'images/demo/rooms/suit.webp',
                'gallery' => [
                    'images/demo/gallery/rooms-1.webp',
                    'images/demo/gallery/exterior-2.webp',
                    'images/demo/gallery/view-1.webp',
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
                'cover_image' => 'images/demo/rooms/aile.webp',
                'gallery' => [
                    'images/demo/gallery/rooms-1.webp',
                    'images/demo/gallery/view-2.webp',
                    'images/demo/gallery/lobby-1.webp',
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
                'cover_image' => 'images/demo/rooms/deluxe.webp',
                'gallery' => [
                    'images/demo/gallery/view-1.webp',
                    'images/demo/gallery/exterior-1.webp',
                    'images/demo/gallery/lobby-1.webp',
                    'images/demo/gallery/rooms-1.webp',
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
                'cover_image' => 'images/demo/rooms/premium.webp',
                'gallery' => [
                    'images/demo/gallery/view-2.webp',
                    'images/demo/gallery/exterior-2.webp',
                    'images/demo/gallery/view-1.webp',
                    'images/demo/gallery/lobby-1.webp',
                    'images/demo/gallery/rooms-1.webp',
                ],
                'sort_order' => 50,
            ],
        ];

        foreach ($rooms as $room) {
            Room::updateOrCreate(
                ['slug' => $room['slug']],
                array_merge($room, ['is_active' => true]),
            );
        }

        $this->command->info(count($rooms).' oda hazırlandı (demo cover image dahil).');
    }
}
