@extends('layouts.public')

@section('title', 'Gizlilik Politikası')
@section('description', 'Koğ Suit Otel gizlilik politikası — kişisel verilerinizin nasıl toplandığı, saklandığı, korunduğu ve haklarınız.')

@section('content')

<section class="py-lg md:py-xl">
    <div class="max-w-[800px] mx-auto px-md">
        <p class="font-display text-xs tracking-[0.2em] uppercase text-accent-dark mb-sm">Yasal</p>
        <h1 class="font-display font-bold text-4xl md:text-5xl tracking-tight text-ink mb-md leading-tight">
            Gizlilik Politikası
        </h1>

        <p class="text-sm text-ink-mute mb-lg">
            Son güncelleme: {{ \Carbon\Carbon::create(2026, 5, 24)->translatedFormat('d F Y') }}
        </p>

        <div class="prose prose-ink max-w-none text-ink-soft leading-relaxed space-y-md">

            <p>
                Koğ Suit Otel olarak misafirlerimizin ve site ziyaretçilerimizin gizliliğine en
                üst düzeyde önem veriyoruz. Bu Gizlilik Politikası, kişisel verilerinizin hangi
                amaçlarla toplandığını, nasıl korunduğunu ve haklarınızı açıklar. KVKK uyumlu
                detaylı bilgi için
                <a href="{{ route('kvkk') }}" class="text-primary underline-grow">KVKK Aydınlatma Metnimizi</a>
                inceleyebilirsiniz.
            </p>

            <h2 class="font-display font-semibold text-2xl text-ink mt-lg">1. Hangi Veriler Toplanır?</h2>
            <p>Hizmetlerimizi sunabilmek için yalnızca gerekli verileri topluyoruz:</p>
            <ul class="list-disc pl-md space-y-xs">
                <li><strong>Rezervasyon sırasında:</strong> Ad, soyad, telefon, e-posta,
                    giriş-çıkış tarihleri, oda tercihi, kişi sayısı, varsa özel talepler</li>
                <li><strong>İletişim için yazdığınızda:</strong> Mesajınızda paylaştığınız
                    bilgiler (ad, iletişim bilgisi, talep detayı)</li>
                <li><strong>Otelimize girişinizde:</strong> 1774 sayılı Kimlik Bildirme Kanunu
                    gereği kimlik bilgileriniz</li>
            </ul>

            <h2 class="font-display font-semibold text-2xl text-ink mt-lg">2. Verileriniz Nasıl Kullanılır?</h2>
            <ul class="list-disc pl-md space-y-xs">
                <li>Rezervasyonunuzu kayıt etmek ve sizinle iletişim kurmak</li>
                <li>Konaklama hizmetini sunmak</li>
                <li>Yasal yükümlülükleri (konaklama bildirim, vergi kayıtları) yerine getirmek</li>
                <li>Hizmet kalitemizi artırmak için iç değerlendirme yapmak</li>
            </ul>
            <p>
                Verileriniz <strong>reklam, pazarlama veya ticari amaçla üçüncü taraflara
                aktarılmaz, satılmaz</strong> ve hiçbir koşulda kötüye kullanılmaz.
            </p>

            <h2 class="font-display font-semibold text-2xl text-ink mt-lg">3. Ödeme Bilgileriniz</h2>
            <p>
                Sitemizde <strong>online ödeme alınmaz</strong>. Konaklama ücreti rezervasyon
                onayından sonra paylaştığımız IBAN'a havale ile ödenir. Sitemizde kredi kartı,
                banka kartı veya benzeri ödeme bilgileriniz <strong>hiçbir zaman istenmez,
                toplanmaz veya saklanmaz</strong>.
            </p>

            <h2 class="font-display font-semibold text-2xl text-ink mt-lg">4. Verileriniz Nerede Saklanır?</h2>
            <p>
                Verileriniz Avrupa Birliği üyesi Almanya'da bulunan
                <strong>kendi özel sunucumuzda</strong> şifreli ortamda saklanır. Yetkisiz
                erişime karşı şu önlemler alınır:
            </p>
            <ul class="list-disc pl-md space-y-xs">
                <li>Tüm bağlantılar HTTPS / TLS 1.3 ile şifrelenir</li>
                <li>Sunucu firewall ile sadece Cloudflare CDN trafiğine açıktır</li>
                <li>Yönetim paneli iki katmanlı güvenlik (güçlü şifre + IP kısıtlaması) ile korunur</li>
                <li>Veritabanı düzenli olarak yedeklenir, yedekler şifrelenir</li>
                <li>Şifreler bcrypt algoritması ile geri döndürülemez şekilde hash'lenir</li>
            </ul>

            <h2 class="font-display font-semibold text-2xl text-ink mt-lg">5. Ne Kadar Süre Saklanır?</h2>
            <p>
                Veriler yasal saklama süresince muhafaza edilir (rezervasyon kayıtları 10 yıl,
                mali kayıtlar 5-10 yıl). Süre sonunda silinir veya anonimleştirilir.
                Detay için
                <a href="{{ route('kvkk') }}" class="text-primary underline-grow">KVKK Aydınlatma Metni</a>'nin
                6. bölümünü inceleyebilirsiniz.
            </p>

            <h2 class="font-display font-semibold text-2xl text-ink mt-lg">6. Çerezler</h2>
            <p>
                Sitemiz iki kategoride çerez kullanır:
            </p>
            <ul class="list-disc pl-md space-y-xs">
                <li><strong>Zorunlu teknik çerezler</strong> — her zaman aktif (oturum,
                    form güvenliği, Cloudflare bot koruması). Reklam veya profil çıkartma
                    çerezi yoktur.</li>
                <li><strong>İsteğe bağlı analitik çerezler</strong> — yalnızca açık rızanız
                    sonrası aktif. Sayfa altındaki çerez tercih banner'ından
                    <em>"Tümünü Kabul Et"</em> tıklarsanız Google Analytics 4 anonim
                    istatistik çerezi yerleşir. Reddederseniz hiçbir analitik çerez
                    yerleşmez.</li>
            </ul>
            <p>
                Detay (çerez listesi, süreleri, sağlayıcılar, tercih sıfırlama):
                <a href="{{ route('cookie-policy') }}" class="text-primary underline-grow">Çerez Politikası</a>.
            </p>

            <h2 class="font-display font-semibold text-2xl text-ink mt-lg">7. Haklarınız</h2>
            <p>
                Verilerinize ilişkin (görme, düzeltme, silme, itiraz vb.) tüm yasal haklarınızı
                kullanabilirsiniz. KVKK m.11 hakları, başvuru kanalları ve sürecin detayı için
                <a href="{{ route('kvkk') }}" class="text-primary underline-grow">KVKK Aydınlatma Metni</a>'nin
                7-8. bölümlerini inceleyebilirsiniz.
            </p>

            <h2 class="font-display font-semibold text-2xl text-ink mt-lg">8. İletişim</h2>
            <p>
                Gizlilik konusunda her türlü soru, talep veya endişeniz için bize ulaşabilirsiniz:
            </p>
            <ul class="list-disc pl-md space-y-xs">
                <li><strong>E-posta:</strong>
                    <a href="mailto:{{ \App\Models\Setting::get('email') }}" class="text-primary underline-grow">{{ \App\Models\Setting::get('email') }}</a>
                </li>
                <li><strong>Telefon:</strong> {{ \App\Models\Setting::get('phone') }}</li>
                <li><strong>Adres:</strong> {{ \App\Models\Setting::get('address') }}</li>
            </ul>

            <h2 class="font-display font-semibold text-2xl text-ink mt-lg">9. Güncellemeler</h2>
            <p>
                Bu politika ihtiyaç halinde güncellenebilir. Önemli değişiklikler sayfanın
                üst kısmındaki "Son güncelleme" tarihinden takip edilebilir. Politika değişikliği
                misafirlerimize yeni rezervasyon sırasında bildirilir.
            </p>

        </div>
    </div>
</section>

@endsection
