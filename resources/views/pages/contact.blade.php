@extends('layouts.public')

@section('title', 'İletişim — Telefon, WhatsApp, Adres')
@section('description', 'Koğ Suit Otel iletişim bilgileri: Varto/Muş adresimiz, telefon, 7/24 WhatsApp hattı, e-posta ve giriş-çıkış saatleri. Sorularınız için doğrudan ulaşın.')

@push('head')
@include('partials.schema-breadcrumb', ['items' => [
    ['name' => 'Ana Sayfa', 'url' => route('home')],
    ['name' => 'İletişim', 'url' => route('contact')],
]])
@endpush

@section('content')

<section class="py-lg md:py-xl">
    <div class="max-w-[1100px] mx-auto px-md">
        <p class="font-display text-xs tracking-[0.2em] uppercase text-accent-dark mb-sm">İletişim</p>
        <h1 class="font-display font-bold text-4xl md:text-6xl tracking-tight text-ink mb-md leading-tight">
            Bize ulaşın
        </h1>
        <p class="text-lg text-ink-soft leading-relaxed mb-lg max-w-2xl">
            Her sorunuz, her özel isteğiniz için yanınızdayız. WhatsApp üzerinden 7/24 hızlı yanıt alabilirsiniz.
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-md">
            <div class="bg-surface-card rounded-card p-md shadow-soft">
                <div class="w-12 h-12 rounded-pill bg-primary-soft flex items-center justify-center mb-sm text-primary-dark">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                </div>
                <h3 class="font-display font-semibold text-lg text-ink mb-xs">Telefon</h3>
                <a href="tel:{{ $settings['phone'] ?? '' }}" class="text-primary hover:text-primary-dark font-display font-medium underline-grow">
                    {{ $settings['phone'] ?? '—' }}
                </a>
            </div>

            <div class="bg-surface-card rounded-card p-md shadow-soft">
                <div class="w-12 h-12 rounded-pill bg-primary-soft flex items-center justify-center mb-sm text-primary-dark">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                </div>
                <h3 class="font-display font-semibold text-lg text-ink mb-xs">WhatsApp</h3>
                @php
                    $waPhone = preg_replace('/\D/', '', $settings['whatsapp'] ?? '');
                    $waUrl = $waPhone ? 'https://wa.me/'.(str_starts_with($waPhone, '90') ? $waPhone : '90'.ltrim($waPhone, '0')) : null;
                @endphp
                <a href="{{ $waUrl ?? '#' }}" target="_blank" rel="noopener" class="text-primary hover:text-primary-dark font-display font-medium underline-grow">
                    {{ $settings['whatsapp'] ?? '—' }}
                </a>
            </div>

            <div class="bg-surface-card rounded-card p-md shadow-soft">
                <div class="w-12 h-12 rounded-pill bg-primary-soft flex items-center justify-center mb-sm text-primary-dark">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="font-display font-semibold text-lg text-ink mb-xs">E-posta</h3>
                <a href="mailto:{{ $settings['email'] ?? '' }}" class="text-primary hover:text-primary-dark font-display font-medium underline-grow">
                    {{ $settings['email'] ?? '—' }}
                </a>
            </div>

            <div class="bg-surface-card rounded-card p-md shadow-soft">
                <div class="w-12 h-12 rounded-pill bg-primary-soft flex items-center justify-center mb-sm text-primary-dark">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <h3 class="font-display font-semibold text-lg text-ink mb-xs">Adres</h3>
                <p class="text-ink-soft">{{ $settings['address'] ?? 'Varto, Muş' }}</p>
            </div>
        </div>

        <div class="mt-lg pt-md border-t border-border-soft">
            <p class="font-display text-xs tracking-[0.2em] uppercase text-accent-dark mb-md">Konaklama Saatleri</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-md">
                <div class="bg-surface-card rounded-card p-md shadow-softer">
                    <p class="font-display text-xs tracking-[0.2em] uppercase text-ink-mute mb-xs">Giriş Saati</p>
                    <p class="font-display font-semibold text-2xl text-ink mb-xs">{{ $settings['checkin_time'] ?? '14:00' }}</p>
                    <p class="text-xs text-ink-soft leading-relaxed">
                        Misafirlerimizi belirtilen saatten itibaren ağırlıyoruz. Erken giriş için lütfen önceden bilgi verin.
                    </p>
                </div>
                <div class="bg-surface-card rounded-card p-md shadow-softer">
                    <p class="font-display text-xs tracking-[0.2em] uppercase text-ink-mute mb-xs">Çıkış Saati</p>
                    <p class="font-display font-semibold text-2xl text-ink mb-xs">{{ $settings['checkout_time'] ?? '12:00' }}</p>
                    <p class="text-xs text-ink-soft leading-relaxed">
                        Odanızı belirtilen saate kadar boşaltmanızı rica ederiz. Geç çıkış için talep iletebilirsiniz.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
