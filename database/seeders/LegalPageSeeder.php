<?php

namespace Database\Seeders;

use App\Models\LegalPage;
use Illuminate\Database\Seeder;

/**
 * KVKK / Gizlilik / Çerez Politikası — mevcut blade dosyalarının içeriği
 * regex ile çıkarılıp DB'ye taşınır. firstOrCreate: sahip canlıda düzenlediyse
 * re-seed override etmez (hukuki metin koruma).
 */
class LegalPageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            'kvkk' => 'KVKK Aydınlatma Metni',
            'privacy' => 'Gizlilik Politikası',
            'cookie-policy' => 'Çerez Politikası',
        ];

        foreach ($pages as $slug => $title) {
            $bladePath = resource_path("views/pages/{$slug}.blade.php");

            if (! file_exists($bladePath)) {
                $this->command->warn("Atlanan: {$slug}.blade.php bulunamadı");

                continue;
            }

            $content = $this->extractContent(file_get_contents($bladePath));

            LegalPage::firstOrCreate(
                ['slug' => $slug],
                [
                    'title' => $title,
                    'content_html' => $content,
                    'last_reviewed_at' => '2026-05-24', // mevcut blade'deki "Son güncelleme" tarihi
                ],
            );
        }

        $this->command->info(count($pages).' hukuki sayfa hazırlandı (blade\'den içerik aktarıldı).');
    }

    /**
     * Blade dosyasının @section('content') ... @endsection bloğunu çıkarır,
     * Blade syntax'ı temizler ve düz HTML döner. {{ Setting::get('X') }}
     * çağrıları placeholder'a ({{ X }}) çevrilir — public view sonra enjekte eder.
     */
    private function extractContent(string $blade): string
    {
        // @section('content') ... @endsection arası
        if (! preg_match('/@section\(\'content\'\)(.*?)@endsection/s', $blade, $match)) {
            return '';
        }

        $html = trim($match[1]);

        // Setting::get('phone') → {{ phone }} placeholder
        $html = preg_replace(
            '/\{\{\s*\\\\?App\\\\Models\\\\Setting::get\([\'"]([a-z_]+)[\'"](?:,\s*[\'"][^\'"]*[\'"])?\)\s*\}\}/',
            '{{ $1 }}',
            $html,
        );

        // Carbon date inline → tarih sabit (sahibe last_reviewed_at field var, oradan render edilir)
        $html = preg_replace(
            '/\{\{\s*\\\\?Carbon\\\\Carbon::create\([^)]+\)->translatedFormat\([\'"][^\'"]+[\'"]\)\s*\}\}/',
            '{{ last_reviewed_at }}',
            $html,
        );

        return $html;
    }
}
