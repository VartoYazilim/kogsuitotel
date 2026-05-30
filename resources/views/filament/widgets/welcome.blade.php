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

        {{-- Decorative blur orb --}}
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
        ">
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
                max-width: 48rem;
                line-height: 1.5;
                margin: 0;
            ">Koğ Suit Otel yönetim paneline hoş geldiniz. Bugünün özeti aşağıdaki kartlarda; bekleyen işler için sol menüden Rezervasyonlar bölümüne geçin.</p>
        </div>
    </div>
</x-filament-widgets::widget>
