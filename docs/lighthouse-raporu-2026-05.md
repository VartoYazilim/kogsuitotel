# Lighthouse Performance + Accessibility Raporu — Koğ Suit Otel

**Tarih:** 2026-05-23 (Faz 2 Block 4)
**Yöntem:** Lighthouse CLI v12, desktop preset, headless Chrome, lokal `php artisan serve` (HTTP 8000).
**Kapsam:** 5 kritik sayfa — Ana sayfa, Odalar listesi, Oda detay (standart), Galeri, Rezervasyon formu.

Bu rapor; sahibe ve geliştiriciye yöneliktir. Performance/Accessibility/SEO/Best Practices skorlarını, uygulanan a11y düzeltmelerini ve kalan iyileştirme alanlarını gösterir.

---

## Sonuç Özeti (final — v2)

| Sayfa | Performance | Accessibility | SEO | Best Practices |
|---|---|---|---|---|
| Ana Sayfa (`/`) | **100** | **96** | 100 | 100 |
| Odalar (`/odalar`) | **100** | **95** | 100 | 100 |
| Oda detay (`/odalar/standart-oda`) | **100** | **95** | 100 | 100 |
| Galeri (`/galeri`) | **100** | **95** | 100 | 100 |
| Rezervasyon (`/rezervasyon`) | **100** | **96** | 100 | 100 |

**Hedef:** Performance 90+, Accessibility 95+ → **TÜMÜ AŞILDI ✓**

### Core Web Vitals (desktop, lokal)

| Metrik | Değer | Hedef | Durum |
|---|---|---|---|
| LCP (Largest Contentful Paint) | 0.5s | < 2.5s | ✓ (5x üzerinde marj) |
| CLS (Cumulative Layout Shift) | 0 | < 0.1 | ✓ |
| TBT (Total Blocking Time) | 0ms | < 200ms | ✓ |
| FCP (First Contentful Paint) | 0.5s | < 1.8s | ✓ |
| Speed Index | 0.8s | < 3.4s | ✓ |

> **Not:** Lokal ölçümlerde network gecikmesi yok. Production (Cloudflare + nginx) gerçek skorları muhtemelen ~5-10 puan düşer ama hedeflerin altına inmesi beklenmez.

---

## Block 4'te Uygulanan A11y Düzeltmeleri

### Baseline (v1) → Final (v2)

| Sayfa | A11y v1 | A11y v2 | Δ | Düzeltilen |
|---|---|---|---|---|
| Ana sayfa | 91 | 96 | **+5** | select-name + color-contrast |
| Odalar | 93 | 95 | +2 | heading-order + color-contrast |
| Oda detay | 95 | 95 | 0 | (zaten temiz) |
| Galeri | 95 | 95 | 0 | (zaten temiz) |
| Rezervasyon | 93 | 96 | **+3** | target-size + color-contrast |

### Detaylı Fix Listesi

1. **`text-accent-dark` token koyulaştırması** (`resources/css/app.css`)
   `#8b7449` → `#735f3d` — surface üzerinde kontrast 4.07 → ~5.5 (WCAG AA pass). Olive Sanctuary tonu korundu.

2. **`text-ink-mute` token koyulaştırması** (`resources/css/app.css`)
   `#8a8c82` → `#6d6f65` — surface üzerinde kontrast 3.1 → ~5.5 (yumuşak gri-yeşil korundu).

3. **Header logo "Otel" wordmark** (`resources/views/layouts/public.blade.php`)
   `text-accent` → `text-accent-dark` — krem zemin üzerinde okunabilirlik.

4. **CTA butonları** (10 yer, 6 dosya)
   `bg-primary text-surface` → `bg-primary text-white` — kontrast 4.45 → 5.5+ (Lighthouse strict 4.5 threshold).
   - layouts/public (header CTA + mobile menü CTA)
   - gallery/index (kategori filtre 2 yer)
   - rooms/show (sidebar CTA + hover state)
   - pages/home (form submit)
   - pages/about (CTA)
   - reservations/create (step numbers 2 yer)

5. **Hero CTA bonus iyileştirme** (`pages/home.blade.php` + `reservations/create.blade.php`)
   `bg-accent text-surface` → `bg-accent-dark text-white` — kontrast 2.4 → 5.5+ (hero "Rezervasyon Yap" + submit "Bilgileri Onayla" butonları daha okunabilir).

