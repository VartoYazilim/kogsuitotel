# Güvenlik Denetimi — Koğ Suit Otel

**Tarih:** 2026-05-23 (Faz 2 Block 3)
**Yöntem:** İki bağımsız Agent paralel kapsamlı kod tarama (security + KVKK)
**Kapsam:** Tüm `app/`, `routes/`, `config/`, `resources/views/`, `database/`, `deploy/`

Bu doküman; geliştirici (yarınki ben + sonraki geliştiriciler) ve sahibe yöneliktir. Hangi güvenlik açıklarının tespit edildiğini, hangilerinin Block 3'te kapatıldığını, hangilerinin sonraki bloka kaldığını gösterir.

---

## Genel Resim

Proje Laravel + Filament 4 güvenli default'larına bağlı kalmış: CSRF, password hashing (bcrypt 12), `FilamentUser::canAccessPanel`, honeypot, rate limiting, `$fillable`, escape edilmiş Blade, parametreli Eloquent. Ancak Block 3'te **1 kritik + 2 yüksek + 4 orta** seviye bulgu tespit edildi. Hiçbiri RCE/SQLi seviyesinde değil. Prod henüz aktif olmadığı için **deploy öncesi düzeltilebilir** — kullanıcı verisi henüz risk altında değil.

Block 3 sonrası: **8 kod fix uygulandı**, **kritik IDOR kapatıldı**, **mass assignment temizlendi**, **KVKK aydınlatma kabul flow'u eklendi**. Kalanlar (2FA, audit log, login throttle) Faz 3 öncesi sahip onayı ile eklenmeli.

---

## Block 3'te Uygulanan Fix'ler

### 1. KRİTİK — IDOR / PII Leakage (kod enumeration)

**Risk:** `reservation_code` formatı `KSO-2026-0001` sıralı + 4 dijit. Saldırgan 10K request ile (`/rezervasyon/basarili/KSO-2026-NNNN` taraması) tüm misafirlerin **ad, telefon, e-posta, oda, tarih, tutar + IBAN** verilerini okuyabilir. `firstOrFail()` dışında auth/ownership kontrolü yok.

**Etki:** KVKK ihlali, rakip otellere doluluk ifşası, müşteri güveni kaybı.

**Fix:** `reservation_code` formatı `KSO-YYYY-AAAAAAAA` (8 char base32, ~40 bit entropy) + success route `throttle:20,1`. Saldırgan tahmin edemez + 20/dk ile yavaşlatılır.

**Dosyalar:**
- `app/Models/Reservation.php:152-176` — `generateCode()` rewrite
- `routes/web.php:32` — `->middleware('throttle:20,1')`
- `tests/Unit/ReservationCodeGeneratorTest.php` — yeniden yazıldı (regex `[A-Z0-9]{8}`, ardışıklık testi kaldırıldı)
- `tests/Feature/ReservationFlowTest.php:73,544` — regex + 404 testi güncellemesi

### 2. YÜKSEK — Mass Assignment

**Risk:** `User.$fillable` içinde `is_admin` → gelecekte register endpoint veya `UserResource` eklenince privilege escalation. Bir geliştirici `User::create($request->all())` yazarsa attacker `is_admin=true` injection yapar.

**Fix:** `$fillable`'dan `is_admin` çıkarıldı. Seeder `Model::unguarded()` ile explicit (tek meşru yol).

**Dosyalar:**
- `app/Models/User.php:17-25` — fillable temizlik + yorum
- `database/seeders/AdminUserSeeder.php` — `Model::unguarded` wrap

### 3. YÜKSEK — Notification PII Duplikasyonu

**Risk:** `notifications.data` tablosunda misafir adı + oda adı plain JSON kopyalanır. Süresiz birikir, silme prosedürü yok. KVKK m.4/2-d ihlal.

