@extends('layouts.public')

@section('title', 'Galeri — Varto Otel Fotoğrafları')
@section('description', 'Koğ Suit Otel oda, lobi, manzara, kahvaltı ve detay fotoğrafları. Varto Muş\'taki butik konaklamamızı görsel olarak keşfedin.')

@push('head')
@include('partials.schema-breadcrumb', ['items' => [
    ['name' => 'Ana Sayfa', 'url' => route('home')],
    ['name' => 'Galeri', 'url' => route('gallery.index')],
]])
@endpush

@section('content')

<section class="py-lg md:py-xl">
    <div class="max-w-[1200px] mx-auto px-md">
        <p class="font-display text-xs tracking-[0.2em] uppercase text-accent-dark mb-sm">Galeri</p>
        <h1 class="font-display font-bold text-4xl md:text-6xl tracking-tight text-ink mb-md leading-tight">
            Otelimizden kareler
        </h1>

        <div class="flex flex-wrap gap-xs mb-md">
            <a href="{{ route('gallery.index') }}"
               @class([
                   'px-md py-xs rounded-pill text-sm font-display font-medium transition-colors',
                   'bg-primary text-white' => ! $activeCategory,
                   'bg-surface-card text-ink-soft border border-border-soft hover:border-primary' => $activeCategory,
               ])>
                Tümü
            </a>
            @foreach ($categories as $slug => $label)
                <a href="{{ route('gallery.index', ['kategori' => $slug]) }}"
                   @class([
                       'px-md py-xs rounded-pill text-sm font-display font-medium transition-colors',
                       'bg-primary text-white' => $activeCategory === $slug,
                       'bg-surface-card text-ink-soft border border-border-soft hover:border-primary' => $activeCategory !== $slug,
                   ])>
                    {{ $label }}
                </a>
            @endforeach
        </div>

        @if ($images->isEmpty())
            <div class="bg-surface-card rounded-card p-lg text-center shadow-soft">
                <p class="text-ink-mute">Bu kategoride henüz görsel eklenmemiş.</p>
            </div>
        @else
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-sm">
                @foreach ($images as $image)
                    <div class="aspect-square rounded-card overflow-hidden bg-surface-alt shadow-softer lift relative group cursor-zoom-in">
                        <img src="{{ asset('storage/'.$image->path) }}" alt="{{ $image->alt_text }}"
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 ease-out"
                             loading="lazy" />
                        <div class="absolute inset-0 bg-gradient-to-t from-ink/40 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-sm translate-y-2 group-hover:translate-y-0 opacity-0 group-hover:opacity-100 transition-all duration-500 ease-out">
                            <p class="text-surface text-xs font-display font-medium drop-shadow-md">{{ $image->alt_text }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

@endsection
