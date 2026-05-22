@extends('layouts.public')

@section('title', 'KVKK Aydınlatma Metni')
@section('description', 'Koğ Suit Otel\'in 6698 sayılı KVKK kapsamında kişisel verilerin işlenmesi hakkında aydınlatma metni ve misafir hakları.')

@section('content')

<section class="py-lg md:py-xl">
    <div class="max-w-[800px] mx-auto px-md">
        <p class="font-display text-xs tracking-[0.2em] uppercase text-accent-dark mb-sm">Yasal</p>
        <h1 class="font-display font-bold text-4xl md:text-5xl tracking-tight text-ink mb-md leading-tight">
            KVKK Aydınlatma Metni
        </h1>

        <div class="prose prose-ink max-w-none text-ink-soft leading-relaxed space-y-md">
            <p>
                Koğ Suit Otel olarak, 6698 sayılı Kişisel Verilerin Korunması Kanunu ("KVKK") uyarınca,
                kişisel verilerinizin işlenmesi hakkında sizi bilgilendirmek isteriz.
            </p>

            <h2 class="font-display font-semibold text-2xl text-ink mt-lg">1. Veri Sorumlusu</h2>
            <p>
                İşbu aydınlatma metni kapsamında veri sorumlusu Koğ Suit Otel'dir.
                İletişim adresi: {{ \App\Models\Setting::get('address') }}
            </p>

            <h2 class="font-display font-semibold text-2xl text-ink mt-lg">2. İşlenen Kişisel Veriler</h2>
            <ul class="list-disc pl-md space-y-xs">
                <li>Kimlik bilgileri (ad, soyad)</li>
                <li>İletişim bilgileri (telefon, e-posta)</li>
                <li>Rezervasyon bilgileri (giriş-çıkış tarihleri, kişi sayısı, oda tercihi)</li>
                <li>Talep ve şikayet bilgileri</li>
            </ul>

            <h2 class="font-display font-semibold text-2xl text-ink mt-lg">3. Kişisel Verilerin İşlenme Amaçları</h2>
            <ul class="list-disc pl-md space-y-xs">
                <li>Rezervasyon işlemlerinizin gerçekleştirilmesi</li>
                <li>Konaklama hizmetinin sunulması</li>
                <li>Yasal yükümlülüklerin yerine getirilmesi (konaklama tesisleri yasası gereği bildirim)</li>
                <li>İletişim ve müşteri memnuniyeti</li>
            </ul>

            <h2 class="font-display font-semibold text-2xl text-ink mt-lg">4. Haklarınız</h2>
            <p>
                KVKK'nın 11. maddesi uyarınca; verilerinizin işlenip işlenmediğini öğrenme, düzeltilmesini
                isteme, silinmesini talep etme ve diğer yasal haklarınızı kullanabilirsiniz. Başvurularınızı
                <a href="mailto:{{ \App\Models\Setting::get('email') }}" class="text-primary underline-grow">{{ \App\Models\Setting::get('email') }}</a>
                adresine iletebilirsiniz.
            </p>

            <p class="text-xs text-ink-mute mt-lg pt-md border-t border-border-soft">
                <em>Bu metin örnek niteliğindedir. Yayına alınmadan önce KVKK uyumluluğu açısından
                bir avukat tarafından gözden geçirilmesi önerilir.</em>
            </p>
        </div>
    </div>
</section>

@endsection
