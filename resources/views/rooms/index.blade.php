@extends('layouts.public')

@section('title', 'Odalarımız — Varto Suit & Standart Oda')
@section('description', 'Varto/Muş\'ta 5 özenli oda: Standart, Suit, Aile, Deluxe ve Premium Süit. Panoramik manzara, ücretsiz Wi-Fi, açık büfe kahvaltı. Koğ Suit Otel oda fiyatları ve detayları.')

@push('head')
@include('partials.schema-breadcrumb', ['items' => [
    ['name' => 'Ana Sayfa', 'url' => route('home')],
    ['name' => 'Odalar', 'url' => route('rooms.index')],
]])
@endpush

@section('content')

<section class="py-lg md:py-xl">
    <div class="max-w-[1200px] mx-auto px-md">
        <div class="max-w-2xl mb-lg">
            <p class="font-display text-xs tracking-[0.2em] uppercase text-accent-dark mb-sm">Odalarımız</p>
            <h1 class="font-display font-bold text-4xl md:text-6xl tracking-tight text-ink mb-md leading-tight">
                Beş özenli süit
            </h1>
            <p class="text-lg text-ink-soft leading-relaxed">
                Çift kişilik standarttan panoramik manzaralı premium süite — her oda kendi karakteriyle tasarlandı.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-md">
            @foreach ($rooms as $room)
                <a href="{{ route('rooms.show', $room) }}"
                   class="group bg-surface-card rounded-card overflow-hidden shadow-soft border border-border-soft/30 lift relative">
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
                        <p class="text-sm text-ink-soft leading-relaxed mb-sm line-clamp-3">
                            {{ $room->description }}
                        </p>

                        @if (! empty($room->features))
                            <div class="flex flex-wrap gap-xs mb-sm">
                                @foreach (array_slice($room->features, 0, 4) as $feature)
                                    <span class="border border-border-soft text-ink-soft px-xs py-[2px] rounded text-[10px]">
                                        {{ $feature }}
                                    </span>
                                @endforeach
                                @if (count($room->features) > 4)
                                    <span class="text-ink-mute text-[10px] py-[2px]">+{{ count($room->features) - 4 }}</span>
                                @endif
                            </div>
                        @endif

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

@endsection
