<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Demo görsel path'lerini eski public-direct formatından storage disk formatına geçirir.
 *
 * Eski: `images/demo/rooms/X.webp`     (public/images/demo/rooms/X.webp altında)
 * Yeni: `rooms/covers/X.webp`          (storage/app/public/rooms/covers/X.webp altında)
 *
 * Filament FileUpload `disk('public')` ile yeni format'ı doğru preview eder;
 * polymorphic accessor `Room::resolvePublicUrl` her iki pattern'i de handle eder
 * (backward compat). Sahip-yüklediği path'lere dokunulmaz.
 *
 * Etkilenen tablolar:
 * - rooms.cover_image (string)
 * - rooms.gallery (JSON array)
 * - gallery_images.path (string)
 */
return new class extends Migration
{
    public function up(): void
    {
        $this->migrateRoomCovers();
        $this->migrateRoomGalleries();
        $this->migrateGalleryImages();
    }

    public function down(): void
    {
        // No-op: sahip canlıda gerçek foto upload etmiş olabilir, ters çevirmek
        // demo asset'lere geri döner ve veri kaybına yol açar. Migration tek-yön.
    }

    /** rooms.cover_image: `images/demo/rooms/X.webp` → `rooms/covers/X.webp` */
    private function migrateRoomCovers(): void
    {
        DB::table('rooms')
            ->where('cover_image', 'like', 'images/demo/rooms/%')
            ->orderBy('id')
            ->each(function ($row) {
                $newPath = 'rooms/covers/'.basename($row->cover_image);
                DB::table('rooms')
                    ->where('id', $row->id)
                    ->update(['cover_image' => $newPath]);
            });
    }

    /** rooms.gallery (JSON array): her item `images/demo/gallery/X.webp` → `rooms/gallery/X.webp` */
    private function migrateRoomGalleries(): void
    {
        DB::table('rooms')
            ->whereNotNull('gallery')
            ->orderBy('id')
            ->each(function ($row) {
                $gallery = json_decode($row->gallery, true);

                if (! is_array($gallery)) {
                    return;
                }

                $changed = false;
                $new = array_map(function ($path) use (&$changed) {
                    if (is_string($path) && str_starts_with($path, 'images/demo/gallery/')) {
                        $changed = true;

                        return 'rooms/gallery/'.basename($path);
                    }

                    return $path;
                }, $gallery);

                if ($changed) {
                    DB::table('rooms')
                        ->where('id', $row->id)
                        ->update(['gallery' => json_encode($new)]);
                }
            });
    }

    /** gallery_images.path: `images/demo/gallery/X.webp` → `gallery/X.webp` */
    private function migrateGalleryImages(): void
    {
        DB::table('gallery_images')
            ->where('path', 'like', 'images/demo/gallery/%')
            ->orderBy('id')
            ->each(function ($row) {
                $newPath = 'gallery/'.basename($row->path);
                DB::table('gallery_images')
                    ->where('id', $row->id)
                    ->update(['path' => $newPath]);
            });
    }
};
