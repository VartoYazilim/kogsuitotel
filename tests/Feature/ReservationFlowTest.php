<?php

namespace Tests\Feature;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Test seeder'ı: 1 oda + 1 setting yeterli
        Setting::create(['key' => 'email', 'value' => 'admin@kogsuitotel.com']);
    }

    public function test_rezervasyon_formu_acilir(): void
    {
        Room::factory()->create(['is_active' => true]);

        $response = $this->get('/rezervasyon');

        $response->assertOk();
        $response->assertSee('Oda &amp; Tarih', false);
        $response->assertSee('Misafir Bilgileri');
    }

    public function test_gecerli_rezervasyon_olusturulur(): void
    {
        $room = Room::factory()->create([
            'is_active' => true,
            'base_price' => 1500,
        ]);

        $payload = [
            'guest_first_name' => 'Ali',
            'guest_last_name' => 'Veli',
            'guest_phone' => '+90 555 123 45 67',
            'guest_email' => 'ali@example.com',
            'room_id' => $room->id,
            'check_in' => now()->addDays(7)->format('Y-m-d'),
            'check_out' => now()->addDays(10)->format('Y-m-d'),
            'adults' => 2,
            'children' => 0,
            'special_requests' => 'Erken giriş istiyoruz',
        ];

        $response = $this->post('/rezervasyon', $payload);

        // DB'de kayıt oluşmuş mu
        $this->assertDatabaseHas('reservations', [
            'guest_email' => 'ali@example.com',
            'room_id' => $room->id,
            'adults' => 2,
            'nights' => 3,
            'total_price' => 4500.00, // 1500 × 3 gece
            'status' => ReservationStatus::Pending->value,
        ]);

        // Reservation code formatı kontrolü
        $reservation = Reservation::where('guest_email', 'ali@example.com')->first();
        $this->assertMatchesRegularExpression('/^KSO-\d{4}-\d{4}$/', $reservation->reservation_code);

        // Success sayfasına redirect
        $response->assertRedirect("/rezervasyon/basarili/{$reservation->reservation_code}");
    }

    public function test_honeypot_dolu_ise_rezervasyon_olusturulmaz(): void
    {
        $room = Room::factory()->create(['is_active' => true]);

        $response = $this->post('/rezervasyon', [
            'website' => 'http://spam.example.com', // honeypot
            'guest_first_name' => 'Bot',
            'guest_last_name' => 'Spam',
            'guest_phone' => '+90 555 000 00 00',
            'guest_email' => 'bot@spam.com',
            'room_id' => $room->id,
            'check_in' => now()->addDays(5)->format('Y-m-d'),
            'check_out' => now()->addDays(7)->format('Y-m-d'),
            'adults' => 1,
        ]);

        $response->assertRedirect('/');
        $this->assertDatabaseMissing('reservations', ['guest_email' => 'bot@spam.com']);
    }

    public function test_eksik_alan_validation_hatasi_doner(): void
    {
        $room = Room::factory()->create(['is_active' => true]);

        $response = $this->post('/rezervasyon', [
            'guest_first_name' => 'Ali',
            // soyad, telefon, email yok
            'room_id' => $room->id,
            'check_in' => now()->addDays(7)->format('Y-m-d'),
            'check_out' => now()->addDays(10)->format('Y-m-d'),
            'adults' => 2,
        ]);

        $response->assertSessionHasErrors(['guest_last_name', 'guest_phone', 'guest_email']);
    }

    public function test_gecmis_tarih_kabul_edilmez(): void
    {
        $room = Room::factory()->create(['is_active' => true]);

        $response = $this->post('/rezervasyon', [
            'guest_first_name' => 'Ali',
            'guest_last_name' => 'Veli',
            'guest_phone' => '+90 555 123 45 67',
            'guest_email' => 'ali@example.com',
            'room_id' => $room->id,
            'check_in' => now()->subDays(1)->format('Y-m-d'), // dün
            'check_out' => now()->addDays(2)->format('Y-m-d'),
            'adults' => 2,
        ]);

        $response->assertSessionHasErrors(['check_in']);
    }

    public function test_cikis_tarihi_giristen_sonra_olmalidir(): void
    {
        $room = Room::factory()->create(['is_active' => true]);

        $response = $this->post('/rezervasyon', [
            'guest_first_name' => 'Ali',
            'guest_last_name' => 'Veli',
            'guest_phone' => '+90 555 123 45 67',
            'guest_email' => 'ali@example.com',
            'room_id' => $room->id,
            'check_in' => now()->addDays(7)->format('Y-m-d'),
            'check_out' => now()->addDays(5)->format('Y-m-d'), // giriş'ten önce
            'adults' => 2,
        ]);

        $response->assertSessionHasErrors(['check_out']);
    }

    public function test_basarili_sayfasi_kod_ile_acilir(): void
    {
        $reservation = Reservation::factory()->create();
        Setting::create(['key' => 'iban', 'value' => 'TR00 0000 0000 0000 0000 0000 00']);

        $response = $this->get("/rezervasyon/basarili/{$reservation->reservation_code}");

        $response->assertOk();
        $response->assertSee($reservation->reservation_code);
        $response->assertSee('TR00 0000 0000 0000 0000 0000 00');
    }

    /* ─────────── Tarih Çakışma Kontrolü (Faz 2n) ─────────── */

    public function test_aktif_rezervasyonla_cakisan_tarih_engellenir(): void
    {
        $room = Room::factory()->create(['is_active' => true]);

        // Var olan onaylı rezervasyon: 5-8 Haziran
        Reservation::factory()->paid()->create([
            'room_id' => $room->id,
            'check_in' => '2026-06-05',
            'check_out' => '2026-06-08',
        ]);

        // Çakışan tarih: 6-7 Haziran
        $response = $this->post('/rezervasyon', [
            'guest_first_name' => 'Test',
            'guest_last_name' => 'Cakisma',
            'guest_phone' => '+90 555 111 22 33',
            'guest_email' => 'cakisma@example.com',
            'room_id' => $room->id,
            'check_in' => '2026-06-06',
            'check_out' => '2026-06-07',
            'adults' => 2,
        ]);

        $response->assertSessionHasErrors(['check_in']);
        $this->assertDatabaseMissing('reservations', ['guest_email' => 'cakisma@example.com']);
    }

    public function test_pending_rezervasyon_cakisma_engellemez(): void
    {
        $room = Room::factory()->create(['is_active' => true]);

        // Pending rezervasyon — 24 saatlik hold, çakışma sayılmaz
        Reservation::factory()->pending()->create([
            'room_id' => $room->id,
            'check_in' => '2026-06-05',
            'check_out' => '2026-06-08',
        ]);

        $response = $this->post('/rezervasyon', [
            'guest_first_name' => 'Test',
            'guest_last_name' => 'Pending',
            'guest_phone' => '+90 555 222 33 44',
            'guest_email' => 'pending-overlap@example.com',
            'room_id' => $room->id,
            'check_in' => '2026-06-06',
            'check_out' => '2026-06-07',
            'adults' => 2,
        ]);

        $this->assertDatabaseHas('reservations', ['guest_email' => 'pending-overlap@example.com']);
    }

    public function test_cikis_gunu_yeni_giris_kabul_edilir(): void
    {
        $room = Room::factory()->create(['is_active' => true]);

        // Önceki rez 5-8 Haziran (8'de çıkış)
        Reservation::factory()->paid()->create([
            'room_id' => $room->id,
            'check_in' => '2026-06-05',
            'check_out' => '2026-06-08',
        ]);

        // Yeni rez 8'de giriş — çakışmamalı (otel kuralı: çıkış günü oda yeniden müsait)
        $response = $this->post('/rezervasyon', [
            'guest_first_name' => 'Test',
            'guest_last_name' => 'Bitisik',
            'guest_phone' => '+90 555 333 44 55',
            'guest_email' => 'bitisik@example.com',
            'room_id' => $room->id,
            'check_in' => '2026-06-08',
            'check_out' => '2026-06-10',
            'adults' => 2,
        ]);

        $this->assertDatabaseHas('reservations', ['guest_email' => 'bitisik@example.com']);
    }

    /* ─────────── Notification dispatch (Faz 2b admin bell) ─────────── */

    public function test_yeni_rezervasyon_admine_notification_gonderir(): void
    {
        \Illuminate\Support\Facades\Notification::fake();

        $admin = \App\Models\User::factory()->create(['is_admin' => true]);
        $nonAdmin = \App\Models\User::factory()->create(['is_admin' => false]);
        $room = Room::factory()->create(['is_active' => true]);

        $this->post('/rezervasyon', [
            'guest_first_name' => 'Test',
            'guest_last_name' => 'Notif',
            'guest_phone' => '+90 555 123 45 67',
            'guest_email' => 'notif@test.com',
            'room_id' => $room->id,
            'check_in' => now()->addDays(5)->format('Y-m-d'),
            'check_out' => now()->addDays(8)->format('Y-m-d'),
            'adults' => 2,
        ]);

        // Admin'e gönderildi
        \Illuminate\Support\Facades\Notification::assertSentTo(
            $admin,
            \App\Notifications\ReservationCreated::class
        );

        // Non-admin'e GÖNDERİLMEDİ
        \Illuminate\Support\Facades\Notification::assertNotSentTo(
            $nonAdmin,
            \App\Notifications\ReservationCreated::class
        );
    }

    public function test_reservation_created_notification_database_payload_calisir(): void
    {
        // Bu test wrong namespace gibi runtime hatalarını yakalar.
        $room = Room::factory()->create(['name' => 'Test Süit']);
        $reservation = Reservation::factory()->create([
            'room_id' => $room->id,
            'guest_first_name' => 'Ali',
            'guest_last_name' => 'Veli',
        ]);
        $admin = \App\Models\User::factory()->create(['is_admin' => true]);

        $notification = new \App\Notifications\ReservationCreated($reservation);

        // toDatabase çalışmazsa exception fırlar — testi düşürür
        $payload = $notification->toDatabase($admin);

        $this->assertIsArray($payload);
        $this->assertArrayHasKey('title', $payload);
        $this->assertSame('Yeni Rezervasyon Talebi', $payload['title']);
    }

    /* ─────────── Model invariantları (kapasite + update çakışma) ─────────── */

    public function test_kapasite_ustu_misafir_sayisi_engellenir(): void
    {
        $room = Room::factory()->create(['is_active' => true, 'capacity' => 2]);

        $this->expectException(\Illuminate\Validation\ValidationException::class);

        Reservation::factory()->create([
            'room_id' => $room->id,
            'adults' => 2,
            'children' => 2,  // toplam 4, kapasite 2 → fail
        ]);
    }

    public function test_kapasite_sinirindaki_misafir_kabul_edilir(): void
    {
        $room = Room::factory()->create(['is_active' => true, 'capacity' => 3]);

        $reservation = Reservation::factory()->create([
            'room_id' => $room->id,
            'adults' => 2,
            'children' => 1,  // toplam 3, kapasite 3 → OK
        ]);

        $this->assertNotNull($reservation->id);
    }

    public function test_update_ile_baska_odaya_cakisan_tarihte_gecirilemez(): void
    {
        $standartOda = Room::factory()->create(['is_active' => true, 'name' => 'Standart']);
        $suitOda = Room::factory()->create(['is_active' => true, 'name' => 'Suit']);

        // Standart oda 5-8 Haziran dolu (paid)
        Reservation::factory()->paid()->create([
            'room_id' => $standartOda->id,
            'check_in' => '2026-06-05',
            'check_out' => '2026-06-08',
        ]);

        // Müşteri Suit oda 6-7 Haziran rezervasyonu yapmış
        $rezervasyon = Reservation::factory()->paid()->create([
            'room_id' => $suitOda->id,
            'check_in' => '2026-06-06',
            'check_out' => '2026-06-07',
        ]);

        // Admin Suit oda'yı Standart oda'ya çevirmek istiyor — engellenmeli
        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $rezervasyon->update(['room_id' => $standartOda->id]);
    }

    public function test_pending_rez_aktif_status_yapilirken_cakisma_engellenir(): void
    {
        $oda = Room::factory()->create(['is_active' => true]);

        // 5-8 Haziran zaten paid var
        Reservation::factory()->paid()->create([
            'room_id' => $oda->id,
            'check_in' => '2026-06-05',
            'check_out' => '2026-06-08',
        ]);

        // Aynı aralıkta pending rez. yaratılabilir (24h hold)
        $pendingRez = Reservation::factory()->pending()->create([
            'room_id' => $oda->id,
            'check_in' => '2026-06-06',
            'check_out' => '2026-06-07',
        ]);

        $this->assertNotNull($pendingRez->id);

        // Ama pending'i paid'e çevirmek engellenmeli
        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $pendingRez->update(['status' => \App\Enums\ReservationStatus::Paid]);
    }

    public function test_anasayfa_arama_formunda_tarih_rezervasyon_sayfasina_tasinir(): void
    {
        $room = Room::factory()->create(['is_active' => true, 'slug' => 'standart-oda']);

        $checkIn = now()->addDays(10)->format('Y-m-d');
        $checkOut = now()->addDays(12)->format('Y-m-d');

        $response = $this->get("/rezervasyon?oda=standart-oda&check_in={$checkIn}&check_out={$checkOut}");

        $response->assertOk();
        $response->assertSee($checkIn, false);
        $response->assertSee($checkOut, false);
    }

    public function test_unavailable_dates_api_listesi_doner(): void
    {
        $room = Room::factory()->create(['is_active' => true, 'slug' => 'test-oda']);

        Reservation::factory()->paid()->create([
            'room_id' => $room->id,
            'check_in' => now()->addDays(5)->startOfDay(),
            'check_out' => now()->addDays(8)->startOfDay(),
        ]);

        Reservation::factory()->pending()->create([
            'room_id' => $room->id,
            'check_in' => now()->addDays(15)->startOfDay(),
            'check_out' => now()->addDays(18)->startOfDay(),
        ]);

        $response = $this->getJson('/api/rooms/test-oda/unavailable-dates');

        $response->assertOk();
        // Sadece paid olan dönmeli, pending dönmemeli
        $response->assertJsonCount(1);
        $response->assertJsonStructure([['from', 'to']]);
    }
}
