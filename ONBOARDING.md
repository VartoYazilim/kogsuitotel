# Onboarding — Koğ Suit Otel

> İlk-gün rehberi. Detaylı bilgi için `README.md` ve `CLAUDE.md`'ye bak.

## 30 saniye

- **Ne**: Muş Varto'da yeni açılacak butik otelin web sitesi + admin paneli + rezervasyon sistemi
- **Stack**: Laravel 12 + Filament 4 + Tailwind 4 + Olive Sanctuary tasarım
- **Canlı**: [kogsuitotel.com](https://kogsuitotel.com) (Contabo VPS, Cloudflare proxy)
- **Hafıza**: `CLAUDE.md` — tüm kararlar, faz planı, çalışma kuralları

## 5 dakika — lokal kurulum

```bash
git clone https://github.com/VartoYazilim/kogsuitotel
cd kogsuitotel
cp .env.example .env

# ADMIN_PASSWORD'i .env'de set et (min 12 char + mixedCase + numbers — placeholder reddedilir)
# Örnek üretim: php -r "echo base64_encode(random_bytes(24)) . PHP_EOL;"

composer install
npm install
php artisan key:generate
php artisan migrate --seed
npm run build

php artisan serve   # http://localhost:8000
```

Admin: `http://localhost:8000/kog-yonetim/login` — `.env`'deki `ADMIN_EMAIL` + `ADMIN_PASSWORD`

## Kritik kurallar (ihlal etmeyin)

| Kural | Niye |
|---|---|
| **Türkçe** her kullanıcı yüzü metin | Yerel kitle, marka tutarlılığı |
| **Stack değiştirme yok** (Laravel 12 + Filament 4 + Tailwind 4 + Flatpickr) | Sahibin onayladığı tercih |
| **Mail SMTP YOK** — sadece WhatsApp + dashboard + success page | 2026-05-23 sahip kararı (deliverability + bakım yükü) |
| **Booking.com integration, çoklu dil, online ödeme, dinamik fiyat SCOPE DIŞI** | "Basic butik otel" |
| **Hızlı fix yok — her commit prod-grade** | Sahip uyarısı (`feedback-prod-grade-discipline`) |
| **Her büyük adımda onay** | Sessizce 50 dosya değiştirme |

## İlk-saat checklist

- [ ] `CLAUDE.md` Section 10 (açık kararlar) ve Section 11 (faz planı) oku
- [ ] `composer test` — 112 test geçiyor mu kontrol
- [ ] `php artisan serve` + tarayıcıda anasayfa + admin panel deneme
- [ ] Sahibinin tasarım yönü için: `base/DESIGN.md` ve `base/*.html` referans Stitch dosyaları
- [ ] Filament admin'i gez: Rezervasyonlar → Müsaitlik → Odalar → Galeri → Ayarlar → Profil

## Sık yapılan iş örnekleri

### Yeni public sayfa eklemek

1. `routes/web.php` → route + name + controller method
2. `app/Http/Controllers/PageController.php` → method
3. `resources/views/pages/<name>.blade.php` (extends `layouts.public`)
4. `config/seo.php` ve `app/Http/Controllers/SitemapController.php` → meta + sitemap
5. Test: `tests/Feature/<Name>Test.php` (response 200 + içerik assertion)

### Yeni admin Resource eklemek

```bash
php artisan make:filament-resource <Model> --view
```

Sonra: navigation group/sort + Türkçe label override + relation manager (varsa Activity log için `ActivitiesRelationManager::class`).

### Yeni Filament Page (form-tabanlı)

`App\Filament\Pages\BusinessSettings` örneğine bak — `HasForms` trait + `form(Schema)` + custom blade view.

### Görsel upload alanı eklemek

```php
FileUpload::make('field')
    ->image()
    ->disk('public')
    ->directory('rooms/covers')
    ->imageEditor()
    ->saveUploadedFileUsing(fn ($file) =>
        app(\App\Services\ImageWebpConverter::class)->convert($file, 'rooms/covers')
    )
```

Otomatik WebP dönüşüm + random isim + PNG alpha preserve.

## VPS Deploy (kod değişikliği)

```bash
# Local'de
git push origin main

# VPS'te (deploy user)
ssh root@164.68.108.73
sudo -iu deploy
cd /var/www/kogsuitotel
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force                   # yeni migration varsa
npm ci && npm run build
php artisan config:clear && php artisan config:cache
php artisan route:cache && php artisan view:cache

# Cloudflare cache (asset değişikliği varsa)
# https://dash.cloudflare.com → kogsuitotel.com → Caching → Purge Everything
```

## Yardım kaynağı

- **Proje hafızası**: `CLAUDE.md` (her oturum başında oku)
- **Tasarım sistemi**: `base/DESIGN.md` + `design.html` preview
- **Filament 4 docs**: https://filamentphp.com/docs/4.x
- **Laravel 12 docs**: https://laravel.com/docs/12.x

Sorularda Section 10 açık kararlarına bak — yoksa sahibe sor.
