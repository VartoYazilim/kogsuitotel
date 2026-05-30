<?php

namespace Tests\Feature;

use App\Filament\Resources\LegalPages\LegalPageResource;
use App\Models\LegalPage;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LegalPageAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_kvkk_db_de_varsa_db_iceriginden_render_eder(): void
    {
        LegalPage::create([
            'slug' => 'kvkk',
            'title' => 'KVKK Test Başlığı',
            'content_html' => '<p>Bu DB üzerinden gelen test içeriği.</p>',
            'last_reviewed_at' => '2026-05-25',
        ]);

        $response = $this->get('/kvkk');

        $response->assertStatus(200);
        $response->assertSeeText('KVKK Test Başlığı');
        $response->assertSeeText('Bu DB üzerinden gelen test içeriği.');
    }

    public function test_kvkk_db_bos_ise_fallback_blade_kullanir(): void
    {
        // DB'de hiç legal_page yok → fallback pages.kvkk blade kullanılır
        $response = $this->get('/kvkk');

        $response->assertStatus(200);
        $response->assertSeeText('KVKK Aydınlatma');
    }

    public function test_legal_page_placeholders_settings_den_doldurur(): void
    {
        Setting::set('phone', '+90 555 999 88 77');
        Setting::set('email', 'test@kog.com');

        LegalPage::create([
            'slug' => 'kvkk',
            'title' => 'KVKK Placeholder Test',
            'content_html' => '<p>Telefon: {{ phone }}, E-posta: {{ email }}</p>',
            'last_reviewed_at' => '2026-05-25',
        ]);

        $response = $this->get('/kvkk');

        $response->assertStatus(200);
        $response->assertSeeText('+90 555 999 88 77');
        $response->assertSeeText('test@kog.com');
        $response->assertDontSeeText('{{ phone }}');
    }

    public function test_gizlilik_ve_cerez_pathleri_de_db_den_okur(): void
    {
        LegalPage::create([
            'slug' => 'privacy',
            'title' => 'Gizlilik Test',
            'content_html' => '<p>Gizlilik test DB içerik.</p>',
        ]);
        LegalPage::create([
            'slug' => 'cookie-policy',
            'title' => 'Çerez Test',
            'content_html' => '<p>Çerez test DB içerik.</p>',
        ]);

        $this->get('/gizlilik')->assertStatus(200)->assertSeeText('Gizlilik test DB içerik.');
        $this->get('/cerez-politikasi')->assertStatus(200)->assertSeeText('Çerez test DB içerik.');
    }

    public function test_admin_legal_pages_listeleyebilir(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->get('/kog-yonetim/legal-pages')
            ->assertStatus(200);
    }

    public function test_non_admin_legal_pages_a_giremez(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)
            ->get('/kog-yonetim/legal-pages')
            ->assertStatus(403);
    }

    public function test_admin_legal_page_silemez(): void
    {
        // canDelete() false döner — UI'da delete action gözükmez,
        // direct policy check ile de korunur (Resource konfigürasyonu)
        $this->assertFalse(LegalPageResource::canDelete(
            LegalPage::create(['slug' => 'kvkk', 'title' => 'T', 'content_html' => 'X'])
        ));
    }

    public function test_admin_yeni_legal_page_olusturamaz(): void
    {
        // canCreate() false döner — slug'lar sabit (kvkk, privacy, cookie-policy)
        $this->assertFalse(LegalPageResource::canCreate());
    }
}
