@extends('layouts.public')

@section('title', 'KVKK Aydınlatma Metni')
@section('description', 'Koğ Suit Otel 6698 sayılı KVKK kapsamında kişisel verilerin işlenmesi, aktarılması, saklanması hakkında aydınlatma metni ve misafir hakları.')

@section('content')

<section class="py-lg md:py-xl">
    <div class="max-w-[800px] mx-auto px-md">
        <p class="font-display text-xs tracking-[0.2em] uppercase text-accent-dark mb-sm">Yasal</p>
        <h1 class="font-display font-bold text-4xl md:text-5xl tracking-tight text-ink mb-md leading-tight">
            KVKK Aydınlatma Metni
        </h1>

        <p class="text-sm text-ink-mute mb-lg">
            Son güncelleme: {{ \Carbon\Carbon::create(2026, 5, 24)->translatedFormat('d F Y') }}
        </p>

        <div class="prose prose-ink max-w-none text-ink-soft leading-relaxed space-y-md">

            <p>
                Koğ Suit Otel ("Otel", "biz") olarak, 6698 sayılı Kişisel Verilerin Korunması Kanunu
                ("KVKK") uyarınca veri sorumlusu sıfatıyla, misafirlerimizin ve site ziyaretçilerimizin
                kişisel verilerinin işlenmesine ilişkin sizi bilgilendirmek isteriz. Bu Aydınlatma
                Metni KVKK'nın 10. maddesi gereğince hazırlanmıştır.
            </p>

            <h2 class="font-display font-semibold text-2xl text-ink mt-lg">1. Veri Sorumlusu</h2>
            <p>
                Veri sorumlusu <strong>Koğ Suit Otel</strong>'dir.
            </p>
            <ul class="list-disc pl-md space-y-xs">
                <li><strong>Adres:</strong> {{ \App\Models\Setting::get('address', 'Varto, Muş, Türkiye') }}</li>
                <li><strong>E-posta:</strong>
                    <a href="mailto:{{ \App\Models\Setting::get('email') }}" class="text-primary underline-grow">{{ \App\Models\Setting::get('email') }}</a>
                </li>
                <li><strong>Telefon:</strong> {{ \App\Models\Setting::get('phone') }}</li>
            </ul>

            <h2 class="font-display font-semibold text-2xl text-ink mt-lg">2. İşlenen Kişisel Veri Kategorileri</h2>
            <p>Sitemiz ve hizmetlerimiz kapsamında aşağıdaki kişisel veri kategorileri işlenmektedir:</p>
            <ul class="list-disc pl-md space-y-xs">
                <li><strong>Kimlik bilgileri:</strong> Ad, soyad</li>
                <li><strong>İletişim bilgileri:</strong> Telefon numarası, e-posta adresi</li>
                <li><strong>Müşteri işlem bilgileri:</strong> Rezervasyon kodu, giriş-çıkış tarihleri,
                    konaklayacak kişi sayısı (yetişkin/çocuk), oda tercihi, özel talepler</li>
                <li><strong>Finansal bilgiler:</strong> Toplam konaklama tutarı (havale dekontu ile
                    teyit edilir; banka/IBAN bilgileri tarafınızca tarafımıza iletilir,
                    sitemizde ödeme alınmaz)</li>
                <li><strong>İşlem güvenliği bilgileri:</strong> Form gönderim sırasında IP adresi
                    (rate-limit ve bot koruması amacıyla geçici log'lanır, kalıcı saklanmaz)</li>
                <li><strong>Analitik veriler (yalnızca açık rıza ile):</strong> Sayfa görüntüleme,
                    yaklaşık ziyaret süresi, ziyaret saati, cihaz/tarayıcı tipi gibi anonim
                    istatistik verileri (Google Analytics 4). IP adresi anonimleştirilerek toplanır,
                    sizi tanımlayan profil çıkarılmaz. Detay: Çerez Politikası Bölüm 3.</li>
                <li><strong>Konaklama bildirim bilgileri:</strong> 1774 sayılı Kimlik Bildirme
                    Kanunu uyarınca girişte kimlik fotokopisi alınır ve kolluk kuvvetlerine
                    elektronik bildirim yapılır</li>
            </ul>

            <h2 class="font-display font-semibold text-2xl text-ink mt-lg">3. Kişisel Verilerin İşlenme Amaçları</h2>
            <ul class="list-disc pl-md space-y-xs">
                <li>Rezervasyon talebinin alınması, değerlendirilmesi ve onaylanması</li>
                <li>Konaklama hizmetinin sunulması, giriş-çıkış işlemlerinin gerçekleştirilmesi</li>
                <li>Misafirlerle iletişim kurulması (rezervasyon onayı, hatırlatmalar,
                    talep ve şikayet yönetimi)</li>
                <li>1774 sayılı Kimlik Bildirme Kanunu kapsamında konaklama bildiriminin
                    yapılması (yasal yükümlülük)</li>
                <li>Vergi Usul Kanunu ve Türk Ticaret Kanunu kapsamında mali kayıtların
                    tutulması (fatura, dekont eşleştirmesi)</li>
                <li>İşletme güvenliğinin sağlanması, dolandırıcılık önlemi (rate-limit, bot koruması)</li>
                <li>Hizmet kalitesinin artırılması, iç istatistiksel analizler
                    (kimlik bilgileri olmadan agregat olarak)</li>
            </ul>

            <h2 class="font-display font-semibold text-2xl text-ink mt-lg">4. Kişisel Verilerin Aktarılması</h2>

            <h3 class="font-display font-semibold text-lg text-ink mt-md">4.1. Yurt İçi Aktarım</h3>
            <p>Kişisel verileriniz aşağıdaki kişi ve kurumlara aktarılabilir:</p>
            <ul class="list-disc pl-md space-y-xs">
                <li><strong>Yetkili kamu kurum ve kuruluşları</strong> (Emniyet Genel Müdürlüğü,
                    Jandarma Genel Komutanlığı): 1774 sayılı Kimlik Bildirme Kanunu kapsamında
                    konaklama bildirimi için zorunlu</li>
                <li><strong>Vergi daireleri, SGK, mali müşavir:</strong> Mali kayıtların
                    tutulması için yasal yükümlülük çerçevesinde</li>
                <li><strong>Yetkili mahkemeler ve icra müdürlükleri:</strong> Hukuki uyuşmazlık
                    halinde yargı kararı veya talebi üzerine</li>
            </ul>

            <h3 class="font-display font-semibold text-lg text-ink mt-md">4.2. Yurt Dışı Aktarım (KVKK m.9)</h3>
            <p>
                Sitemizin teknik altyapısı için aşağıdaki yurt dışı sağlayıcıları kullanırız.
                Bu aktarımlar yalnızca teknik kısa süreli işleme amacıyla yapılır; rezervasyon
                içeriği gibi temel verileriniz yurt dışına aktarılmaz.
            </p>
            <ul class="list-disc pl-md space-y-xs">
                <li><strong>Cloudflare, Inc. (ABD):</strong> CDN ve güvenlik hizmeti.
                    Trafik yönlendirme, bot koruması ve DDoS engelleme amacıyla IP adresi
                    gibi teknik verileri işler. KVKK m.9/6 kapsamında hizmet sözleşmesi
                    ile teminat altındadır.</li>
                <li><strong>Google LLC (ABD) — Google Analytics 4 (yalnızca açık rızanızla):</strong>
                    Sayfa altındaki çerez tercih banner'ından <em>"Tümünü Kabul Et"</em>
                    seçeneğini tıklarsanız anonim sayfa görüntüleme istatistiği (sayfa adı,
                    yaklaşık ziyaret süresi, anonim ID) Google'a aktarılır. IP adresiniz
                    Google'a aktarılmadan önce anonimleştirilir (`anonymize_ip` aktif).
                    Detay: <a href="{{ route('cookie-policy') }}" class="text-primary underline-grow">Çerez Politikası</a>
                    Bölüm 3. Reddederseniz veya tercih yapmazsanız bu aktarım yapılmaz.</li>
            </ul>
            <p>
                Yukarıdaki yurt dışı aktarımlar KVKK m.9/1 ve m.5/1 (açık rıza) ve
                m.9/6 (hizmet sağlayıcı sözleşmesi) kapsamında gerçekleştirilir.
            </p>
            <p>
                <strong>Verileriniz hiçbir koşulda pazarlama, reklam veya ticari amaçla üçüncü
                taraflara satılmaz veya devredilmez.</strong>
            </p>

            <h2 class="font-display font-semibold text-2xl text-ink mt-lg">5. Veri Toplama Yöntemi ve Hukuki Sebep</h2>
            <p>Kişisel verileriniz şu yöntemlerle toplanır:</p>
            <ul class="list-disc pl-md space-y-xs">
                <li>Web sitesi üzerinden çevrimiçi rezervasyon formu (otomatik)</li>
                <li>Telefon, WhatsApp veya e-posta ile gönderdiğiniz mesajlar (manuel)</li>
                <li>Otelimize girişte alınan kimlik fotokopisi ve kayıt formu (fiziksel)</li>
            </ul>
            <p>İşleme faaliyetinin hukuki sebepleri KVKK m.5 uyarınca şunlardır:</p>
            <ul class="list-disc pl-md space-y-xs">
                <li><strong>m.5/2-a:</strong> Kanunlarda açıkça öngörülmesi (Kimlik Bildirme Kanunu)</li>
                <li><strong>m.5/2-c:</strong> Sözleşmenin kurulması veya ifası için zorunlu olması
                    (rezervasyon sözleşmesi)</li>
                <li><strong>m.5/2-ç:</strong> Hukuki yükümlülüğün yerine getirilmesi (vergi, ticari kayıt)</li>
                <li><strong>m.5/2-f:</strong> Meşru menfaat (hizmet kalitesi, güvenlik)</li>
            </ul>

            <h2 class="font-display font-semibold text-2xl text-ink mt-lg">6. Verilerin Saklama Süresi</h2>
            <p>
                Kişisel verileriniz yasal saklama süresi boyunca işletmemiz nezdinde muhafaza edilir:
            </p>
            <ul class="list-disc pl-md space-y-xs">
                <li><strong>Rezervasyon kayıtları:</strong> Türk Borçlar Kanunu zamanaşımı süresi
                    olan <strong>10 yıl</strong></li>
                <li><strong>Mali kayıtlar (fatura, dekont):</strong> Vergi Usul Kanunu m.253
                    uyarınca <strong>5 yıl</strong>, Türk Ticaret Kanunu m.82 uyarınca
                    <strong>10 yıl</strong></li>
                <li><strong>Konaklama bildirim kayıtları:</strong> Kimlik Bildirme Kanunu
                    uygulama yönetmeliği gereği</li>
                <li><strong>İletişim talepleri:</strong> Amaç gerçekleştikten sonra makul süre
                    içinde silinir</li>
            </ul>
            <p>
                Yasal saklama süresi sonunda verileriniz silinir, yok edilir veya
                anonimleştirilir.
            </p>

            <h2 class="font-display font-semibold text-2xl text-ink mt-lg">7. KVKK m.11 — Veri Sahibi Hakları</h2>
            <p>Veri sahibi olarak Otel'e başvurarak şu haklarınızı kullanabilirsiniz:</p>
            <ul class="list-disc pl-md space-y-xs">
                <li>Kişisel verilerinizin işlenip işlenmediğini öğrenme</li>
                <li>İşlenmişse buna ilişkin bilgi talep etme</li>
                <li>İşlenme amacını ve amaca uygun kullanılıp kullanılmadığını öğrenme</li>
                <li>Yurt içinde veya yurt dışında aktarıldığı üçüncü kişileri bilme</li>
                <li>Eksik veya yanlış işlenmişse düzeltilmesini isteme</li>
                <li>Silinmesini veya yok edilmesini isteme (yasal saklama yükümlülüğü saklı)</li>
                <li>Düzeltme/silme işlemlerinin aktarılan üçüncü kişilere bildirilmesini isteme</li>
                <li>İşlenen verilerin münhasıran otomatik sistemlerle analiz edilmesi sonucu
                    aleyhinize bir sonuç ortaya çıkmasına itiraz etme</li>
                <li>Kanuna aykırı işleme sebebiyle zarara uğramışsanız zararın giderilmesini talep etme</li>
            </ul>

            <h2 class="font-display font-semibold text-2xl text-ink mt-lg">8. Başvuru Yolları</h2>
            <p>
                Veri Sorumlusuna Başvuru Usul ve Esasları Hakkında Tebliğ uyarınca
                başvurularınızı şu yollarla yapabilirsiniz:
            </p>
            <ul class="list-disc pl-md space-y-xs">
                <li><strong>E-posta:</strong>
                    <a href="mailto:{{ \App\Models\Setting::get('email') }}" class="text-primary underline-grow">{{ \App\Models\Setting::get('email') }}</a>
                    (kimliğinizi doğrulayan belge ile)</li>
                <li><strong>Posta:</strong> {{ \App\Models\Setting::get('address') }}</li>
                <li><strong>Şahsen başvuru:</strong> Otel resepsiyonunda kimliğinizi ibraz ederek</li>
            </ul>
            <p>
                Başvurunuz en geç <strong>30 gün içinde</strong> ücretsiz olarak yanıtlanır.
                Talebin niteliği işlem maliyeti gerektirdiğinde Kişisel Verileri Koruma Kurulu'nun
                belirlediği tarife üzerinden ücret talep edilebilir.
            </p>

            <h2 class="font-display font-semibold text-2xl text-ink mt-lg">9. Çerez Kullanımı</h2>
            <p>
                Sitemizdeki çerez kullanımı hakkında detaylı bilgi için
                <a href="{{ route('cookie-policy') }}" class="text-primary underline-grow">Çerez Politikamızı</a>
                inceleyiniz.
            </p>

            <h2 class="font-display font-semibold text-2xl text-ink mt-lg">10. Değişiklikler</h2>
            <p>
                Bu Aydınlatma Metni gerektiğinde güncellenebilir. Güncel sürüm her zaman bu
                sayfada yer alır; üst kısımdaki "Son güncelleme" tarihi referans alınır.
            </p>

        </div>
    </div>
</section>

@endsection
