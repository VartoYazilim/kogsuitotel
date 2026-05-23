<?php

namespace Tests\Feature;

use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_anonim_kullanici_panele_giremez(): void
    {
        $response = $this->get('/kog-yonetim');

        $response->assertRedirect();
    }

    public function test_admin_olmayan_kullanici_panele_giremez(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($user)->get('/kog-yonetim');

        $response->assertForbidden();
    }

    public function test_admin_kullanici_panele_girebilir(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->get('/kog-yonetim');

        $response->assertOk();
    }

    public function test_admin_path_obfuscated(): void
    {
        // /admin yerine /kog-yonetim kullanıyoruz — eski path 404 olmalı
        $response = $this->get('/admin');

        $response->assertNotFound();
    }

    /* ─────────── Filament Resource sayfa testleri (runtime hata yakalama) ─────────── */

    public function test_admin_rezervasyon_olustur_sayfasi_yuklenir(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->get('/kog-yonetim/reservations/create');

        $response->assertOk();
    }

    public function test_admin_oda_olustur_sayfasi_yuklenir(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->get('/kog-yonetim/rooms/create');

        $response->assertOk();
    }

    public function test_admin_galeri_sayfasi_yuklenir(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->get('/kog-yonetim/gallery-images');

        $response->assertOk();
    }

    public function test_admin_ayarlar_sayfasi_yuklenir(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->get('/kog-yonetim/ayarlar');

        $response->assertOk();
    }

    /* ─────────── Tekil kayıt sayfaları (view/edit) — Filament infolist+form runtime ─────────── */

    public function test_admin_rezervasyon_goruntule_sayfasi_yuklenir(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $reservation = Reservation::factory()->create();

        $response = $this->actingAs($admin)
            ->get("/kog-yonetim/reservations/{$reservation->id}");

        $response->assertOk();
    }

    public function test_admin_rezervasyon_duzenle_sayfasi_yuklenir(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $reservation = Reservation::factory()->create();

        $response = $this->actingAs($admin)
            ->get("/kog-yonetim/reservations/{$reservation->id}/edit");

        $response->assertOk();
    }

    public function test_admin_oda_duzenle_sayfasi_yuklenir(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $room = Room::factory()->create();

        $response = $this->actingAs($admin)
            ->get("/kog-yonetim/rooms/{$room->id}/edit");

        $response->assertOk();
    }

    public function test_admin_dashboard_yuklenir(): void
    {
        // Widget'ların hepsi render edilebiliyor mu (Welcome, Stats, Chart, TodayActivity, LatestReservations)
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->get('/kog-yonetim');

        $response->assertOk();
    }

    public function test_admin_musaitlik_sayfasi_yuklenir(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->get('/kog-yonetim/availability');

        $response->assertOk();
    }
}
