@extends('layouts.public')

@section('title', 'Rezervasyon Alındı')
@section('description', 'Rezervasyon talebiniz başarıyla iletildi.')

@push('head')
{{-- Bu sayfa rezervasyon kodu içerir, indexlenmez --}}
<meta name="robots" content="noindex, nofollow" />
@endpush

@section('content')

<section class="py-lg md:py-xl">
    <div class="max-w-[800px] mx-auto px-md">
        <div class="bg-success/10 border border-success/30 rounded-card p-md mb-md flex items-center gap-sm">
            <div class="w-10 h-10 rounded-pill bg-success text-surface flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div>
                <p class="font-display font-semibold text-success">Rezervasyon talebiniz alındı.</p>
                <p class="text-sm text-ink-soft">Aşağıdaki bilgileri kontrol edip havale işlemini başlatabilirsiniz.</p>
            </div>
        </div>

        <div class="bg-surface-card rounded-card p-md md:p-lg shadow-soft mb-md">
            <div class="flex items-center justify-between mb-md pb-md border-b border-border-soft">
                <div>
                    <p class="font-display text-xs tracking-[0.2em] uppercase text-accent-dark mb-xs">Rezervasyon Kodu</p>
                    <p class="font-display font-bold text-3xl text-primary-dark">{{ $reservation->reservation_code }}</p>
                </div>
                <span class="bg-primary-soft text-primary-dark px-sm py-xs rounded-pill text-xs font-display font-semibold">
                    Bekliyor
                </span>
            </div>

            <h2 class="font-display font-semibold text-xl text-ink mb-sm">Konaklama Özeti</h2>
            <div class="grid grid-cols-2 gap-sm text-sm mb-md">
                <div>
                    <p class="text-ink-mute text-xs uppercase tracking-wider">Oda</p>
                    <p class="text-ink font-medium">{{ $reservation->room->name }}</p>
                </div>
                <div>
                    <p class="text-ink-mute text-xs uppercase tracking-wider">Misafir</p>
                    <p class="text-ink font-medium">{{ $reservation->guest_full_name }}</p>
                </div>
                <div>
                    <p class="text-ink-mute text-xs uppercase tracking-wider">Giriş</p>
                    <p class="text-ink font-medium">{{ $reservation->check_in->format('d.m.Y') }}</p>
                </div>
                <div>
                    <p class="text-ink-mute text-xs uppercase tracking-wider">Çıkış</p>
                    <p class="text-ink font-medium">{{ $reservation->check_out->format('d.m.Y') }}</p>
                </div>
                <div>
                    <p class="text-ink-mute text-xs uppercase tracking-wider">Gece</p>
                    <p class="text-ink font-medium">{{ $reservation->nights }} gece</p>
                </div>
                <div>
                    <p class="text-ink-mute text-xs uppercase tracking-wider">Kişi</p>
                    <p class="text-ink font-medium">{{ $reservation->adults }} yetişkin{{ $reservation->children > 0 ? ', '.$reservation->children.' çocuk' : '' }}</p>
                </div>
            </div>

            <div class="bg-surface-alt rounded-btn p-sm flex items-end justify-between">
                <span class="text-sm text-ink-soft">Tahmini Toplam Tutar</span>
                <span class="font-display font-bold text-2xl text-primary-dark">
                    ₺{{ number_format($reservation->total_price, 2, ',', '.') }}
                </span>
            </div>
        </div>

        {{-- IBAN --}}
        <div class="bg-primary-dark text-surface rounded-card p-md md:p-lg shadow-lift mb-md">
            <p class="font-display text-xs tracking-[0.2em] uppercase text-accent mb-sm">Ödeme Bilgileri</p>
            <h2 class="font-display font-semibold text-2xl mb-md">Havale / EFT</h2>

            <div class="space-y-sm">
                <div>
                    <p class="text-xs text-surface/60 mb-xs uppercase tracking-wider">IBAN</p>
                    <p class="font-mono font-medium text-lg select-all break-all">{{ $iban ?? '—' }}</p>
                </div>
                <div class="grid grid-cols-2 gap-sm pt-sm border-t border-surface/10">
                    <div>
                        <p class="text-xs text-surface/60 mb-xs uppercase tracking-wider">Hesap Sahibi</p>
                        <p class="font-medium">{{ $ibanHolder ?? 'Koğ Suit Otel' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-surface/60 mb-xs uppercase tracking-wider">Banka</p>
                        <p class="font-medium">{{ $bankName ?? '—' }}</p>
                    </div>
                </div>
                <div class="pt-sm border-t border-surface/10">
                    <p class="text-xs text-surface/60 mb-xs uppercase tracking-wider">Açıklama</p>
                    <p class="font-mono font-medium">{{ $reservation->reservation_code }}</p>
                </div>
            </div>
        </div>

        {{-- Sonraki Adımlar --}}
        <div class="bg-surface-card rounded-card p-md md:p-lg shadow-soft mb-md">
            <h2 class="font-display font-semibold text-xl text-ink mb-md">Sonraki Adımlar</h2>
            <ol class="space-y-sm text-sm">
                <li class="flex gap-sm">
                    <span class="w-6 h-6 rounded-pill bg-primary-soft text-primary-dark font-display font-bold text-xs flex items-center justify-center shrink-0">1</span>
                    <span class="text-ink-soft">Yukarıdaki IBAN'a tutarı havale/EFT olarak gönderin. Açıklama kısmına rezervasyon kodunuzu (<strong class="font-mono text-ink">{{ $reservation->reservation_code }}</strong>) ekleyin.</span>
                </li>
                <li class="flex gap-sm">
                    <span class="w-6 h-6 rounded-pill bg-primary-soft text-primary-dark font-display font-bold text-xs flex items-center justify-center shrink-0">2</span>
                    <span class="text-ink-soft">Dekontu WhatsApp üzerinden bize iletin:
                        @php
                            $waPhone = preg_replace('/\D/', '', $whatsapp ?? '');
                            $waUrl = $waPhone ? 'https://wa.me/'.(str_starts_with($waPhone, '90') ? $waPhone : '90'.ltrim($waPhone, '0')).'?text='.urlencode("Merhaba, {$reservation->reservation_code} kodlu rezervasyonum için dekontu gönderiyorum.") : null;
                        @endphp
                        @if ($waUrl)
                            <a href="{{ $waUrl }}" target="_blank" rel="noopener" class="text-primary font-semibold underline-grow">{{ $whatsapp }}</a>
                        @endif
                    </span>
                </li>
                <li class="flex gap-sm">
                    <span class="w-6 h-6 rounded-pill bg-primary-soft text-primary-dark font-display font-bold text-xs flex items-center justify-center shrink-0">3</span>
                    <span class="text-ink-soft">Dekont onayı sonrası rezervasyonunuz kesinleştirilir ve size onay mesajı gönderilir.</span>
                </li>
            </ol>
        </div>

        <div class="text-center">
            <a href="{{ route('home') }}" class="text-primary hover:text-primary-dark font-display font-semibold text-sm inline-flex items-center gap-xs underline-grow">
                Ana Sayfaya Dön
            </a>
        </div>
    </div>
</section>

@endsection
