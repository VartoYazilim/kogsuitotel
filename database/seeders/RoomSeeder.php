<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        // Demo cover_image path'leri `public/images/demo/rooms/*.webp` —
        // Unsplash kaynaklı geçici görseller. Sahibin gerçek otel foto'ları
        // geldiğinde Filament admin'den FileUpload ile değiştirilir (yeni
        // path `storage/app/public/rooms/covers/*` formatında olur).
        // Model accessor `cover_image_url` her iki pattern'i de handle eder.
        $rooms = [
            [
                'name' => 'Standart Oda',
                'slug' => 'standart-oda',
                'description' => 'Kompakt ve konforlu. İş seyahatleri ve kısa konaklamalar için ideal; modern minimalist tasarım, ferah çalışma alanı, premium yatak.',
                'capacity' => 2,
                'base_price' => 1500.00,
                'features' => ['Wi-Fi', 'Klima', 'Smart TV', 'Sıcak Su', 'Çalışma Masası'],
                'cover_image' => 'images/demo/rooms/standart.webp',
                'sort_order' => 10,
            ],
            [
                'name' => 'Suit Oda',
                'slug' => 'suit-oda',
                'description' => 'Standart odaya kıyasla daha geniş alan ve oturma köşesi. Hafta sonu kaçamakları için sıcak, sakin bir konaklama.',
                'capacity' => 2,
                'base_price' => 2000.00,
                'features' => ['Wi-Fi', 'Klima', 'Smart TV', 'Mini Bar', 'Oturma Köşesi'],
                'cover_image' => 'images/demo/rooms/suit.webp',
                'sort_order' => 20,
            ],
            [
                'name' => 'Aile Odası',
                'slug' => 'aile-odasi',
                'description' => 'Geniş ve ferah yaşam alanı. Çift ana yatak + ek tek yatak, çocuklu aileler için tasarlandı.',
                'capacity' => 4,
                'base_price' => 2400.00,
                'features' => ['Wi-Fi', 'Klima', 'Smart TV', 'Mini Bar', 'Çocuk Yatağı', 'Geniş Banyo'],
                'cover_image' => 'images/demo/rooms/aile.webp',
                'sort_order' => 30,
            ],
            [
                'name' => 'Deluxe Süit',
                'slug' => 'deluxe-suit',
                'description' => 'Panoramik manzara, jakuzili banyo, oturma alanı ve özenli detaylar. Özel günler için.',
                'capacity' => 3,
                'base_price' => 3500.00,
                'features' => ['Wi-Fi', 'Klima', 'Smart TV', 'Mini Bar', 'Jakuzi', 'Panoramik Manzara', 'Yastık Menüsü'],
                'cover_image' => 'images/demo/rooms/deluxe.webp',
                'sort_order' => 40,
            ],
            [
                'name' => 'Premium Süit',
                'slug' => 'premium-suit',
                'description' => 'Otelimizin amiral süiti. Özel teras, şömine, butler hizmeti ve eksiksiz konfor. En üst düzey lüks ve mahremiyet.',
                'capacity' => 4,
                'base_price' => 5000.00,
                'features' => ['Wi-Fi', 'Klima', 'Smart TV', 'Mini Bar', 'Jakuzi', 'Şömine', 'Özel Teras', 'Butler Hizmeti', 'Karşılama İkramı'],
                'cover_image' => 'images/demo/rooms/premium.webp',
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
