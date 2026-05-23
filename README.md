# Koğ Suit Otel

[![CI](https://github.com/VartoYazilim/kogsuitotel/actions/workflows/ci.yml/badge.svg?branch=main)](https://github.com/VartoYazilim/kogsuitotel/actions/workflows/ci.yml)

Muş Varto'da açılacak yeni butik otelin resmi web sitesi ve rezervasyon sistemi.

**Domain**: [kogsuitotel.com](https://kogsuitotel.com) (Cloudflare DNS aktif, VPS satın alındığında canlıya alınır)
**Marka**: "Refined Hospitality in Varto"
**Geliştirici**: [Varto Yazılım](https://vartoyazilim.com)

> ℹ️ **Proje hafızası**: Tüm mimari kararlar, faz planları, açık karar noktaları ve çalışma kuralları için **`CLAUDE.md`** dosyasına bakın. Bu README sadece hızlı başlangıç içindir.

---

## Stack

- **Laravel 12.60** + **Filament 4.11** (admin paneli, custom panel theme)
- **PHP 8.3.30** (Laragon — prod parity Ubuntu 24.04 default ile uyumlu)
- **Tailwind v4** (CSS-first config, `@theme` directive)
- **SQLite** lokal · **PostgreSQL/MariaDB** prod (CI 2 driver matrix)
- **Flatpickr** (modern tarih seçici, TR locale, public site)
- **Filament native MFA** (Google Authenticator, `pragmarx/google2fa` + `bacon/bacon-qr-code`)
- **Spatie ActivityLog** (admin aksiyonları audit trail — KVKK m.12/3)
- **Spatie Laravel Sitemap** (yerel SEO için)
- **Olive Sanctuary** tasarım sistemi (zeytin yeşili + antika altın + sıcak krem, light + dark variant)

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
composer test                  # PHPUnit (53 test, 133 assertion)

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

**Multi-Factor Authentication (MFA)** — Filament 4 native:
- Login sonrası Google Authenticator / Authy ile QR code tara
- 6 haneli kod ile giriş; kaybedilirse recovery code'lar ile

İçerikler:
- **Operasyon** → Rezervasyonlar (kanban, filtre, WhatsApp aksiyonu, status workflow), Müsaitlik sorgusu
- **İçerik** → Odalar, Galeri Görselleri
- **Sistem** → Ayarlar (IBAN, telefon, saatler, sosyal medya)

**Audit trail** — admin status/oda/setting değişimleri `activity_log` tablosuna işlenir (Spatie ActivityLog, KVKK m.12/3 denetim).

---

## Önemli Dizin Yapısı

```
.
├── CLAUDE.md                    # Proje hafızası — başlamadan önce mutlaka oku
├── base/                        # Tasarım kaynakları (Stitch + DESIGN.md + LOGO)
├── design.html                  # Olive Sanctuary tasarım sistemi preview
├── app/
│   ├── Enums/ReservationStatus.php
│   ├── Filament/Resources/      # 4 admin resource (Reservation, Room, Gallery, Setting)
│   ├── Filament/Widgets/        # Dashboard widget'ları
│   ├── Models/                  # 4 model + relations
│   └── Http/Controllers/        # Public + Admin controllers
├── config/seo.php               # SEO konfigürasyon (yerel — Varto/Muş)
├── database/
│   ├── migrations/              # 5 + Notifications/JSONB + 2FA + ActivityLog
│   └── seeders/                 # Admin user, 5 oda, 6 galeri, 13 setting
├── lang/tr/                     # Türkçe lokalizasyon (validation, auth)
├── public/
│   ├── images/logo.svg          # Marka logosu (Olive Sanctuary)
│   ├── images/demo/             # Geçici görseller (Unsplash, WebP, 14 foto)
│   │   ├── hero/                # Ana sayfa arka plan + hikaye bölümü
│   │   ├── rooms/               # 5 oda cover (sahibin gerçek foto'larıyla değişecek)
│   │   ├── gallery/             # 6 galeri görseli (kategori bazlı)
│   │   └── og/                  # Sosyal medya OG image (JPG — uyumluluk)
│   ├── favicon.svg              # Sade favicon
│   ├── robots.txt               # AI crawler izinli
│   └── site.webmanifest         # PWA install
├── resources/
│   ├── css/app.css              # @theme + selection + scrollbar + microinteractions
│   ├── js/app.js                # Flatpickr + dinamik fiyat
│   └── views/
│       ├── layouts/public.blade.php
│       ├── pages/               # home, about, contact, faq, kvkk, privacy
│       ├── rooms/               # index, show
│       ├── gallery/index.blade.php
│       ├── reservations/        # create, success
│       └── partials/schema-breadcrumb.blade.php
└── tests/                       # 53 PHPUnit test (Feature + Unit), CI 2 driver matrix
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

## Production Deploy

VPS satın alındıktan sonra (Contabo Cloud VPS 10, Ubuntu 24.04):

1. `deploy/` klasöründeki örnek nginx config ve `deploy.sh`'i prod sunucuda kullan
2. `.env.production` şablonundan prod env oluştur
3. `php artisan migrate --force --seed`
4. Cloudflare proxy ON + Origin Certificate
5. `php artisan optimize` (config, route, view cache)

Detaylı adımlar **CLAUDE.md Section 8** ve **Section 11 (Faz 3)**.

---

## Lisans

Bu proje özeldir. Tüm haklar Koğ Suit Otel'e aittir.
Web sitesi geliştirmesi [Varto Yazılım](https://vartoyazilim.com) tarafından yapılmıştır.
