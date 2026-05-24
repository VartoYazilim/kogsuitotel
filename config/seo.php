<?php

return [

    /*
    |--------------------------------------------------------------------------
    | SEO & Yerel Arama Konfigürasyonu
    |--------------------------------------------------------------------------
    | Bu dosya Koğ Suit Otel'in tüm SEO meta verisini merkezi olarak tutar.
    | Yerel arama (Varto/Muş) odaklı.
    */

    'site_name' => env('APP_NAME', 'Koğ Suit Otel'),
    'tagline' => 'Refined Hospitality in Varto',
    'description_default' => 'Koğ Suit Otel — Muş Varto\'da, beş özenli süitle modern konfor ve geleneksel sıcaklığı bir araya getiren butik bir konaklama deneyimi.',

    // Doğrulama meta tag'leri (.env'den okunur)
    'google_verification' => env('SEO_GOOGLE_VERIFICATION'),
    'bing_verification' => env('SEO_BING_VERIFICATION'),
    'yandex_verification' => env('SEO_YANDEX_VERIFICATION'),

    // Google Analytics 4 (G-XXXXXXXXXX). Boş bırakılırsa GA4 tag render edilmez,
    // cookie consent banner da gizlenir. KVKK m.5 açık rıza: Consent Mode v2
    // default deny — kullanıcı banner'dan kabul edene kadar GA4 çerez yazmaz.
    'google_analytics_id' => env('SEO_GA4_ID'),

    // Yerel (geo) bilgiler — Varto merkezi
    'geo' => [
        'latitude' => env('SEO_GEO_LAT', '38.9211'),
        'longitude' => env('SEO_GEO_LNG', '41.4544'),
        'region' => 'TR-49', // Muş ISO 3166-2
        'placename' => 'Varto',
        'city' => 'Varto',
        'province' => 'Muş',
        'country' => 'TR',
        'country_name' => 'Türkiye',
        'postal_code' => '49600',
    ],

    // İşletme bilgileri (Schema.org + footer NAP tutarlılığı için tek kaynak)
    'business' => [
        'legal_name' => 'Koğ Suit Otel',
        'brand_name' => 'Koğ Suit Otel',
        'price_range' => '₺₺',
        'currencies_accepted' => 'TRY',
        'payment_accepted' => 'Havale, EFT',
        'opening_hours' => 'Mo-Su 00:00-23:59', // 7/24 resepsiyon
        'star_rating' => null, // henüz resmi yıldız yok
    ],

    // Open Graph default'ları
    'og' => [
        'default_image' => env('SEO_DEFAULT_OG_IMAGE', '/images/og-default.jpg'),
        'image_width' => 1200,
        'image_height' => 630,
        'locale' => 'tr_TR',
        'type' => 'website',
    ],

    // Anahtar kelimeler — referans (meta keywords artık etkili değil, ama içerikte kullanım için liste)
    'target_keywords' => [
        'varto otel',
        'muş varto otel',
        'varto konaklama',
        'varto butik otel',
        'koğ suit otel',
        'muş varto konaklama',
        'varto rezervasyon',
        'varto suit oda',
        'muş otel',
        'varto premium otel',
    ],

    // robots.txt için varsayılan crawler politikası (etkisi yok, dokümantasyon)
    'allow_ai_crawlers' => env('SEO_ALLOW_AI_CRAWLERS', true),
];
