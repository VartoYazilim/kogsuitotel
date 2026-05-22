@extends('layouts.public')

@section('title', 'Gizlilik Sözleşmesi')
@section('description', 'Koğ Suit Otel Gizlilik Sözleşmesi — misafirlerimizin verilerinin nasıl toplandığı, saklandığı ve korunduğu.')

@section('content')

<section class="py-lg md:py-xl">
    <div class="max-w-[800px] mx-auto px-md">
        <p class="font-display text-xs tracking-[0.2em] uppercase text-accent-dark mb-sm">Yasal</p>
        <h1 class="font-display font-bold text-4xl md:text-5xl tracking-tight text-ink mb-md leading-tight">
            Gizlilik Sözleşmesi
        </h1>

        <div class="prose prose-ink max-w-none text-ink-soft leading-relaxed space-y-md">
            <p>
                Koğ Suit Otel olarak misafirlerimizin gizliliğine ve kişisel verilerinin güvenliğine en üst düzeyde önem veriyoruz.
            </p>

            <h2 class="font-display font-semibold text-2xl text-ink mt-lg">Toplanan Veriler</h2>
            <p>
                Web sitemiz aracılığıyla rezervasyon yaparken ad, soyad, telefon, e-posta ve konaklama
                detaylarınızı topluyoruz. Bunlar yalnızca rezervasyon ve iletişim amaçlı kullanılır.
            </p>

            <h2 class="font-display font-semibold text-2xl text-ink mt-lg">Veri Paylaşımı</h2>
            <p>
                Verileriniz, yasal zorunluluklar haricinde hiçbir üçüncü tarafla paylaşılmaz, satılmaz ya da
                ticari amaçla kullanılmaz.
            </p>

            <h2 class="font-display font-semibold text-2xl text-ink mt-lg">Çerezler</h2>
            <p>
                Sitemizde misafirlerin deneyimini iyileştirmek için temel teknik çerezler kullanılır.
                Reklam veya analiz amaçlı izleyici çerezi kullanılmaz.
            </p>

            <h2 class="font-display font-semibold text-2xl text-ink mt-lg">İletişim</h2>
            <p>
                Gizlilik konusunda sorularınız için bize
                <a href="mailto:{{ \App\Models\Setting::get('email') }}" class="text-primary underline-grow">{{ \App\Models\Setting::get('email') }}</a>
                adresinden ulaşabilirsiniz.
            </p>

            <p class="text-xs text-ink-mute mt-lg pt-md border-t border-border-soft">
                <em>Bu metin örnek niteliğindedir. Yayına alınmadan önce uyumluluk açısından bir avukat
                tarafından gözden geçirilmesi önerilir.</em>
            </p>
        </div>
    </div>
</section>

@endsection
