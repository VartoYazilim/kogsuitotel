<!DOCTYPE html>
<html lang="tr" class="scroll-smooth">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="csrf-token" content="{{ csrf_token() }}" />

@php
    $pageTitle = trim($__env->yieldContent('title'));
    $pageDescription = trim($__env->yieldContent('description')) ?: config('seo.description_default');
    $pageImagePath = trim($__env->yieldContent('og_image')) ?: config('seo.og.default_image');
    $pageImage = str_starts_with($pageImagePath, 'http') ? $pageImagePath : url($pageImagePath);
    $pageUrl = url()->current();
    $fullTitle = $pageTitle
        ? $pageTitle.' | '.config('seo.site_name').' — Varto Muş Butik Otel'
        : config('seo.site_name').' — '.config('seo.tagline');
@endphp

<title>{{ $fullTitle }}</title>
<meta name="description" content="{{ $pageDescription }}" />

{{-- Kanonik + dil --}}
<link rel="canonical" href="{{ $pageUrl }}" />
<link rel="alternate" hreflang="tr-TR" href="{{ $pageUrl }}" />
<link rel="alternate" hreflang="x-default" href="{{ $pageUrl }}" />

{{-- Yerel SEO (Varto/Muş) --}}
<meta name="geo.region" content="{{ config('seo.geo.region') }}" />
<meta name="geo.placename" content="{{ config('seo.geo.placename') }}, {{ config('seo.geo.province') }}" />
<meta name="geo.position" content="{{ config('seo.geo.latitude') }};{{ config('seo.geo.longitude') }}" />
<meta name="ICBM" content="{{ config('seo.geo.latitude') }}, {{ config('seo.geo.longitude') }}" />

{{-- Open Graph (Facebook, LinkedIn, WhatsApp) --}}
<meta property="og:type" content="@yield('og_type', 'website')" />
<meta property="og:title" content="{{ $fullTitle }}" />
<meta property="og:description" content="{{ $pageDescription }}" />
<meta property="og:url" content="{{ $pageUrl }}" />
<meta property="og:image" content="{{ $pageImage }}" />
<meta property="og:image:width" content="{{ config('seo.og.image_width') }}" />
<meta property="og:image:height" content="{{ config('seo.og.image_height') }}" />
<meta property="og:site_name" content="{{ config('seo.site_name') }}" />
<meta property="og:locale" content="{{ config('seo.og.locale') }}" />

{{-- Twitter Card --}}
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:title" content="{{ $fullTitle }}" />
<meta name="twitter:description" content="{{ $pageDescription }}" />
<meta name="twitter:image" content="{{ $pageImage }}" />

