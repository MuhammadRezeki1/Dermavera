<?php

namespace Tests\Feature;

use App\Models\Consultation;
use App\Models\ProductVariant;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_public_pages_render(): void
    {
        foreach (['/', '/metode', '/tentang', '/katalog', '/konsultasi/mulai'] as $uri) {
            $this->get($uri)->assertOk();
        } $this->get('/produk/'.ProductVariant::first()->slug)->assertOk();
    }

    public function test_scope_boundary_copy_does_not_claim_other_ages_cannot_use_facial_wash(): void
    {
        $this->get('/')->assertSee('bukan batas usia wajib', false);
    }

    public function test_home_hero_rotates_active_products_and_starts_with_the_kahf_pair(): void
    {
        $response = $this->get('/')->assertOk();

        $response
            ->assertSee('data-product-rotator', false)
            ->assertSee('data-showcase-product="primary" data-code="K03"', false)
            ->assertSee('data-showcase-product="secondary" data-code="K07"', false)
            ->assertSee('data-code="M02"', false)
            ->assertSee('data-code="M03"', false)
            ->assertSee('data-code="M04"', false)
            ->assertSee('data-showcase-toggle', false)
            ->assertDontSee('SAFETY GATE', false)
            ->assertDontSee('data-showcase-next', false);
    }

    public function test_guest_cannot_open_an_unowned_result_uuid(): void
    {
        $consultation = Consultation::create(['uuid' => (string) \Str::uuid(), 'age_group' => '18-24', 'algorithm_version' => 'saw-1.0.0', 'consent_at' => now(), 'input_snapshot' => [], 'status' => 'referred']);
        $this->get(route('results.show', $consultation))->assertForbidden();
    }

    public function test_user_cannot_open_another_users_result(): void
    {
        $owner = User::factory()->create();
        $stranger = User::factory()->create();
        $consultation = Consultation::create(['uuid' => (string) \Str::uuid(), 'user_id' => $owner->id, 'age_group' => '18-24', 'algorithm_version' => 'saw-1.0.0', 'consent_at' => now(), 'input_snapshot' => [], 'status' => 'referred']);
        $this->actingAs($stranger)->get(route('results.show', $consultation))->assertForbidden();
    }
}
