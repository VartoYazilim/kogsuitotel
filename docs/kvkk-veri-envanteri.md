# KVKK Kişisel Veri Envanteri — Koğ Suit Otel

**Tarih:** 2026-05-23 (Faz 2 Block 3 — Güvenlik + KVKK denetimi)
**Veri sorumlusu:** Koğ Suit Otel (tüzel kişilik avukat tarafından netleştirilecek)
**Hukuki çerçeve:** 6698 sayılı Kişisel Verilerin Korunması Kanunu (KVKK)

Bu doküman; sahibe, avukata ve geliştiriciye yöneliktir. Hangi kişisel verinin (PII) sistemde nerede tutulduğunu, kimin eriştiğini, ne kadar süre saklandığını ve KVKK uyum açısından hangi adımların yapılması gerektiğini gösterir.

---

## 1. PII Haritası

| # | Veri tipi | Tablo / dosya | Toplama yöntemi | Saklama süresi | Erişen | KVKK kategorisi |
|---|---|---|---|---|---|---|
| 1 | Ad-Soyad | `reservations.guest_first_name`, `guest_last_name` | Public form (`/rezervasyon`) | **Tanımsız → 2 yıl önerisi** (m.4/2-d) | Tüm admin (Filament panel) | Kimlik (Genel) |
| 2 | Telefon | `reservations.guest_phone` (indeksli) | Public form | Tanımsız → 2 yıl önerisi | Tüm admin | İletişim (Genel) |
| 3 | E-posta | `reservations.guest_email` | Public form | Tanımsız → 2 yıl önerisi | Tüm admin | İletişim (Genel) |
| 4 | Rezervasyon detayları (oda + tarih + kişi sayısı) | `reservations.*` | Public form | Tanımsız (mali kayıt 10 yıl gerekli olabilir, anonimleştirilebilir) | Tüm admin | Müşteri işlem (Genel) |
| 5 | Özel istekler | `reservations.special_requests` (max 500 char, Block 3'te 1000→500'e indirildi) | Public form (uyarı metni eklendi) | Tanımsız → 2 yıl önerisi | Tüm admin | **Riskli** — sağlık/inanç sızabilir (m.6 özel nitelikli) |
| 6 | Admin notları | `reservations.admin_notes` (max 1000 char) | Filament admin | Süresiz | Sadece admin | **Riskli** — TCKN/sağlık yazılabilir (admin'e uyarı eklendi) |
| 7 | Admin email + parola hash | `users.email`, `users.password` (bcrypt 12) | Seeder + admin paneli | Süresiz (çalışan ayrılırsa kayıt silinir) | Kendisi + sistem | Çalışan kişisel verisi |
| 8 | Oturum verisi | `sessions` (ip_address, user_agent, payload longText) | Otomatik (her ziyaretçi) | 120 dk default `SESSION_LIFETIME` (prod'da 30 dk önerisi) | Sistem | Trafik verisi (Genel) — payload'da form `old()` PII olabilir |
| 9 | Bildirim kuyruğu | `notifications.data` (text, JSON) | `ReservationCreated` notification | Süresiz (silen yok — TODO purge cron) | Tüm admin (Filament bell) | **Block 3 sonrası PII minimize** — sadece rezervasyon kodu tutulur, misafir adı duplike edilmez |
| 10 | İşletme IBAN | `settings` (key=`iban`, `iban_holder`, `bank_name`) | Seeder + admin | Süresiz | Tüm admin + **public success sayfası** | İşletme finansı (KVKK kapsamı dışı, ticari sır) |
| 11 | Cache | `cache` tablosu (5 dk TTL settings) | Otomatik | 5 dk | Sistem | IBAN cache'lenir |
| 12 | Log dosyası | `storage/logs/laravel.log` | Otomatik (exception/info) | Tanımsız (prod'da daily rotation + 14 gün retention önerisi) | Sunucu admin | **Risk:** LOG_LEVEL=debug PII trace sızdırabilir — prod'da `warning` zorunlu |

**KAPSAM DIŞI (bilinçli toplanmıyor — KVKK avantajı):**
- TCKN, pasaport
- Kredi kartı / ödeme bilgileri (online ödeme YOK, havale + WhatsApp dekontu)
- Doğum tarihi, cinsiyet
- Sağlık / biyometrik veri
- Cookie tracking (sadece zorunlu PHPSESSID + XSRF-TOKEN — analytics yok)

---

## 2. Veri Akışı

```
[Misafir]
   │ POST /rezervasyon (HTTPS — Faz 3'te) + honeypot + throttle 5/dk + CSRF + KVKK consent
   ▼
[ReservationController::store]
   │ validation (server-side) — KVKK consent zorunlu (Block 3 sonrası)
   ▼
[Reservation::create] → DB
   │ saving event → kapasite + çakışma invariant'ları
   ▼
[Notification::send] → notifications tablosu (sadece kod — Block 3 sonrası)
   ▼
[Filament admin bell] → tüm admin görür (granüler yetki yok)
   ▼
[Manuel WhatsApp link wa.me/{phone}] → Meta'ya telefon numarası gider (yurt dışı aktarım, KVKK m.9)
```

**Aktarılan 3. taraflar:**
- **Hosting:** Contabo (Almanya, Nürnberg) — yurt dışı aktarım (KVKK m.9), AB GDPR "uygun koruma" sayılır ama formal değil — aydınlatmada belirtilmeli + Contabo AVV (Auftragsverarbeitungsvereinbarung) imzalanmalı
- **CDN:** Cloudflare (ABD, global edge) — yurt dışı aktarım, Cloudflare DPA imzalanmalı
- **WhatsApp:** Meta (ABD) — manuel iletişim sırasında telefon numarası gider, aydınlatmada belirtilmeli
- **Emniyet (KBS/AHBS):** Konaklama Tesisleri Yönetmeliği — fiziksel girişte yapılır, bu sistemde değil

---

## 3. Block 3 Sonrası Uygulanan Teknik Tedbirler

### Kapatılan gap'ler (kod fix)

| # | Gap | Çözüm | Dosya |
|---|---|---|---|
| 1 | **IDOR / kod enumeration** — `KSO-2026-NNNN` tahmin edilebilir, tüm misafir PII'si saldırgan tarafından okunabilir | `KSO-YYYY-AAAAAAAA` formatı (8 char base32, ~40 bit entropy) + success route `throttle:20,1` | `app/Models/Reservation.php`, `routes/web.php` |
| 2 | **Mass assignment** — `User.$fillable` içinde `is_admin` → privilege escalation riski | `is_admin` `$fillable`'dan çıkarıldı; seeder `Model::unguarded` ile explicit | `app/Models/User.php`, `database/seeders/AdminUserSeeder.php` |
| 3 | **PII duplikasyonu** — `notifications.data` içinde misafir adı + oda plain JSON, süresiz | Notification body sadece rezervasyon kodu içerir; detay görmek için "Detayını Aç" Resource view | `app/Notifications/ReservationCreated.php` |
| 4 | **KVKK aydınlatma kabul kanıtı yok** | Form'a checkbox + link + backend `accepted` rule + test | `resources/views/reservations/create.blade.php`, `app/Http/Controllers/ReservationController.php` |
| 5 | **Özel istekler 1000 char** + uyarı yok | `max:500` + form uyarı metni + admin Filament helperText (TCKN/sağlık yazmayın) | Controller + create.blade + ReservationForm.php |
| 6 | **JSON-LD XSS riski** — Settings'ten gelen admin input plain JSON | 5 view dosyasında `JSON_HEX_TAG \| JSON_HEX_AMP \| JSON_HEX_APOS \| JSON_HEX_QUOT` flag eklendi | layouts/public, pages/home, pages/faq, rooms/show, partials/schema-breadcrumb |
| 7 | **Session env defaults eksik** | `.env.example`'a prod hatırlatıcı yorumlar (SESSION_SECURE_COOKIE, SESSION_ENCRYPT, SESSION_LIFETIME, LOG_LEVEL) | `.env.example` |
| 8 | **CSP header yok** | nginx.conf.example'a `Content-Security-Policy` eklendi | `deploy/nginx.conf.example` |

### Mevcut güçlü tedbirler (devam ediyor)

- HTTPS (Faz 3 — Cloudflare Full Strict + Origin Cert)
- bcrypt 12 round parola hash
- CSRF token (Laravel default + Filament middleware + Blade `@csrf`)
- Honeypot (`website` field, `aria-hidden`)
- Rate limiting (form `throttle:5,1`, success `throttle:20,1`)
- `$fillable` + `$hidden` her modelde
- Eloquent parametreli sorgular (SQLi yok)
- Blade `{{ }}` default escape (`{!! !!}` sadece kontrollü JSON-LD)
- Storage symlink (`public/` separation)
- nginx `.env`, `.git`, `/storage`, `/vendor` deny
- Domain invariant guards (`Reservation::saving` event — kapasite + çakışma)
- 51 PHPUnit test + Pint + Larastan CI

---

## 4. Block 3 Sonrası KALAN Gap'ler

### Kritik (üretim öncesi — geliştirici tarafı)

| # | Gap | Sebep | Çözüm |
|---|---|---|---|
| A | **Saklama süresi otomasyonu yok** | KVKK m.4/2-d — süreyle sınırlı muhafaza şart. Mevcutta hiçbir veri otomatik silinmez. | **Sahip kararı sonrası** — 2 yıl önerisi + anonimleştirme cron (3+ yıl önceki rezervasyonların `guest_*` alanları `null`'a çekilir, tarih/tutar istatistik için kalır). Spatie Backup retention da paralel. |
| B | **Audit log yok** | KVKK m.12/3 denetim altyapısı zayıf. Admin "ödendi" yanlış işaretlerse geri alma trail'i yok. | `spatie/laravel-activitylog` paketi (Faz 4'te plan ama 3 öncesi önerilir). |
| C | **2FA yok** | Tek admin tek faktör (email+password). Parola leak = tüm misafir verisi açılır. | `pragmarx/google2fa` veya Filament resmi 2FA plugin'i. **Faz 3 öncesi zorunlu** (CLAUDE.md notu var). |
| D | **`notifications.data` purge yok** | PII minimize edildi ama notifications süresiz birikir (kod kalır). | Okunmuş 30+ gün notification'ları silen cron. |

### Orta (üretim sonrasında iterasyon)

| # | Gap | Sebep | Çözüm |
|---|---|---|---|
| E | **Veri sahibi hakları işleme süreci manuel** | KVKK m.11 — bilgi/silme/düzeltme talepleri admin'in tek tek bakmasına bağlı | Admin paneline "KVKK Başvuru" mini-modul: e-posta ile gelen başvuru ekran kayıt + 30 gün cevap süresi takip |
| F | **Çerez politikası dokümantasyon eksik** | Zorunlu çerez listesi (PHPSESSID, XSRF-TOKEN, kogsuitotel-session) tablo halinde yok | KVKK metnine eklenir (avukat onayı) |
| G | **Login throttle özel route'ta yok** | Filament default Laravel global rate limit | `RateLimiter::for('login')` config'i + Filament panel'e middleware |
| H | **`SESSION_LIFETIME=120` admin için uzun** | 2 saat — açık unutursa risk | Prod `.env`'sinde 30 dk + idle timeout (`.env.example` notlu) |
| I | **Veri ihlali müdahale planı yazılı değil** | KVKK m.12/5 — 72 saatte Kurul'a bildirim zorunlu | Yazılı prosedür + sorumlu kişi listesi (sahibin işi) |

### Sahibin yapacağı (hukuki — kod ile kapatılamaz)

| # | Aksiyon | Detay |
|---|---|---|
| 1 | **VERBİS kaydı kontrolü** | Otel = veri sorumlusu. Çalışan 50+ veya ciro 100M+ değilse muafiyet olabilir (KVKK Kurulu 2018/87) — avukat netleştirsin |
| 2 | **Aydınlatma metni avukat onayı** | Mevcut `kvkk.blade.php` placeholder. Konaklama tesisleri için özelleşmiş KVKK avukatı şart |
| 3 | **KEP adresi alımı** | PTT KEP ~250 TL/yıl. KVKK başvuruları için hukuki tebligat gücü |
| 4 | **Cloudflare DPA + Contabo AVV imzalama** | Veri işleyen sıfatıyla yazılı sözleşme (KVKK m.12/2) |
| 5 | **Tüzel kişilik netleştirme** | "Koğ Suit Otel" tüzel unvan + MERSİS no + VKN aydınlatma metnine yazılır |
| 6 | **Çalışan gizlilik taahhüdü** | İleride resepsiyon ekibi eklenirse her birinden KVKK taahhütnamesi (Faz 4a) |
| 7 | **`kvkk@kogsuitotel.com` alias oluşturma** | KVKK başvuruları için ayrı mail kutusu, sahibin gözünden kaçmasın |
| 8 | **Saklama süresi kararı** | Yasal 2 yıl + mali 5/10 yıl arası seçim; geliştirici buna göre anonimleştirme cron'u yazar |
| 9 | **Hosting bölge tercihi gözden geçirme** | Contabo (DE) tercih nedeni: AB GDPR uygun koruma. Alternatif: Türkiye lokasyon (Turhost VPS) — yurt dışı aktarım sorun değil ama performance/maliyet farkı |

---

## 5. KVKK Madde 10 Aydınlatma Metni Gap Analizi

Mevcut `resources/views/pages/kvkk.blade.php` placeholder. Avukat onayı öncesi şu unsurlar **eklenmeli**:

| Zorunluluk | Mevcut | Eksik |
|---|---|---|
| a) Veri sorumlusu kimliği | "Koğ Suit Otel" (marka) | Tüzel unvan + MERSİS + VKN + adres + yetkili kişi |
| b) İşleme amaçları | 4 madde | OK (KBS bildirimi açıklığa kavuştur) |
| c) Aktarılan alıcılar | **YOK** | Contabo (DE), Cloudflare (US), WhatsApp/Meta (US), Emniyet (KBS), Banka |
| d) Toplama yöntemi + hukuki sebep | **YOK** | "Web formu, sözleşmenin ifası için zorunlu, KVKK m.5/2-c" |
| e) Saklama süresi | **YOK** | 2 yıl (yasal) / mali kayıt için 5-10 yıl, sahip kararı sonrası |
| f) 8 hak listesi | Genel cümle | Her hak ayrı satır (m.11 a-h) |
| g) Başvuru yöntemi | E-posta linki | Yazılı / KEP / mobil imza / kayıtlı eposta; başvuru formu PDF |
| h) Çerez politikası | Privacy sayfasında 1 paragraf | Tablo: çerez adı + süre + amaç |
| i) Otomatik karar verme | YOK | "Otomatik karar verme uygulanmamaktadır" cümlesi |

**Plus:** Privacy + KVKK iki ayrı sayfa → "Kişisel Verilerin Korunması ve Çerez Politikası" tek dokümana birleştirilebilir (avukat tercihi).

---

## 6. Sonraki Adım

1. Sahip ile demo + bu doküman gözden geçirme
2. Sahibin yapacağı 9 madde (yukarıda) → avukatla görüşme + sözleşme imzaları
3. Saklama süresi kararı → otomatik anonimleştirme cron implementasyonu
4. 2FA + audit log paketleri (Faz 3 öncesi zorunlu)
5. Aydınlatma metni avukat onayı sonrası `kvkk.blade.php` rewrite

**İlgili dosya:** `docs/guvenlik-denetimi-2026-05.md`

---

*Bu doküman 2026-05-23 Faz 2 Block 3 güvenlik + KVKK denetiminin çıktısıdır. İki paralel agent raporu (security review + KVKK envanter) sentezlendi, kod fix'leri uygulandı, sahibe iletilecek hukuki aksiyonlar listelendi.*
