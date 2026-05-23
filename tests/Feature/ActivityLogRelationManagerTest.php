<?php

namespace Tests\Feature;

use App\Enums\ReservationStatus;
use App\Filament\RelationManagers\ActivitiesRelationManager;
use App\Filament\Resources\Reservations\Pages\EditReservation;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

class ActivityLogRelationManagerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_rezervasyon_view_sayfasi_relation_manager_kaydiyla_yuklenir(): void
    {
        // Filament 4'te relation manager Livewire lazy load — initial HTML'de
        // tab title bulunmayabilir ama registration başarılıysa sayfa 200 döner.
        // Relation manager kayıt başarısızsa exception atar, 500 olur.
        $admin = User::factory()->create(['is_admin' => true]);
        $reservation = Reservation::factory()->create();

        $response = $this->actingAs($admin)->get('/kog-yonetim/reservations/'.$reservation->id);

        $response->assertOk();
    }

    public function test_admin_rezervasyon_edit_sayfasi_relation_manager_kaydiyla_yuklenir(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $reservation = Reservation::factory()->create();

        $response = $this->actingAs($admin)->get('/kog-yonetim/reservations/'.$reservation->id.'/edit');

        $response->assertOk();
    }

    public function test_admin_oda_edit_sayfasi_relation_manager_kaydiyla_yuklenir(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $room = Room::factory()->create();

        $response = $this->actingAs($admin)->get('/kog-yonetim/rooms/'.$room->id.'/edit');

        $response->assertOk();
    }

    public function test_activities_relation_manager_livewire_test_render_eder(): void
    {
        // Livewire ile direkt component testi — relation manager içeriği render
        $admin = User::factory()->create(['is_admin' => true]);
        $reservation = Reservation::factory()->create();
        $reservation->update(['status' => ReservationStatus::Confirmed]);

        Livewire::actingAs($admin)
            ->test(ActivitiesRelationManager::class, [
                'ownerRecord' => $reservation,
                'pageClass' => EditReservation::class,
            ])
            ->assertSuccessful()
            ->assertCanSeeTableRecords($reservation->activities);
    }

    public function test_reservation_status_degisikligi_activity_log_kaydeder(): void
    {
        // Spatie ActivityLog'un canlı çalıştığını doğrula (relation manager içeriği için)
        $reservation = Reservation::factory()->create(['status' => ReservationStatus::Pending]);

        // İlk create + status değişikliği iki ayrı kayıt yazar
        $reservation->update(['status' => ReservationStatus::Confirmed]);

        $activities = $reservation->fresh()->activities;
        $this->assertGreaterThanOrEqual(2, $activities->count(), 'create + update için en az 2 activity log kaydı bekleniyor');

        $latestUpdate = $activities->where('event', 'updated')->first();
        $this->assertNotNull($latestUpdate, 'Update activity kaydedildi');
        $this->assertEquals('pending', $latestUpdate->properties->get('old')['status'] ?? null);
        $this->assertEquals('confirmed', $latestUpdate->properties->get('attributes')['status'] ?? null);
    }

    public function test_room_fiyat_degisikligi_activity_log_kaydeder(): void
    {
        $room = Room::factory()->create(['base_price' => 1500.00]);

        $room->update(['base_price' => 1800.00]);

        $activities = $room->fresh()->activities;
        $update = $activities->where('event', 'updated')->first();

        $this->assertNotNull($update);
        $this->assertEquals('1500.00', $update->properties->get('old')['base_price']);
        $this->assertEquals('1800.00', $update->properties->get('attributes')['base_price']);
    }

    public function test_format_changes_helper_eski_yeni_diff_render_eder(): void
    {
        $activity = new Activity([
            'event' => 'updated',
            'properties' => collect([
                'old' => ['status' => 'pending', 'total_price' => '1500.00'],
                'attributes' => ['status' => 'confirmed', 'total_price' => '1800.00'],
            ]),
        ]);

        $html = ActivitiesRelationManager::formatChanges($activity);

        $this->assertStringContainsString('status:', $html);
        $this->assertStringContainsString('pending', $html);
        $this->assertStringContainsString('confirmed', $html);
        $this->assertStringContainsString('total_price:', $html);
        $this->assertStringContainsString('1500.00', $html);
        $this->assertStringContainsString('1800.00', $html);
    }

    public function test_format_changes_helper_create_event_sadece_yeni_degerler(): void
    {
        $activity = new Activity([
            'event' => 'created',
            'properties' => collect([
                'attributes' => ['name' => 'Yeni Oda', 'base_price' => '2000.00'],
            ]),
        ]);

        $html = ActivitiesRelationManager::formatChanges($activity);

        $this->assertStringContainsString('name:', $html);
        $this->assertStringContainsString('Yeni Oda', $html);
        $this->assertStringNotContainsString('<s ', $html); // strikethrough yok create için
    }
}
