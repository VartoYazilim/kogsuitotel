<?php

namespace Tests\Unit;

use App\Services\ImageWebpConverter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\TestCase;

class ImageWebpConverterTest extends TestCase
{
    private ImageWebpConverter $converter;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->converter = new ImageWebpConverter(quality: 82, disk: 'public');
    }

    public function test_jpg_dosyasi_webp_format_olarak_kaydedilir(): void
    {
        $file = $this->fakeJpeg();

        $path = $this->converter->convert($file, 'rooms/covers');

        $this->assertStringStartsWith('rooms/covers/', $path);
        $this->assertStringEndsWith('.webp', $path);
        Storage::disk('public')->assertExists($path);

        $contents = Storage::disk('public')->get($path);
        $this->assertStringStartsWith('RIFF', substr($contents, 0, 4));
        $this->assertSame('WEBP', substr($contents, 8, 4));
    }

    public function test_png_dosyasi_webp_format_olarak_kaydedilir_alpha_preserve(): void
    {
        $file = $this->fakePngWithAlpha();

        $path = $this->converter->convert($file, 'rooms/gallery');

        $this->assertStringEndsWith('.webp', $path);
        Storage::disk('public')->assertExists($path);

        // WebP dosyası alpha preserve etti mi — imagecreatefromwebp + imageistruecolor ile spot check
        $fullPath = Storage::disk('public')->path($path);
        $im = imagecreatefromwebp($fullPath);
        $this->assertNotFalse($im);
        $this->assertTrue(imageistruecolor($im));
        imagedestroy($im);
    }

    public function test_webp_dosyasi_oldugu_gibi_saklanir(): void
    {
        $file = $this->fakeWebp();
        $originalContent = file_get_contents($file->getRealPath());

        $path = $this->converter->convert($file, 'gallery');

        $this->assertStringEndsWith('.webp', $path);
        Storage::disk('public')->assertExists($path);

        $this->assertSame($originalContent, Storage::disk('public')->get($path));
    }

    public function test_gif_dosyasi_oldugu_gibi_saklanir_format_korunur(): void
    {
        $file = $this->fakeGif();

        $path = $this->converter->convert($file, 'gallery');

        $this->assertStringEndsWith('.gif', $path);
        Storage::disk('public')->assertExists($path);
    }

    public function test_dosya_adi_random_orijinal_isim_sizdirilmaz(): void
    {
        $file = $this->fakeJpeg('gizli-musteri-belgesi.jpg');

        $path = $this->converter->convert($file, 'rooms/covers');

        $this->assertStringNotContainsString('gizli-musteri-belgesi', $path);
    }

    public function test_desteklenmeyen_mime_exception_firlatir(): void
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'test_txt_');
        file_put_contents($tempFile, 'just a text file');

        $file = new UploadedFile($tempFile, 'document.txt', 'text/plain', null, true);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Desteklenmeyen mime');

        $this->converter->convert($file, 'rooms/covers');
    }

    public function test_directory_leading_trailing_slash_temizlenir(): void
    {
        $file = $this->fakeJpeg();

        $path = $this->converter->convert($file, '/rooms/covers/');

        $this->assertStringStartsWith('rooms/covers/', $path);
        $this->assertStringNotContainsString('//', $path);
    }

    private function fakeJpeg(string $name = 'test.jpg'): UploadedFile
    {
        $temp = tempnam(sys_get_temp_dir(), 'test_jpg_');
        $im = imagecreatetruecolor(50, 50);
        imagefilledrectangle($im, 0, 0, 49, 49, imagecolorallocate($im, 0, 128, 0));
        imagejpeg($im, $temp, 90);
        imagedestroy($im);

        return new UploadedFile($temp, $name, 'image/jpeg', null, true);
    }

    private function fakePngWithAlpha(string $name = 'test.png'): UploadedFile
    {
        $temp = tempnam(sys_get_temp_dir(), 'test_png_');
        $im = imagecreatetruecolor(50, 50);
        imagealphablending($im, false);
        imagesavealpha($im, true);
        $transparent = imagecolorallocatealpha($im, 0, 0, 0, 127);
        imagefilledrectangle($im, 0, 0, 49, 49, $transparent);
        imagepng($im, $temp);
        imagedestroy($im);

        return new UploadedFile($temp, $name, 'image/png', null, true);
    }

    private function fakeWebp(string $name = 'test.webp'): UploadedFile
    {
        $temp = tempnam(sys_get_temp_dir(), 'test_webp_');
        $im = imagecreatetruecolor(50, 50);
        imagefilledrectangle($im, 0, 0, 49, 49, imagecolorallocate($im, 200, 100, 50));
        imagewebp($im, $temp, 82);
        imagedestroy($im);

        return new UploadedFile($temp, $name, 'image/webp', null, true);
    }

    private function fakeGif(string $name = 'test.gif'): UploadedFile
    {
        $temp = tempnam(sys_get_temp_dir(), 'test_gif_');
        $im = imagecreatetruecolor(50, 50);
        imagefilledrectangle($im, 0, 0, 49, 49, imagecolorallocate($im, 50, 100, 200));
        imagegif($im, $temp);
        imagedestroy($im);

        return new UploadedFile($temp, $name, 'image/gif', null, true);
    }
}
