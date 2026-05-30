<?php

namespace Tests\Feature;

use App\Models\SocialLink;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Dinamik SocialLink CRUD — public footer + contact'ta görünür.
 * Boş ve pasif kayıtlar render edilmez (defensive).
 */
class SocialIconsTest extends TestCase
{
    use RefreshDatabase;

    public function test_footer_aktif_social_link_render_eder(): void
    {
        SocialLink::create([
            'platform' => 'instagram',
            'label' => 'Instagram',
            'url' => 'https://instagram.com/test-kog',
            'sort_order' => 10,
            'is_active' => true,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('https://instagram.com/test-kog', false);
        $response->assertSee('aria-label="Instagram"', false);
    }

    public function test_footer_hic_link_yoksa_section_render_etmez(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertDontSee('aria-label="Instagram"', false);
        $response->assertDontSee('aria-label="Facebook"', false);
    }

    public function test_pasif_link_public_sayfada_gizlenir(): void
    {
        SocialLink::create([
            'platform' => 'facebook',
            'label' => 'Facebook',
            'url' => 'https://facebook.com/pasif',
            'sort_order' => 10,
            'is_active' => false,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertDontSee('https://facebook.com/pasif', false);
    }

    public function test_contact_sayfasinda_sosyal_medya_section_gosterir(): void
    {
        SocialLink::create([
            'platform' => 'facebook',
            'label' => 'Facebook',
            'url' => 'https://facebook.com/test-kog',
            'sort_order' => 10,
            'is_active' => true,
        ]);

        $response = $this->get(route('contact'));

        $response->assertStatus(200);
        $response->assertSee('Sosyal Medya');
        $response->assertSee('https://facebook.com/test-kog', false);
    }

    public function test_birden_fazla_platform_dolu_ise_hepsi_render_eder(): void
    {
        SocialLink::create(['platform' => 'instagram', 'label' => 'IG', 'url' => 'https://instagram.com/a', 'sort_order' => 10, 'is_active' => true]);
        SocialLink::create(['platform' => 'facebook', 'label' => 'FB', 'url' => 'https://facebook.com/b', 'sort_order' => 20, 'is_active' => true]);
        SocialLink::create(['platform' => 'tripadvisor', 'label' => 'TA', 'url' => 'https://tripadvisor.com/c', 'sort_order' => 30, 'is_active' => true]);
        SocialLink::create(['platform' => 'x', 'label' => 'X', 'url' => 'https://x.com/d', 'sort_order' => 40, 'is_active' => true]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('aria-label="IG"', false);
        $response->assertSee('aria-label="FB"', false);
        $response->assertSee('aria-label="TA"', false);
        $response->assertSee('aria-label="X"', false);
    }

    public function test_sort_order_a_gore_listeler(): void
    {
        SocialLink::create(['platform' => 'facebook', 'label' => 'Facebook', 'url' => 'https://facebook.com/b', 'sort_order' => 30, 'is_active' => true]);
        SocialLink::create(['platform' => 'instagram', 'label' => 'Instagram', 'url' => 'https://instagram.com/a', 'sort_order' => 10, 'is_active' => true]);
        SocialLink::create(['platform' => 'x', 'label' => 'X', 'url' => 'https://x.com/c', 'sort_order' => 20, 'is_active' => true]);

        $response = $this->get('/');
        $content = $response->getContent();

        $posIG = strpos($content, 'instagram.com/a');
        $posX = strpos($content, 'x.com/c');
        $posFB = strpos($content, 'facebook.com/b');

        $this->assertNotFalse($posIG);
        $this->assertLessThan($posX, $posIG);
        $this->assertLessThan($posFB, $posX);
    }

    public function test_sameas_schema_org_social_link_urllerini_ekler(): void
    {
        SocialLink::create([
            'platform' => 'instagram',
            'label' => 'Instagram',
            'url' => 'https://instagram.com/schema-test-xyz',
            'sort_order' => 10,
            'is_active' => true,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $content = $response->getContent();
        $this->assertStringContainsString('"sameAs"', $content);
        $this->assertStringContainsString('schema-test-xyz', $content);
    }

    public function test_admin_social_links_listeleyebilir(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->get('/kog-yonetim/social-links')
            ->assertStatus(200);
    }

    public function test_non_admin_social_links_a_giremez(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)
            ->get('/kog-yonetim/social-links')
            ->assertStatus(403);
    }
}
