<?php

namespace Tests\Feature;

use App\Models\PriceObservation;
use App\Models\ProductSku;
use App\Models\Source;
use App\Services\DatasetVersionService;
use App\Services\RecommendationService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecommendationFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    private function profile(array $overrides = []): array
    {
        return array_merge(['age' => 24, 'primary_complaint' => 'jerawat_ringan', 'secondary_concerns' => [], 'skin_type' => 'berminyak', 'sensitive' => false, 'barrier_impaired' => false, 'acne_therapy' => false, 'allergies' => [], 'red_flags' => [], 'min_price' => null, 'max_price' => null, 'packaging_preference' => '', 'consent_at' => now()], $overrides);
    }

    public function test_red_flag_never_creates_a_normal_ranking(): void
    {
        $consultation = app(RecommendationService::class)->evaluate($this->profile(['red_flags' => ['nodul_kistik']]));
        $this->assertSame('referred', $consultation->status);
        $this->assertTrue($consultation->safetyAssessment->red_flag);
        $this->assertDatabaseCount('recommendation_results', 0);
    }

    public function test_safe_flow_persists_x_r_w_v_versions_and_deterministic_ranking(): void
    {
        $source = Source::first();
        ProductSku::query()->each(fn ($sku) => PriceObservation::create(['product_sku_id' => $sku->id, 'source_id' => $source->id, 'amount' => 30000 + ($sku->id * 100), 'unit_price_per_100' => 30000 + ($sku->id * 100), 'currency' => 'IDR', 'seller' => 'Fixture resmi', 'observed_at' => now(), 'promo' => false, 'evidence_status' => 'verified_fixture', 'is_available' => true, 'ranking_eligible' => true]));
        $this->activateFreshDataset('1.0.1');
        $consultation = app(RecommendationService::class)->evaluate($this->profile());
        $run = $consultation->runs->last();
        $this->assertSame('completed', $consultation->status);
        $this->assertSame('completed', $run->status);
        $this->assertNotEmpty($run->calculation_snapshot['X']);
        $this->assertNotEmpty($run->calculation_snapshot['R']);
        $this->assertCount(6, $run->calculation_snapshot['W']);
        $this->assertNotEmpty($run->calculation_snapshot['V']);
        $this->assertSame('saw-1.3.0', $run->calculation_snapshot['algorithm_version']);
        $this->assertSame('criteria-1.4.0', $run->calculation_snapshot['scoring_version']);
        $this->assertSame('1.0.1', $run->calculation_snapshot['dataset_version']);
        $this->assertGreaterThanOrEqual(3, $run->results->where('eligibility_status', 'eligible')->count());
    }

    public function test_allergy_excludes_matching_formula_before_matrix(): void
    {
        $source = Source::first();
        ProductSku::query()->each(fn ($sku) => PriceObservation::create(['product_sku_id' => $sku->id, 'source_id' => $source->id, 'amount' => 35000, 'unit_price_per_100' => 35000, 'seller' => 'Fixture', 'observed_at' => now(), 'evidence_status' => 'verified_fixture', 'is_available' => true, 'ranking_eligible' => true]));
        $this->activateFreshDataset('1.0.2');
        $consultation = app(RecommendationService::class)->evaluate($this->profile(['allergies' => ['SALICYLIC ACID']]));
        $excluded = $consultation->runs->last()->results->where('eligibility_status', 'excluded');
        $this->assertTrue($excluded->contains(fn ($result) => in_array('HC-01', $result->explanation_codes, true)));
    }

    public function test_budget_exclusions_are_persisted_and_auditable(): void
    {
        $source = Source::first();
        ProductSku::query()->each(fn ($sku) => PriceObservation::create(['product_sku_id' => $sku->id, 'source_id' => $source->id, 'amount' => 35000, 'unit_price_per_100' => 35000, 'seller' => 'Fixture', 'observed_at' => now(), 'evidence_status' => 'verified_fixture', 'is_available' => true, 'ranking_eligible' => true]));
        $this->activateFreshDataset('1.0.3');

        $consultation = app(RecommendationService::class)->evaluate($this->profile(['max_price' => 10000]));
        $run = $consultation->runs->last();

        $this->assertSame('no_safe_alternative', $consultation->status);
        $this->assertTrue($run->results->contains(fn ($result) => in_array('above_max_budget', $result->explanation_codes, true)));
        $this->assertContains(['above_max_budget'], $run->calculation_snapshot['excluded']);
    }

    public function test_secondary_concern_modifies_complaint_score(): void
    {
        $base = $this->profile();
        $withoutSecondary = app(RecommendationService::class)->evaluate($base);
        $withSecondary = app(RecommendationService::class)->evaluate(array_merge($base, ['secondary_concerns' => ['kusam']]));

        $plain = $withoutSecondary->runs->last()->results->first(fn ($result) => $result->variant->catalog_code === 'B01');
        $modified = $withSecondary->runs->last()->results->first(fn ($result) => $result->variant->catalog_code === 'B01');

        $this->assertNotNull($plain);
        $this->assertNotNull($modified);
        $this->assertLessThan($modified->raw_scores['C1'], $plain->raw_scores['C1']);
        $this->assertContains('secondary_concern_support', $modified->explanation_codes);
    }

    public function test_dry_skin_context_penalizes_irritant_formula(): void
    {
        $base = $this->profile(['primary_complaint' => 'kering', 'skin_type' => 'kering']);
        $withoutIrritant = app(RecommendationService::class)->evaluate($base);

        $sensitive = app(RecommendationService::class)->evaluate(array_merge($base, ['sensitive' => true]));
        $plain = $withoutIrritant->runs->last()->results->first(fn ($result) => $result->variant->catalog_code === 'B01');
        $cautious = $sensitive->runs->last()->results->first(fn ($result) => $result->variant->catalog_code === 'B01');

        $this->assertNotNull($plain);
        $this->assertNotNull($cautious);
        $this->assertLessThanOrEqual($plain->raw_scores['C2'], $cautious->raw_scores['C2']);
    }

    private function activateFreshDataset(string $version): void
    {
        $service = app(DatasetVersionService::class);
        $service->activate($service->snapshot($version, 'Test snapshot with fresh prices'));
    }
}
