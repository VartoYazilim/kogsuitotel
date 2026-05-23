@extends('layouts.public')

@section('title', $room->name.' — Varto Otel Süit Oda')
@section('description', $room->name.' · '.$room->capacity.' kişilik · ₺'.number_format($room->base_price, 0, ',', '.').' gecelik. '.Str::limit(strip_tags($room->description), 100).' Koğ Suit Otel, Varto Muş.')

@push('head')
{{-- HotelRoom schema — rich snippet, oda fiyat + kapasite gösterimi --}}
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'HotelRoom',
    '@id' => url()->current().'#room',
    'name' => $room->name,
    'description' => Str::limit(strip_tags($room->description), 200),
    'url' => route('rooms.show', $room),
    'occupancy' => [
        '@type' => 'QuantitativeValue',
        'maxValue' => $room->capacity,
        'unitText' => 'kişi',
    ],
    'amenityFeature' => collect($room->features ?? [])->map(fn ($f) => [
        '@type' => 'LocationFeatureSpecification',
        'name' => $f,
        'value' => true,
    ])->values()->all(),
    'image' => $room->cover_image ? [url('storage/'.$room->cover_image)] : [url(config('seo.og.default_image'))],
    'containedInPlace' => ['@id' => url('/').'#hotel'],
    'offers' => [
        '@type' => 'Offer',
        'price' => (string) $room->base_price,
        'priceCurrency' => 'TRY',
        'availability' => 'https://schema.org/InStock',
        'url' => route('reservations.create', ['oda' => $room->slug]),
        'priceSpecification' => [
            '@type' => 'UnitPriceSpecification',
            'price' => (string) $room->base_price,
            'priceCurrency' => 'TRY',
            'unitText' => 'gecelik',
        ],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}
</script>

@include('partials.schema-breadcrumb', ['items' => [
    ['name' => 'Ana Sayfa', 'url' => route('home')],
    ['name' => 'Odalar', 'url' => route('rooms.index')],
    ['name' => $room->name, 'url' => route('rooms.show', $room)],
]])
@endpush

@section('content')

<section class="py-lg md:py-xl">
    <div class="max-w-[1200px] mx-auto px-md">
        <a href="{{ route('rooms.index') }}" class="text-ink-soft hover:text-primary text-sm inline-flex items-center gap-xs mb-md transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Tüm Odalar
        </a>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-lg">
            <div class="lg:col-span-2">
                <div class="aspect-[16/9] rounded-card overflow-hidden bg-gradient-to-br from-primary to-secondary mb-md">
                    @if ($room->cover_image)
                        <img src="{{ asset('storage/'.$room->cover_image) }}" alt="{{ $room->name }}"
                             class="w-full h-full object-cover" />
                    @endif
                </div>

                @if (! empty($room->gallery))
                    <div class="grid grid-cols-4 gap-xs">
                        @foreach (array_slice($room->gallery, 0, 4) as $img)
                            <div class="aspect-square rounded-btn overflow-hidden bg-surface-alt">
                                <img src="{{ asset('storage/'.$img) }}" alt="{{ $room->name }}" class="w-full h-full object-cover" loading="lazy" />
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="mt-lg">
                    <p class="font-display text-xs tracking-[0.2em] uppercase text-accent-dark mb-sm">
                        Süit · {{ $room->capacity }} Kişilik
                    </p>
                    <h1 class="font-display font-bold text-4xl md:text-6xl tracking-tight text-ink mb-md leading-tight">
                        {{ $room->name }}
                    </h1>
                    <p class="text-lg text-ink-soft leading-relaxed mb-lg">
                        {{ $room->description }}
                    </p>

                    @if (! empty($room->features))
                        <h2 class="font-display font-semibold text-xl text-ink mb-sm">Özellikler</h2>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-xs mb-lg">
                            @foreach ($room->features as $feature)
                                <div class="flex items-center gap-xs text-sm text-ink-soft">
                                    <svg class="w-4 h-4 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $feature }}
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Reservation Card --}}
            <div class="lg:col-span-1">
                <div class="bg-surface-card rounded-card p-md shadow-lift sticky top-24">
                    <p class="font-display text-[10px] tracking-[0.2em] uppercase text-accent-dark mb-xs">gecelik fiyat</p>
                    <p class="font-display font-bold text-4xl text-primary-dark mb-md">
                        ₺{{ number_format($room->base_price, 0, ',', '.') }}
                    </p>
                    <div class="space-y-xs text-sm text-ink-soft mb-md pb-md border-b border-border-soft">
                        <div class="flex justify-between">
                            <span>Kapasite</span>
                            <span class="text-ink font-medium">{{ $room->capacity }} kişi</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Giriş</span>
                            <span class="text-ink font-medium">{{ \App\Models\Setting::get('checkin_time', '14:00') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Çıkış</span>
                            <span class="text-ink font-medium">{{ \App\Models\Setting::get('checkout_time', '12:00') }}</span>
                        </div>
                    </div>
                    <a href="{{ route('reservations.create', ['oda' => $room->slug]) }}"
                       class="block w-full bg-primary hover:bg-primary-dark text-white font-display font-semibold tracking-wide text-center py-sm rounded-btn transition-colors mb-xs">
                        Bu Odayı Rezerve Et
                    </a>
                    @php
                        $waPhone = preg_replace('/\D/', '', \App\Models\Setting::get('whatsapp') ?? '');
                        $waUrl = $waPhone ? 'https://wa.me/'.(str_starts_with($waPhone, '90') ? $waPhone : '90'.ltrim($waPhone, '0')).'?text='.urlencode('Merhaba, '.$room->name.' hakkında bilgi almak istiyorum.') : null;
                    @endphp
                    @if ($waUrl)
                        <a href="{{ $waUrl }}" target="_blank" rel="noopener"
                           class="block w-full border-2 border-primary text-primary hover:bg-primary hover:text-white font-display font-semibold tracking-wide text-center py-sm rounded-btn transition-colors">
                            WhatsApp ile Sor
                        </a>
                    @endif
                </div>
            </div>
        </div>

        @if ($otherRooms->isNotEmpty())
            <div class="mt-xl pt-lg border-t border-border-soft">
                <h2 class="font-display font-bold text-2xl md:text-3xl tracking-tight text-ink mb-md">Diğer Odalar</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-sm">
                    @foreach ($otherRooms as $other)
                        <a href="{{ route('rooms.show', $other) }}"
                           class="group bg-surface-card rounded-card overflow-hidden shadow-soft hover:shadow-lift transition-all">
                            <div class="aspect-[4/3] bg-gradient-to-br from-primary-light to-secondary-light">
                                @if ($other->cover_image)
                                    <img src="{{ asset('storage/'.$other->cover_image) }}" alt="{{ $other->name }}" class="w-full h-full object-cover" loading="lazy" />
                                @endif
                            </div>
                            <div class="p-md">
                                <h3 class="font-display font-semibold text-lg text-ink mb-xs">{{ $other->name }}</h3>
                                <p class="font-display font-bold text-lg text-primary-dark">
                                    ₺{{ number_format($other->base_price, 0, ',', '.') }} <span class="text-xs text-ink-mute font-normal">/ gece</span>
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>

@endsection
