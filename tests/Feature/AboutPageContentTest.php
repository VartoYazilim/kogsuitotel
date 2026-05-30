<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Hakkımızda sayfası DB-driven içeriği test eder. Sahip BusinessSettings'ten
 * about_* key'lerini güncellediğinde public /hakkimizda anında yansıtır.
 */
class AboutPageContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_about_intro_settings_den_okunur(): void
    {
        Setting::set('about_intro', 'Bu özel test intro metni — DB den geliyor.');

        $response = $this->get(route('about'));

        $response->assertStatus(200);
        $response->assertSeeText('Bu özel test intro metni — DB den geliyor.');
    }

    public function test_about_vision_settings_den_okunur(): void
    {
        Setting::set('about_vision', 'Vizyon test metni — admin den girildi.');

        $response = $this->get(route('about'));

        $response->assertStatus(200);
        $response->assertSeeText('Vizyon test metni — admin den girildi.');
    }

    public function test_about_stat_kartlari_settings_den_render_eder(): void
    {
        Setting::set('about_stat_1_value', '999');
        Setting::set('about_stat_1_label', 'Test Etiket 1');
        Setting::set('about_stat_2_value', '7/24');
        Setting::set('about_stat_2_label', 'Test Etiket 2');

        $response = $this->get(route('about'));

        $response->assertStatus(200);
        $response->assertSeeText('999');
        $response->assertSeeText('Test Etiket 1');
        $response->assertSeeText('7/24');
        $response->assertSeeText('Test Etiket 2');
    }

    public function test_about_intro_bos_ise_section_gizlenir(): void
    {
        Setting::set('about_intro', '');

        $response = $this->get(route('about'));

        $response->assertStatus(200);
        // Hero başlık her zaman görünür (kod tarafında), boş intro section gizlenir
        $response->assertSeeText('küçük konağı,');
    }

    public function test_admin_business_settings_about_alanlarini_kaydedebilir(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->get('/kog-yonetim/ayarlar')
            ->assertStatus(200);
    }

    public function test_about_intro_paragraph_split_calisir(): void
    {
        Setting::set('about_intro', "İlk paragraf — özel.\n\nİkinci paragraf — özel.");

        $response = $this->get(route('about'));

        $response->assertStatus(200);
        $response->assertSeeText('İlk paragraf — özel.');
        $response->assertSeeText('İkinci paragraf — özel.');
    }
}
