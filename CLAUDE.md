# Koğ Suit Otel — Proje Handoff

> Bu dosya Claude Code için proje hafızasıdır. Her oturum başında oku, kararlara
> bağlı kal, gerektiğinde "Açık Kararlar" bölümündeki noktaları kullanıcıya sor.

---

## 1. Proje Özeti

**Koğ Suit Otel**, Muş/Varto'da açılacak yeni bir butik otel. Bu proje, otelin
resmi web sitesini ve rezervasyon sistemini içerir.

- **Domain**: kogsuitotel.com (Turhost'tan alındı, Cloudflare DNS aktif)
- **Hedef kitle**: Türkçe konuşan misafirler (TR locale öncelikli)
- **Marka**: "Refined Hospitality in Varto" — minimalist, premium, sakin
- **Tasarımlar**: Google Stitch ile üretildi (HTML export + PNG + Design System.md
  uploads klasöründe)

---

## 2. Teknoloji Stack'i (Karar Verilmiş — Değiştirme)

### Backend / Framework
- **Laravel 12** (Filament 4'ün en yeni desteklediği Laravel sürümü;
  Laravel 13 mevcut ama Filament 4 henüz Laravel 13 desteklemiyor —
  `illuminate/contracts ^11.28|^12.0` constraint). Laravel 13'e geçiş,
  Filament 5 veya Filament 4.x'in Laravel 13 desteği eklenince yapılır.
- **Filament 4** (admin paneli, kurulu: v4.11.5)
- **PHP 8.3** (local Laragon 8.3.30 + prod Ubuntu 24.04 default = parity)

### Veritabanı
- **Local**: SQLite (geliştirme hızı için)
- **Production**: MariaDB 11.x (Ubuntu 24.04 default)

### Cache / Queue
- **Redis** (session, cache, queue)

### Frontend
- **Tailwind CSS 4** (Laravel 12 + Filament 4 default'u). CSS-first config
  (`@theme` directive), `tailwind.config.js` YOK. Tüm Olive Sanctuary token'ları
  `resources/css/app.css` içindeki `@theme` bloğunda.
- **Stitch HTML'leri Tailwind v3 syntax'ında** geldi — Blade'e port ederken
  küçük cleanup: inline `<script>tailwind.config={...}</script>` kaldırılır,
  custom token'lar app.css'ten geliyor.
- **Blade templates** (Stitch HTML'leri buraya taşınacak)
- Vanilla JS yeterli; Alpine.js Filament ile zaten geliyor
- React / Vue / Inertia YOK — gereksiz karmaşa

### Web Server
- **nginx + PHP-FPM 8.3** (production)
- Local'de Laragon (nginx mode)

### Mail
- Dış SMTP servisi — **Brevo veya Resend** arasında karar verilecek
- Local'de: `MAIL_MAILER=log` (storage/logs/laravel.log'a yazar)

### Deployment
- **Docker YOK, Coolify YOK** — native install
- Basit git pull + composer + artisan script
- Detay: bölüm 8

---

## 3. Geliştirme Ortamı

### Local Setup (Windows + Laragon)
- Laragon kurulu, PHP 8.3 aktif
- **Repo yolu**: `C:\Users\WORK\Documents\Github\VartoYazilim\kogsuitotel` (git çalışma dizini)
- Local URL: `https://kogsuitotel.test` (Laragon vhost manuel kuruldu — Github
  klasörüne özel vhost; ya da `php artisan serve` ile `http://localhost:8000`)
- Node.js 20+ (Filament asset build için)
- Tasarım kaynakları (Stitch HTML/PNG, logo, DESIGN.md): `./base/` klasöründe.
  Bunlar referans dosyalarıdır, Blade'e taşınınca silinmeyecek (versiyonda kalsın).

### İlk Kurulum Komutları (Yapıldı — referans için)
```bash
# Laragon PHP 8.3.30 ile composer çağrılıyor:
PATH="/c/laragon/bin/php/php-8.3.30-Win32-vs16-x64:$PATH" composer create-project laravel/laravel temp-app
# temp-app içeriğini bu dizine taşı, temp-app'i sil. .git, CLAUDE.md, base/, design.html korunur.
PATH="/c/laragon/bin/php/php-8.3.30-Win32-vs16-x64:$PATH" composer require filament/filament:"^4.0"
PATH="/c/laragon/bin/php/php-8.3.30-Win32-vs16-x64:$PATH" php artisan filament:install --panels
```

> Her artisan/composer komutunda Laragon PHP'sini PATH'e prepend etmek gerek;
> sistem PATH'inde PHP 8.5 var. İlerideki tüm komutlarda bu prefix korunur.

### Önemli Notlar
- `.env` dosyası git'e GİTMEYECEK (`.gitignore` kontrolü ilk commit'te yapılacak)
- `APP_KEY` her ortam için ayrı (`php artisan key:generate`)
- Local'de `APP_DEBUG=true`, prod'da `APP_DEBUG=false` (kesin)

---

## 4. Veri Modeli

### Tablolar (Migration sırası)

**users** (Laravel default + Filament uyumlu)
- id, name, email, email_verified_at, password, remember_token, timestamps
- `is_admin` boolean (Filament panel erişimi için)

**rooms**
- id
- name (string) — "Standart Oda", "Suit Oda", "Aile Odası", "Deluxe Suit", "Premium Süit"
- slug (string, unique)
- description (text)
- capacity (integer) — kaç kişi
- base_price (decimal 10,2) — TL cinsinden gecelik
- features (json) — ["Wi-Fi", "Klima", "TV", "Minibar", "Jakuzi", ...]
- cover_image (string, nullable) — storage path
- gallery (json, nullable) — ["path1.jpg", "path2.jpg"]
- is_active (boolean, default true)
- sort_order (integer)
- timestamps

**reservations**
- id
- reservation_code (string, unique) — "KSO-2026-0001" formatı, otomatik
- room_id (foreign key)
- guest_first_name (string)
- guest_last_name (string)
- guest_phone (string)
- guest_email (string)
- check_in (date)
- check_out (date)
- adults (integer)
- children (integer, default 0)
- nights (integer, hesaplanmış)
- total_price (decimal 10,2)
- special_requests (text, nullable)
- status (enum) — pending, confirmed, paid, completed, cancelled, no_show
- admin_notes (text, nullable) — sadece admin görür
- created_at, updated_at

**gallery_images**
- id
- category (string) — "exterior", "rooms", "lobby", "view"
- path (string)
- alt_text (string)
- sort_order (integer)
- timestamps

**settings** (key-value store)
- key (string, unique) — "iban", "iban_holder", "bank_name",
  "phone", "whatsapp", "email", "address", "checkin_time", "checkout_time"
- value (text)

### İlk Seeder İçeriği
- 1 admin user (env'den ADMIN_EMAIL + ADMIN_PASSWORD)
- 5 oda (Stitch tasarımlarındaki: Standart, Suit, Aile, Deluxe, Premium)
- Settings tablosu için placeholder değerler (sahibi sonra dolduracak)

---

## 5. Sayfa Yapısı (Public Site)

Stitch'in ürettiği HTML'ler `./base/` altında. Her birini Blade'e taşırken
`./base/DESIGN.md`'deki design tokens'i `tailwind.config.js`'e aktar.

### Route'lar (referans Stitch dosyaları)
- `/` — Ana Sayfa → `base/ana_sayfa_ko_suit_otel.html`
- `/odalar` — Odalar listesi → `base/odalar_ko_suit_otel.html`
- `/odalar/{slug}` — Tek oda detay → `base/oda_detay_ko_suit_otel.html`
  (özel varyantlar: `base/premium_suit_oda_*.html`)
- `/galeri` — Galeri (kategori filtreli) → `base/galeri_ko_suit_otel.html`
- `/hakkimizda` — Hakkımızda → `base/hakk_m_zda_ko_suit_otel.html`
- `/iletisim` — İletişim → `base/i_leti_im_ko_suit_otel.html`
- `/sss` — SSS → `base/s_k_sorulan_sorular_ko_suit_otel.html`
- `/rezervasyon` — Rezervasyon formu → `base/rezervasyon_ko_suit_otel.html`
- `/rezervasyon/basarili/{code}` — Başarı/IBAN sayfası →
  `base/deme_ve_onay_bilgisi_ko_suit_otel.html`

### Layout
- `layouts/public.blade.php` — header (logo + nav + Rezervasyon Yap butonu),
  footer (Menü + Yasal)
- Header'da nav: Ana Sayfa, Odalar, Galeri, Hakkımızda, İletişim
- Footer linkleri: KVKK (`/kvkk`), Gizlilik Sözleşmesi (`/gizlilik`)

---

## 6. Admin Paneli (Filament)

### Panel Konfigürasyonu
- **Path: `/kog-yonetim`** — `/admin` DEĞİL (security through obscurity)
- Brand name: "Koğ Suit Yönetim"
- Tek panel (multi-tenancy yok)
- Login required; `is_admin = true` olmayan kullanıcı erişemez

### Resource'lar
1. **ReservationResource** (en önemli)
   - Liste: kanban view ile status sütunları (pending → confirmed → paid → completed)
   - Filtreler: tarih aralığı, oda, status, telefon arama
   - Aksiyon butonları: "Onayla", "Ödendi olarak işaretle", "İptal et", "WhatsApp'tan ara"
   - WhatsApp aksiyon: `https://wa.me/90{phone}?text=Merhaba+{name}...` link açar
   - Tek kayıt detay: tüm misafir bilgileri + admin notes düzenlenebilir
   - Yeni rezervasyon manuel oluşturulabilir (telefon/walk-in için)

2. **RoomResource**
   - CRUD, sıralama drag-and-drop
   - Görsel upload (Filament FileUpload, image)
   - Features tag-input

3. **GalleryImageResource**
   - Kategori bazlı listeleme
   - Toplu upload
   - Drag-and-drop sıralama

4. **SettingResource** (veya Filament Settings plugin)
   - IBAN, iletişim, çalışma saatleri vs.

### Dashboard Widget'ları
- Bugünün giriş/çıkışları
- Bekleyen rezervasyon sayısı (pending status)
- Bu ayın gelir özeti (paid + completed)
- Son 7 günlük rezervasyon grafiği

### Kullanıcı Rolleri
- **Faz 1**: Tek admin user (otel sahibi). Filament default auth yeterli.
- **Faz 2** (sonra eklenebilir): Spatie Permission + Filament Shield ile
  birden fazla personel + roller (resepsiyon vs. yönetici)
- **2FA**: Filament resmi 2FA plugin'i (faz 2'de eklenebilir, prod canlıya
  alınmadan ÖNCE)

---

## 7. Rezervasyon Akışı

### Müşteri Tarafı
1. Müşteri `/rezervasyon` formunu doldurur (kişisel bilgiler + tarihler + oda)
2. Form submit → backend validation
3. `Reservation` kaydı oluşturulur, `status = pending`
4. Müşteri `/rezervasyon/basarili/{code}` sayfasına yönlendirilir
5. Başarı sayfasında:
   - Rezervasyon kodu
   - Özet (tarih, oda, toplam tutar)
   - **IBAN bilgisi** (Settings tablosundan)
   - "Dekontu WhatsApp'tan gönderin: +90 XXX" talimatı
6. Müşteriye onay maili gider (rezervasyon kodu ile)

### Admin Tarafı
1. Yeni rezervasyon → admin'e mail bildirimi (hemen)
2. Admin paneline rezervasyon `pending` olarak düşer
3. Müşteri WhatsApp'tan dekont gönderir
4. Admin manuel olarak status'u `paid` yapar
5. Giriş günü geldiğinde `confirmed` (opsiyonel)
6. Çıkış sonrası `completed`

### Ödeme YOK
- Online ödeme entegrasyonu yapılmayacak
- Iyzico/PayTR vs. ENTEGRASYONU YAPMA
- Tüm ödeme akışı havale + WhatsApp ile manuel

---

## 8. Production Deployment

### Hedef Ortam (henüz satın alınmadı)
- **Sağlayıcı**: Contabo Cloud VPS 10
- **Spec**: 4 vCPU / 8 GB RAM / 75 GB NVMe
- **Lokasyon**: Almanya (Nürnberg veya Düsseldorf)
- **OS**: Ubuntu 24.04 LTS
- **Maliyet**: ~€44/yıl + bir kerelik setup fee

> ⚠️ VPS henüz alınmadı. Local geliştirme bitip sahibe demo gösterilecek,
> onay + ödeme sonrası VPS satın alınacak. O zamana kadar deploy konularına
> girmeyelim.

### Deploy Hazırlığı (VPS geldiğinde yapılacak — şimdi DEĞİL)

#### 8.1. VPS Hardening
- SSH key auth (password disabled)
- Root login disabled
- Non-root `deploy` user (sudo yetkili)
- UFW firewall: sadece 22, 80, 443
- **Origin firewall**: 80/443 sadece Cloudflare IP aralıklarına açık
  (https://www.cloudflare.com/ips/ — script ile UFW'ye işlenecek)
- fail2ban (SSH ve nginx için)
- `unattended-upgrades` aktif (otomatik güvenlik patch'leri)
- SSH port 22 → opsiyonel değiştirme (zorunlu değil, Cloudflare zaten önde)

#### 8.2. Base Stack
- nginx
- PHP 8.3-FPM + uzantılar: mbstring, xml, mysql, curl, zip, gd, intl, redis, bcmath
- MariaDB 11.x
- Redis
- Composer 2.x
- Node.js 20 LTS + npm
- Certbot (Cloudflare Origin Certificate kullanılacak ama backup için)

#### 8.3. Cloudflare
- DNS zaten aktif (kogsuitotel.com → CF)
- A record: kogsuitotel.com → VPS IP, **proxy ON (turuncu bulut)**
- SSL/TLS mode: **Full (Strict)**
- Cloudflare Origin Certificate oluştur (15 yıl), nginx'e koy
- Always Use HTTPS: ON
- Auto HTTPS Rewrites: ON
- Bot Fight Mode: ON
- Security Level: Medium

#### 8.4. Deploy Script
Basit bash script `/home/deploy/deploy.sh` (prod yolu `/var/www/kogsuitotel`):
```bash
cd /var/www/kogsuitotel
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
npm ci && npm run build
sudo systemctl reload php8.3-fpm
```

#### 8.5. Backup — Local VPS Storage + Spatie Laravel Backup

**Tercih edilen**: VPS içinde local storage (`/var/backups/kogsuitotel/`).
Cloudflare R2 / Backblaze B2 / AWS gibi 3.taraf storage'lar değerlendirildi
ve **reddedildi** (2026-05-24 sahip kararı):
- Sahip işletme kart bilgisi vermek istemiyor (R2 subscribe + B2 hesap)
- Vendor lock-in riski (CF free mode kapansa? Sahip teknik takip etmiyor)
- Sahip "Local çözüm bul" direktifi

**Trade-off (kabul edilen risk)**: VPS disk crash → data kaybı. Single
point of failure. Geliştirici (Varto Yazılım) periodik SCP ile dışarı
indirme yapmak zorunda (manuel arşivleme). Faz 4'te NAS/Hetzner Storage
Box gibi ek katman düşünülebilir (sahip karar verirse).

**Paketler** (composer):
- `spatie/laravel-backup ^10` — backup orchestration
- `league/flysystem-aws-s3-v3 ^3` — S3 driver (transitive dep, hot-swap için)

**Config** (`config/backup.php` + `config/filesystems.php`):
- `local-backup` disk: `local` driver, `root: /var/backups/kogsuitotel`
- Destination disk: `['local-backup']`
- `filename_prefix`: `kogsuit_`
- Encryption: AES-256 (`BACKUP_ARCHIVE_PASSWORD` env zorunlu)
- Retention: 7 + 16 günlük + 8 haftalık + 4 aylık + 2 yıllık + **5GB cap**
  (VPS 75GB diskin %7'sini geçmez)
- Notifications: kapalı (mail YOK kararı) — backup olayları
  `storage/logs/laravel.log`'a yazılır

**Scheduled tasks** (`routes/console.php`):
- `03:00` — `backup:run` (Europe/Istanbul, onOneServer, withoutOverlapping)
- `04:00` — `backup:clean` (retention strategy uygula)
- `04:30` — `backup:monitor` (sağlık check)
- `hourly` — disk usage %80 üstüne çıkarsa log warning

**VPS crontab** (deploy user):
```
* * * * * cd /var/www/kogsuitotel && php artisan schedule:run >> /dev/null 2>&1
```

**VPS setup** (ilk deploy sırasında):
```bash
# Backup dizini oluştur (root user)
mkdir -p /var/backups/kogsuitotel
chown deploy:deploy /var/backups/kogsuitotel
chmod 750 /var/backups/kogsuitotel
```

**.env**: Sadece `BACKUP_ARCHIVE_PASSWORD` (R2/AWS env yok). Üretmek için:
```bash
php -r "echo base64_encode(random_bytes(32)) . PHP_EOL;"
```
Bitwarden gibi yere kaydet — restore için zorunlu.

**Geliştirici manuel arşivleme** (haftalık/aylık):
```bash
# Local makineden SCP ile backup'ı indir (encrypted, AES-256)
scp deploy@164.68.108.73:/var/backups/kogsuitotel/kogsuit_*.zip ~/Backups/kogsuit/

# Veya rsync ile sadece yeni dosyaları al
rsync -av --progress deploy@164.68.108.73:/var/backups/kogsuitotel/ ~/Backups/kogsuit/
```

**Restore**:
```bash
# 1. Backup zip'i hedef VPS'e koy
scp ~/Backups/kogsuit/kogsuit_2026-XX-XX.zip deploy@vps:/tmp/

# 2. BACKUP_ARCHIVE_PASSWORD ile zip aç (Bitwarden'dan al)
unzip -P "$BACKUP_ARCHIVE_PASSWORD" /tmp/kogsuit_2026-XX-XX.zip -d /tmp/restore/

# 3. DB dump'ı restore (MariaDB)
mariadb -u kogsuit -p kogsuitotel < /tmp/restore/db-dumps/*.sql

# 4. storage/app dosyalarını yerine koy
rsync -av /tmp/restore/var/www/kogsuitotel/storage/app/ /var/www/kogsuitotel/storage/app/

# 5. Cleanup
rm -rf /tmp/restore /tmp/kogsuit_2026-XX-XX.zip
```

Detay: `config/backup.php` yorumları + `tests/Feature/BackupConfigTest.php`.

---

## 9. Güvenlik Kontrol Listesi

### Geliştirme Sırasında
- [ ] `.env` `.gitignore`'da
- [ ] `APP_KEY` her ortam için unique
- [ ] Mass assignment için `$fillable` her modelde tanımlı
- [ ] Form validasyonları Request sınıflarında
- [ ] File upload validation (mime type + size + dimension)
- [ ] Rate limiting rezervasyon formuna (`throttle:5,1` örneği)
- [ ] CSRF token tüm formlarda (Laravel default)
- [ ] Honeypot field rezervasyon formunda (bot koruması)

### Prod'a Geçmeden Önce
- [ ] `APP_DEBUG=false`
- [ ] `APP_ENV=production`
- [ ] Telescope/Debugbar dev-only
- [ ] Admin user şifresi güçlü (min 20 karakter, random)
- [ ] Filament admin path obfuscated (`/kog-yonetim`)
- [ ] 2FA aktif edilmiş
- [ ] DB credentials güçlü, .env'de
- [ ] Storage permissions doğru (`storage/`, `bootstrap/cache/` 775)
- [ ] Origin firewall (CF IP whitelist) aktif
- [ ] Log dosyaları log rotation'a alınmış
- [ ] Sentry veya basit error mail bildirimi
- [ ] Robots.txt + sitemap.xml
- [ ] KVKK aydınlatma metni hazır + `/kvkk` route'u
- [ ] Çerez bilgilendirmesi (Cloudflare Analytics çerez gerektirmiyor,
      Google Analytics kullanılacaksa banner şart)

---

## 10. Açık Kararlar (Kullanıcıya Sor)

Bu noktalar henüz netleştirilmedi. İlgili faza gelindiğinde kullanıcıya sor:

1. **Mail servisi**: Brevo (300/gün ücretsiz tier, TR'ye iyi delivery) mi,
   Resend (3000/ay ücretsiz, modern API) mi? Karar: Faz 7'ye gelindiğinde.

2. **WhatsApp entegrasyonu**: Faz 1'de manuel link (`wa.me/...`) yeterli.
   Faz 2'de WhatsApp Business Cloud API ile otomatik mesaj gönderimi
   düşünülebilir (Meta hesabı + iş doğrulama gerekiyor).

3. ~~**Google Analytics**~~ → **EKLENDİ** (2026-05-24, commit `fb41111`).
   GA4 (G-B9M3M8L95H) + Consent Mode v2 default deny + KVKK uyumlu cookie
   consent banner + `anonymize_ip` aktif + Google LLC ABD yurt dışı aktarım
   disclosure (`/kvkk` 4.2 + `/cerez-politikasi` 3). Pattern detay:
   `memory/reference-kvkk-cookie-consent-pattern.md`.

4. **Galeri + oda fotoğrafları**: 2026-05-23'te demo amaçlı **Unsplash
   kaynaklı 14 görsel** eklendi (`public/images/demo/` altında: hero,
   rooms/, gallery/, og/). RoomSeeder + GalleryImageSeeder bu path'leri
   set ediyor. Model accessor (`cover_image_url`, `path_url`) hem demo
   (`images/demo/...`) hem Filament admin upload (`storage/...`)
   pattern'ini handle eder.

   Sahibin gerçek foto'ları geldiğinde Filament admin'den FileUpload ile
   tek tek değiştirilir; demo dosyaları `public/images/demo/` altında kalır
   (ek bir referans/snapshot için), CLAUDE.md notu güncellenir. **Sahibinden
   ne zaman talep edilir?** — Faz 2k canlı demo'dan sonra.

5. **Oda fiyatları**: Sahibinden alınacak. Şimdilik seeder'da placeholder
   (1500 TL, 2000 TL, 2400 TL, 3500 TL, 5000 TL gibi).

6. **Sezonsal fiyatlandırma**: Faz 1'de sabit fiyat. İleride sezon/hafta sonu
   farklılaştırma istenirse Faz 2.

7. **Çoklu dil**: Şimdilik sadece Türkçe. İngilizce gerekirse Laravel'in
   localization sistemiyle eklenir (Faz 2+).

8. ~~**Audit log**~~ → **UYGULANDI** (2026-05-23, commit `bff796b`).
   `spatie/laravel-activitylog` v4.12 + Reservation/Room/Setting modellerine
   `LogsActivity` trait + `getActivitylogOptions()`. Status, fiyat, IBAN
   değişimleri `activity_log` tablosuna `properties.old/attributes` ile
   yazılır. (Filament UI relation manager Faz 4'te eklenebilir.)

9. **2FA**: 2026-05-23'te entegre edildi (commit `bff796b`), sahibin kararıyla
   aynı gün KALDIRILDI (sahip "2FA kaldır" talimatı verdi — demo basit kalsın
   diye). Filament 4 native MFA + `pragmarx/google2fa` + recovery code'lar
   hepsi geri çıkarıldı. Tek faktör güvenlik (email+password) riski kabul edildi
   — admin password disiplini (env zorunlu, min 12 char) + güvenli network
   (Cloudflare proxy + Origin IP whitelist) ile ek katman. Gelecek talep
   gelirse: Filament 4 native `multiFactorAuthentication([AppAuthentication::make()])`
   tek satırla geri eklenebilir (paket zaten Filament transitive dep).

10. ~~**KVKK saklama süresi**~~ → **GEÇİLDİ** (2026-05-24, sahip kararı).
    Anonimleştirme cron'u **yapılmayacak**. Hukuki risk kabul edildi —
    sahip ileride avukat görüşü sonrası yeniden değerlendirebilir.

11. ~~**VERBİS kayıt durumu**~~ → **KAPALI** (2026-05-24, sahip araştırdı).
    Otel ölçeğinde VERBİS kaydı **zorunlu değil** (KVKK Kurulu 2018/87
    kararı muafiyet eşiği altında: çalışan < 50, yıllık ciro < 100M).

12. **KVKK metinleri (2h)** → 2026-05-24 itibariyle yazılıyor:
    `/kvkk` (aydınlatma metni m.10), `/gizlilik` (politika),
    `/cerez-politikasi` (Cloudflare Web Analytics çerezsiz notu).
    Faz 2h kapatılıyor.

---

## 11. Geliştirme Sırası (Sıralı Plan)

### Genel Durum (2026-05-24 sonu — Faz 3 KAPANDI, SİTE CANLIDA)

- ✅ **Faz 1** tamam
- ✅ **Faz 2** kod + içerik kapandı (2e sahip foto'ları için bekliyor)
- ✅ **Faz 2h KVKK metinleri** — /kvkk + /gizlilik + /cerez-politikasi (commit `457f079`)
- ✅ **Sahip demo onayladı** (2026-05-24)
- ✅ **VERBİS kapalı** — zorunlu değil (otel ölçeği muafiyet)
- ❌ **KVKK saklama süresi** geçildi — anonimleştirme cron yok (hukuki risk kabul)
- 🚀 **Faz 3 TAM KAPANDI** — site **kogsuitotel.com** CANLIDA (Contabo VPS 10, Cloudflare proxy, SSL Full Strict)
- 🌐 **Public URL'ler**: `kogsuitotel.com`, `www.kogsuitotel.com`, `yonetim.kogsuitotel.com` (→ /kog-yonetim)
- 📊 **Google Analytics 4** aktif (G-B9M3M8L95H, Consent Mode v2 default deny, KVKK m.5/m.9 uyumlu cookie banner)
- 🔍 **Google Search Console** verified (Cloudflare-GSC otomatik DNS TXT)
- 💾 **Spatie Backup + Cloudflare R2** hazır (10GB ücretsiz, encrypted AES-256, retention 5GB cap, scheduled task'lar daily 03:00) — VPS aktivasyon R2 token sonrası
- 🧪 **127 PHPUnit test, 433 assertion** — Pint temiz, Larastan 0 hata, CI 2 driver matrix
- 🎨 Olive Sanctuary palette + dark mode + vectorized logo + Filament custom panel theme

### Faz 2 Sonrası Ek Polish (2026-05-24 — bu seans)

| Commit | Alan | Test |
|---|---|---|
| `f41bb80` | Admin ImageColumn polymorphic accessor (demo + Filament upload + external) | mevcut yeşil |
| `e384fec` | Çoklu oda galerisi public + vanilla JS lightbox + `Room::resolvePublicUrl` helper + `gallery_urls` accessor + RoomSeeder demo gallery | +13 |
| `ffa890e` | `App\Services\ImageWebpConverter` (PHP GD q=82, alpha preserve, random isim) + 3 FileUpload `saveUploadedFileUsing` entegre | +10 |
| `1ef6110` | Filament 4 `->profile(isSimple: false)` + `Password::defaults(min(12)+mixedCase+numbers)` | +7 |
| `1a23422` | SettingResource → `App\Filament\Pages\BusinessSettings` redesign (4 Section + IBAN auto-cleanup + batch save + custom slug `/ayarlar`) | +10 |

**Toplam (Faz 2 polish):** 53 → 93 test, 133 → 287 assertion.

### Faz 3 VPS Deploy + Sonrası (2026-05-24 sonu — aynı seans devam)

| Alan | Detay |
|---|---|
| **VPS hardening** | SSH key-only (`id_ed25519_varto`), `deploy` user sudo NOPASSWD, UFW (22/80/443 → CF IP whitelist İş 5 ile), fail2ban, unattended-upgrades, sshd `99-kog-hardening.conf` cloud-init override |
| **Base stack** | nginx 1.24 + PHP 8.3.6-FPM + MariaDB 10.11.14 + Redis 7.0.15 + Composer 2.9.8 + Node 20.20.2 + Certbot 2.9.0 |
| **DNS + SSL** | Cloudflare API ile Vercel A records temizliği, VPS IP A records, Origin Cert (15 yıl, RSA 2048), Full Strict SSL, HSTS 1 yıl, HTTP/2 |
| **nginx vhost** | HTTP→HTTPS 301, real_ip CF, gzip, asset 1y cache, `^~ /livewire/` + `^~ /filament/` prefix location (static regex'ten ÖNCE) |
| **Repo deploy** | `git clone` deploy user, `.env` prod (APP_URL + güçlü ADMIN_PASSWORD + DB pass random 24-char), `composer install --no-dev`, `npm ci && npm run build`, `migrate --seed`, optimize cache |
| **Livewire fix** | `livewire:publish --assets --force` + nginx vhost `^~ /livewire/` block + Cloudflare cache purge (eski 404 cache) |
| **Admin login fix** | `config:clear → db:seed AdminUserSeeder → config:cache` sırası (env() okuma için) |
| **Vercel cleanup** | `*.kogsuitotel.com` wildcards + `_domainconnect` CNAME silindi |
| **yonetim subdomain** | A record + nginx server block, 301 redirect `https://kogsuitotel.com/kog-yonetim$request_uri` (path preserve) |
| **İş 9: Activity log Filament UI** | `ActivitiesRelationManager` DRY class, Reservation+Room detayında "Geçmiş" tab + diff render helper (eski → yeni) |
| **İş 10: Timezone fix** | `config/app.php` `'timezone' => env('APP_TIMEZONE', 'UTC')` (hardcoded UTC kaldırıldı) |
| **İş 11: README + ONBOARDING** | README VPS canlı + 112 test güncel, yeni ONBOARDING.md (5dk-onboarding rehberi) |
| **İş 5: Origin firewall** | UFW 80/443 sadece Cloudflare IPv4 + IPv6 aralıklarına açık, SSH (22) anywhere key-only |

**Toplam (Faz 3 sonrası):** 93 → 112 test, 287 → 379 assertion (+19: 8 activity + 3 timezone + 8 KVKK).

**Çözülen 8 deploy gotcha** (memory `reference-vps-deploy-gotchas.md`):
1. Livewire asset 404 (publish + nginx prefix block)
2. Cloudflare cache'lenmiş 404 (purge)
3. nginx 1.24 `http2 on;` desteklemez (`listen ... ssl http2;`)
4. sshd_config.d cloud-init override (99-kog-hardening.conf)
5. config:cache + env() çakışması (clear → seed → cache sırası)
6. `$realpath_root` boş (SFTP ile vhost upload, `$document_root` kullan)
7. Git "dubious ownership" (`safe.directory '*'` + deploy user heredoc)
8. Vercel DNS kalıntıları (API ile DELETE)

### Bu Oturumun (2026-05-22 → 23) Önemli Eklemeleri

| Alan | Detay |
|---|---|
| **Reservation invariant guard** | `Reservation::saving` event → kapasite + çakışma kontrolü (defense in depth, model layer'da koruma) |
| **Admin Müsaitlik sayfası** | `/kog-yonetim/availability` — wire:model.live tarih aralığı, oda durumu kartları, çakışan rez. listesi |
| **Filament admin polish** | Navigation groups, sidebar collapsible, SPA mode, database notifications (bell + 30s polling), user menu "Siteyi Aç" |
| **Welcome widget** | Inline CSS (Filament panel Tailwind scan etmediği için), gradient + 3 metrik |
| **Database notifications** | Yeni rezervasyon → admin'lere `ReservationCreated` notification → bell ikonu |
| **Settings kategori gruplama** | SQL CASE ile manuel sıra: Banka → İletişim → Konaklama → Sosyal Medya |
| **Logo SVG** | Sahibin vectorized JPG'si `sed` ile Olive Sanctuary'ye dönüştürüldü, header/footer/schema/manifest/admin'de aktif |
| **Test coverage** | 22 → 38 test (+16), admin sayfa render testleri + notification dispatch testleri + invariant testleri |

### Bu oturumda öğrenilen dersler (memory'de kalıcı)

- `feedback-domain-thinking-discipline.md` — 6 lens checklist (invariant/state/persona/defense/end-to-end/edge)
- `feedback-test-coverage-discipline.md` — Filament/notification değişikliği sonrası runtime test ekle + Postgres CI matrix
- `reference-filament4-namespaces.md` — Filament 3→4 namespace farkları cheatsheet + custom panel theme kurulumu
- `feedback-prod-grade-discipline.md` — Hızlı fix yok; her commit prod-grade kalitede

### Laravel Cloud demo deploy sonrası prod-grade düzeltmeler (2026-05-23 sonu)

Laravel Cloud free tier (cloud.laravel.com) ile sahibe demo deploy yapıldı.
Postgres-spesifik 2 hata + Filament UI dark mode + admin login disiplini için
**hızlı hotfix pattern'ı reddedildi** (sahip "her şey prod seviyesinde olmalı"
talimatı). Hotfix'ler doğru çözümle değiştirildi:

| Alan | Hotfix (kabul edilmedi) | Prod-grade çözüm (uygulanan) |
|---|---|---|
| **Notifications JSONB** | (yok, hotfix-1 doğruydu) | `notifications.data` TEXT→JSONB migration (driver-aware ALTER), yeni install için de doğru |
| **SettingResource groups** | Group::make tamamen silindi | `Group::make()->orderQueryUsing(closure)` ile Postgres-uyumlu raw `CASE WHEN` ORDER BY; collapsible accordion korundu |
| **Filament UI dark mode** | `darkMode(false)` | Custom panel theme (`make:filament-theme kog`) + Olive Sanctuary light + dark variant tasarlandı, `darkMode(true)` sistem tercihi destekli |
| **availability.blade.php inline CSS** | Tamamen inline `style="..."` hack | Custom panel theme sayesinde Tailwind class'lar custom Blade'lerde çalışır → Tailwind class'lara geri dönüldü, sürdürülebilir kod |
| **Admin login** | `.env` placeholder + seeder `'changeme123'` fallback | `.env.example` placeholder yok, seeder `ADMIN_PASSWORD` env zorunlu (boş veya placeholder ise Exception); `.env.example`'da `openssl rand -base64 32` talimatı |
| **CI test gap (driver)** | Sadece SQLite → Cloud Postgres sürprizleri | GitHub Actions `services: postgres:16` + her PHPUnit step 2 kez (SQLite + Postgres matrix) |

**Sonuç:** 51 test/128 assertion hâlâ yeşil, Pint temiz, Larastan 0 hata.
Filament panel artık dark mode destekli, custom Blade'ler Tailwind-temiz,
admin password disiplini prod-grade, CI Cloud-eşdeğer driver'la test ediyor.

### Demo görseller + prod-grade güvenlik (2026-05-23 son blok)

Cloud demo deploy stabil hale geldikten sonra eklenen iyileştirmeler:

| Alan | Detay |
|---|---|
| **Demo görseller** | 14 Unsplash foto `public/images/demo/` (hero/rooms/gallery/og), Room + GalleryImage modellerine `cover_image_url` / `path_url` accessor (demo + Filament upload path polymorphic). RoomSeeder + GalleryImageSeeder demo path'leri set ediyor. |
| **WebP convert** | 13 görsel PHP GD ile `.jpg` → `.webp` (q=82), **~41% tasarruf** (3.7 MB → 2.2 MB) — LCP iyileştirme. OG image `.jpg` kalır (sosyal medya uyumluluğu). |
| **Hero arka plan** | Gradient backdrop → `varto-anatolia.webp` arka plan + Olive Sanctuary gradient overlay (90/75/55% opacity), LCP `eager` + `fetchpriority=high`. |
| **2FA** | ~~Filament 4 native MFA eklendi~~ → Sahibin kararıyla AYNI GÜN kaldırıldı (demo basit kalsın). Section 10 madde 9'da detay. |
| **Audit log** | `spatie/laravel-activitylog` v4.12, Reservation/Room/Setting modellerine LogsActivity trait + getActivitylogOptions. `activity_log` tablosu, `properties.old/attributes` JSON. KVKK m.12/3 denetim altyapısı. |
| **Test coverage** | 51 → **53 test, 133 assertion** (+2 audit log: reservation status + setting value değişimi). |
| **Mobile fix** | Hamburger menü saf JS (Alpine yok), Flatpickr `disableMobile:true`, hero form sırası "oda → tarih" (sahibin tercihi). |
| **Availability sayfası** | Native `<input type=date>` → Filament HasForms + DatePicker (TR locale + Olive Sanctuary + mobile responsive). |

### Faz 1 — Lokal Altyapı + Public Site (TAMAMLANDI ✓)

1. ✅ Karar verme aşaması bitti
2. ✅ Laravel 12 + Filament 4 kurulumu (Laragon PHP 8.3.30)
3. ✅ Git repo, `.env`/`.gitignore` doğrulandı
4. ✅ Filament panel `/kog-yonetim`, Olive Sanctuary palette uygulandı
5. ✅ 5 migration (users.is_admin, rooms, reservations, gallery_images, settings)
6. ✅ Modeller (Room, Reservation, GalleryImage, Setting) + relationships
   + `ReservationStatus` enum (HasLabel/HasColor/HasIcon)
7. ✅ Seeder'lar (admin user, 5 oda, 13 setting placeholder)
8. ✅ 4 Filament Resource Türkçe label + filtre + WhatsApp/Onayla/Ödendi/İptal aksiyonları
9. ✅ Tailwind v4 + Olive Sanctuary token'ları (`@theme` CSS-first)
   + Manrope/Inter (self-hosted, KVKK uyumlu)
10. ✅ 9 public sayfa (Ana, Odalar, Oda detay, Galeri, Hakkımızda, İletişim,
    SSS, Rezervasyon, Rezervasyon başarılı) + KVKK + Gizlilik placeholderları
11. ✅ Rezervasyon flow: form → validation → store → success/IBAN → admin'e düşer
12. ✅ Türkçe lokalizasyon (Filament UI + Laravel core validation/auth/passwords)
13. ✅ Honeypot + rate limiting (5/dk) + CSRF
14. ✅ Schema.org JSON-LD (Hotel + HotelRoom)
15. ✅ Laragon vhost (junction kurulu), dev server testleri 200 OK

### Faz 2 — İterasyon, Polish, Demo Hazırlığı

**2a · Bildirimler — İPTAL EDİLDİ (2026-05-23)**
- Mail bildirimi proje scope'undan tamamen çıkarıldı (üçüncü-parti SMTP free
  tier riski + deliverability yükü + bakım giderleri).
- Yerine geçen mevcut akış: **Dashboard widget'ları** (sahibe bekleyen rez.
  sayısı görünür — Faz 2b) + **Success sayfası** (IBAN + özet + WhatsApp link
  zaten var) + **WhatsApp manuel iletişim** (CLAUDE.md ana akış).
- Silinen dosyalar: `app/Mail/`, `resources/views/emails/`,
  `ReservationController::sendNotifications()`.
- `.env`'de `MAIL_MAILER=log` minimal config kalıyor (Laravel core exception
  bildirimleri için, harici mail gönderimi yok).

**2b · Dashboard Widget'ları (Filament)**
- Bugünün giriş/çıkışları (StatsOverview widget)
- Bekleyen rezervasyon sayısı (pending status)
- Bu ayın gelir özeti (paid + completed `total_price` toplamı)
- Son 7 günlük rezervasyon grafiği (ChartWidget)

**2c · Test'ler (Pest tercih edilir)**
- Feature: `tests/Feature/ReservationFlowTest.php` — form post → DB record → success view
- Feature: `tests/Feature/AdminPanelAccessTest.php` — is_admin=false reddedilir
- Unit: `tests/Unit/ReservationCodeGeneratorTest.php` — KSO-YYYY-NNNN unique
- Unit: `tests/Unit/NightsCalculationTest.php` — Carbon diffInDays doğru
- En az %60 line coverage

**2d · Logo — TAMAM ✓ (2026-05-23)**
- Sahibin orijinal raster JPG'si `vectorizer.io` benzeri bir araçla **tam
  vectorize** edildi (`base/kog-suit-otel-logo-vectorized-transparent.svg`).
- İki renkten oluşan path tabanlı yapı:
  - `#273136` (orijinal lacivert/antrasit) → `#4a5240` (Olive Sanctuary
    primary-dark — koyu zeytin)
  - `#B99A55` (orijinal parlak altın) → `#b89b6e` (Olive Sanctuary accent —
    antika altın)
- Dönüşüm tek satır `sed` ile yapıldı:
  `sed -e 's/#273136/#4a5240/g' -e 's/#B99A55/#b89b6e/g' base/<src>.svg > public/images/logo.svg`
- `public/images/logo.svg` (17 kB transparent, viewBox 1186×1341) header
  + footer + apple-touch-icon + schema.org Organization.logo'da kullanılıyor.
- `public/favicon.svg` (777 byte sade versiyon) browser tab + mask-icon için.
- Yeni logo sürümleri için sahibin orijinaline bağlı kalmaya devam edilir;
  başka palette istenirse yine `sed` ile yeniden dönüştürülür.

**2e · Sahibinden Gerçek İçerik (BLOKE EDEN)**
- Gerçek oda fotoğrafları (cover + galeri, her oda için ~6-8 görsel)
- Gerçek bina/lobi/manzara/kahvaltı fotoğrafları (galeri için ~20-30)
- IBAN + hesap sahibi + banka adı (Settings'e gerçek değer)
- Gerçek telefon, WhatsApp, e-posta, adres
- Gerçek oda fiyatları (placeholder yerine)
- Otel sahibinin kısa hikayesi (about sayfası için)

**2f · İçerik Polish'i**
- Hero görseli: gerçek oda/manzara fotoğrafı (gradient yerine)
- Galeri bento: gerçek fotoğraflar
- Hakkımızda: sahibin yazdığı/onayladığı içerikle değiştir
- SSS: gerçek otel kuralları (iptal politikası, evcil hayvan, vs.)

**2g · SEO — Yerel Odaklı (BU PROJENİN EN ÖNEMLİ FAZ 2 BAŞLIĞI)**

Hedef: Varto/Muş bölgesinde "varto otel", "muş varto konaklama",
"varto butik otel", "koğ suit otel" gibi aramalarda **1. sayfa, ilk 3 sonuç**.
Yerel rakip yok denecek kadar az — agresif SEO ile dominate edilebilir.

- **Technical SEO**
  - `spatie/laravel-sitemap` → otomatik `/sitemap.xml` (tüm public + odalar dahil)
  - `public/robots.txt` (`Disallow /kog-yonetim`, sitemap link)
  - Canonical URL'ler her sayfada (`<link rel="canonical">`)
  - `hreflang="tr-TR"` (TR-only ama explicit)
  - HTTPS (prod), HTTP/2 (nginx config)
  - Core Web Vitals: LCP < 2.5s, CLS < 0.1, INP < 200ms

- **Meta + Open Graph**
  - Her sayfa için **unique** `<title>` ve `<meta description>`
  - Title formatı: `{Sayfa Başlığı} | Koğ Suit Otel — Varto Muş Butik Otel`
  - Description'lar manuel yazılır (otomatik üretim yok), 150-160 karakter,
    anahtar kelimeler doğal yerleştirilir
  - Open Graph: `og:title`, `og:description`, `og:image` (1200×630), `og:type`
  - Twitter Card: `summary_large_image`
  - `og:locale` = `tr_TR`

- **Schema.org Yapısal Veri** (kritik — yerel arama için)
  - **Hotel + LocalBusiness** (ana sayfa): name, address (street + Varto + Muş + TR),
    `geo` (lat/lng — Varto koordinatları), `telephone`, `email`,
    `openingHours` (24/7), `checkinTime`, `checkoutTime`,
    `priceRange` ("₺₺"), `hasMap`, `image`, `aggregateRating` (gerçek yorumlar gelince)
  - **HotelRoom** (her oda detay sayfası): name, occupancy, amenities,
    `containedInPlace` (Hotel'e bağ), `offers` (price + TRY currency + availability)
  - **BreadcrumbList** (her iç sayfada): site hiyerarşisi
  - **FAQPage** (SSS sayfası): rich snippet için
  - **WebSite** (root): SearchAction (site içi arama gelirse)

- **Içerik SEO (on-page)**
  - Her sayfada **tek `<h1>`** (içeriğin ana başlığı, anahtar kelimeli)
  - h2/h3 hiyerarşisi (semantic)
  - Her görselde `alt` attribute (Türkçe, açıklayıcı)
  - Internal linking: odalar → rezervasyon, hakkımızda → iletişim, vs.
  - URL'ler Türkçe ve okunaklı (`/odalar/standart-oda`, `/iletisim`)
  - Anahtar kelimelerin **organic** kullanımı (keyword stuffing YOK):
    "Varto otel", "Muş Varto konaklama", "Varto butik otel", "Varto suit",
    "Muş'ta kalınacak yer", "Varto rezervasyon", "Koğ Suit Otel"
  - "Hakkımızda" sayfasında bölge tanıtımı (Varto coğrafyası, yakın yerler)
  - Footer'da NAP (Name, Address, Phone) tutarlı format

- **Yerel İşaretler**
  - Adres her yerde aynı format: "Varto, Muş, Türkiye"
  - Telefon her yerde aynı (TR formatı)
  - Google Business Profile (sahibin oluşturacağı — site link, foto, yorum)
  - Bing Places for Business (opsiyonel, az TR trafiği)

- **Doğrulama + Araçlar**
  - Google Search Console meta tag (`.env` üzerinden okunur,
    `SEO_GOOGLE_VERIFICATION` değişkeni)
  - Bing Webmaster meta tag (opsiyonel)
  - Cloudflare Web Analytics (KVKK uyumlu, çerezsiz — varsayılan)
  - Sitemap GSC'ye submit edilir (sahibin GSC'ye erişimi sonrası)

- **Önemli (gerçek içerik öncesi nokta)**
  - SEO altyapısı şimdi (placeholder içerikle) kurulabilir
  - Gerçek metin/foto Faz 2e'de gelince anahtar kelime yoğunluğu son haline gelir
  - İlk indexlenme prod'a çıktıktan ~1-3 hafta sonra başlar
  - "Sıfırdan SEO" → ilk 6 ay zayıf, sonra hızla artar (yerel rekabet yokluğu)

**2h · KVKK + Gizlilik Hukuki Onay**
- Mevcut placeholder metinler avukat tarafından gözden geçirilsin
- Çerez politikası eklenir (Cloudflare Web Analytics çerezsiz, GA varsa banner şart)
- KVKK başvuru e-posta adresi netleşsin (`kvkk@kogsuitotel.com` ayrı mı?)

**2i · Performance Audit**
- Lighthouse hedef: Performance 90+, Accessibility 95+, SEO 95+, Best Practices 95+
- Image optimization: WebP'ye çevir, `loading="lazy"`
- CSS purge teyit (build çıktısı kontrolü)
- Font preload (Manrope + Inter)
- Compression: gzip/brotli (nginx tarafı, prod)

**2j · Cloudflare Hazırlığı**
- DNS A record: kogsuitotel.com → VPS IP (henüz VPS yok, placeholder)
- Proxy henüz OFF (DNS only) — VPS gelince ON
- SSL/TLS Full (Strict) — Origin Certificate üretilecek
- Test domain (`staging.kogsuitotel.com`) opsiyonel

**2k · Canlı Demo (Sahibe)**
- ngrok veya Cloudflare Tunnel ile localhost'u dış dünyaya aç
- Kısa bir Loom/screen-record video — feature tour
- Sahibinden onay + son geri bildirim listesi al

**2l · Karar Noktaları**
- Mail servisi: **YOK** — mail bildirimi 2026-05-23'te tamamen kaldırıldı.
  WhatsApp+dashboard+success page yeterli (bkz: feedback-no-mail-notifications)
- Analytics: **Cloudflare Web Analytics** (çerezsiz, KVKK dostu — varsayılan)
  vs. Google Analytics (banner şart)
- WhatsApp: **Manuel `wa.me/...` linkleri** (faz 1'de hazır) vs. **WhatsApp
  Business Cloud API** (Meta hesabı + iş doğrulama, faz 4'e öteleme)

**2m · Tarih Seçici UI İyileştirmesi**
- Native `<input type="date">` → modern, Türkçe locale'li date picker'a geçiş
- Tercih: **Flatpickr** (vanilla JS, ~10kb gzip, Alpine.js ile uyumlu, TR built-in)
- Placeholder: "gg.aa.yyyy" yerine **"Tarih Seçiniz"** veya benzer Türkçe ifade
- Renk teması Olive Sanctuary'ye uyarlanır (CSS variables)
- Mobile'da native fallback (Flatpickr'ın `disableMobile: false` ile karar)
- Range mode: giriş+çıkış tek picker'da (rezervasyon formunda kullanım)

**2n · Tarih Çakışma Kontrolü (KRİTİK)**
- Sahibin riski: aynı oda aynı tarihlerde 2 farklı misafire satılmasın
- Üç katman gerekli:
  1. **Backend validation** (`ReservationController@store`): aynı `room_id`
     için `confirmed` veya `paid` veya `completed` statusta çakışan tarih
     varsa Validation::ValidationException → kullanıcıya "Bu tarihler dolu"
  2. **API endpoint** (`GET /api/rooms/{slug}/unavailable-dates`): bir
     odanın müsait olmayan tarih aralıklarını JSON döner
  3. **Frontend** (Flatpickr `disable` option): tarih seçicide o günler
     gri ve tıklanamaz, kullanıcı baştan görür
- Edge case: `pending` rezervasyonlar 24 saat sonra otomatik iptal olabilir
  (cron) — şimdilik pending tarihler çakışmaz, sadece confirmed+ statusler
- Test: Feature test `test_cakisan_tarih_rezervasyon_engelenir`

**2o · Terminoloji & Yerel Bilgilendirme**
- "Check-in" / "Check-out" → "Giriş Saati" / "Çıkış Saati"
  (Varto yerel kitlesi için TR daha anlaşılır)
- "Konaklama bilgi kutusu" şu sayfalarda görünür:
  - SSS (zaten var) ✓
  - İletişim (zaten var, terminoloji güncellenmeli) ✓
  - Rezervasyon Oluşturma (eklenir — formun yanında veya altında küçük bilgi kartı)
- Bilgi kutusu içeriği: Giriş 14:00'ten, Çıkış 12:00'a kadar, açık büfe
  kahvaltı saatleri, evcil hayvan politikası kısa not

### Faz 3 — Production Deploy

> Faz 2'nin 2e (sahibinden gerçek içerik) ve 2k (sahibin onayı) tamamlanmadan
> başlama. VPS'in maliyeti var, gereksiz erken alma.

**3a · VPS Satın Alma**
- Contabo Cloud VPS 10 (4 vCPU / 8 GB RAM / 75 GB NVMe / Nürnberg)
- Tahmini maliyet: €44/yıl + bir kerelik kurulum ücreti
- Ubuntu 24.04 LTS image

**3b · VPS Hardening** (Section 8.1 referansı)
- SSH key auth, password disabled, root login disabled
- Non-root `deploy` user (sudo'lu)
- UFW: 22, 80, 443 (sadece Cloudflare IP'leri 80/443)
- fail2ban + unattended-upgrades

**3c · Base Stack** (Section 8.2)
- nginx + PHP-FPM 8.3 + uzantılar
- MariaDB 11.x (DB credentials güçlü, .env'de)
- Redis (opsiyonel — cache/queue ihtiyacı olursa)
- Composer 2.x + Node.js 20 LTS + npm

**3d · Cloudflare** (Section 8.3)
- A record proxy ON (turuncu)
- SSL Full (Strict), Origin Certificate (15 yıl)
- Always HTTPS, Auto HTTPS Rewrites
- Bot Fight Mode, Security Medium
- Page Rules: Cache static assets

**3e · İlk Deploy**
- Repo clone → `/var/www/kogsuitotel`
- `.env` üretimi (APP_DEBUG=false, APP_ENV=production)
- `php artisan key:generate`
- DB migrate + seed
- Storage symlink (`php artisan storage:link`)
- File permissions (`storage/`, `bootstrap/cache/` 775)
- npm ci + npm run build

**3f · Mail Servisi**
- Brevo veya Resend (Faz 2l'de karara bağlı)
- SMTP credentials .env'de
- Test mail: `php artisan tinker` ile

**3g · Güvenlik Son Kontrolleri**
- 2FA admin user için aktif (Filament 2FA plugin veya `pragmarx/google2fa`)
- Origin firewall (CF IP whitelist) doğrulandı
- Log rotation aktif (`/etc/logrotate.d/laravel`)
- Sentry veya basit error mail bildirimi

**3h · Backup**
- Spatie Laravel Backup (DB + storage)
- Backblaze B2 veya Wasabi hedefi (yıllık ~€5-10)
- Cron job: günlük 03:00
- 30 günlük retention, 1 yıllık monthly snapshot

**3i · Smoke Test**
- Login → admin panel açılıyor
- Public sayfalar 200 OK
- Rezervasyon flow uçtan uca (form → success → admin panel'de görünüyor)
- Mail geldi (admin'e + müşteriye)
- IBAN gösterildi

**3j · Monitoring**
- UptimeRobot veya basit cron-curl ile dakikada bir health check
- Cloudflare Web Analytics dashboard
- Sentry (opsiyonel) → app exception'ları

**3k · Canlıya Alma**
- DNS proxy ON (turuncu bulut)
- Sahibin son onayı
- Sosyal medya / Google'a duyuru

### Faz 4 — Talep-Bazlı Eklentiler (Post-Launch)

> Bu fazda yalnızca **sahibin somut talebi** olursa girilir. Sırasız.
> Aşağıdaki üçü dışındaki "advanced" özellikler (Booking.com integration,
> sezonsal dinamik fiyat, çoklu dil, online ödeme, loyalty, channel manager)
> **bu projenin scope'una girmiyor** — over-engineering. Sahibin niyeti
> kesinleşmedikçe önerme bile.

**4a · Çoklu Personel + Roller** (talep: sahibinin yanında resepsiyon ekibi olursa)
- Spatie Permission + Filament Shield
- Roller: `super-admin` (sahibi), `reception` (sadece rez. okur/düzenler)
- Activity log (`spatie/laravel-activitylog`)

**4b · WhatsApp Business Cloud API** (talep: sahibin manuel mesaj yorulursa)
- Meta Business hesabı + iş doğrulama (1-2 hafta süreç)
- Otomatik bildirim: rezervasyon onayı, ödeme hatırlatma, check-in günü
- Şablon mesajları Meta tarafından onaylanmalı
- Maliyet: ülke + mesaj tipi başına (~$0.005-0.05)

**4f · Aylık Otomatik PDF Rapor** (talep: sahibin tablo isterse)
- Sahibe PDF rapor: toplam rez., gelir, ortalama gece, oda doluluk
- E-posta ile her ayın 1'inde
- `barryvdh/laravel-dompdf` ile basit template

### Faz Geçiş Kontrol Listesi

Bir fazı kapatmadan önce şu sorulara `evet` cevabı verilmeli:

**Faz 1 → 2:**
- [x] Lokalde public site tüm sayfalar 200 OK
- [x] Rezervasyon flow uçtan uca çalışıyor
- [x] Filament admin paneli tüm CRUD ile çalışıyor
- [x] Türkçe lokalizasyon tüm public + admin ekranlarında

**Faz 2 → 3:** ✅ TAMAMLANDI
- [x] Sahibin onayı alınmış (Cloud demo + canlı demo görüntülendi 2026-05-24)
- [ ] Gerçek içerik (foto + IBAN + fiyat + iletişim) — sahip yüklediğinde
      admin'den (polymorphic path hazır, scope dışı sayıldı)
- [x] Test coverage ≥ %60 (112 test / 379 assertion)
- [x] Logo yeniden tasarımı bitti (Olive Sanctuary vectorized SVG)
- [x] Lighthouse Performance ≥ 90 (100/100/100/95-96)
- [x] KVKK metinleri tamamlandı (/kvkk + /gizlilik + /cerez-politikasi)

**Faz 3 → 4 (post-launch operasyonel kalıntılar):**
- [x] VPS canlıda, kogsuitotel.com aktif, SSL Full Strict
- [x] Admin login + Profil + Settings + Activity log canlı
- [x] yonetim.kogsuitotel.com subdomain
- [x] Vercel temizliği
- [x] Origin IP firewall (UFW CF whitelist)
- [x] **GA4 + KVKK uyumlu cookie consent** (Consent Mode v2 + banner + m.9 disclosure)
- [x] **GSC verified** (Cloudflare-GSC otomatik DNS TXT)
- [x] **Backup config** — Spatie Backup + Cloudflare R2 (10GB ücretsiz, encrypted, 5GB retention cap)
- [ ] **Backup VPS aktivasyon** — R2 token sahibinden gelince .env + crontab register
- [ ] **GSC sitemap submit** — kullanıcı GSC dashboard'undan `sitemap.xml` Submit
- [ ] **Monitoring** (UptimeRobot 5dk HTTP 200 check) — kalan iş
- [ ] **CI/CD otomatik deploy** (GitHub Actions webhook) — opsiyonel polish
- [ ] **Google Business Profile** — sahibin postcard verification (1-2 hafta)
- [ ] **Site canlıda 30 gün stabil** — geçiş eşiği
- [ ] **İlk 5+ gerçek rezervasyon** — geçiş eşiği

**Faz 3 → 4:**
- [ ] Site canlıda en az 30 gün stabil çalışıyor
- [ ] İlk 5+ gerçek rezervasyon alındı
- [ ] Sahibin yeni feature talebi geldi (4a-4h listesinden)

---

## 12. Çalışma Kuralları (Claude Code için)

- **Türkçe konuş** (kod yorumları İngilizce olabilir).
- Her büyük adımda **özet ver ve onay iste**, sessizce 50 dosya değiştirme.
- Migration'lar oluşturduktan sonra `php artisan migrate` çalıştırmadan ÖNCE göster.
- `composer require` ile yeni paket eklerken neden eklediğini söyle —
  bağımlılık şişmesin (özellikle çok kullanılmayan paketler).
- Şüphede kalırsan bu dokümanın 10. bölümündeki açık kararlardan birine
  giriyor mu kontrol et, gerekirse kullanıcıya sor.
- Stack'i değiştirme önerme — kararlar zaten verilmiş (React'a geçelim,
  Inertia ekleyelim gibi öneriler YAPMA).
- Production deploy konularına Faz 3'e gelene kadar GİRME, sadece local
  ortamla ilgilen.
- Test yaz: en azından Reservation create flow'u için bir feature test
  ve kritik validation'lar için unit test.
- Filament 4 dokümantasyonu için referans: https://filamentphp.com/docs/4.x
- Laravel 12 dokümantasyonu: https://laravel.com/docs/12.x
