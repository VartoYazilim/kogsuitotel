@extends('layouts.public')

@section('title', 'Sıkça Sorulan Sorular — Varto Otel')
@section('description', 'Koğ Suit Otel rezervasyon, ödeme, giriş-çıkış saatleri, evcil hayvan, kahvaltı ve otopark hakkında sıkça sorulan soruların yanıtları.')

@php
    $faqItems = [
        ['q' => 'Otele giriş ve çıkış saatleri nedir?', 'a' => 'Giriş saati 14:00\'ten itibaren, çıkış ise saat 12:00\'a kadardır. Erken giriş veya geç çıkış talepleriniz için rezervasyon sırasında "Özel İstekler" bölümünden bizimle iletişime geçebilirsiniz.'],
        ['q' => 'Ödeme nasıl yapılır?', 'a' => 'Rezervasyon onayı sonrası tarafınıza iletilen IBAN\'a havale/EFT ile ödeme yapabilirsiniz. Dekontunuzu WhatsApp üzerinden bize ulaştırdığınızda rezervasyonunuz kesinleştirilir. Online kart ödemesi şu an için kabul edilmemektedir.'],
        ['q' => 'Rezervasyonumu iptal edebilir miyim?', 'a' => 'Giriş tarihinden 7 gün öncesine kadar yapılan iptaller için ödenen tutar iade edilir. Bu süreden sonraki iptallerde iade yapılamamaktadır. Detaylar için bizimle iletişime geçin.'],
        ['q' => 'Otelde evcil hayvan kabul ediyor musunuz?', 'a' => 'Evet, küçük ırk evcil hayvanlar belirli odalarımızda kabul edilmektedir. Rezervasyon öncesi mutlaka bizimle iletişime geçerek uygunluğu teyit etmeniz gerekir.'],
        ['q' => 'Otoparkınız var mı?', 'a' => 'Evet, otelimizin yanında misafirlerimize özel ücretsiz ve kameralı bir otopark alanımız bulunmaktadır.'],
        ['q' => 'Kahvaltı fiyata dahil mi?', 'a' => 'Evet, açık büfe yöresel kahvaltımız tüm konaklamalarda ücretsiz olarak sunulmaktadır. Saat 08:00 - 10:30 arası açıktır.'],
        ['q' => 'Wi-Fi sağlıyor musunuz?', 'a' => 'Tüm odalarımızda ve ortak alanlarda yüksek hızlı ücretsiz Wi-Fi mevcuttur.'],
        ['q' => 'Çocuklar için ek yatak imkanı var mı?', 'a' => 'Aile Odası ve Premium Süit\'te ek yatak imkanı bulunmaktadır. Diğer odalar için lütfen bizimle iletişime geçiniz.'],
    ];
@endphp

@push('head')
{{-- FAQPage schema — Google rich snippet için kritik (her soru kartı arama sonucunda görünür) --}}
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => collect($faqItems)->map(fn ($item) => [
        '@type' => 'Question',
        'name' => $item['q'],
        'acceptedAnswer' => [
            '@type' => 'Answer',
            'text' => $item['a'],
        ],
    ])->values()->all(),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}
</script>

@include('partials.schema-breadcrumb', ['items' => [
    ['name' => 'Ana Sayfa', 'url' => route('home')],
    ['name' => 'Sıkça Sorulan Sorular', 'url' => route('faq')],
]])
@endpush

@section('content')

<section class="py-lg md:py-xl">
    <div class="max-w-[900px] mx-auto px-md">
        <p class="font-display text-xs tracking-[0.2em] uppercase text-accent-dark mb-sm">SSS</p>
        <h1 class="font-display font-bold text-4xl md:text-6xl tracking-tight text-ink mb-md leading-tight">
            Sıkça Sorulan Sorular
        </h1>
        <p class="text-lg text-ink-soft leading-relaxed mb-lg">
            Aklınızdaki soruların hızlı cevapları. Daha fazlası için WhatsApp ya da telefonla
            <a href="{{ route('contact') }}" class="text-primary underline-grow">bize ulaşabilirsiniz</a>.
        </p>

        <div class="space-y-0">
            @foreach ($faqItems as $item)
                <details class="border-b border-border-soft py-sm group transition-colors hover:border-accent/50">
                    <summary class="flex items-center justify-between py-xs cursor-pointer list-none gap-md group/sum">
                        <span class="font-display font-semibold text-lg text-ink pr-md group-hover/sum:text-primary transition-colors">{{ $item['q'] }}</span>
                        <span class="w-8 h-8 rounded-pill bg-primary-soft/50 group-open:bg-accent text-accent group-open:text-surface text-xl flex items-center justify-center transition-all duration-300 ease-out group-open:rotate-45 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </span>
                    </summary>
                    <p class="text-ink-soft mt-sm leading-relaxed pl-xs animate-fade-in">{{ $item['a'] }}</p>
                </details>
            @endforeach
        </div>
    </div>
</section>

@endsection