{{-- Search engine doğrulama (.env'den okunur, boşsa render edilmez) --}}
@if (config('seo.google_verification'))
    <meta name="google-site-verification" content="{{ config('seo.google_verification') }}" />
@endif
@if (config('seo.bing_verification'))
    <meta name="msvalidate.01" content="{{ config('seo.bing_verification') }}" />
@endif
@if (config('seo.yandex_verification'))
    <meta name="yandex-verification" content="{{ config('seo.yandex_verification') }}" />
@endif

{{-- Mobile / tarayıcı ipuçları --}}
<meta name="theme-color" content="#6b7553" />
<meta name="format-detection" content="telephone=yes" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="color-scheme" content="light" />
<meta name="referrer" content="strict-origin-when-cross-origin" />

{{-- Favicon + PWA manifest --}}
<link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
<link rel="apple-touch-icon" href="{{ asset('images/logo.svg') }}" />
<link rel="mask-icon" href="{{ asset('favicon.svg') }}" color="#4a5240" />
<link rel="manifest" href="{{ asset('site.webmanifest') }}" />
<meta name="msapplication-TileColor" content="#4a5240" />
<meta name="msapplication-TileImage" content="{{ asset('images/logo.svg') }}" />

{{-- Sitemap --}}
<link rel="sitemap" type="application/xml" title="Sitemap" href="{{ route('sitemap') }}" />

{{-- Global JSON-LD: Organization + WebSite (her sayfada) --}}
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@graph' => [
        [
            '@type' => 'Organization',
            '@id' => url('/').'#organization',
            'name' => config('seo.business.legal_name'),
            'alternateName' => config('seo.business.brand_name'),
            'url' => url('/'),
            'logo' => [
                '@type' => 'ImageObject',
                'url' => url('/images/logo.svg'),
                'contentUrl' => url('/images/logo.svg'),
                'caption' => 'Koğ Suit Otel logosu',
            ],
            'image' => url('/images/logo.svg'),
            'sameAs' => array_values(array_filter([
                \App\Models\Setting::get('instagram_url'),
                \App\Models\Setting::get('facebook_url'),
                \App\Models\Setting::get('tripadvisor_url'),
            ])),
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'telephone' => \App\Models\Setting::get('phone'),
                'contactType' => 'customer service',
                'areaServed' => 'TR',
                'availableLanguage' => ['Turkish'],
            ],
        ],
        [
            '@type' => 'WebSite',
            '@id' => url('/').'#website',
            'url' => url('/'),
            'name' => config('seo.site_name'),
            'description' => config('seo.description_default'),
            'publisher' => ['@id' => url('/').'#organization'],
            'inLanguage' => 'tr-TR',
        ],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}
</script>

@vite(['resources/css/app.css', 'resources/js/app.js'])

@stack('head')
</head>
<body class="bg-surface text-ink antialiased font-body">

<a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:top-2 focus:left-2 focus:z-50 focus:bg-primary focus:text-surface focus:px-md focus:py-sm focus:rounded-btn">
    İçeriğe atla
</a>

{{-- ──────────────────────── HEADER ──────────────────────── --}}
<header class="sticky top-0 z-40 backdrop-blur-md bg-surface/85 border-b border-border-soft">
    <div class="max-w-[1200px] mx-auto px-md h-20 flex items-center justify-between">
        <a href="{{ route('home') }}" class="flex items-center gap-sm group" aria-label="Ana sayfaya dön">
            <img src="{{ asset('images/logo.svg') }}"
                 alt="Koğ Suit Otel"
                 width="50" height="56"
                 class="h-14 w-auto transition-transform duration-500 ease-out group-hover:scale-105 group-hover:rotate-[-3deg]" />
            <div class="flex flex-col">
                <span class="font-display font-bold text-base leading-tight text-ink tracking-tight">
                    Koğ Suit <span class="text-accent-dark">Otel</span>
                </span>
                <span class="font-display text-[10px] tracking-[0.25em] uppercase text-ink-mute leading-tight mt-0.5">
                    Varto · Muş
                </span>
            </div>
        </a>

        <nav class="hidden md:flex items-center gap-md">
            @php
                $nav = [
                    ['url' => route('home'), 'label' => 'Ana Sayfa', 'active' => request()->routeIs('home')],
                    ['url' => route('rooms.index'), 'label' => 'Odalar', 'active' => request()->routeIs('rooms.*')],
                    ['url' => route('gallery.index'), 'label' => 'Galeri', 'active' => request()->routeIs('gallery.*')],
                    ['url' => route('about'), 'label' => 'Hakkımızda', 'active' => request()->routeIs('about')],
                    ['url' => route('contact'), 'label' => 'İletişim', 'active' => request()->routeIs('contact')],
                ];
            @endphp
            @foreach ($nav as $item)
                <a href="{{ $item['url'] }}"
                   @class([
                       'font-display text-sm tracking-wide font-medium transition-colors underline-grow',
                       'text-primary-dark' => $item['active'],
                       'text-ink-soft hover:text-primary' => ! $item['active'],
                   ])>
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="flex items-center gap-sm">
            <a href="{{ route('reservations.create') }}"
               class="hidden sm:inline-flex bg-primary hover:bg-primary-dark text-white font-display font-semibold tracking-wide px-md py-sm rounded-btn transition-colors text-sm">
                Rezervasyon Yap
            </a>
            {{-- Mobil menü toggle — saf JS (public site'de Alpine yok).
                 aria-expanded/aria-controls erişilebilirlik için. --}}
            <button type="button"
                    id="mobile-menu-toggle"
                    aria-label="Menüyü Aç/Kapat"
                    aria-controls="mobile-menu"
                    aria-expanded="false"
                    onclick="
                        const m = document.getElementById('mobile-menu');
                        const hidden = m.classList.toggle('hidden');
                        this.setAttribute('aria-expanded', hidden ? 'false' : 'true');
                    "
                    class="md:hidden p-sm rounded-btn hover:bg-surface-alt transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Mobile menü (basit, JS olmadan toggle) --}}
    <div id="mobile-menu" class="hidden md:hidden border-t border-border-soft bg-surface">
        <nav class="px-md py-sm flex flex-col gap-xs">
            @foreach ($nav as $item)
                <a href="{{ $item['url'] }}"
                   @class([
                       'block py-sm font-display text-sm font-medium',
                       'text-primary-dark' => $item['active'],
                       'text-ink-soft' => ! $item['active'],
                   ])>
                    {{ $item['label'] }}
                </a>
            @endforeach
            <a href="{{ route('reservations.create') }}"
               class="block mt-sm bg-primary text-white font-display font-semibold text-center px-md py-sm rounded-btn">
                Rezervasyon Yap
            </a>
        </nav>
    </div>
</header>

<main id="main">
    @yield('content')
</main>

{{-- ──────────────────────── FOOTER ──────────────────────── --}}
<footer class="bg-primary-dark text-surface mt-xl">
    <div class="max-w-[1200px] mx-auto px-md py-lg grid grid-cols-1 md:grid-cols-4 gap-md">
        <div>
            <div class="flex items-center gap-sm mb-sm">
                <img src="{{ asset('images/logo.svg') }}"
                     alt="Koğ Suit Otel"
                     width="42" height="48"
                     class="h-12 w-auto" />
                <p class="font-display font-bold text-xl tracking-tight">
                    Koğ Suit <span class="text-accent">Otel</span>
                </p>
            </div>
            <p class="text-sm text-surface/70 leading-relaxed">
                Refined Hospitality in Varto. Anadolu'nun sakin konağı, beş özenli süitle.
            </p>
        </div>
        <div>
            <p class="font-display font-semibold text-sm uppercase tracking-wider text-accent mb-sm">Menü</p>
            <ul class="space-y-xs text-sm text-surface/70">
                <li><a href="{{ route('home') }}" class="hover:text-accent transition-colors">Ana Sayfa</a></li>
                <li><a href="{{ route('rooms.index') }}" class="hover:text-accent transition-colors">Odalar</a></li>
                <li><a href="{{ route('gallery.index') }}" class="hover:text-accent transition-colors">Galeri</a></li>
                <li><a href="{{ route('about') }}" class="hover:text-accent transition-colors">Hakkımızda</a></li>
                <li><a href="{{ route('contact') }}" class="hover:text-accent transition-colors">İletişim</a></li>
                <li><a href="{{ route('faq') }}" class="hover:text-accent transition-colors">SSS</a></li>
            </ul>
        </div>
        <div>
            <p class="font-display font-semibold text-sm uppercase tracking-wider text-accent mb-sm">Yasal</p>
            <ul class="space-y-xs text-sm text-surface/70">
                <li><a href="{{ route('kvkk') }}" class="hover:text-accent transition-colors">KVKK Aydınlatma</a></li>
                <li><a href="{{ route('privacy') }}" class="hover:text-accent transition-colors">Gizlilik Sözleşmesi</a></li>
            </ul>
        </div>
        <div>
            <p class="font-display font-semibold text-sm uppercase tracking-wider text-accent mb-sm">İletişim</p>
            <ul class="space-y-xs text-sm text-surface/70">
                <li>{{ \App\Models\Setting::get('address', 'Varto, Muş') }}</li>
                <li>
                    <a href="tel:{{ \App\Models\Setting::get('phone') }}" class="hover:text-accent transition-colors">
                        {{ \App\Models\Setting::get('phone') }}
                    </a>
                </li>
                <li>
                    <a href="mailto:{{ \App\Models\Setting::get('email') }}" class="hover:text-accent transition-colors">
                        {{ \App\Models\Setting::get('email') }}
                    </a>
                </li>
            </ul>
        </div>
    </div>
    {{-- Ornament — altın ince çizgi ayraç --}}
    <div class="max-w-[1200px] mx-auto px-md">
        <div class="h-px bg-gradient-to-r from-transparent via-accent/40 to-transparent"></div>
    </div>

    <div class="border-t border-surface/10">
        <div class="max-w-[1200px] mx-auto px-md py-md flex flex-col md:flex-row justify-between items-center gap-sm">
            <p class="text-xs text-surface/55">© {{ date('Y') }} Koğ Suit Otel. Tüm hakları saklıdır.</p>
            <p class="font-display text-[10px] tracking-[0.25em] uppercase text-accent/70">Refined Hospitality in Varto</p>
            <p class="text-xs text-surface/45">
                <a href="https://vartoyazilim.com" target="_blank" rel="noopener" class="hover:text-accent transition-colors">
                    Varto Yazılım
                </a> tarafından geliştirildi
            </p>
        </div>
    </div>
</footer>

@include('partials.lightbox')

@stack('scripts')
</body>
</html>
