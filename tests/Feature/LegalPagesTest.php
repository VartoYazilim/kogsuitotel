<?php

namespace Tests\Feature;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * KVKK/Gizlilik/Çerez sayfaları KVKK m.10 + ETBİS uygulaması gereği canlı olmalı.
 * Bu testler içerik full + linkler ve placeholder Setting değerleri doğru render.
 */
class LegalPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Setting'ler KVKK metninde ad/iletişim olarak render edilir
        Setting::set('email', 'kvkk@kogsuitotel.com');
        Setting::set('phone', '+90 555 123 45 67');
        Setting::set('address', 'Varto, Muş, Türkiye');
    }

    public function test_kvkk_sayfasi_yuklenir_ve_m10_zorunlu_bolumlerini_icerir(): void
    {
        $response = $this->get(route('kvkk'));

        $response->assertOk();
        $response->assertSeeText('KVKK Aydınlatma Metni');

        // KVKK m.10 zorunlu bölümler
        $response->assertSeeText('Veri Sorumlusu');
        $response->assertSeeText('İşlenen Kişisel Veri Kategorileri');
        $response->assertSeeText('İşlenme Amaçları');
        $response->assertSeeText('Aktarılması');
        $response->assertSeeText('Veri Toplama Yöntemi ve Hukuki Sebep');
        $response->assertSeeText('Saklama Süresi');
        $response->assertSeeText('Veri Sahibi Hakları');
        $response->assertSeeText('Başvuru Yolları');

        // Yasal referanslar
        $response->assertSeeText('6698');
        $response->assertSeeText('Kimlik Bildirme Kanunu');

        // KVKK m.11 hakları en az 5 madde
        $response->assertSeeText('işlenip işlenmediğini öğrenme');
        $response->assertSeeText('Silinmesini');

        // Setting'lerden iletişim bilgileri
        $response->assertSeeText('kvkk@kogsuitotel.com');
        $response->assertSeeText('Varto, Muş, Türkiye');
    }

    public function test_kvkk_sayfasi_diger_yasal_sayfalara_link_verir(): void
    {
        $response = $this->get(route('kvkk'));

        $response->assertOk();
        $response->assertSee(route('cookie-policy'), false);
    }

    public function test_gizlilik_sayfasi_yuklenir_ve_full_bolumlerini_icerir(): void
    {
        $response = $this->get(route('privacy'));

        $response->assertOk();
        $response->assertSeeText('Gizlilik Politikası');

        // Temel bölümler
        $response->assertSeeText('Hangi Veriler Toplanır');
        $response->assertSeeText('Nasıl Kullanılır');
        $response->assertSeeText('Ödeme Bilgileriniz');
        $response->assertSeeText('Saklanır');
        $response->assertSeeText('Çerezler');
        $response->assertSeeText('Haklarınız');

        // Online ödeme alınmadığı vurgusu (sahibin business model'i)
        $response->assertSeeText('online ödeme alınmaz');

        // KVKK + Çerez sayfalarına link
        $response->assertSee(route('kvkk'), false);
        $response->assertSee(route('cookie-policy'), false);
    }

    public function test_cerez_politikasi_sayfasi_yuklenir_ve_zorunlu_cerez_listesini_icerir(): void
    {
        $response = $this->get(route('cookie-policy'));

        $response->assertOk();
        $response->assertSeeText('Çerez Politikası');

        // Bölümler
        $response->assertSeeText('Çerez Nedir');
        $response->assertSeeText('Sitemiz Hangi Çerezleri Kullanıyor');
        $response->assertSeeText('Analitik');
        $response->assertSeeText('Üçüncü Taraf');
        $response->assertSeeText('Tercihlerini Nasıl Yönetirim');

        // Belirli çerez isimleri (transparent disclosure)
        $response->assertSeeText('kog-suit-otel-session');
        $response->assertSeeText('XSRF-TOKEN');
        $response->assertSeeText('cf_bm');

        // GA4 disclosure (KVKK m.5 açık rıza + m.9 yurt dışı aktarım — Google LLC ABD)
        $response->assertSeeText('Google Analytics 4');
        $response->assertSeeText('Consent Mode v2');
        $response->assertSeeText('Google LLC');
        $response->assertSeeText('anonymize_ip');

        // Reklam/profil çıkartma çerezi olmadığı vurgusu (Facebook Pixel vb.)
        $response->assertSeeText('Facebook Pixel');
        $response->assertSeeText('kullanılmamaktadır');

        // İlgili belgelere link
        $response->assertSee(route('kvkk'), false);
        $response->assertSee(route('privacy'), false);
    }

    public function test_cerez_politikasi_url_turkceleri_dogru_slug(): void
    {
        // SEO için Türkçe slug — "/cerez-politikasi" tutarlılık (host APP_URL'a bağlı, path sabit)
        $this->assertSame('/cerez-politikasi', route('cookie-policy', absolute: false));
    }

    public function test_uc_yasal_sayfa_da_footer_layoutu_kullanir(): void
    {
        // Layout'a bağlı olduklarını doğrula (footer'da diğer linkler görünür)
        foreach (['kvkk', 'privacy', 'cookie-policy'] as $routeName) {
            $response = $this->get(route($routeName));

            $response->assertOk();
            // Footer'daki tüm yasal linkler her sayfada görünmeli
            $response->assertSee('KVKK Aydınlatma', false);
            $response->assertSee('Gizlilik Politikası', false);
            $response->assertSee('Çerez Politikası', false);
        }
    }

    public function test_sitemap_xml_yeni_cerez_politikasi_url_unu_icerir(): void
    {
        $response = $this->get(route('sitemap'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');

        $content = $response->getContent();
        $this->assertStringContainsString('/cerez-politikasi', $content);
        $this->assertStringContainsString('/kvkk', $content);
        $this->assertStringContainsString('/gizlilik', $content);
    }

    public function test_yasal_sayfalar_setting_email_address_phone_dinamik_okur(): void
    {
        Setting::set('email', 'baska-email@kogsuit.test');
        Setting::set('phone', '+90 111 222 33 44');

        $response = $this->get(route('kvkk'));
        $response->assertSeeText('baska-email@kogsuit.test');
        $response->assertSeeText('+90 111 222 33 44');
    }
}
