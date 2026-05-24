<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GoogleAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_ga4_id_set_oldugunda_gtag_script_layoutta_render_edilir(): void
    {
        config(['seo.google_analytics_id' => 'G-TESTABC123']);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('googletagmanager.com/gtag/js?id=G-TESTABC123', false);
        $response->assertSee("gtag('config', 'G-TESTABC123'", false);
    }

    public function test_ga4_consent_mode_v2_default_deny_dogru_render(): void
    {
        // KVKK m.5 — kullanıcı kabul edene kadar HİÇBİR depolama izinli olmamalı
        config(['seo.google_analytics_id' => 'G-TESTABC123']);

        $response = $this->get(route('home'));

        $response->assertSee("gtag('consent', 'default'", false);
        $response->assertSee("'ad_storage': 'denied'", false);
        $response->assertSee("'ad_user_data': 'denied'", false);
        $response->assertSee("'ad_personalization': 'denied'", false);
        $response->assertSee("'analytics_storage': 'denied'", false);
        $response->assertSee("'anonymize_ip': true", false);
    }

    public function test_ga4_id_bos_oldugunda_gtag_script_render_edilmez(): void
    {
        config(['seo.google_analytics_id' => null]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertDontSee('googletagmanager.com/gtag/js', false);
        $response->assertDontSee('gtag(', false);
    }

    public function test_cookie_banner_ga4_set_oldugunda_render_edilir(): void
    {
        config(['seo.google_analytics_id' => 'G-TESTABC123']);

        $response = $this->get(route('home'));

        $response->assertSee('id="kog-cookie-banner"', false);
        $response->assertSee('data-cookie-action="accept"', false);
        $response->assertSee('data-cookie-action="reject"', false);
        $response->assertSeeText('Tümünü Kabul Et');
        $response->assertSeeText('Reddet');
        $response->assertSee(route('cookie-policy'), false); // banner'dan çerez politikası link
    }

    public function test_cookie_banner_ga4_bos_oldugunda_render_edilmez(): void
    {
        // GA4 yok → çerez yerleştirme yok → banner gereksiz
        config(['seo.google_analytics_id' => null]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertDontSee('id="kog-cookie-banner"', false);
    }

    public function test_kvkk_metni_google_llc_yurt_disi_aktarim_disclosure(): void
    {
        $response = $this->get(route('kvkk'));

        $response->assertOk();
        $response->assertSeeText('Yurt Dışı Aktarım');
        $response->assertSeeText('Google LLC');
        $response->assertSeeText('ABD');
        $response->assertSeeText('Google Analytics 4');
        $response->assertSeeText('anonymize_ip');
        $response->assertSeeText('açık rıza');
        $response->assertSeeText('Cloudflare');
    }
}
