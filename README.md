# Koğ Suit Otel

[![CI](https://github.com/VartoYazilim/kogsuitotel/actions/workflows/ci.yml/badge.svg?branch=main)](https://github.com/VartoYazilim/kogsuitotel/actions/workflows/ci.yml)

Muş Varto'da açılacak yeni butik otelin resmi web sitesi ve rezervasyon sistemi.

**Domain**: [kogsuitotel.com](https://kogsuitotel.com) — **CANLIDA** (Contabo VPS 10, Cloudflare proxy ON, SSL Full Strict)
**Admin paneli**: [yonetim.kogsuitotel.com](https://yonetim.kogsuitotel.com) (`/kog-yonetim`'e 301)
**Marka**: "Refined Hospitality in Varto"
**Geliştirici**: [Varto Yazılım](https://vartoyazilim.com)

> ℹ️ **Proje hafızası**: Tüm mimari kararlar, faz planları, açık karar noktaları ve çalışma kuralları için **`CLAUDE.md`** dosyasına bakın. Bu README sadece hızlı başlangıç içindir.

---

## Stack

- **Laravel 12.60** + **Filament 4.11** (admin paneli, custom panel theme, `->profile()` ile şifre değiştirme)
- **PHP 8.3.30** (Laragon — prod parity Ubuntu 24.04 default ile uyumlu)
- **Tailwind v4** (CSS-first config, `@theme` directive)
- **SQLite** lokal · **MariaDB 10.11** prod · **PostgreSQL 16** CI (matrix)
- **Flatpickr** (public site tarih seçici, TR locale) · **Filament DatePicker** (admin)
- **Spatie ActivityLog** (admin aksiyonları audit trail — Filament UI "Geçmiş" tab + KVKK m.12/3)
- **Spatie Laravel Sitemap** (yerel SEO için)
- **App\Services\ImageWebpConverter** (admin upload JPG/PNG → WebP otomatik dönüşüm, q=82)
- **Olive Sanctuary** tasarım sistemi (zeytin yeşili + antika altın + sıcak krem, light + dark variant)
- **Vanilla JS lightbox** (oda galeri detayda, paket yok)

---

## Hızlı Başlangıç (Lokal)

### Önkoşullar

- **Laragon** (Windows) — PHP 8.3.30, MySQL, nginx hazır gelir
- **Node.js 20+** (Vite + asset build için)
- **Composer 2.x**

### Kurulum

```bash
# 1. Repo klonla
git clone <repo-url> kogsuitotel
cd kogsuitotel

# 2. .env oluştur (örnekten kopyala)
cp .env.example .env

# 3. Dependencies (Laragon PHP 8.3.30 ile)
PATH="/c/laragon/bin/php/php-8.3.30-Win32-vs16-x64:$PATH" composer install
npm install

# 4. APP_KEY üret
php artisan key:generate

# 5. SQLite DB + migrate + seed
php artisan migrate --seed

# 6. Frontend build
npm run build

# 7. (Opsiyonel) Laragon vhost junction
mklink /J "C:\laragon\www\kogsuitotel" "<repo-path>"  # CMD ile
```

### Dev server

İki seçenek:
```bash
# Seçenek A — Laravel built-in server (en basit)
php artisan serve   # http://localhost:8000

# Seçenek B — Laragon vhost (junction kuruluysa)
# https://kogsuitotel.test  (Laragon Auto Virtual Hosts ile otomatik)
```

### Test ve Kalite Kontrolü

```bash
composer pint                  # Kod stili kontrolü (sadece check)
composer pint:fix              # Kod stilini otomatik düzelt
vendor/bin/phpstan analyse     # Larastan statik analiz (level 5)
composer test                  # PHPUnit (112 test, 379 assertion)

php artisan test --coverage    # Coverage raporu (Xdebug gerekli)
```

> Her push + PR `.github/workflows/ci.yml` ile aynı 4 adımı (Pint, Larastan, PHPUnit, composer audit) GitHub Actions'ta tekrar koşar.
>
> **Not (Windows + Laragon)**: `composer analyse` script'i koymadık çünkü Windows'ta composer subprocess + PHPStan TTY tespiti sessiz exit 1 dönüyor. Linux CI'da sorunsuz; lokal Windows'ta `vendor/bin/phpstan analyse` direkt komutu kullan.

### Asset development (hot reload)

```bash
npm run dev    # Vite dev server, HMR aktif
```

---

## Admin Panel

**URL**: `/kog-yonetim` (security through obscurity — `/admin` değil)

**Admin user** — seeder env-driven, hardcoded password YOK:
- Email: `admin@kogsuitotel.com` (`.env`'de `ADMIN_EMAIL`)
- Şifre: `.env`'de **`ADMIN_PASSWORD`** zorunlu (min 12 karakter, placeholder reddedilir)
- Güçlü password üret: `php -r "echo base64_encode(random_bytes(24)) . PHP_EOL;"`

İçerikler:
- **Operasyon** → Rezervasyonlar (kanban, filtre, WhatsApp aksiyonu, status workflow), Müsaitlik sorgusu
- **İçerik** → Odalar (çoklu galeri upload, otomatik WebP), Galeri Görselleri
- **Sistem** → İşletme Ayarları (`/kog-yonetim/ayarlar` — kategorize tek sayfa: Banka/İletişim/Konaklama/Sosyal Medya)
- **Sağ üst avatar menüsü** → Profil (`/kog-yonetim/profile` — ad, e-posta, şifre değiştirme)

**Audit trail** — Reservation/Room/Setting değişimleri `activity_log` tablosuna işlenir + Filament admin'de detay sayfalarında **"Geçmiş"** tab'ında kim/ne zaman/eski→yeni diff görünür (Spatie ActivityLog, KVKK m.12/3 denetim).

**Görsel upload** — JPG/PNG yüklenince otomatik WebP'ye dönüşür (`App\Services\ImageWebpConverter`, q=82, PNG alpha preserve, random isim KVKK güvenlik).

---

## Önemli Dizin Yapısı

```
.
├── CLAUDE.md                    # Proje hafızası — başlamadan önce mutlaka oku
├── base/                        # Tasarım kaynakları (Stitch + DESIGN.md + LOGO)
├── design.html                  # Olive Sanctuary tasarım sistemi preview
├── app/
│   ├── Enums/ReservationStatus.php
│   ├── Filament/
│   │   ├── Pages/               # BusinessSettings + Availability (custom Filament pages)
│   │   ├── RelationManagers/    # ActivitiesRelationManager (DRY, Reservation+Room "Geçmiş")
│   │   ├── Resources/           # 3 admin resource (Reservation, Room, GalleryImage)
│   │   └── Widgets/             # Dashboard widget'ları (Welcome + Stats + Trend + Latest)
│   ├── Models/                  # 4 model + LogsActivity trait
│   ├── Services/                # ImageWebpConverter (FileUpload otomatik WebP)
│   └── Http/Controllers/        # Public + Admin controllers
├── config/seo.php               # SEO konfigürasyon (yerel — Varto/Muş)
├── database/
│   ├── migrations/              # 5 + Notifications/JSONB + 2FA + ActivityLog
│   └── seeders/                 # Admin user, 5 oda, 6 galeri, 13 setting
├── lang/tr/                     # Türkçe lokalizasyon (validation, auth)
├── public/
│   ├── images/logo.svg          # Marka logosu (Olive Sanctuary)
│   ├── images/
│   │   ├── logo.svg             # TEK logo kaynağı — header + favicon + admin brand
│   │   └── demo/                # Geçici hero + OG görseller (oda + galeri demo'ları
│   │       │                    #   database/demo-images/ altına taşındı)
│   │       ├── hero/            # Ana sayfa arka plan + hikaye bölümü
│   │       └── og/              # Sosyal medya OG image (JPG — uyumluluk)
│   ├── robots.txt               # AI crawler izinli
│   └── site.webmanifest         # PWA install
├── resources/
│   ├── css/app.css              # @theme + selection + scrollbar + microinteractions
│   ├── css/filament/kog/        # Custom Filament panel theme (Olive Sanctuary light+dark)
│   ├── js/app.js                # Flatpickr + dinamik fiyat + lightbox (vanilla)
│   └── views/
│       ├── layouts/public.blade.php
│       ├── filament/pages/      # business-settings, availability (custom Page Blade'ler)
│       ├── pages/               # home, about, contact, faq, kvkk, privacy, cookie-policy
│       ├── rooms/               # index, show (+ lightbox triggers)
│       ├── gallery/index.blade.php
│       ├── reservations/        # create, success
│       └── partials/            # schema-breadcrumb, lightbox (Olive Sanctuary modal)
└── tests/                       # 112 PHPUnit test (Feature + Unit), CI 2 driver matrix
    ├── Feature/                 # Public flow, Admin panel, Activity log, Legal pages,
    │                            # Profile password, BusinessSettings, Webp upload
    └── Unit/                    # ReservationCode, Nights, Status, RoomImageUrl,
                                 # ImageWebpConverter, AppTimezone
```

---

## Geliştirme Kuralları

CLAUDE.md Section 12'de tüm kurallar var. Özet:

- **Türkçe**: Tüm kullanıcı-yüzü metin TR. Schema.org gibi makina-okunabilir alanlar İngilizce kalabilir.
- **Stack değiştirme yok**: Laravel 12 + Filament 4 + Tailwind 4 + Flatpickr.
- **Basic kalır**: Booking.com / sezonsal fiyat / çoklu dil / online ödeme / loyalty SCOPE DIŞI.
- **Mail YOK**: 2026-05-23'te kaldırıldı. WhatsApp + dashboard + success page yeterli.
- **Yerel SEO öncelik**: Tüm güncellemeler "Varto otel" anahtar kelimesini destekler.
- **Tests**: Yeni feature için en az 1 feature test + 1 unit test.
- **Karar verme**: Açık karar noktaları için CLAUDE.md Section 10'a bak.

---

## Production Deploy ✅ CANLIDA

**Sunucu**: Contabo VPS 10 (Ubuntu 24.04, 4 vCPU / 8 GB RAM / 75 GB NVMe, Nürnberg)
**Stack**: nginx 1.24 + PHP 8.3.6-FPM + MariaDB 10.11 + Redis 7 + Composer 2.9 + Node 20
**SSL**: Cloudflare Origin Certificate (15 yıl, 2041'e kadar) + Full Strict mode + HSTS 1 yıl
**Hardening**: SSH key-only auth, non-root deploy user, UFW (22/80/443), fail2ban, unattended-upgrades

### Yeni deploy (kod değişikliği sonrası)

VPS'te `deploy` user olarak:

```bash
cd /var/www/kogsuitotel
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force                   # yeni migration varsa
php artisan livewire:publish --assets --force # Livewire asset publish (gerekirse)
npm ci && npm run build
php artisan config:clear && php artisan config:cache
php artisan route:cache && php artisan view:cache
```

### İlk deploy adımları

Detaylı tüm adımlar `deploy/README.md` ve **CLAUDE.md Section 8** ve **Section 11 (Faz 3)**.

### Önemli notlar

- **Cloudflare cache purge** — Livewire/Filament asset değişimi sonrası Cloudflare panel veya API ile cache purge (eski 404'ler cache'lenmiş olabilir)
- **nginx vhost** — `/livewire/` ve `/filament/` prefix location'ları `^~` modifier ile static asset regex'inden ÖNCE eşleşir, PHP route'a yönlendirilir
- **`config/app.php`** `'timezone' => env('APP_TIMEZONE', 'UTC')` — env'den okur, hardcoded değil

---

## Lisans

Bu proje özeldir. Tüm haklar Koğ Suit Otel'e aittir.
Web sitesi geliştirmesi [Varto Yazılım](https://vartoyazilim.com) tarafından yapılmıştır.
