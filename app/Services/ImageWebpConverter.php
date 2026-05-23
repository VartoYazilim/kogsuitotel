<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Yüklenen JPG/PNG'leri WebP'ye çevirir, public disk'e yazar, göreceli path döner.
 * WebP/GIF olduğu gibi saklanır (animasyon/saydamlık korunur, gereksiz transcode yok).
 *
 * Filament FileUpload `saveUploadedFileUsing()` callback'inde kullanılır.
 * Faz 2 demo görselleri PHP GD ile WebP'ye çevrilmişti (%41 tasarruf, LCP);
 * admin upload'larında da aynı disiplin: her upload sonrası tek format = .webp.
 */
class ImageWebpConverter
{
    public function __construct(
        private readonly int $quality = 82,
        private readonly string $disk = 'public',
    ) {}

    /**
     * Yüklenen dosyayı WebP'ye dönüştürür ve diske yazar.
     *
     * @param  UploadedFile  $file  Filament'in geçici upload'ı (TemporaryUploadedFile da bu sınıfı extend eder)
     * @param  string  $directory  Disk üzerinde göreceli klasör (örn. "rooms/covers")
     * @return string Disk üzerinde göreceli path (örn. "rooms/covers/abc.webp")
     */
    public function convert(UploadedFile $file, string $directory): string
    {
        $directory = trim($directory, '/');
        $extension = $this->detectExtension($file);
        $filename = Str::random(40);

        // WebP / GIF — convert etmeden olduğu gibi sakla
        if ($extension === 'webp' || $extension === 'gif') {
            return $file->storeAs($directory, "$filename.$extension", $this->disk);
        }

        // JPG / PNG → WebP
        $sourceImage = $this->loadImage($file, $extension);

        try {
            if ($extension === 'png') {
                imagepalettetotruecolor($sourceImage);
                imagealphablending($sourceImage, false);
                imagesavealpha($sourceImage, true);
            }

            $relativePath = "$directory/$filename.webp";
            $fullPath = Storage::disk($this->disk)->path($relativePath);
            $targetDir = dirname($fullPath);

            if (! is_dir($targetDir) && ! mkdir($targetDir, 0775, true) && ! is_dir($targetDir)) {
                throw new RuntimeException("Klasör oluşturulamadı: $targetDir");
            }

            if (! imagewebp($sourceImage, $fullPath, $this->quality)) {
                throw new RuntimeException('WebP yazılamadı: '.$file->getClientOriginalName());
            }
        } finally {
            imagedestroy($sourceImage);
        }

        return $relativePath;
    }

    /**
     * UploadedFile'dan extension belirler. Filament'in `image()` validation'ı zaten
     * mime kontrol eder, burada ek defansif kontrol.
     */
    private function detectExtension(UploadedFile $file): string
    {
        $mime = $file->getMimeType();

        return match ($mime) {
            'image/jpeg', 'image/jpg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
            default => throw new RuntimeException("Desteklenmeyen mime: $mime ({$file->getClientOriginalName()})"),
        };
    }

    /**
     * @return \GdImage
     */
    private function loadImage(UploadedFile $file, string $extension)
    {
        $path = $file->getRealPath();

        $image = match ($extension) {
            'jpg' => @imagecreatefromjpeg($path),
            'png' => @imagecreatefrompng($path),
            default => false,
        };

        if ($image === false) {
            throw new RuntimeException("Görsel okunamadı ($extension): ".$file->getClientOriginalName());
        }

        return $image;
    }
}
