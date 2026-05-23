<?php

namespace Tests\Unit;

use App\Models\Room;
use Tests\TestCase;

class RoomImageUrlTest extends TestCase
{
    public function test_null_path_null_doner(): void
    {
        $this->assertNull(Room::resolvePublicUrl(null));
        $this->assertNull(Room::resolvePublicUrl(''));
    }

    public function test_https_path_oldugu_gibi_doner(): void
    {
        $url = 'https://cdn.example.com/foto.jpg';
        $this->assertSame($url, Room::resolvePublicUrl($url));
    }

    public function test_http_path_oldugu_gibi_doner(): void
    {
        $url = 'http://example.com/foto.jpg';
        $this->assertSame($url, Room::resolvePublicUrl($url));
    }

    public function test_leading_slash_path_public_root_olur(): void
    {
        $this->assertSame(asset('foto.jpg'), Room::resolvePublicUrl('/foto.jpg'));
    }

    public function test_images_prefix_demo_public_root_olur(): void
    {
        $this->assertSame(
            asset('images/demo/rooms/standart.webp'),
            Room::resolvePublicUrl('images/demo/rooms/standart.webp')
        );
    }

    public function test_filament_upload_path_storage_prefix_alir(): void
    {
        $this->assertSame(
            asset('storage/rooms/covers/abc.webp'),
            Room::resolvePublicUrl('rooms/covers/abc.webp')
        );
    }

    public function test_gallery_urls_accessor_tum_pathleri_resolve_eder(): void
    {
        $room = new Room([
            'gallery' => [
                'images/demo/gallery/rooms-1.webp',
                'rooms/gallery/uploaded.webp',
                'https://external.cdn/foto.jpg',
            ],
        ]);

        $urls = $room->gallery_urls;

        $this->assertCount(3, $urls);
        $this->assertSame(asset('images/demo/gallery/rooms-1.webp'), $urls[0]);
        $this->assertSame(asset('storage/rooms/gallery/uploaded.webp'), $urls[1]);
        $this->assertSame('https://external.cdn/foto.jpg', $urls[2]);
    }

    public function test_gallery_urls_bos_array_doner_eger_gallery_null(): void
    {
        $room = new Room(['gallery' => null]);

        $this->assertSame([], $room->gallery_urls);
    }

    public function test_gallery_urls_null_pathleri_filtreler(): void
    {
        $room = new Room([
            'gallery' => [
                'images/demo/gallery/rooms-1.webp',
                null,
                '',
                'rooms/gallery/uploaded.webp',
            ],
        ]);

        $urls = $room->gallery_urls;

        $this->assertCount(2, $urls);
        $this->assertSame(asset('images/demo/gallery/rooms-1.webp'), $urls[0]);
        $this->assertSame(asset('storage/rooms/gallery/uploaded.webp'), $urls[1]);
    }

    public function test_cover_image_url_accessor_resolver_uzerinden_calisir(): void
    {
        $room = new Room(['cover_image' => 'images/demo/rooms/standart.webp']);
        $this->assertSame(
            asset('images/demo/rooms/standart.webp'),
            $room->cover_image_url
        );

        $room->cover_image = null;
        $this->assertNull($room->cover_image_url);
    }
}