**Fix:** `ReservationCreated::toDatabase()` sadece rezervasyon kodu içerir; detay görmek için "Detayını Aç" → Resource view sayfası. Notifications tablosu silinmesi unutulsa bile sızıntı yüzeyi minimal.

**Dosya:** `app/Notifications/ReservationCreated.php:22-43`

### 4. KVKK — Form Üzerinde Aydınlatma Kabul Kanıtı

**Risk:** Form'da KVKK link + checkbox yok. KVKK Madde 10 aydınlatma yükümlülüğünün **kanıtı** (checkbox + zaman damgası) tutulmuyor.

**Fix:** Form'a görünür checkbox + link, backend `'kvkk_consent' => ['accepted']` validation, TR Türkçe error mesajı. Test eklendi.

**Dosyalar:**
- `resources/views/reservations/create.blade.php` (submit'ten önce checkbox)
- `app/Http/Controllers/ReservationController.php` (validation + mesaj)
- `tests/Feature/ReservationFlowTest.php:test_kvkk_consent_eksik_ise_validation_hatasi_doner`

### 5. ORTA — Special Requests Özel Nitelikli Veri Riski

**Risk:** 1000 char serbest metin → misafir "celiac'ım", "namaz vakti uyandırmayın" yazabilir → KVKK m.6 özel nitelikli sızıntı.

**Fix:** `max:500` (controller + form) + form'da uyarı metni ("sağlık/inanç bilgisi yazmayın, telefonla iletin") + admin Filament form helperText.

**Dosyalar:**
- `app/Http/Controllers/ReservationController.php` (`max:500`)
- `resources/views/reservations/create.blade.php` (textarea uyarı + maxlength)
- `app/Filament/Resources/Reservations/Schemas/ReservationForm.php` (special_requests + admin_notes helperText)

### 6. ORTA — JSON-LD Stored XSS Riski

**Risk:** Settings tablosu admin tarafından düzenlenir, JSON-LD'de `json_encode` ile schema.org'a yazılır. `json_encode` default'unda `<script>` tag'leri Unicode escape edilmez. Admin "address" alanına `</script><script>alert(1)</script>` yazarsa script tag'i sızar.

**Fix:** Tüm 5 JSON-LD çağrısına `JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT` flag'leri eklendi.

**Dosyalar:** `resources/views/{layouts/public, pages/home, pages/faq, rooms/show, partials/schema-breadcrumb}.blade.php`

### 7. ORTA — Session Env Defaults Eksik

**Risk:** `.env.example`'da `SESSION_SECURE_COOKIE` yok → prod'da unutulursa HTTP üzerinden cookie gönderilir.

**Fix:** `.env.example`'da prod hatırlatıcı yorumlar (SESSION_SECURE_COOKIE, SESSION_ENCRYPT, SESSION_LIFETIME, LOG_LEVEL).

**Dosya:** `.env.example`

### 8. DÜŞÜK — CSP Header Eksikliği

**Risk:** XSS halinde script kaynaklarını sınırlama yok. Filament inline script kullandığı için `unsafe-inline` geçici.

**Fix:** `deploy/nginx.conf.example`'a CSP eklendi (default-src self, img-src data:/https:, frame-ancestors self, base-uri/form-action self).

**Dosya:** `deploy/nginx.conf.example`

---

## Block 3 Sonrası KALAN Gap'ler

### Geliştirici Tarafı (kod ile çözülür — sonraki bloka)

| # | Gap | Öncelik | Tahmini iş | Notu |
|---|---|---|---|---|
| 9 | **2FA admin için** | Faz 3 öncesi ZORUNLU | 2-3 saat | Filament resmi 2FA plugin VEYA `pragmarx/google2fa-laravel` |
| 10 | **Audit log** (`spatie/laravel-activitylog`) | Üretim öncesi öneri | 1-2 saat | Admin "ödendi" yanlış işaretlerse trail; CLAUDE.md Faz 4'te ama erkene alınmalı |
| 11 | **Login throttle** (Filament panel) | Üretim öncesi | 30 dk | `RateLimiter::for('login')` + middleware |
| 12 | **Saklama süresi cron** (anonimleştirme) | Sahip kararı sonrası | 1 saat | Sahip "2 yıl mı 5 yıl mı" karar verince |
| 13 | **Notifications purge cron** (30+ gün) | Üretim öncesi | 30 dk | Scheduled task |
| 14 | **`robots.txt` `/kog-yonetim` çıkar** | Düşük | 5 dk | Saldırgana yol göstermesin; meta `noindex` daha iyi |
| 15 | **`config/cors.php` publish** | Faz 3 deploy öncesi | 15 dk | API endpoint için explicit CORS, sadece `kogsuitotel.com` izinli |
| 16 | **FileUpload `acceptedFileTypes` + EXIF strip** | Düşük | 30 dk | RoomForm + GalleryImageResource cover_image / gallery |
| 17 | **Race condition (TOCTOU)** çakışma kontrolünde | Düşük (Varto trafik az) | 1-2 saat | `DB::transaction` + advisory lock VEYA partial unique index |

### Sahip Tarafı (hukuki — kod ile kapatılamaz)

| # | Aksiyon |
|---|---|
| A | VERBİS kaydı kontrolü (avukat) |
| B | Aydınlatma metni avukat onayı |
| C | KEP adresi alımı (PTT, ~250 TL/yıl) |
| D | Cloudflare DPA + Contabo AVV imzalama |
| E | Tüzel kişilik netleştirme (MERSİS + VKN) |
| F | `kvkk@kogsuitotel.com` alias oluşturma |
| G | Saklama süresi kararı (2 yıl mı, 5 yıl mı) |
| H | Veri ihlali müdahale planı yazılı dokümantasyon |
| I | Hosting bölge tercihi (Contabo DE vs. yerli) |

Detaylı liste: `docs/kvkk-veri-envanteri.md` Bölüm 4.

---

## Üretime Çıkmadan ÖNCE Mutlaka

1. **2FA aktif** (en kritik — Faz 3 başlangıç koşulu)
2. **Aydınlatma metni** avukat onayı + ilgili sayfa rewrite
3. **APP_DEBUG=false** prod `.env`'de
4. **`SESSION_SECURE_COOKIE=true` + `SESSION_ENCRYPT=true`** prod `.env`'de
5. **`LOG_LEVEL=warning`** prod'da (debug PII trace engelle)
6. **Audit log** (sahip onaylarsa)
7. **Saklama süresi cron** (sahip karar verince)
8. **Cloudflare DPA + Contabo AVV** imzalanmış
9. **VERBİS kayıt** durumu netleştirilmiş

---

## Test + Doğrulama

| Araç | Sonuç (Block 3 sonu) |
|---|---|
| PHPUnit | **51/51 yeşil, 128 assertion** |
| Pint | Temiz (0 stil ihlali) |
| Larastan | Level 5, 0 hata (baseline 9 → 8 ignore, ReservationCreated PII minimize sonrası 1 azaldı) |
| composer audit | 0 vulnerability |
| GitHub Actions CI | Yeşil (push'tan sonra) |

---

## İlgili Dosyalar

- **PII Envanteri:** `docs/kvkk-veri-envanteri.md`
- **CLAUDE.md Section 10 (Açık Kararlar):** sahip karar verecek noktalar güncellendi
- **README.md:** dev komutları + CI badge
- **CI workflow:** `.github/workflows/ci.yml`
- **PHPStan config:** `phpstan.neon` + `phpstan-baseline.neon`

---

*Bu doküman 2026-05-23 Faz 2 Block 3 güvenlik + KVKK denetiminin çıktısıdır. Yarınki geliştiriciye/sahibe hangi açıkların kapandığını ve hangilerinin kapatılması gerektiğini gösterir.*
