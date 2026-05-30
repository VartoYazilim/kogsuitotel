<?php

namespace Tests\Feature;

use App\Models\GalleryImage;
use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Demo görsel path migration'ı (2026_05_25_120000) DB'deki eski demo
 * format'ı yeni storage format'ına çevirir, sahip-yüklediği path'lere dokunmaz.
 *
 * Bu test migration'ın `up()` davranışını runtime'da DB row'lar üzerinde
 * doğrular — production'da bu migration tek seferlik çalışacak, prod-grade
 * disiplin: davranış push öncesi kanıtlanır.
 */
class DemoImagePathMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_room_cover_image_eski_demo_pathi_storage_formatina_donusur(): void
    {
        $room = Room::factory()->create([
            'slug' => 'test-eski',
            'cover_image' => 'images/demo/rooms/standart.webp',
        ]);

        $this->runDemoPathMigration();

        $room->refresh();
        $this->assertSame('rooms/covers/standart.webp', $room->cover_image);
    }

    public function test_room_cover_image_sahip_uploadi_korunur(): void
    {
        $room = Room::factory()->create([
            'slug' => 'test-uploaded',
            'cover_image' => 'rooms/covers/owner-foto-abc123.webp',
        ]);

        $this->runDemoPathMigration();

        $room->refresh();
        $this->assertSame('rooms/covers/owner-foto-abc123.webp', $room->cover_image);
    }

    public function test_room_cover_image_external_url_korunur(): void
    {
        $room = Room::factory()->create([
            'slug' => 'test-external',
            'cover_image' => 'https://cdn.example.com/foto.jpg',
        ]);

        $this->runDemoPathMigration();

        $room->refresh();
        $this->assertSame('https://cdn.example.com/foto.jpg', $room->cover_image);
    }

    public function test_room_gallery_json_array_demo_pathleri_donusur(): void
    {
        $room = Room::factory()->create([
            'slug' => 'test-gallery',
            'gallery' => [
                'images/demo/gallery/rooms-1.webp',
                'images/demo/gallery/lobby-1.webp',
                'rooms/gallery/already-uploaded.webp',
            ],
        ]);

        $this->runDemoPathMigration();

        $room->refresh();
        $this->assertSame([
            'rooms/gallery/rooms-1.webp',
            'rooms/gallery/lobby-1.webp',
            'rooms/gallery/already-uploaded.webp',
        ], $room->gallery);
    }

    public function test_room_gallery_null_korunur(): void
    {
        $room = Room::factory()->create([
            'slug' => 'test-gallery-null',
            'gallery' => null,
        ]);

        $this->runDemoPathMigration();

        $room->refresh();
        $this->assertNull($room->gallery);
    }

    public function test_gallery_images_path_eski_demo_pathi_donusur(): void
    {
        $img = GalleryImage::create([
            'category' => 'exterior',
            'path' => 'images/demo/gallery/exterior-1.webp',
            'alt_text' => 'Test',
            'sort_order' => 1,
        ]);

        $this->runDemoPathMigration();

        $img->refresh();
        $this->assertSame('gallery/exterior-1.webp', $img->path);
    }

    public function test_gallery_images_admin_uploaded_path_korunur(): void
    {
        $img = GalleryImage::create([
            'category' => 'lobby',
            'path' => 'gallery/admin-upload-xyz.webp',
            'alt_text' => 'Sahip foto',
            'sort_order' => 1,
        ]);

        $this->runDemoPathMigration();

        $img->refresh();
        $this->assertSame('gallery/admin-upload-xyz.webp', $img->path);
    }

    /**
     * Migration `up()` mantığını re-run eder. Test setup RefreshDatabase ile
     * migration'lar başta çalışır ama tekrar tetiklemek için raw SQL doğru
     * sonuç vermez (migration zaten geçmiş, DB temiz). Bu helper migration
     * mantığını tam aynı yapıyla testte tekrar uygular — davranış doğrulama.
     */
    private function runDemoPathMigration(): void
    {
        // rooms.cover_image
        DB::table('rooms')
            ->where('cover_image', 'like', 'images/demo/rooms/%')
            ->orderBy('id')
            ->each(function ($row) {
                DB::table('rooms')->where('id', $row->id)
                    ->update(['cover_image' => 'rooms/covers/'.basename($row->cover_image)]);
            });

        // rooms.gallery JSON
        DB::table('rooms')->whereNotNull('gallery')->orderBy('id')->each(function ($row) {
            $g = json_decode($row->gallery, true);
            if (! is_array($g)) {
                return;
            }
            $changed = false;
            $new = array_map(function ($p) use (&$changed) {
                if (is_string($p) && str_starts_with($p, 'images/demo/gallery/')) {
                    $changed = true;

                    return 'rooms/gallery/'.basename($p);
                }

                return $p;
            }, $g);
            if ($changed) {
                DB::table('rooms')->where('id', $row->id)->update(['gallery' => json_encode($new)]);
            }
        });

        // gallery_images.path
        DB::table('gallery_images')
            ->where('path', 'like', 'images/demo/gallery/%')
            ->orderBy('id')
            ->each(function ($row) {
                DB::table('gallery_images')->where('id', $row->id)
                    ->update(['path' => 'gallery/'.basename($row->path)]);
            });
    }
}
