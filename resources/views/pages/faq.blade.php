@extends('layouts.public')

@section('title', 'Sıkça Sorulan Sorular — Varto Otel')
@section('description', 'Koğ Suit Otel rezervasyon, ödeme, giriş-çıkış saatleri, evcil hayvan, kahvaltı ve otopark hakkında sıkça sorulan soruların yanıtları.')

@push('head')
{{-- FAQPage schema — Google rich snippet için kritik (her soru kartı arama sonucunda görünür) --}}
@if ($faqs->isNotEmpty())
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => $faqs->map(fn ($faq) => [
        '@type' => 'Question',
        'name' => $faq->question,
        'acceptedAnswer' => [
            '@type' => 'Answer',
            'text' => $faq->answer,
        ],
    ])->values()->all(),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}
</script>
@endif

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

        @if ($faqs->isEmpty())
            <div class="bg-surface-soft border border-border-soft rounded-2xl p-lg text-center">
                <p class="text-ink-soft">Henüz soru eklenmedi. Sorularınız için
                    <a href="{{ route('contact') }}" class="text-primary underline-grow">iletişim sayfamızı</a>
                    ziyaret edebilirsiniz.
                </p>
            </div>
        @else
            <div class="space-y-0">
                @foreach ($faqs as $faq)
                    <details class="border-b border-border-soft py-sm group transition-colors hover:border-accent/50">
                        <summary class="flex items-center justify-between py-xs cursor-pointer list-none gap-md group/sum">
                            <span class="font-display font-semibold text-lg text-ink pr-md group-hover/sum:text-primary transition-colors">{{ $faq->question }}</span>
                            <span class="w-8 h-8 rounded-pill bg-primary-soft/50 group-open:bg-accent text-accent group-open:text-surface text-xl flex items-center justify-center transition-all duration-300 ease-out group-open:rotate-45 shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            </span>
                        </summary>
                        <p class="text-ink-soft mt-sm leading-relaxed pl-xs animate-fade-in whitespace-pre-line">{{ $faq->answer }}</p>
                    </details>
                @endforeach
            </div>
        @endif
    </div>
</section>

@endsection
