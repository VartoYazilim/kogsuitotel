{{--
    Sosyal medya ikonları — BusinessSettings'ten okuyup boş olmayanları gösterir.
    Kullanım: @include('partials.social-icons', ['variant' => 'footer'])
                                                  veya 'contact'

    variant=footer  → küçük, açık renkli ikonlar (dark footer bg üzerinde)
    variant=contact → orta boy, primary renkli ikonlar (light bg üzerinde)
--}}
@php
    $variant = $variant ?? 'footer';
    $sizeClass = $variant === 'contact' ? 'w-10 h-10' : 'w-8 h-8';
    $iconClass = $variant === 'contact' ? 'w-5 h-5' : 'w-4 h-4';
    $bgClass = $variant === 'contact'
        ? 'bg-surface-soft hover:bg-primary-soft text-primary hover:text-primary-dark border border-border-soft'
        : 'bg-surface/10 hover:bg-accent/30 text-surface/80 hover:text-accent border border-surface/10';

    $socials = collect([
        ['url' => \App\Models\Setting::get('instagram_url'),  'label' => 'Instagram',   'icon' => 'instagram'],
        ['url' => \App\Models\Setting::get('facebook_url'),   'label' => 'Facebook',    'icon' => 'facebook'],
        ['url' => \App\Models\Setting::get('tripadvisor_url'),'label' => 'Tripadvisor', 'icon' => 'tripadvisor'],
        ['url' => \App\Models\Setting::get('google_maps_url'),'label' => 'Google Maps', 'icon' => 'mappin'],
    ])->filter(fn ($s) => ! empty($s['url']))->values();
@endphp

@if ($socials->isNotEmpty())
    <div class="flex gap-xs flex-wrap">
        @foreach ($socials as $s)
            <a href="{{ $s['url'] }}"
               target="_blank"
               rel="noopener noreferrer"
               aria-label="{{ $s['label'] }}"
               title="{{ $s['label'] }}"
               class="{{ $sizeClass }} rounded-full flex items-center justify-center transition-all duration-200 {{ $bgClass }}">

                @switch($s['icon'])
                    @case('instagram')
                        <svg class="{{ $iconClass }}" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                        </svg>
                        @break

                    @case('facebook')
                        <svg class="{{ $iconClass }}" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M9.101 23.691v-7.98H6.627v-3.667h2.474v-1.58c0-4.085 1.848-5.978 5.858-5.978.401 0 .955.042 1.468.103a8.68 8.68 0 0 1 1.141.195v3.325a8.623 8.623 0 0 0-.653-.036 26.805 26.805 0 0 0-.733-.009c-.707 0-1.259.096-1.675.309a1.686 1.686 0 0 0-.679.622c-.258.42-.374.995-.374 1.752v1.297h3.919l-.386 2.103-.287 1.564h-3.246v8.245C19.396 23.238 24 18.179 24 12.044c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.628 3.874 10.35 9.101 11.647Z"/>
                        </svg>
                        @break

                    @case('tripadvisor')
                        <svg class="{{ $iconClass }}" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12.006 4.295c-2.67 0-5.338.784-7.645 2.353H0l1.963 2.135a5.997 5.997 0 0 0 4.04 10.43 5.976 5.976 0 0 0 4.075-1.6L12 19.705l1.922-2.09a5.972 5.972 0 0 0 4.072 1.598 6 6 0 0 0 6-5.998 5.982 5.982 0 0 0-1.957-4.432L24 6.648h-4.36a13.682 13.682 0 0 0-7.634-2.353zM12 6.255c1.553 0 3.07.275 4.504.806C13.585 8.187 11.997 10.92 12 13.79c0-2.87-1.588-5.604-4.504-6.728A13.046 13.046 0 0 1 12 6.255zm-5.997 3.557a3.41 3.41 0 0 1 3.405 3.405 3.408 3.408 0 0 1-3.405 3.404A3.408 3.408 0 0 1 2.6 13.217a3.41 3.41 0 0 1 3.403-3.405zm11.992 0a3.41 3.41 0 0 1 3.405 3.405 3.408 3.408 0 0 1-3.405 3.404 3.408 3.408 0 0 1-3.404-3.404 3.41 3.41 0 0 1 3.404-3.405zm-11.992 1.43a1.973 1.973 0 0 0-1.974 1.975c0 1.09.884 1.974 1.974 1.974a1.975 1.975 0 0 0 0-3.95zm11.992 0a1.973 1.973 0 0 0-1.974 1.975c0 1.09.884 1.974 1.974 1.974a1.974 1.974 0 0 0 1.975-1.974 1.974 1.974 0 0 0-1.975-1.974z"/>
                        </svg>
                        @break

                    @case('mappin')
                        <svg class="{{ $iconClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        @break
                @endswitch
            </a>
        @endforeach
    </div>
@endif
