<?php

namespace Tests\Feature;

use App\Filament\Resources\GalleryImages\Pages\ManageGalleryImages;
use App\Filament\Resources\Rooms\Pages\CreateRoom;
use App\Models\GalleryImage;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class AdminWebpUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_admin_jpg_yuklerse_db_de_webp_path_yazilir(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $jpg = File::image('hotel.jpg', 800, 600);

        Livewire::actingAs($admin)
            ->test(CreateRoom::class)
            ->fillForm([
                'name' => 'WebP Test Odası',
                'slug' => 'webp-test',
                'description' => 'Otomatik WebP dönüşüm testi.',
                'capacity' => 2,
                'base_price' => 1500,
                'sort_order' => 99,
                'cover_image' => $jpg,
                'is_active' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $room = Room::where('slug', 'webp-test')->firstOrFail();

        $this->assertStringStartsWith('rooms/covers/', $room->cover_image);
        $this->assertStringEndsWith('.webp', $room->cover_image);
        $this->assertStringNotContainsString('hotel.jpg', $room->cover_image);
        Storage::disk('public')->assertExists($room->cover_image);
    }

    public function test_admin_png_galeri_yuklerse_hepsi_webp_olur(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $png1 = File::image('foto1.png', 600, 400);
        $png2 = File::image('foto2.png', 600, 400);
        $png3 = File::image('foto3.png', 600, 400);

        Livewire::actingAs($admin)
            ->test(CreateRoom::class)
            ->fillForm([
                'name' => 'Galeri WebP Test',
                'slug' => 'galeri-webp-test',
                'description' => 'Multi-upload WebP testi.',
                'capacity' => 2,
                'base_price' => 1500,
                'sort_order' => 99,
                'gallery' => [$png1, $png2, $png3],
                'is_active' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $room = Room::where('slug', 'galeri-webp-test')->firstOrFail();

        $this->assertCount(3, $room->gallery);
        foreach ($room->gallery as $path) {
            $this->assertStringStartsWith('rooms/gallery/', $path);
            $this->assertStringEndsWith('.webp', $path);
            Storage::disk('public')->assertExists($path);
        }
    }

    public function test_admin_galeri_image_yuklerse_webp_olur(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $jpg = File::image('lobby.jpg', 1200, 800);

        Livewire::actingAs($admin)
            ->test(ManageGalleryImages::class)
            ->callAction('create', [
                'category' => 'lobby',
                'path' => $jpg,
                'alt_text' => 'Otel lobi alanı',
                'sort_order' => 1,
            ])
            ->assertHasNoActionErrors();

        $image = GalleryImage::where('alt_text', 'Otel lobi alanı')->firstOrFail();

        $this->assertStringStartsWith('gallery/', $image->path);
        $this->assertStringEndsWith('.webp', $image->path);
        Storage::disk('public')->assertExists($image->path);
    }
}
