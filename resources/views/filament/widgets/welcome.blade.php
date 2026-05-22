<x-filament-widgets::widget>
    <div style="
        position: relative;
        overflow: hidden;
        border-radius: 0.75rem;
        color: #fff;
        box-shadow: 0 10px 30px -10px rgba(42, 45, 36, 0.25);
        background: linear-gradient(135deg, #4a5240 0%, #6b7553 50%, #566042 100%);
    ">
        {{-- Grain overlay --}}
        <div style="
            position: absolute;
            inset: 0;
            pointer-events: none;
            opacity: 0.22;
            background-image: radial-gradient(rgba(255,255,255,0.18) 1px, transparent 1px);
            background-size: 4px 4px;
        "></div>

        {{-- Decorative blur orbs --}}
        <div style="
            position: absolute;
            top: -80px; right: -80px;
            width: 260px; height: 260px;
            border-radius: 9999px;
            pointer-events: none;
            background: rgba(184, 155, 110, 0.28);
            filter: blur(60px);
        "></div>
        <div style="
            position: absolute;
            bottom: -80px; left: -80px;
            width: 260px; height: 260px;
            border-radius: 9999px;
            pointer-events: none;
            background: rgba(184, 155, 110, 0.18);
            filter: blur(60px);
        "></div>

        <div style="
            position: relative;
            z-index: 10;
            padding: 1.75rem 2rem;
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
            align-items: center;
        " class="welcome-grid">

            {{-- Sol: Selamlama --}}
            <div>
                <p style="
                    font-size: 11px;
                    font-weight: 600;
                    letter-spacing: 0.25em;
                    text-transform: uppercase;
                    color: #d4b98a;
                    margin: 0 0 0.5rem 0;
                ">{{ $todayDate }}</p>

                <h2 style="
                    font-weight: 700;
                    font-size: 1.75rem;
                    line-height: 1.2;
                    letter-spacing: -0.02em;
                    margin: 0 0 0.5rem 0;
                ">{{ $greeting }}, {{ $userName }}.</h2>

                <p style="
                    font-size: 0.875rem;
                    opacity: 0.85;
                    max-width: 36rem;
                    line-height: 1.5;
                    margin: 0;
                ">Koğ Suit Otel yönetim paneline hoş geldiniz. Bugünün özeti yan tarafta.</p>
            </div>

            {{-- Sağ: 3 Metrik kartı --}}
            <div style="
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 0.75rem;
            ">
                {{-- Bekleyen --}}
                <div style="
                    text-align: center;
                    padding: 0.875rem 0.5rem;
                    background: rgba(255, 255, 255, 0.08);
                    border: 1px solid rgba(255, 255, 255, 0.12);
                    border-radius: 0.5rem;
                    backdrop-filter: blur(8px);
                ">
                    <div style="height: 18px; display: flex; align-items: center; justify-content: center; margin-bottom: 0.25rem;">
                        @if ($pendingCount > 0)
                            <span style="
                                display: inline-block;
                                width: 8px; height: 8px;
                                border-radius: 9999px;
                                background: #d4b98a;
                                box-shadow: 0 0 0 4px rgba(212, 185, 138, 0.3);
                                animation: pulse 2s infinite;
                            "></span>
                        @endif
                    </div>
                    <p style="font-weight: 700; font-size: 1.75rem; line-height: 1; margin: 0; font-variant-numeric: tabular-nums;">{{ $pendingCount }}</p>
                    <p style="
                        font-size: 10px;
                        text-transform: uppercase;
                        letter-spacing: 0.05em;
                        opacity: 0.75;
                        margin: 0.5rem 0 0 0;
                    ">Bekleyen</p>
                </div>

                {{-- Giriş --}}
                <div style="
                    text-align: center;
                    padding: 0.875rem 0.5rem;
                    background: rgba(255, 255, 255, 0.08);
                    border: 1px solid rgba(255, 255, 255, 0.12);
                    border-radius: 0.5rem;
                    backdrop-filter: blur(8px);
                ">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                         style="display: block; margin: 0 auto 0.25rem; opacity: 0.85;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                    <p style="font-weight: 700; font-size: 1.75rem; line-height: 1; margin: 0; font-variant-numeric: tabular-nums;">{{ $todayArrivals }}</p>
                    <p style="
                        font-size: 10px;
                        text-transform: uppercase;
                        letter-spacing: 0.05em;
                        opacity: 0.75;
                        margin: 0.5rem 0 0 0;
                    ">Bugün Giriş</p>
                </div>

                {{-- Çıkış --}}
                <div style="
                    text-align: center;
                    padding: 0.875rem 0.5rem;
                    background: rgba(255, 255, 255, 0.08);
                    border: 1px solid rgba(255, 255, 255, 0.12);
                    border-radius: 0.5rem;
                    backdrop-filter: blur(8px);
                ">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                         style="display: block; margin: 0 auto 0.25rem; opacity: 0.85;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <p style="font-weight: 700; font-size: 1.75rem; line-height: 1; margin: 0; font-variant-numeric: tabular-nums;">{{ $todayDepartures }}</p>
                    <p style="
                        font-size: 10px;
                        text-transform: uppercase;
                        letter-spacing: 0.05em;
                        opacity: 0.75;
                        margin: 0.5rem 0 0 0;
                    ">Bugün Çıkış</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Responsive: 1024px+ ekranlarda 2 kolonlu (selamlama + metrikler yan yana) --}}
    <style>
        @media (min-width: 1024px) {
            .welcome-grid {
                grid-template-columns: 3fr 2fr !important;
            }
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
    </style>
</x-filament-widgets::widget>
