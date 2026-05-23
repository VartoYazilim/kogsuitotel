@extends('layouts.public')

@section('title', 'Çerez Politikası')
@section('description', 'Koğ Suit Otel çerez politikası — sitemizde kullanılan çerezler, amaçları ve tercih yönetimi.')

@section('content')

<section class="py-lg md:py-xl">
    <div class="max-w-[800px] mx-auto px-md">
        <p class="font-display text-xs tracking-[0.2em] uppercase text-accent-dark mb-sm">Yasal</p>
        <h1 class="font-display font-bold text-4xl md:text-5xl tracking-tight text-ink mb-md leading-tight">
            Çerez Politikası
        </h1>

        <p class="text-sm text-ink-mute mb-lg">
            Son güncelleme: {{ \Carbon\Carbon::create(2026, 5, 24)->translatedFormat('d F Y') }}
        </p>

        <div class="prose prose-ink max-w-none text-ink-soft leading-relaxed space-y-md">

            <p>
                Koğ Suit Otel olarak misafirlerimizin gizliliğine saygı duyuyoruz. Bu sayfa
                sitemizde hangi çerezlerin kullanıldığını, hangi amaçlarla kullanıldığını ve
                çerez tercihlerinizi nasıl yönetebileceğinizi açıklar.
            </p>

            <h2 class="font-display font-semibold text-2xl text-ink mt-lg">1. Çerez Nedir?</h2>
            <p>
                Çerezler (cookies), siteleri ziyaret ettiğinizde tarayıcınız tarafından küçük
                metin dosyaları olarak cihazınızda saklanan verilerdir. Çerezler, oturum
                bilgilerini hatırlamak, formların güvenli çalışmasını sağlamak veya site
                kullanım istatistiklerini analiz etmek için kullanılabilir.
            </p>

            <h2 class="font-display font-semibold text-2xl text-ink mt-lg">2. Sitemiz Hangi Çerezleri Kullanıyor?</h2>

            <p>
                <strong>Sitemiz yalnızca zorunlu teknik çerezler kullanır.</strong> Reklam,
                izleyici, profil çıkartma, üçüncü taraf analiz çerezi
                <strong>kullanılmamaktadır</strong>.
            </p>

            <div class="my-md overflow-x-auto">
                <table class="w-full text-sm border-collapse">
                    <thead class="bg-surface-card border-b-2 border-border">
                        <tr>
                            <th class="text-left p-sm font-display font-semibold text-ink">Çerez Adı</th>
                            <th class="text-left p-sm font-display font-semibold text-ink">Amaç</th>
                            <th class="text-left p-sm font-display font-semibold text-ink">Süre</th>
                            <th class="text-left p-sm font-display font-semibold text-ink">Tür</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border-soft">
                        <tr>
                            <td class="p-sm font-mono text-xs">kogsuitotel_session</td>
                            <td class="p-sm">Oturum yönetimi (rezervasyon formu adımları, admin paneli giriş durumu)</td>
                            <td class="p-sm">2 saat</td>
                            <td class="p-sm"><span class="text-primary font-medium">Zorunlu</span></td>
                        </tr>
                        <tr>
                            <td class="p-sm font-mono text-xs">XSRF-TOKEN</td>
                            <td class="p-sm">CSRF saldırılarına karşı form güvenliği</td>
                            <td class="p-sm">2 saat</td>
                            <td class="p-sm"><span class="text-primary font-medium">Zorunlu</span></td>
                        </tr>
                        <tr>
                            <td class="p-sm font-mono text-xs">__cf_bm, cf_clearance</td>
                            <td class="p-sm">Cloudflare bot koruması ve güvenlik (CDN sağlayıcı)</td>
                            <td class="p-sm">30 dk — 1 yıl</td>
                            <td class="p-sm"><span class="text-primary font-medium">Zorunlu</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <p>
                Bu çerezler sitenin temel işlevlerinin çalışması için <strong>zorunludur</strong>
                ve bu nedenle yasal olarak ayrı bir izin gerektirmez (KVKK ve ETBİS uygulamasında
                "kesinlikle gerekli çerezler" kategorisi).
            </p>

            <h2 class="font-display font-semibold text-2xl text-ink mt-lg">3. Analitik / İzleme</h2>
            <p>
                Sitemizde Google Analytics, Facebook Pixel veya benzeri kullanıcı izleme araçları
                <strong>kullanılmamaktadır</strong>. Site trafik istatistikleri için yalnızca
                Cloudflare Web Analytics kullanılır; bu hizmet
                <strong>çerez yerleştirmeden, kişisel veri toplamadan</strong> agregat sayfa
                görüntüleme istatistiği sunar. Sizi tanımlayan, sizinle ilgili profil çıkartan
                hiçbir bilgi toplanmaz.
            </p>

            <h2 class="font-display font-semibold text-2xl text-ink mt-lg">4. Üçüncü Taraf Çerezleri</h2>
            <p>
                Sitemiz harici hizmetlere bağlanmaz; üçüncü taraf reklam ağı, sosyal medya
                widget'ı veya benzeri çerez yerleştiren entegrasyonlar
                <strong>bulunmamaktadır</strong>. Yalnızca CDN sağlayıcımız Cloudflare'in
                güvenlik amaçlı yerleştirdiği teknik çerezler vardır
                (yukarıdaki tabloda belirtilmiştir).
            </p>

            <h2 class="font-display font-semibold text-2xl text-ink mt-lg">5. Çerez Tercihlerini Nasıl Yönetirim?</h2>
            <p>
                Çerezler tarayıcı seviyesinde yönetilir. Kullandığınız tarayıcının ayarlarından
                tüm çerezleri silebilir veya engelleyebilirsiniz:
            </p>
            <ul class="list-disc pl-md space-y-xs">
                <li><strong>Google Chrome:</strong>
                    Ayarlar → Gizlilik ve güvenlik → Çerezler ve diğer site verileri</li>
                <li><strong>Mozilla Firefox:</strong>
                    Ayarlar → Gizlilik ve Güvenlik → Çerezler ve Site Verileri</li>
                <li><strong>Safari:</strong>
                    Tercihler → Gizlilik → Web sitesi verilerini yönet</li>
                <li><strong>Microsoft Edge:</strong>
                    Ayarlar → Çerezler ve site izinleri → Çerezler ve site verilerini yönet ve sil</li>
            </ul>
            <p>
                <strong>Önemli:</strong> Zorunlu teknik çerezleri engellerseniz rezervasyon
                formu, admin paneli ve oturum yönetimi gibi temel işlevler düzgün çalışmayabilir.
            </p>

            <h2 class="font-display font-semibold text-2xl text-ink mt-lg">6. Çerez Politikası Değişiklikleri</h2>
            <p>
                Bu politika gerektiğinde güncellenebilir. Güncel sürüm her zaman bu sayfada yer alır;
                üst kısımdaki "Son güncelleme" tarihi referans alınır. Önemli değişiklikler
                misafirlerimize bildirilir.
            </p>

            <h2 class="font-display font-semibold text-2xl text-ink mt-lg">7. İletişim</h2>
            <p>
                Çerez kullanımı hakkında soru veya talepleriniz için:
                <a href="mailto:{{ \App\Models\Setting::get('email') }}" class="text-primary underline-grow">{{ \App\Models\Setting::get('email') }}</a>
            </p>

            <h2 class="font-display font-semibold text-2xl text-ink mt-lg">İlgili Belgeler</h2>
            <ul class="list-disc pl-md space-y-xs">
                <li><a href="{{ route('kvkk') }}" class="text-primary underline-grow">KVKK Aydınlatma Metni</a></li>
                <li><a href="{{ route('privacy') }}" class="text-primary underline-grow">Gizlilik Politikası</a></li>
            </ul>

        </div>
    </div>
</section>

@endsection
