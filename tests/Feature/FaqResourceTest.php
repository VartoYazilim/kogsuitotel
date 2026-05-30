<?php

namespace Tests\Feature;

use App\Filament\Resources\Faqs\Pages\ManageFaqs;
use App\Models\Faq;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FaqResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_sss_sayfasi_db_den_sorulari_okur(): void
    {
        Faq::create([
            'question' => 'Test sorusu — özel?',
            'answer' => 'Test cevabı — uzun.',
            'category' => 'Test',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $response = $this->get(route('faq'));

        $response->assertStatus(200);
        $response->assertSeeText('Test sorusu — özel?');
        $response->assertSeeText('Test cevabı — uzun.');
    }

    public function test_pasif_sss_public_sayfada_gizlenir(): void
    {
        Faq::create([
            'question' => 'Pasif soru',
            'answer' => 'Görünmemeli',
            'sort_order' => 1,
            'is_active' => false,
        ]);

        $response = $this->get(route('faq'));

        $response->assertStatus(200);
        $response->assertDontSeeText('Pasif soru');
    }

    public function test_sss_sayfasi_sort_order_a_gore_listeler(): void
    {
        Faq::create(['question' => 'İkinci soru', 'answer' => 'B', 'sort_order' => 20, 'is_active' => true]);
        Faq::create(['question' => 'İlk soru', 'answer' => 'A', 'sort_order' => 10, 'is_active' => true]);
        Faq::create(['question' => 'Üçüncü soru', 'answer' => 'C', 'sort_order' => 30, 'is_active' => true]);

        $response = $this->get(route('faq'));

        $content = $response->getContent();
        $posIlk = strpos($content, 'İlk soru');
        $posIkinci = strpos($content, 'İkinci soru');
        $posUcuncu = strpos($content, 'Üçüncü soru');

        $this->assertNotFalse($posIlk);
        $this->assertLessThan($posIkinci, $posIlk);
        $this->assertLessThan($posUcuncu, $posIkinci);
    }

    public function test_admin_sss_listeleyebilir(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->get('/kog-yonetim/faqs')
            ->assertStatus(200);
    }

    public function test_non_admin_sss_resource_a_giremez(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)
            ->get('/kog-yonetim/faqs')
            ->assertStatus(403);
    }

    public function test_admin_yeni_soru_olusturabilir(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        Livewire::actingAs($admin)
            ->test(ManageFaqs::class)
            ->callAction('create', [
                'question' => 'Yeni admin sorusu?',
                'answer' => 'Yeni admin cevabı.',
                'category' => 'Konaklama',
                'sort_order' => 100,
                'is_active' => true,
            ])
            ->assertHasNoActionErrors();

        $this->assertDatabaseHas('faqs', [
            'question' => 'Yeni admin sorusu?',
            'is_active' => true,
        ]);
    }

    public function test_faq_schema_org_json_ld_dahil_edilir(): void
    {
        Faq::create([
            'question' => 'Schema test sorusu?',
            'answer' => 'Schema test cevabı.',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $response = $this->get(route('faq'));
        $content = $response->getContent();

        $this->assertStringContainsString('"@type":"FAQPage"', $content);
        $this->assertStringContainsString('Schema test sorusu', $content);
    }
}
