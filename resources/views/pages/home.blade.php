@extends('layouts.public')

@section('title', 'Varto Butik Otel')
@section('description', 'Koğ Suit Otel — Muş Varto\'da 5 özenli süitle butik konaklama. Modern konfor, geleneksel sıcaklık, panoramik manzara. Varto otel rezervasyon için ücretsiz talep.')

@push('head')
{{-- Ana sayfa: Hotel + LocalBusiness (yerel SEO için en kritik schema) --}}
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => ['Hotel', 'LocalBusiness'],
    '@id' => url('/').'#hotel',
    'name' => 'Koğ Suit Otel',
    'alternateName' => 'Koğ Suit',
    'description' => 'Muş Varto\'da beş özenli süitle modern konfor ve geleneksel sıcaklığı bir araya getiren butik otel. Refined Hospitality in Varto.',
    'url' => url('/'),
    'telephone' => \App\Models\Setting::get('phone'),
    'email' => \App\Models\Setting::get('email'),
    'priceRange' => config('seo.business.price_range'),
    'currenciesAccepted' => config('seo.business.currencies_accepted'),
    'paymentAccepted' => config('seo.business.payment_accepted'),
    'address' => [
        '@type' => 'PostalAddress',
        'streetAddress' => \App\Models\Setting::get('address'),
        'addressLocality' => config('seo.geo.city'),
        'addressRegion' => config('seo.geo.province'),
        'postalCode' => config('seo.geo.postal_code'),
        'addressCountry' => config('seo.geo.country'),
    ],
    'geo' => [
        '@type' => 'GeoCoordinates',
        'latitude' => (float) config('seo.geo.latitude'),
        'longitude' => (float) config('seo.geo.longitude'),
    ],
    'openingHoursSpecification' => [[
        '@type' => 'OpeningHoursSpecification',
        'dayOfWeek' => ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'],
        'opens' => '00:00',
        'closes' => '23:59',
    ]],
    'checkinTime' => \App\Models\Setting::get('checkin_time').':00',
    'checkoutTime' => \App\Models\Setting::get('checkout_time').':00',
    'numberOfRooms' => \App\Models\Room::active()->count(),
    'amenityFeature' => [
        ['@type' => 'LocationFeatureSpecification', 'name' => 'Ücretsiz Wi-Fi', 'value' => true],
        ['@type' => 'LocationFeatureSpecification', 'name' => 'Ücretsiz Otopark', 'value' => true],
        ['@type' => 'LocationFeatureSpecification', 'name' => 'Açık Büfe Kahvaltı', 'value' => true],
        ['@type' => 'LocationFeatureSpecification', 'name' => 'Klima', 'value' => true],
        ['@type' => 'LocationFeatureSpecification', 'name' => '7/24 Resepsiyon', 'value' => true],
        ['@type' => 'LocationFeatureSpecification', 'name' => 'WhatsApp İletişim', 'value' => true],
    ],
    'image' => [url(config('seo.og.default_image'))],
    'hasMap' => \App\Models\Setting::get('google_maps_url') ?: null,
    'areaServed' => [
        '@type' => 'AdministrativeArea',
        'name' => 'Varto, Muş',
    ],
    'isAcceptingNewCustomers' => true,
    'knowsLanguage' => ['Turkish'],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}
</script>
@endpush

@section('content')

