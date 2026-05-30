<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Banka / Havale (sahibinden gerçek değer alınana kadar placeholder)
            'iban' => 'TR00 0000 0000 0000 0000 0000 00',
            'iban_holder' => 'Koğ Suit Otel',
            'bank_name' => '—',

            // İletişim
            'phone' => '+90 555 123 45 67',
            'whatsapp' => '+90 555 123 45 67',
            'email' => 'info@kogsuitotel.com',
            'address' => 'Varto, Muş',

            // Konaklama saatleri
            'checkin_time' => '14:00',
            'checkout_time' => '12:00',

            // Sosyal medya artık ayrı SocialLink tablosunda (dinamik CRUD).
            // Eski 4 key (instagram_url, facebook_url, google_maps_url,
            // tripadvisor_url) SettingSeeder'dan çıkarıldı; mevcut canlı
            // değerler 2026_05_25_150001 migration ile social_links'e taşınır.

            // Hakkımızda sayfası içerikleri (admin'den editlenebilir)
            'about_intro' => "Koğ Suit Otel, Muş Varto'da 5 odalı küçük bir butik otel. Misafirlerimize evlerindeymiş gibi hissedecekleri rahat bir konaklama sunmak için kuruldu.\n\nOdalarımız sade ve konforlu; doğal tonlar, kaliteli yatak ve ferah alanlarla dinlenmenize odaklandık. Karmaşık değil, samimi bir otel deneyimi.",
            'about_vision' => "Varto'ya gelen her misafirin rahat etmesi. Karmaşık servis değil, küçük dokunuşlar — temiz bir oda, sıcak bir karşılama ve sorularınızı yanıtlayan bir ekip. Bir dostun evine gelmiş gibi hissedebilmeniz için elimizden geleni yapıyoruz.",
            'about_varto_region' => "Muş iline bağlı bir ilçe olan Varto, Doğu Anadolu'nun yüksek rakımlı yaylaları ve berrak hava kalitesiyle tanınır. Akdoğan Dağları'nın eteklerinde, Şerafettin Dağları'na komşu konumuyla doğa tutkunları ve sakin bir kaçış arayanlar için ideal bir varış noktasıdır.\n\nBölgenin temiz yayla havası, geleneksel Anadolu mutfağı ve sıcakkanlı insanlarıyla Varto, küçük ama unutulmaz bir keşif deneyimi sunar. Koğ Suit Otel olarak hem iş seyahatleri hem hafta sonu kaçamakları hem de yayla turları için merkezi ve sakin bir konaklama noktasıyız.\n\nVarto, Muş merkezine yaklaşık 60 km mesafededir. Bingöl, Erzurum ve Elazığ gibi çevre illere karayolu ile ulaşım kolaydır. Otelimizden bölgenin en güzel manzaralarına ve geleneksel köy yaşamına kısa bir sürede ulaşabilirsiniz.",
            'about_stat_1_value' => '5',
            'about_stat_1_label' => 'Konforlu Oda',
            'about_stat_2_value' => '24/7',
            'about_stat_2_label' => 'WhatsApp Destek',
            'about_stat_3_value' => '100%',
            'about_stat_3_label' => 'Misafir Memnuniyeti Odağı',
        ];

        // firstOrCreate — sahibin canlıda güncellediği değerleri korur (re-seed override etmez).
        // Yeni key'ler eklendiğinde sadece eksik olanlar oluşur, mevcutlara dokunulmaz.
        foreach ($settings as $key => $value) {
            Setting::firstOrCreate(
                ['key' => $key],
                ['value' => $value],
            );
        }

        $this->command->info(count($settings).' setting hazırlandı.');
    }
}
