<?php

namespace Tests\Feature;

use App\Filament\Pages\BusinessSettings;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BusinessSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_anonim_kullanici_ayarlar_sayfasina_giremez(): void
    {
        $response = $this->get('/kog-yonetim/ayarlar');

        $response->assertRedirect();
    }

    public function test_admin_olmayan_kullanici_ayarlar_sayfasina_giremez(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($user)->get('/kog-yonetim/ayarlar');

        $response->assertForbidden();
    }

    public function test_admin_ayarlar_sayfasini_yukler(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->get('/kog-yonetim/ayarlar');

        $response->assertOk();
        $response->assertSeeText('İşletme Ayarları');
        $response->assertSeeText('Banka Bilgileri');
        $response->assertSeeText('İletişim');
        $response->assertSeeText('Konaklama Saatleri');
        $response->assertSeeText('Sosyal Medya');
    }

    public function test_mount_mevcut_setting_degerlerini_form_a_doldurur(): void
    {
        Setting::set('iban', 'TR99 0000 1111 2222 3333 4444 55');
        Setting::set('phone', '+90 444 11 22');
        Setting::set('email', 'merhaba@kogsuitotel.com');

        $admin = User::factory()->create(['is_admin' => true]);

        Livewire::actingAs($admin)
            ->test(BusinessSettings::class)
            ->assertFormSet([
                'iban' => 'TR99 0000 1111 2222 3333 4444 55',
                'phone' => '+90 444 11 22',
                'email' => 'merhaba@kogsuitotel.com',
            ]);
    }

    public function test_kaydet_tum_key_value_lari_db_ye_yazar(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        Livewire::actingAs($admin)
            ->test(BusinessSettings::class)
            ->fillForm([
                'iban' => 'TR12 3456 7890 1234 5678 9012 34',
                'iban_holder' => 'Test Hesap Sahibi',
                'bank_name' => 'Ziraat Bankası',
                'phone' => '+90 555 11 22 33',
                'whatsapp' => '+90 555 11 22 33',
                'email' => 'test@kogsuitotel.com',
                'address' => 'Test Adres, Varto, Muş',
                'checkin_time' => '14:00',
                'checkout_time' => '12:00',
                'instagram_url' => 'https://instagram.com/test',
                'facebook_url' => 'https://facebook.com/test',
                'google_maps_url' => 'https://maps.google.com/?cid=123',
                'tripadvisor_url' => 'https://tripadvisor.com/Hotel_Test',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('TR123456789012345678901234', Setting::get('iban')); // boşluklar temizlenir
        $this->assertSame('Test Hesap Sahibi', Setting::get('iban_holder'));
        $this->assertSame('Ziraat Bankası', Setting::get('bank_name'));
        $this->assertSame('test@kogsuitotel.com', Setting::get('email'));
        $this->assertSame('14:00', Setting::get('checkin_time'));
        $this->assertSame('https://instagram.com/test', Setting::get('instagram_url'));
    }

    public function test_iban_bosluklari_temizlenir_dbye_compact_yazilir(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        Livewire::actingAs($admin)
            ->test(BusinessSettings::class)
            ->fillForm([
                'iban' => 'TR12 3456 7890 1234 5678 9012 34',
                'iban_holder' => 'Test',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        // Boşluksuz 26 karakter (TR + 24)
        $this->assertSame('TR123456789012345678901234', Setting::get('iban'));
    }

    public function test_hatali_email_validation_calisir(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        Livewire::actingAs($admin)
            ->test(BusinessSettings::class)
            ->fillForm([
                'iban' => 'TR12 3456 7890 1234 5678 9012 34',
                'iban_holder' => 'Test',
                'email' => 'gecersiz-email',
            ])
            ->call('save')
            ->assertHasFormErrors(['email']);
    }

    public function test_hatali_url_validation_calisir(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        Livewire::actingAs($admin)
            ->test(BusinessSettings::class)
            ->fillForm([
                'iban' => 'TR12 3456 7890 1234 5678 9012 34',
                'iban_holder' => 'Test',
                'instagram_url' => 'bu bir url degil',
            ])
            ->call('save')
            ->assertHasFormErrors(['instagram_url']);
    }

    public function test_save_sonrasi_setting_cache_invalidate_eder(): void
    {
        Setting::set('phone', '+90 444 11 11');
        $this->assertSame('+90 444 11 11', Setting::get('phone'));

        $admin = User::factory()->create(['is_admin' => true]);

        Livewire::actingAs($admin)
            ->test(BusinessSettings::class)
            ->fillForm([
                'iban' => 'TR12 3456 7890 1234 5678 9012 34',
                'iban_holder' => 'Test',
                'phone' => '+90 555 22 33 44',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('+90 555 22 33 44', Setting::get('phone'));
    }

    public function test_eski_setting_resource_url_artik_404(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->get('/kog-yonetim/settings');

        $response->assertNotFound();
    }
}
