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
                Sitemiz iki kategoride çerez kullanır: <strong>Zorunlu teknik çerezler</strong>
                (her zaman aktif) ve <strong>İsteğe bağlı analitik çerezler</strong>
                (yalnızca açık rızanız sonrası aktif).
            </p>

            <div class="my-md overflow-x-auto">
                <table class="w-full text-sm border-collapse">
                    <thead class="bg-surface-card border-b-2 border-border">
                        <tr>
                            <th class="text-left p-sm font-display font-semibold text-ink">Çerez Adı</th>
                            <th class="text-left p-sm font-display font-semibold text-ink">Amaç</th>
                            <th class="text-left p-sm font-display font-semibold text-ink">Süre</th>
                            <th class="text-left p-sm font-display font-semibold text-ink">Tür</th>
                            <th class="text-left p-sm font-display font-semibold text-ink">Sağlayıcı</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border-soft">
                        <tr>
                            <td class="p-sm font-mono text-xs">kog-suit-otel-session</td>
                            <td class="p-sm">Oturum yönetimi (rezervasyon formu adımları, admin paneli giriş durumu)</td>
                            <td class="p-sm">2 saat</td>
                            <td class="p-sm"><span class="text-primary font-medium">Zorunlu</span></td>
                            <td class="p-sm">Koğ Suit Otel</td>
                        </tr>
                        <tr>
                            <td class="p-sm font-mono text-xs">XSRF-TOKEN</td>
                            <td class="p-sm">CSRF saldırılarına karşı form güvenliği</td>
                            <td class="p-sm">2 saat</td>
                            <td class="p-sm"><span class="text-primary font-medium">Zorunlu</span></td>
                            <td class="p-sm">Koğ Suit Otel</td>
                        </tr>
                        <tr>
                            <td class="p-sm font-mono text-xs">cookie_consent</td>
                            <td class="p-sm">Çerez tercih banner cevabınızı hatırlar (kabul/red)</td>
                            <td class="p-sm">Tarayıcı kapatılana kadar (localStorage)</td>
                            <td class="p-sm"><span class="text-primary font-medium">Zorunlu</span></td>
                            <td class="p-sm">Koğ Suit Otel</td>
                        </tr>
                        <tr>
                            <td class="p-sm font-mono text-xs">__cf_bm, cf_clearance</td>
                            <td class="p-sm">Cloudflare bot koruması ve güvenlik (CDN sağlayıcı)</td>
                            <td class="p-sm">30 dk — 1 yıl</td>
                            <td class="p-sm"><span class="text-primary font-medium">Zorunlu</span></td>
                            <td class="p-sm">Cloudflare, Inc.</td>
                        </tr>
                        <tr class="bg-warning/5">
                            <td class="p-sm font-mono text-xs">_ga</td>
                            <td class="p-sm">Anonim kullanıcı tanımlama (Google Analytics 4) — sayfa trafiği analizi</td>
                            <td class="p-sm">2 yıl</td>
                            <td class="p-sm"><span class="text-warning font-medium">İsteğe bağlı</span></td>
                            <td class="p-sm">Google LLC (ABD)</td>
                        </tr>
                        <tr class="bg-warning/5">
                            <td class="p-sm font-mono text-xs">_ga_*</td>
                            <td class="p-sm">Google Analytics 4 oturum durumu (mülk-spesifik)</td>
                            <td class="p-sm">2 yıl</td>
                            <td class="p-sm"><span class="text-warning font-medium">İsteğe bağlı</span></td>
                            <td class="p-sm">Google LLC (ABD)</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <p>
                <strong>Zorunlu çerezler</strong> sitenin temel işlevlerinin çalışması için
                gereklidir; KVKK m.5/2-c kapsamında sözleşmenin ifası için zorunlu olduğundan
                ayrı bir izin gerektirmez.
            </p>
            <p>
                <strong>İsteğe bağlı analitik çerezler</strong> yalnızca sayfanın altında
                gösterilen çerez tercih banner'ından <em>"Tümünü Kabul Et"</em> seçeneğini
                tıklamanız sonrası aktive edilir (KVKK m.5/1 açık rıza). Reddederseniz veya
                tercih yapmazsanız Google Analytics çerezi yerleştirilmez.
            </p>

            <h2 class="font-display font-semibold text-2xl text-ink mt-lg">3. Analitik / İzleme — Google Analytics 4</h2>
            <p>
                Site trafik istatistiklerini (ziyaretçi sayısı, hangi sayfaların ilgi gördüğü,
                ortalama oturum süresi gibi) anlamak için <strong>Google Analytics 4</strong>
                kullanıyoruz. GA4'ün <strong>Consent Mode v2</strong> özelliği aktif:
            </p>
            <ul class="list-disc pl-md space-y-xs">
                <li><strong>Default deny:</strong> Sitemizi ilk ziyaret ettiğinizde GA4
                    <strong>hiçbir çerez yerleştirmez</strong>, kişisel veri toplamaz.</li>
                <li><strong>IP anonimleştirme:</strong> IP adresinizin son okteti maskelenir
                    (`anonymize_ip` aktif), tam IP Google'a aktarılmaz.</li>
                <li><strong>Reklam çerezi yok:</strong> `ad_storage`, `ad_user_data`,
                    `ad_personalization` tamamen kapalıdır — reklam profilleme yapılmaz.</li>
                <li>Banner'dan "Tümünü Kabul Et" seçerseniz yalnızca
                    <strong>anonim sayfa görüntüleme istatistiği</strong> için
                    `analytics_storage` aktive edilir.</li>
            </ul>
            <p>
                Reddederseniz, kapatırsanız veya tarayıcınızda çerezleri engellerseniz
                GA4 hiçbir çerez yerleştirmez ve sitenin işleyişi etkilenmez.
            </p>
            <p>
                <strong>Yurt dışı veri aktarımı:</strong> GA4 sağlayıcısı Google LLC'nin
                sunucuları ABD'dedir. Açık rızanızı verirseniz toplanan anonim istatistik
                verisi KVKK m.9/1 kapsamında ABD'ye aktarılabilir.
                Detay: <a href="{{ route('kvkk') }}" class="text-primary underline-grow">KVKK Aydınlatma Metni</a> Bölüm 4.
            </p>

            <h2 class="font-display font-semibold text-2xl text-ink mt-lg">4. Üçüncü Taraf Çerezleri</h2>
            <p>
                Sitemizde Facebook Pixel, TikTok Pixel, reklam ağları, sosyal medya widget'ları
                veya benzeri üçüncü taraf izleme çerezleri <strong>kullanılmamaktadır</strong>.
                Yalnızca aşağıdaki dış hizmetler çerez yerleştirebilir:
            </p>
            <ul class="list-disc pl-md space-y-xs">
                <li><strong>Cloudflare (CDN + güvenlik):</strong> Zorunlu teknik çerezler
                    (`__cf_bm`, `cf_clearance`) — bot koruması</li>
                <li><strong>Google Analytics 4 (analitik):</strong> Yalnızca açık rızanız
                    sonrası `_ga`, `_ga_*` — anonim sayfa istatistiği</li>
            </ul>

            <h2 class="font-display font-semibold text-2xl text-ink mt-lg">5. Çerez Tercihlerini Nasıl Yönetirim?</h2>

            <h3 class="font-display font-semibold text-lg text-ink mt-md">5.1. Sitemizdeki Çerez Tercih Banner'ı</h3>
            <p>
                Analitik çerezler için verdiğiniz onayı dilediğiniz zaman değiştirebilirsiniz:
            </p>
            @if (config('seo.google_analytics_id'))
            <p class="my-md">
                <button type="button"
                        onclick="try { localStorage.removeItem('cookie_consent'); location.reload(); } catch(e) {}"
                        class="px-md py-xs rounded-btn bg-primary text-white hover:bg-primary-dark transition-colors font-display font-semibold text-sm">
                    Çerez Tercihimi Sıfırla
                </button>
                <span class="text-sm text-ink-mute ml-sm">Tıkladığınızda sayfa yenilenir ve banner tekrar görünür.</span>
            </p>
            @endif

            <h3 class="font-display font-semibold text-lg text-ink mt-md">5.2. Tarayıcı Ayarlarından Çerez Yönetimi</h3>
            <p>
                Tüm çerezleri tarayıcınızdan da silebilir veya engelleyebilirsiniz:
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
