{{-- Olive Sanctuary lightbox — vanilla JS, paket yok. app.js initLightbox() ile etkinleşir. --}}
<div id="kog-lightbox"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-ink/95 backdrop-blur-sm p-md"
     role="dialog"
     aria-modal="true"
     aria-label="Görsel önizleme"
     aria-hidden="true">

    <button type="button"
            data-lightbox-close
            class="absolute top-md right-md w-12 h-12 rounded-full bg-surface/10 hover:bg-surface/20 text-surface flex items-center justify-center transition-colors focus:outline-none focus:ring-2 focus:ring-surface"
            aria-label="Kapat (Esc)">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>

    <button type="button"
            data-lightbox-prev
            class="absolute left-md top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-surface/10 hover:bg-surface/20 text-surface flex items-center justify-center transition-colors focus:outline-none focus:ring-2 focus:ring-surface hidden"
            aria-label="Önceki görsel (←)">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
    </button>

    <button type="button"
            data-lightbox-next
            class="absolute right-md top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-surface/10 hover:bg-surface/20 text-surface flex items-center justify-center transition-colors focus:outline-none focus:ring-2 focus:ring-surface hidden"
            aria-label="Sonraki görsel (→)">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
    </button>

    <figure class="max-w-[90vw] max-h-[90vh] flex flex-col items-center gap-sm">
        <img data-lightbox-image
             src=""
             alt=""
             class="max-w-full max-h-[80vh] object-contain rounded-card shadow-lift" />
        <figcaption data-lightbox-caption
                    class="text-surface/80 text-sm font-display tracking-wide text-center"></figcaption>
        <span data-lightbox-counter
              class="text-surface/60 text-xs font-display tracking-[0.2em] uppercase"></span>
    </figure>
</div>
