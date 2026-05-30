@extends('layouts.public')

@section('title', 'Hakkımızda — Varto\'nun Butik Konağı')
@section('description', 'Koğ Suit Otel: Muş Varto\'da 5 odalı butik otel. Sade tasarım, sıcak karşılama, rahat konaklama. Hikayemiz ve Varto hakkında bilgi.')

@push('head')
@include('partials.schema-breadcrumb', ['items' => [
    ['name' => 'Ana Sayfa', 'url' => route('home')],
    ['name' => 'Hakkımızda', 'url' => route('about')],
]])
@endpush

@section('content')

<section class="py-lg md:py-xl">
    <div class="max-w-[900px] mx-auto px-md">
        <p class="font-display text-xs tracking-[0.2em] uppercase text-accent-dark mb-sm">Hakkımızda</p>
        <h1 class="font-display font-bold text-4xl md:text-6xl tracking-tight text-ink mb-md leading-tight">
            Varto'nun küçük konağı,<br />
            <span class="text-primary">samimi bir kaçış.</span>
        </h1>

        @if (! empty($settings['about_intro']))
            <div class="text-ink-soft leading-relaxed mb-md space-y-md">
                @foreach (preg_split("/\n\s*\n/", trim($settings['about_intro'])) as $i => $p)
                    <p class="{{ $i === 0 ? 'text-lg' : '' }}">{{ $p }}</p>
                @endforeach
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-md mt-lg">
            <div class="bg-surface-card rounded-card p-md shadow-soft">
                <p class="font-display font-bold text-3xl text-primary mb-xs">{{ $settings['about_stat_1_value'] ?? '5' }}</p>
                <p class="text-sm text-ink-soft">{{ $settings['about_stat_1_label'] ?? 'Konforlu Oda' }}</p>
            </div>
            <div class="bg-surface-card rounded-card p-md shadow-soft">
                <p class="font-display font-bold text-3xl text-primary mb-xs">{{ $settings['about_stat_2_value'] ?? '24/7' }}</p>
                <p class="text-sm text-ink-soft">{{ $settings['about_stat_2_label'] ?? 'WhatsApp Destek' }}</p>
            </div>
            <div class="bg-surface-card rounded-card p-md shadow-soft">
                <p class="font-display font-bold text-3xl text-primary mb-xs">{{ $settings['about_stat_3_value'] ?? '100%' }}</p>
                <p class="text-sm text-ink-soft">{{ $settings['about_stat_3_label'] ?? 'Misafir Memnuniyeti Odağı' }}</p>
            </div>
        </div>

        @if (! empty($settings['about_vision']))
            <div class="mt-xl pt-lg border-t border-border-soft">
                <h2 class="font-display font-bold text-2xl md:text-3xl tracking-tight text-ink mb-md">Vizyonumuz</h2>
                <p class="text-ink-soft leading-relaxed whitespace-pre-line">{{ $settings['about_vision'] }}</p>
            </div>
        @endif

        {{-- Varto Bölge Bilgisi — yerel SEO için bölgesel ilgi sinyali --}}
        @if (! empty($settings['about_varto_region']))
            <div class="mt-xl pt-lg border-t border-border-soft">
                <h2 class="font-display font-bold text-2xl md:text-3xl tracking-tight text-ink mb-md">Varto Hakkında</h2>
                <div class="text-ink-soft leading-relaxed space-y-md">
                    @foreach (preg_split("/\n\s*\n/", trim($settings['about_varto_region'])) as $p)
                        <p>{{ $p }}</p>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="mt-xl pt-lg border-t border-border-soft text-center">
            <p class="font-display text-xs tracking-[0.2em] uppercase text-accent-dark mb-sm">Rezervasyon</p>
            <p class="text-ink-soft mb-md max-w-2xl mx-auto">
                Varto'da konaklama için doğru adres. Online talep bırakın, biz size birkaç dakika
                içinde dönelim. WhatsApp ile de iletişime geçebilirsiniz.
            </p>
            <a href="{{ route('reservations.create') }}"
               class="inline-flex items-center gap-xs bg-primary hover:bg-primary-dark text-white font-display font-semibold tracking-wide px-md py-sm rounded-btn transition-colors">
                Rezervasyon Yap
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>
    </div>
</section>

@endsection
