{{-- KVKK m.5 + GDPR uyumlu cookie consent banner.
     Sadece GA4 ID set ise render edilir (zorunlu çerezler için banner gereksiz).
     localStorage'da `cookie_consent` = accepted / rejected — tercih kalıcı.
     "Kabul Et" → gtag('consent', 'update', { analytics_storage: 'granted' })
     "Reddet" → analytics_storage denied kalır, sadece anonim sayfa görüntüleme.

     app.js initCookieConsent() ile etkinleşir. --}}
@if (config('seo.google_analytics_id'))
<div id="kog-cookie-banner"
     class="fixed bottom-0 left-0 right-0 z-50 hidden bg-ink/95 backdrop-blur-md text-surface border-t border-accent/30 shadow-lift"
     role="dialog"
     aria-modal="false"
     aria-labelledby="cookie-banner-title"
     aria-describedby="cookie-banner-desc">

    <div class="max-w-[1200px] mx-auto px-md py-md flex flex-col md:flex-row items-start md:items-center gap-md">

        <div class="flex-1 min-w-0">
            <p id="cookie-banner-title" class="font-display font-semibold text-base mb-xs">
                Çerez tercihiniz
            </p>
            <p id="cookie-banner-desc" class="text-sm text-surface/80 leading-relaxed">
                Sitemizdeki temel teknik çerezler (oturum, güvenlik) her zaman aktiftir.
                Site kullanım istatistiği için <strong>Google Analytics</strong> çerezlerini
                yalnızca onayınız sonrası aktive ediyoruz. Detay için
                <a href="{{ route('cookie-policy') }}" class="underline hover:text-accent transition-colors">
                    Çerez Politikası
                </a>.
            </p>
        </div>

        <div class="flex flex-col sm:flex-row gap-xs w-full md:w-auto shrink-0">
            <button type="button"
                    data-cookie-action="reject"
                    class="px-md py-xs rounded-btn border border-surface/30 text-surface hover:bg-surface/10 transition-colors font-display text-sm whitespace-nowrap">
                Reddet
            </button>
            <button type="button"
                    data-cookie-action="accept"
                    class="px-md py-xs rounded-btn bg-accent text-ink hover:bg-accent-light transition-colors font-display font-semibold text-sm whitespace-nowrap">
                Tümünü Kabul Et
            </button>
        </div>

    </div>
</div>
@endif
