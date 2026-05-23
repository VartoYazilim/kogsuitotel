@extends('layouts.public')

@section('title', 'Rezervasyon — Varto Otel Online Talep')
@section('description', 'Koğ Suit Otel\'de online rezervasyon talebi oluşturun. Varto Muş\'ta beş süit arasından seçin, IBAN ile havale ile kolayca rezervasyon yapın.')

@push('head')
@include('partials.schema-breadcrumb', ['items' => [
    ['name' => 'Ana Sayfa', 'url' => route('home')],
    ['name' => 'Rezervasyon', 'url' => route('reservations.create')],
]])
@endpush

@section('content')

<section class="py-lg md:py-xl">
    <div class="max-w-[1100px] mx-auto px-md">
        <a href="{{ url()->previous() }}" class="text-ink-soft hover:text-primary text-sm inline-flex items-center gap-xs mb-md transition-colors underline-grow">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Geri Dön
        </a>

        <div class="max-w-2xl mb-lg">
            <p class="font-display text-xs tracking-[0.2em] uppercase text-accent-dark mb-sm">Rezervasyon</p>
            <h1 class="font-display font-bold text-4xl md:text-6xl tracking-tight text-ink mb-md leading-[1.05]">
                Sizi ağırlamaktan<br /><span class="text-primary">mutluluk duyarız</span>
            </h1>
            <p class="text-ink-soft leading-relaxed">
                Bu aşamada hiçbir ücret tahsil edilmez. Talebiniz alındığında size IBAN bilgilerimizi
                ileteceğiz; havale ve WhatsApp dekontu sonrası rezervasyonunuz kesinleşir.
            </p>
        </div>

        @if ($errors->any())
            <div role="alert" class="bg-error/8 border-l-4 border-error rounded-card p-md mb-md shadow-softer">
                <p class="font-display font-semibold text-error mb-xs flex items-center gap-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Lütfen aşağıdaki hataları düzeltin:
                </p>
                <ul class="text-sm text-error space-y-xs list-disc pl-md">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('reservations.store') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-3 gap-md">
            @csrf

            {{-- Honeypot — bot koruma --}}
            <div class="hidden" aria-hidden="true">
                <label>Website (boş bırakın)</label>
                <input type="text" name="website" tabindex="-1" autocomplete="off" />
            </div>

            <div class="lg:col-span-2 space-y-md">

                {{-- ─────── 1. Oda & Tarih ─────── --}}
                <div class="bg-surface-card rounded-card p-md md:p-lg shadow-soft border border-border-soft/40 lift">
                    <div class="flex items-center gap-sm mb-md pb-sm border-b border-border-soft">
                        <span class="w-8 h-8 rounded-pill bg-primary text-white flex items-center justify-center font-display font-bold text-sm shadow-softer">1</span>
                        <div>
                            <h2 class="font-display font-semibold text-xl text-ink leading-tight">Oda &amp; Tarih</h2>
                            <p class="font-display text-[10px] tracking-[0.2em] uppercase text-ink-mute mt-0.5">Önce odanızı seçin, ardından tarihleri belirleyin</p>
                        </div>
                    </div>

                    {{-- Oda Seçimi --}}
                    <div class="mb-sm">
                        <label for="room_id" class="block font-display text-[10px] tracking-[0.2em] uppercase text-ink-soft mb-xs">Oda Seçimi *</label>
                        <select id="room_id" name="room_id" required
                                data-fp-room-select
                                class="w-full bg-surface border border-border-soft focus:bg-surface-card focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none rounded-btn px-sm py-sm transition appearance-none cursor-pointer">
                            <option value="">— Oda seçin —</option>
                            @foreach ($rooms as $r)
                                <option value="{{ $r->id }}"
                                        data-slug="{{ $r->slug }}"
                                        data-price="{{ $r->base_price }}"
                                        data-name="{{ $r->name }}"
                                        {{ old('room_id', $selectedRoom?->id) == $r->id ? 'selected' : '' }}>
                                    {{ $r->name }} · ₺{{ number_format($r->base_price, 0, ',', '.') }} / gece · {{ $r->capacity }} kişi
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-ink-mute mt-xs flex items-center gap-xs">
                            <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Oda seçtiğinizde tarih takviminde dolu günler gri görünür.
                        </p>
                    </div>

                    {{-- Tarihler --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-sm mb-sm">
                        <div>
                            <label for="check_in" class="block font-display text-[10px] tracking-[0.2em] uppercase text-ink-soft mb-xs">Giriş Tarihi *</label>
                            <input type="text" id="check_in" name="check_in" value="{{ old('check_in', $prefillCheckIn ?? '') }}" required
                                   data-fp-checkin
                                   placeholder="Tarih Seçiniz"
                                   autocomplete="off"
                                   class="w-full bg-surface border border-border-soft focus:bg-surface-card focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none rounded-btn px-sm py-sm transition cursor-pointer" />
                        </div>
                        <div>
                            <label for="check_out" class="block font-display text-[10px] tracking-[0.2em] uppercase text-ink-soft mb-xs">Çıkış Tarihi *</label>
                            <input type="text" id="check_out" name="check_out" value="{{ old('check_out', $prefillCheckOut ?? '') }}" required
                                   data-fp-checkout
                                   placeholder="Tarih Seçiniz"
                                   autocomplete="off"
                                   class="w-full bg-surface border border-border-soft focus:bg-surface-card focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none rounded-btn px-sm py-sm transition cursor-pointer" />
                        </div>
                    </div>

                    {{-- Kişi Sayıları --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-sm">
                        <div>
                            <label for="adults" class="block font-display text-[10px] tracking-[0.2em] uppercase text-ink-soft mb-xs">Yetişkin Sayısı *</label>
                            <select id="adults" name="adults" required
                                    class="w-full bg-surface border border-border-soft focus:bg-surface-card focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none rounded-btn px-sm py-sm transition cursor-pointer">
                                @for ($i = 1; $i <= 6; $i++)
                                    <option value="{{ $i }}" {{ old('adults', 2) == $i ? 'selected' : '' }}>{{ $i }} Yetişkin</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label for="children" class="block font-display text-[10px] tracking-[0.2em] uppercase text-ink-soft mb-xs">Çocuk Sayısı</label>
                            <select id="children" name="children"
                                    class="w-full bg-surface border border-border-soft focus:bg-surface-card focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none rounded-btn px-sm py-sm transition cursor-pointer">
                                @for ($i = 0; $i <= 4; $i++)
                                    <option value="{{ $i }}" {{ old('children', 0) == $i ? 'selected' : '' }}>{{ $i }} Çocuk</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>

                {{-- ─────── 2. Misafir Bilgileri ─────── --}}
                <div class="bg-surface-card rounded-card p-md md:p-lg shadow-soft border border-border-soft/40 lift">
                    <div class="flex items-center gap-sm mb-md pb-sm border-b border-border-soft">
                        <span class="w-8 h-8 rounded-pill bg-primary text-white flex items-center justify-center font-display font-bold text-sm shadow-softer">2</span>
                        <div>
                            <h2 class="font-display font-semibold text-xl text-ink leading-tight">Misafir Bilgileri</h2>
                            <p class="font-display text-[10px] tracking-[0.2em] uppercase text-ink-mute mt-0.5">Rezervasyonu yapan kişinin iletişim bilgileri</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-sm">
                        <div>
                            <label for="guest_first_name" class="block font-display text-[10px] tracking-[0.2em] uppercase text-ink-soft mb-xs">Ad *</label>
                            <input type="text" id="guest_first_name" name="guest_first_name" value="{{ old('guest_first_name') }}" required maxlength="100"
                                   autocomplete="given-name"
                                   class="w-full bg-surface border border-border-soft focus:bg-surface-card focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none rounded-btn px-sm py-sm transition" />
                        </div>
                        <div>
                            <label for="guest_last_name" class="block font-display text-[10px] tracking-[0.2em] uppercase text-ink-soft mb-xs">Soyad *</label>
                            <input type="text" id="guest_last_name" name="guest_last_name" value="{{ old('guest_last_name') }}" required maxlength="100"
                                   autocomplete="family-name"
                                   class="w-full bg-surface border border-border-soft focus:bg-surface-card focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none rounded-btn px-sm py-sm transition" />
                        </div>
                        <div>
                            <label for="guest_phone" class="block font-display text-[10px] tracking-[0.2em] uppercase text-ink-soft mb-xs">Telefon *</label>
                            <input type="tel" id="guest_phone" name="guest_phone" value="{{ old('guest_phone') }}" required maxlength="30"
                                   placeholder="+90 555 123 45 67"
                                   autocomplete="tel"
                                   class="w-full bg-surface border border-border-soft focus:bg-surface-card focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none rounded-btn px-sm py-sm transition" />
                        </div>
                        <div>
                            <label for="guest_email" class="block font-display text-[10px] tracking-[0.2em] uppercase text-ink-soft mb-xs">E-posta *</label>
                            <input type="email" id="guest_email" name="guest_email" value="{{ old('guest_email') }}" required maxlength="150"
                                   placeholder="ornek@email.com"
                                   autocomplete="email"
                                   class="w-full bg-surface border border-border-soft focus:bg-surface-card focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none rounded-btn px-sm py-sm transition" />
                        </div>
                    </div>
                </div>

                {{-- ─────── 3. Özel İstekler (opsiyonel) ─────── --}}
                <div class="bg-surface-card rounded-card p-md md:p-lg shadow-soft border border-border-soft/40 lift">
                    <div class="flex items-center gap-sm mb-md pb-sm border-b border-border-soft">
                        <span class="w-8 h-8 rounded-pill bg-surface-alt border border-border-strong text-ink-soft flex items-center justify-center font-display font-bold text-sm">3</span>
                        <div class="flex-1">
                            <h2 class="font-display font-semibold text-xl text-ink leading-tight">Özel İstekler</h2>
                            <p class="font-display text-[10px] tracking-[0.2em] uppercase text-ink-mute mt-0.5">Opsiyonel — varsa belirtebilirsiniz</p>
                        </div>
                    </div>
                    <textarea id="special_requests" name="special_requests" rows="4" maxlength="500"
                              placeholder="Erken giriş, ek yatak, vejetaryen kahvaltı gibi taleplerinizi belirtin."
                              class="w-full bg-surface border border-border-soft focus:bg-surface-card focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none rounded-btn px-sm py-sm transition resize-none">{{ old('special_requests') }}</textarea>
                    <p class="text-[10px] text-ink-mute mt-xs leading-relaxed flex items-start gap-xs">
                        <svg class="w-3 h-3 shrink-0 mt-0.5 text-accent-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z"/></svg>
                        <span>
                            Lütfen sağlık, inanç veya diğer özel nitelikli kişisel
                            bilgilerinizi bu alana yazmayın. Bu tür bilgiler için bizi
                            telefon veya WhatsApp ile arayın.
                        </span>
                    </p>
                </div>
            </div>

            {{-- ─────── Sticky Özet ─────── --}}
            <aside class="lg:col-span-1">
                <div class="bg-surface-card rounded-card overflow-hidden shadow-lift sticky top-24 border border-border-soft/40">

                    {{-- Üst bant (Olive Sanctuary altın detay) --}}
                    <div class="bg-primary-dark text-surface px-md py-sm flex items-center justify-between">
                        <span class="font-display text-[10px] tracking-[0.2em] uppercase text-accent">Rezervasyon</span>
                        <svg class="w-4 h-4 text-accent" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/></svg>
                    </div>

                    <div class="p-md">
                        <h2 class="font-display font-bold text-2xl text-ink mb-md leading-tight">Özet</h2>

                        <div class="space-y-sm text-sm mb-md">
                            <div class="flex justify-between items-start">
                                <span class="text-ink-soft">Oda</span>
                                <span class="text-ink font-medium text-right" data-summary-room>— Seçilmedi —</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-ink-soft">Konaklama</span>
                                <span class="text-ink font-medium" data-summary-nights>—</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-ink-soft">Gecelik</span>
                                <span class="text-ink font-medium" data-summary-price-per-night>—</span>
                            </div>
                        </div>

                        <div class="pt-md border-t border-border-soft mb-md">
                            <div class="flex justify-between items-end">
                                <div>
                                    <p class="font-display text-[10px] tracking-[0.2em] uppercase text-ink-mute">Tahmini Toplam</p>
                                    <p class="text-[10px] text-ink-mute mt-0.5">Vergiler dahil</p>
                                </div>
                                <span class="font-display font-bold text-3xl text-primary-dark tracking-tight" data-summary-total>₺—</span>
                            </div>
                        </div>

                        <div class="bg-primary-soft/60 border border-primary-light/60 rounded-btn p-sm mb-md">
                            <div class="flex items-start gap-xs">
                                <svg class="w-4 h-4 text-primary mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <p class="text-xs text-primary-dark leading-relaxed">
                                    Şu an ücret ödemiyorsunuz. Onaylandığında size IBAN gönderilir.
                                </p>
                            </div>
                        </div>

                        {{-- KVKK aydınlatma onayı (zorunlu — backend `accepted` rule) --}}
                        <label class="flex items-start gap-xs mb-sm text-xs text-ink-soft leading-relaxed cursor-pointer">
                            <input type="checkbox" name="kvkk_consent" value="1" required
                                   {{ old('kvkk_consent') ? 'checked' : '' }}
                                   {{-- WCAG 2.2 target-size: min 24x24px touch target (a11y) --}}
                                   class="mt-0.5 w-6 h-6 accent-primary focus:ring-2 focus:ring-primary/20 cursor-pointer shrink-0" />
                            <span>
                                <a href="{{ route('kvkk') }}" target="_blank" rel="noopener" class="text-primary underline-grow hover:text-primary-dark font-medium">KVKK Aydınlatma Metnini</a>
                                okudum, kişisel verilerimin rezervasyon işlemleri için
                                işlenmesini kabul ediyorum.
                            </span>
                        </label>

                        <button type="submit"
                                class="w-full bg-accent-dark hover:bg-accent text-white font-display font-semibold tracking-wide py-sm rounded-btn transition-colors inline-flex items-center justify-center gap-xs shadow-soft hover:shadow-lift active:scale-[0.98]">
                            <span>Bilgileri Onayla</span>
                            <svg class="w-4 h-4 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                        <p class="text-[10px] text-ink-mute text-center mt-xs">Bu aşamada ödeme alınmaz.</p>
                    </div>

                    {{-- Konaklama Bilgi Kutusu — sticky'in altında --}}
                    <div class="bg-surface-alt/60 border-t border-border-soft p-md">
                        <p class="font-display text-[10px] tracking-[0.2em] uppercase text-accent-dark mb-sm">Konaklama Bilgileri</p>
                        <ul class="space-y-xs text-xs text-ink-soft leading-relaxed">
                            <li class="flex items-start gap-xs">
                                <svg class="w-3 h-3 text-accent shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="4"/></svg>
                                <span><strong class="text-ink">Giriş:</strong> {{ \App\Models\Setting::get('checkin_time', '14:00') }}'ten itibaren</span>
                            </li>
                            <li class="flex items-start gap-xs">
                                <svg class="w-3 h-3 text-accent shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="4"/></svg>
                                <span><strong class="text-ink">Çıkış:</strong> {{ \App\Models\Setting::get('checkout_time', '12:00') }}'a kadar</span>
                            </li>
                            <li class="flex items-start gap-xs">
                                <svg class="w-3 h-3 text-accent shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="4"/></svg>
                                <span><strong class="text-ink">Kahvaltı:</strong> 08:00 - 10:30 (ücretsiz)</span>
                            </li>
                            <li class="flex items-start gap-xs">
                                <svg class="w-3 h-3 text-accent shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="4"/></svg>
                                <span><strong class="text-ink">Wi-Fi &amp; Otopark:</strong> Ücretsiz</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </aside>
        </form>
    </div>
</section>

@endsection
