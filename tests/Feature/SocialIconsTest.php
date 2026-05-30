<?php

namespace Tests\Feature;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * BusinessSettings'teki sosyal medya URL'leri public footer + contact'ta görünür.
 * Boş olanlar render edilmez (defensive).
 */
class SocialIconsTest extends TestCase
{
    use RefreshDatabase;

    public function test_footer_instagram_url_dolu_ise_link_render_eder(): void
    {
        Setting::set('instagram_url', 'https://instagram.com/test-kog');

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('https://instagram.com/test-kog', false);
        $response->assertSee('aria-label="Instagram"', false);
    }

    public function test_footer_bos_url_link_render_etmez(): void
    {
        Setting::set('instagram_url', '');
        Setting::set('facebook_url', '');
        Setting::set('tripadvisor_url', '');
        Setting::set('google_maps_url', '');

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertDontSee('aria-label="Instagram"', false);
        $response->assertDontSee('aria-label="Facebook"', false);
    }

    public function test_contact_sayfasinda_sosyal_medya_section_gosterir(): void
    {
        Setting::set('facebook_url', 'https://facebook.com/test-kog');

        $response = $this->get(route('contact'));

        $response->assertStatus(200);
        $response->assertSee('Sosyal Medya');
        $response->assertSee('https://facebook.com/test-kog', false);
    }

    public function test_birden_fazla_sosyal_medya_dolu_ise_hepsi_render_eder(): void
    {
        Setting::set('instagram_url', 'https://instagram.com/a');
        Setting::set('facebook_url', 'https://facebook.com/b');
        Setting::set('tripadvisor_url', 'https://tripadvisor.com/c');
        Setting::set('google_maps_url', 'https://maps.google.com/d');

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('aria-label="Instagram"', false);
        $response->assertSee('aria-label="Facebook"', false);
        $response->assertSee('aria-label="Tripadvisor"', false);
        $response->assertSee('aria-label="Google Maps"', false);
    }

    public function test_sameas_schema_org_sosyal_medya_urllerini_ekler(): void
    {
        Setting::set('instagram_url', 'https://instagram.com/schema-test-xyz');

        $response = $this->get('/');

        $response->assertStatus(200);
        $content = $response->getContent();
        // JSON-LD UNESCAPED_SLASHES flag ile encode (slashes literal kalır)
        $this->assertStringContainsString('"sameAs"', $content);
        $this->assertStringContainsString('schema-test-xyz', $content);
    }
}
