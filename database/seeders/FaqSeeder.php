<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

/**
 * Default SSS sorular — mevcut /sss sayfasından alındı. Sahibin canlıda
 * eklediği/düzenlediği sorular firstOrCreate ile korunur, re-seed override etmez.
 */
class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            ['category' => 'Konaklama', 'question' => 'Otele giriş ve çıkış saatleri nedir?',
                'answer' => 'Giriş saati 14:00\'ten itibaren, çıkış ise saat 12:00\'a kadardır. Erken giriş veya geç çıkış talepleriniz için rezervasyon sırasında "Özel İstekler" bölümünden bizimle iletişime geçebilirsiniz.'],
            ['category' => 'Ödeme', 'question' => 'Ödeme nasıl yapılır?',
                'answer' => 'Rezervasyon onayı sonrası tarafınıza iletilen IBAN\'a havale/EFT ile ödeme yapabilirsiniz. Dekontunuzu WhatsApp üzerinden bize ulaştırdığınızda rezervasyonunuz kesinleştirilir. Online kart ödemesi şu an için kabul edilmemektedir.'],
            ['category' => 'Rezervasyon', 'question' => 'Rezervasyonumu iptal edebilir miyim?',
                'answer' => 'Giriş tarihinden 7 gün öncesine kadar yapılan iptaller için ödenen tutar iade edilir. Bu süreden sonraki iptallerde iade yapılamamaktadır. Detaylar için bizimle iletişime geçin.'],
            ['category' => 'Hizmetler', 'question' => 'Otelde evcil hayvan kabul ediyor musunuz?',
                'answer' => 'Evet, küçük ırk evcil hayvanlar belirli odalarımızda kabul edilmektedir. Rezervasyon öncesi mutlaka bizimle iletişime geçerek uygunluğu teyit etmeniz gerekir.'],
            ['category' => 'Hizmetler', 'question' => 'Otoparkınız var mı?',
                'answer' => 'Evet, otelimizin yanında misafirlerimize özel ücretsiz ve kameralı bir otopark alanımız bulunmaktadır.'],
            ['category' => 'Hizmetler', 'question' => 'Kahvaltı fiyata dahil mi?',
                'answer' => 'Evet, açık büfe yöresel kahvaltımız tüm konaklamalarda ücretsiz olarak sunulmaktadır. Saat 08:00 - 10:30 arası açıktır.'],
            ['category' => 'Hizmetler', 'question' => 'Wi-Fi sağlıyor musunuz?',
                'answer' => 'Tüm odalarımızda ve ortak alanlarda yüksek hızlı ücretsiz Wi-Fi mevcuttur.'],
            ['category' => 'Konaklama', 'question' => 'Çocuklar için ek yatak imkanı var mı?',
                'answer' => 'Aile Odası ve Premium Süit\'te ek yatak imkanı bulunmaktadır. Diğer odalar için lütfen bizimle iletişime geçiniz.'],
        ];

        foreach ($faqs as $i => $faq) {
            Faq::firstOrCreate(
                ['question' => $faq['question']],
                array_merge($faq, ['sort_order' => ($i + 1) * 10, 'is_active' => true]),
            );
        }

        $this->command->info(count($faqs).' SSS sorusu hazırlandı.');
    }
}