{{-- ─────────── HERO ─────────── --}}
<section class="relative overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-br from-primary-dark via-primary to-secondary"></div>
    <div class="absolute inset-0 grain opacity-30"></div>

    <div class="relative z-10 max-w-[1200px] mx-auto px-md py-xl md:py-[120px] grid grid-cols-1 md:grid-cols-5 gap-md items-center">
        <div class="md:col-span-3 text-surface">
            <p class="font-display text-xs tracking-[0.2em] uppercase text-accent mb-sm">
                Refined Hospitality in Varto
            </p>
            <h1 class="font-display font-bold text-5xl md:text-7xl leading-[1.02] tracking-[-0.03em] mb-md">
                Anadolu'nun<br />
                <span class="text-accent italic font-serif" style="font-family: Georgia, 'Times New Roman', serif;">sakin konağı</span>
            </h1>
            <p class="text-lg md:text-xl leading-relaxed opacity-90 mb-lg max-w-2xl">
                Varto'nun yeşil tepelerinde, beş özenli süitle huzurlu bir kaçışa davetlisiniz.
                Modern konfor, geleneksel sıcaklık.
            </p>
            <div class="flex flex-wrap gap-sm">
                <a href="{{ route('reservations.create') }}"
                   class="bg-accent-dark hover:bg-accent text-white font-display font-semibold tracking-wide px-md py-sm rounded-btn transition-colors inline-flex items-center gap-xs">
                    Rezervasyon Yap
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </a>
                <a href="{{ route('rooms.index') }}"
                   class="border-2 border-surface/30 text-surface hover:bg-surface/10 font-display font-semibold tracking-wide px-md py-sm rounded-btn transition-colors">
                    Odaları Keşfet
                </a>
            </div>
        </div>

        {{-- Hızlı Rezervasyon Form'u --}}
        <div class="md:col-span-2 bg-surface-card rounded-card p-md shadow-lift">
            <p class="font-display font-semibold text-lg text-ink mb-sm">Hızlı Müsaitlik</p>
            <form action="{{ route('reservations.create') }}" method="GET" class="space-y-sm">
                <div class="grid grid-cols-2 gap-sm">
                    <div>
                        <label class="block font-display text-[10px] tracking-[0.2em] uppercase text-ink-soft mb-xs">Giriş</label>
                        <input type="text" name="check_in" id="hero-checkin"
                               data-fp-simple data-fp-linked-to="#hero-checkout"
                               placeholder="Tarih Seçiniz"
                               autocomplete="off"
                               class="w-full bg-surface border border-border-soft focus:bg-surface-card focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none rounded-btn px-sm py-sm text-sm transition cursor-pointer" />
                    </div>
                    <div>
                        <label class="block font-display text-[10px] tracking-[0.2em] uppercase text-ink-soft mb-xs">Çıkış</label>
                        <input type="text" name="check_out" id="hero-checkout"
                               data-fp-simple
                               placeholder="Tarih Seçiniz"
                               autocomplete="off"
                               class="w-full bg-surface border border-border-soft focus:bg-surface-card focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none rounded-btn px-sm py-sm text-sm transition cursor-pointer" />
                    </div>
                </div>
                <div>
                    <label for="hero-room" class="block font-display text-[10px] tracking-[0.2em] uppercase text-ink-soft mb-xs">Oda</label>
                    <select id="hero-room" name="oda" class="w-full bg-surface border border-border-soft focus:bg-surface-card focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none rounded-btn px-sm py-sm text-sm transition">
                        <option value="">Tümünü Göster</option>
                        @foreach (\App\Models\Room::active()->ordered()->get() as $r)
                            <option value="{{ $r->slug }}">{{ $r->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit"
                        class="w-full bg-primary hover:bg-primary-dark text-white font-display font-semibold tracking-wide py-sm rounded-btn transition-colors">
                    Müsaitlik Kontrol Et
                </button>
            </form>
        </div>
    </div>
</section>

{{-- ─────────── ÖZELLİKLER ─────────── --}}
<section class="py-lg md:py-xl">
    <div class="max-w-[1200px] mx-auto px-md">
        <div class="mb-lg max-w-2xl">
            <p class="font-display text-xs tracking-[0.2em] uppercase text-accent-dark mb-sm">Ayrıcalıklarımız</p>
            <h2 class="font-display font-bold text-3xl md:text-5xl tracking-tight text-ink mb-sm">
                Sizin için düşündüklerimiz
            </h2>
            <p class="text-ink-soft leading-relaxed">
                Konforunuzu önceleyen detaylarla konaklamanızı sıcak ve sade kılıyoruz.
            </p>
        </div>

        @php
            $featureIcons = [
                'map-pin' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>',
                'wifi' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/></svg>',
                'chat' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>',
                'car' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1"/></svg>',
                'coffee' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.24 17 7.07c2.5 0 4.5 1.5 4.5 4.5 0 1.5-1 4-3.843 7.087z"/></svg>',
                'sparkles' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>',
            ];

            $features = [
                ['icon' => 'map-pin', 'title' => 'Sakin Konum', 'desc' => 'Varto\'nun yeşil tepelerinde, şehir gürültüsünden uzak bir kaçış.'],
                ['icon' => 'wifi', 'title' => 'Yüksek Hızlı Wi-Fi', 'desc' => 'İş veya eğlence için kesintisiz, ücretsiz bağlantı.'],
                ['icon' => 'chat', 'title' => '7/24 WhatsApp', 'desc' => 'Tüm sorularınız için hızlı ve doğrudan iletişim.'],
                ['icon' => 'car', 'title' => 'Güvenli Otopark', 'desc' => 'Otelimizin yanında kameralı, ücretsiz misafir otoparkı.'],
                ['icon' => 'coffee', 'title' => 'Sıcak Kahvaltı', 'desc' => 'Yöresel ürünlerle her sabah açık büfe kahvaltı.'],
                ['icon' => 'sparkles', 'title' => 'Özenli Hizmet', 'desc' => 'Her detayda misafirperverliğin sıcaklığını hissedeceksiniz.'],
            ];
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-3 gap-sm">
            @foreach ($features as $f)
                <div class="group/feat bg-surface-card rounded-card p-md shadow-soft border border-border-soft/30 lift">
                    <div class="w-12 h-12 rounded-pill bg-primary-soft flex items-center justify-center mb-sm text-primary-dark transition-transform duration-500 ease-out group-hover/feat:scale-110 group-hover/feat:rotate-[-6deg]">
                        {!! $featureIcons[$f['icon']] ?? '' !!}
                    </div>
                    <h3 class="font-display font-semibold text-lg text-ink mb-xs tracking-tight">{{ $f['title'] }}</h3>
                    <p class="text-sm text-ink-soft leading-relaxed">{{ $f['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ─────────── ODALAR ÖNİZLEME ─────────── --}}
<section class="py-lg md:py-xl bg-surface-alt/40">
    <div class="max-w-[1200px] mx-auto px-md">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-lg gap-md">
            <div class="max-w-2xl">
                <p class="font-display text-xs tracking-[0.2em] uppercase text-accent-dark mb-sm">Odalarımız</p>
                <h2 class="font-display font-bold text-3xl md:text-5xl tracking-tight text-ink mb-sm">
                    Her zevke özel
                </h2>
                <p class="text-ink-soft leading-relaxed">
                    Çift kişilik standarttan, panoramik manzaralı premium süite — her oda kendi karakteriyle tasarlandı.
                </p>
            </div>
            <a href="{{ route('rooms.index') }}"
               class="text-primary hover:text-primary-dark font-display font-semibold tracking-wide text-sm inline-flex items-center gap-xs underline-grow whitespace-nowrap">
                Tüm Odaları Gör
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-sm">
            @foreach ($rooms as $room)
                <a href="{{ route('rooms.show', $room) }}"
                   class="group bg-surface-card rounded-card overflow-hidden shadow-soft border border-border-soft/30 lift">
                    <div class="aspect-[4/3] bg-gradient-to-br from-primary-light to-secondary-light relative overflow-hidden">
                        @if ($room->cover_image)
                            <img src="{{ asset('storage/'.$room->cover_image) }}" alt="{{ $room->name }}"
                                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy" />
                        @endif
                        <div class="absolute top-sm left-sm">
                            <span class="bg-surface/90 text-primary-dark text-xs font-display font-semibold px-sm py-xs rounded-pill">
                                {{ $room->capacity }} Kişi
                            </span>
                        </div>
                    </div>
                    <div class="p-md">
                        <h3 class="font-display font-semibold text-xl text-ink mb-xs">{{ $room->name }}</h3>
                        <p class="text-sm text-ink-soft leading-relaxed mb-sm line-clamp-2">
                            {{ Str::limit($room->description, 80) }}
                        </p>
                        <div class="flex items-end justify-between pt-sm border-t border-border-soft">
                            <div>
                                <p class="font-display text-[10px] tracking-[0.2em] uppercase text-ink-mute">gecelik</p>
                                <p class="font-display font-bold text-xl text-primary-dark">
                                    ₺{{ number_format($room->base_price, 0, ',', '.') }}
                                </p>
                            </div>
                            <span class="text-primary group-hover:text-primary-dark font-display font-semibold text-sm tracking-wide inline-flex items-center gap-xs transition-colors">
                                Detay
                                <svg class="w-4 h-4 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- ─────────── HİKAYE ─────────── --}}
<section class="py-lg md:py-xl">
    <div class="max-w-[1200px] mx-auto px-md grid grid-cols-1 md:grid-cols-2 gap-lg items-center">
        <div>
            <p class="font-display text-xs tracking-[0.2em] uppercase text-accent-dark mb-sm">Hikayemiz</p>
            <h2 class="font-display font-bold text-3xl md:text-5xl tracking-tight text-ink mb-md leading-tight">
                Varto'nun yeşil sessizliğinde<br/>
                <span class="text-primary">küçük bir konak.</span>
            </h2>
            <p class="text-ink-soft leading-relaxed mb-sm">
                Koğ Suit Otel, Muş Varto'nun doğa ile iç içe geçtiği bir köşesinde — beş özenle tasarlanmış süitle —
                misafirlerine sıcak, kişisel ve sakin bir konaklama sunar.
            </p>
            <p class="text-ink-soft leading-relaxed mb-md">
                Modern minimalizmi geleneksel Anadolu sıcaklığıyla harmanlayan tasarım anlayışımız, her odada
                kendini gösterir: doğal tonlar, kaliteli tekstiller, ferah alanlar.
            </p>
            <a href="{{ route('about') }}"
               class="text-primary hover:text-primary-dark font-display font-semibold tracking-wide text-sm inline-flex items-center gap-xs underline-grow">
                Hakkımızda Daha Fazla
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>
        <div class="aspect-[4/3] rounded-card bg-gradient-to-br from-primary via-secondary to-accent shadow-lift relative overflow-hidden">
            <div class="absolute inset-0 grain opacity-20"></div>
        </div>
    </div>
</section>

{{-- ─────────── GALERİ ÖNİZLEME ─────────── --}}
<section class="py-lg md:py-xl bg-surface-alt/40">
    <div class="max-w-[1200px] mx-auto px-md">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-lg gap-md">
            <div class="max-w-2xl">
                <p class="font-display text-xs tracking-[0.2em] uppercase text-accent-dark mb-sm">Galeri</p>
                <h2 class="font-display font-bold text-3xl md:text-5xl tracking-tight text-ink mb-sm">
                    Otelimizden kareler
                </h2>
            </div>
            <a href="{{ route('gallery.index') }}"
               class="text-primary hover:text-primary-dark font-display font-semibold tracking-wide text-sm inline-flex items-center gap-xs underline-grow whitespace-nowrap">
                Galeriyi Aç
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>

        @php
            $gradientPalette = [
                'bg-gradient-to-br from-primary to-secondary',
                'bg-gradient-to-br from-secondary-light to-accent',
                'bg-gradient-to-br from-primary-light to-primary-soft',
                'bg-gradient-to-br from-accent to-accent-dark',
                'bg-gradient-to-br from-primary-dark to-secondary',
            ];
        @endphp
        <div class="grid grid-cols-2 md:grid-cols-4 gap-sm h-[400px]">
            <div class="col-span-2 row-span-2 rounded-card {{ $gradientPalette[0] }} shadow-soft overflow-hidden">
                @if ($galleryPreview->isNotEmpty() && $galleryPreview[0]->path)
                    <img src="{{ asset('storage/'.$galleryPreview[0]->path) }}" alt="{{ $galleryPreview[0]->alt_text }}"
                         class="w-full h-full object-cover" loading="lazy" />
                @endif
            </div>
            @for ($i = 1; $i <= 4; $i++)
                <div class="rounded-card {{ $gradientPalette[$i] }} shadow-soft overflow-hidden">
                    @if (isset($galleryPreview[$i]) && $galleryPreview[$i]->path)
                        <img src="{{ asset('storage/'.$galleryPreview[$i]->path) }}" alt="{{ $galleryPreview[$i]->alt_text }}"
                             class="w-full h-full object-cover" loading="lazy" />
                    @endif
                </div>
            @endfor
        </div>
    </div>
</section>

{{-- ─────────── CTA ─────────── --}}
<section class="py-lg md:py-xl">
    <div class="max-w-[1200px] mx-auto px-md">
        <div class="bg-primary-dark text-surface rounded-card p-lg md:p-xl text-center shadow-lift relative overflow-hidden">
            <div class="absolute inset-0 grain opacity-20"></div>
            <div class="relative z-10 max-w-2xl mx-auto">
                <p class="font-display text-xs tracking-[0.2em] uppercase text-accent mb-sm">Hoş Geldiniz</p>
                <h2 class="font-display font-bold text-3xl md:text-5xl tracking-tight mb-md leading-tight">
                    Sizi Varto'da<br />ağırlamak için sabırsızlanıyoruz.
                </h2>
                <p class="text-surface/80 leading-relaxed mb-lg">
                    Rezervasyon için bir kaç dakikanızı ayırmanız yeterli. Sorularınız için WhatsApp üzerinden 7/24 buradayız.
                </p>
                <div class="flex flex-wrap gap-sm justify-center">
                    <a href="{{ route('reservations.create') }}"
                       class="bg-accent hover:bg-accent-dark text-surface font-display font-semibold tracking-wide px-md py-sm rounded-btn transition-colors">
                        Rezervasyon Yap
                    </a>
                    @php
                        $whatsapp = \App\Models\Setting::get('whatsapp');
                        $waPhone = $whatsapp ? preg_replace('/\D/', '', $whatsapp) : null;
                        $waUrl = $waPhone ? 'https://wa.me/'.(str_starts_with($waPhone, '90') ? $waPhone : '90'.ltrim($waPhone, '0')) : null;
                    @endphp
                    @if ($waUrl)
                        <a href="{{ $waUrl }}" target="_blank" rel="noopener"
                           class="border-2 border-surface/30 text-surface hover:bg-surface/10 font-display font-semibold tracking-wide px-md py-sm rounded-btn transition-colors inline-flex items-center gap-xs">
                            WhatsApp ile Yaz
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
