<?php

namespace Tests\Feature;

use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomGalleryRenderTest extends TestCase
{
    use RefreshDatabase;

    public function test_oda_detay_sayfasi_galerideki_tum_gorselleri_render_eder(): void
    {
        $room = Room::factory()->create([
            'slug' => 'test-suit',
            'cover_image' => 'images/demo/rooms/suit.webp',
            'gallery' => [
                'images/demo/gallery/rooms-1.webp',
                'images/demo/gallery/view-1.webp',
                'images/demo/gallery/lobby-1.webp',
            ],
        ]);

        $response = $this->get(route('rooms.show', $room));

        $response->assertOk();

        // Cover lightbox trigger
        $response->assertSee('data-lightbox-trigger', false);
        $response->assertSee('data-lightbox-group="room-test-suit"', false);

        // Her gallery item ayrı bir trigger butonu olarak görünür
        foreach ($room->gallery_urls as $url) {
            $response->assertSee($url, false);
        }

        // Galeri index 1, 2, 3 olarak butonlara işlenir (cover = 0)
        $response->assertSee('data-lightbox-index="0"', false);
        $response->assertSee('data-lightbox-index="1"', false);
        $response->assertSee('data-lightbox-index="3"', false);
    }

    public function test_galeri_bos_oda_yine_yuklenir_sadece_kapakla(): void
    {
        $room = Room::factory()->create([
            'cover_image' => 'images/demo/rooms/standart.webp',
            'gallery' => null,
        ]);

        $response = $this->get(route('rooms.show', $room));

        $response->assertOk();
        $response->assertSee($room->cover_image_url, false);
        $response->assertDontSee('data-lightbox-index="1"', false);
    }

    public function test_lightbox_modal_layout_partial_render_eder(): void
    {
        $room = Room::factory()->create();

        $response = $this->get(route('rooms.show', $room));

        $response->assertSee('id="kog-lightbox"', false);
        $response->assertSee('data-lightbox-image', false);
        $response->assertSee('data-lightbox-close', false);
        $response->assertSee('data-lightbox-prev', false);
        $response->assertSee('data-lightbox-next', false);
        $response->assertSee('aria-label="Görsel önizleme"', false);
    }
}
