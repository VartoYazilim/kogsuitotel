<?php

namespace Tests\Feature;

use App\Models\User;
use Filament\Auth\Pages\EditProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class ProfilePasswordChangeTest extends TestCase
{
    use RefreshDatabase;

    public function test_anonim_kullanici_profil_sayfasina_giremez(): void
    {
        $response = $this->get('/kog-yonetim/profile');

        $response->assertRedirect();
    }

    public function test_admin_olmayan_kullanici_profil_sayfasina_giremez(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($user)->get('/kog-yonetim/profile');

        $response->assertForbidden();
    }

    public function test_admin_profil_sayfasini_yukler(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->get('/kog-yonetim/profile');

        $response->assertOk();
        $response->assertSeeText('Profil');
    }

    public function test_profil_formu_butun_alanlari_iceriyor(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        Livewire::actingAs($admin)
            ->test(EditProfile::class)
            ->assertFormFieldExists('name')
            ->assertFormFieldExists('email')
            ->assertFormFieldExists('password')
            ->assertFormFieldExists('passwordConfirmation')
            ->assertFormFieldExists('currentPassword');
    }

    public function test_admin_sifresini_degistirebilir(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'name' => 'Test Admin',
            'email' => 'admin@kogsuitotel.test',
            'password' => Hash::make('EskiGuvenli123'),
        ]);

        Livewire::actingAs($admin)
            ->test(EditProfile::class)
            ->fillForm([
                'name' => $admin->name,
                'email' => $admin->email,
                'password' => 'YeniGuvenli456',
                'passwordConfirmation' => 'YeniGuvenli456',
                'currentPassword' => 'EskiGuvenli123',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $admin->refresh();
        $this->assertTrue(Hash::check('YeniGuvenli456', $admin->password));
        $this->assertFalse(Hash::check('EskiGuvenli123', $admin->password));
    }

    public function test_zayif_sifre_reddedilir_password_defaults_kurali_uygulanir(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'password' => Hash::make('EskiGuvenli123'),
        ]);

        // Min 12 char + mixedCase + numbers (AppServiceProvider Password::defaults)
        Livewire::actingAs($admin)
            ->test(EditProfile::class)
            ->fillForm([
                'name' => $admin->name,
                'email' => $admin->email,
                'password' => 'kisa', // < 12 char, küçük harf, rakam yok
                'passwordConfirmation' => 'kisa',
                'currentPassword' => 'EskiGuvenli123',
            ])
            ->call('save')
            ->assertHasFormErrors(['password']);

        // Şifre değişmedi
        $admin->refresh();
        $this->assertTrue(Hash::check('EskiGuvenli123', $admin->password));
    }

    public function test_yanlis_mevcut_sifre_ile_degistirme_engellenir(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'password' => Hash::make('DogruEskiSifre123'),
        ]);

        Livewire::actingAs($admin)
            ->test(EditProfile::class)
            ->fillForm([
                'name' => $admin->name,
                'email' => $admin->email,
                'password' => 'YeniGuvenli456',
                'passwordConfirmation' => 'YeniGuvenli456',
                'currentPassword' => 'YanlisEskiSifre',
            ])
            ->call('save')
            ->assertHasFormErrors(['currentPassword']);

        $admin->refresh();
        $this->assertTrue(Hash::check('DogruEskiSifre123', $admin->password));
    }
}