6. **Hero hızlı müsaitlik form select label** (`pages/home.blade.php`)
   `<label>` ve `<select>` arasında `for`/`id` eşleştirmesi (screen reader için).

7. **Odalar listesi heading hierarchy** (`rooms/index.blade.php`)
   Card title `<h3>` → `<h2>` — H1 → H3 atlanan sıra düzeltildi.

8. **KVKK consent checkbox WCAG 2.2 target-size** (`reservations/create.blade.php`)
   13×13px → 24×24px (`w-6 h-6`) — mobil dokunma hedefi.

---

## Kalan Color-Contrast Bulguları (95+ hedef tutuldu, bonus için)

Şu elementler hala color-contrast fail veriyor ama A11y skorunu 95'in altına çekmiyor. Sahibin tasarım onayı ile bir sonraki turda iyileştirilebilir:

| Element | Kontrast | Yer | Çözüm önerisi |
|---|---|---|---|
| Footer başlıkları (`text-accent` üzerinde `bg-primary-dark`) | 3.09 | layouts/public.blade.php:233,244,251 | `text-accent` → `text-surface` (footer kahvaltı tipografisi) |
| `text-accent/70` footer mikro-text | 2.27 | layouts/public.blade.php:275 | opacity kaldır veya text-white kullan |
| `text-surface/55` footer secondary | 3.5 | layouts/public.blade.php (border-t alt bölüm) | `/55` → `/75` veya kaldır |
| `text-primary` linkler | 4.3-4.45 | home.blade.php "Tüm odaları gör" linkleri | `text-primary` → `text-primary-dark` (token değişimi tüm linkleri etkiler) |
| `text-accent` üst band (sidebar) | 3.09 | reservations/create sidebar üst band | aynı footer çözümü |

**Neden bonus:** Bu fix'ler tasarım yönünü etkiler (footer başlık rengi değişimi, link tonu vs.). Sahibin demo'sundan sonra "iyileştirelim mi" kararıyla yapılabilir.

---

## Performance Notları

- **JS bundle:** 54 KB minified, 16 KB gzipped (Alpine.js + Flatpickr + custom JS) — küçük.
- **CSS bundle:** 96 KB minified, 18 KB gzipped (Tailwind v4 JIT + Olive Sanctuary + font subset) — orta, üretim Cloudflare brotli ile daha düşer.
- **Font:** Manrope + Inter self-hosted (woff2, KVKK uyumlu). LCP 0.5s'de yetişiyor, font preload ek iyileştirme getirir (şimdi `display=swap` ile fallback'siz yükleniyor).
- **Image:** Aktif sayfalarda gerçek resim yok (Faz 2e bekliyor — sahibinden gelecek). WebP convert + responsive `srcset` bu aşamada sahibinden gelen JPG'lere uygulanmalı (yeni blok).

---

## Sonraki Adım

1. **Sahip demo'sundan sonra** — tasarım onayı ile footer + link rengi tonlarını iyileştir → A11y 99-100'e çıkar.
2. **Faz 2e içerik geldiğinde** — WebP convert (cwebp veya Intervention) + `loading="lazy"` zaten kullanılıyor + responsive `<picture>` ekle.
3. **Faz 3 prod'da** — Cloudflare APO + Brotli + HTTP/3 ile Performance gerçek değer doğrula.

---

## Çalıştırma Talimatı (geliştirici için)

```bash
# Lighthouse CLI kuruluysa (npm dev dep — Block 4'te eklendi):
PATH="/c/Program Files/Google/Chrome/Application/" \
  npx lighthouse http://127.0.0.1:8000/ \
  --chrome-flags="--headless --no-sandbox" \
  --preset=desktop \
  --quiet \
  --only-categories=performance,accessibility,seo,best-practices \
  --output=html --output-path=lighthouse-home.html
```

> Detay: Lighthouse JSON output'ları `tmp/lighthouse/` klasörüne yazılır (`.gitignore`'da). Sahibe HTML raporu üretmek için `--output=html`.

---

*Bu doküman 2026-05-23 Faz 2 Block 4 (Lighthouse + Performance/a11y) çıktısıdır. Hedef Performance 90+ ve Accessibility 95+ aşıldı. Faz 2 kod tarafı tamamen kapandı.*
