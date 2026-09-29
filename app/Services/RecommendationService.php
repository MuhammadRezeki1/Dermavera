<?php

namespace App\Services;

use App\Domain\Recommendation\Contracts\SawCalculatorContract;
use App\Models\Consultation;
use App\Models\DatasetVersion;
use App\Models\ProductVariant;
use App\Models\RecommendationResult;
use App\Models\SafetyAssessment;
use App\Models\WeightSet;
use Brick\Math\BigDecimal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RecommendationService
{
    public function __construct(
        private readonly SafetyGateService $safety,
        private readonly EligibilityService $eligibility,
        private readonly CriterionScoringService $scoring,
        private readonly SawCalculatorContract $saw,
        private readonly DatasetVersionService $datasets,
    ) {}

    /**
     * @param  array{age:int,primary_complaint:string,secondary_concerns:list<string>,skin_type:string,sensitive:bool,barrier_impaired:bool,acne_therapy:bool,allergies:list<string>,red_flags:list<string>,min_price:int|null,max_price:int|null,packaging_preference:string,consent_at:mixed}  $profile
     */
    public function evaluate(array $profile, ?int $userId = null): Consultation
    {
        return DB::transaction(function () use ($profile, $userId) {
            $dataset = DatasetVersion::where('status', 'active')->lockForUpdate()->firstOrFail();
            $this->datasets->assertCurrent($dataset);
            $weights = WeightSet::with('weights.criterion')->where('is_active', true)->firstOrFail();
            $algorithmVersion = (string) config('dermavera.algorithm_version');
            $consultation = Consultation::create([
                'uuid' => (string) Str::uuid(), 'user_id' => $userId, 'dataset_version_id' => $dataset->id,
                'age_group' => $profile['age'] <= 24 ? '18-24' : '25-30', 'algorithm_version' => $algorithmVersion,
                'consent_at' => $profile['consent_at'] ?? now(), 'input_snapshot' => $profile, 'status' => 'processing',
                'expires_at' => $userId ? null : now()->addDays((int) config('dermavera.guest_retention_days', 30)),
            ]);

            $decision = $this->safety->assess($profile);
            SafetyAssessment::create(['consultation_id' => $consultation->id, 'red_flag' => $decision->shouldRefer(), 'hard_constraint_codes' => $decision->hardConstraints, 'outcome' => $decision->outcome, 'explanation_codes' => $decision->explanationCodes]);
            if ($decision->shouldRefer()) {
                $consultation->update(['status' => 'referred']);

                return $consultation->fresh(['safetyAssessment']);
            }

            $run = $consultation->runs()->create(['weight_set_id' => $weights->id, 'idempotency_key' => (string) Str::uuid(), 'started_at' => now(), 'status' => 'processing']);
            $variants = ProductVariant::with(['brand', 'formulaVersions.source', 'formulaVersions.formulaIngredients.ingredient', 'formulaVersions.sku.latestPrice.source', 'formulaVersions.sku.latestEligiblePrice.source', 'skus.latestPrice.source', 'skus.latestEligiblePrice.source'])
                ->where('is_active', true)->where('status', 'verified')->orderBy('catalog_code')->get();
            $selection = $this->eligibility->filter($variants, $profile);
            $runtimeExcluded = [];

            foreach ($selection['excluded'] as $candidate) {
                RecommendationResult::create(['recommendation_run_id' => $run->id, 'product_variant_id' => $candidate['variant']->id, 'product_sku_id' => $candidate['sku']?->id, 'formula_version_id' => $candidate['formula']?->id, 'raw_scores' => [], 'normalized_scores' => [], 'contributions' => [], 'eligibility_status' => 'excluded', 'explanation_codes' => $candidate['reasons'], 'warnings' => []]);
            }

            $matrix = $metadata = [];
            foreach ($selection['eligible'] as $candidate) {
                $variant = $candidate['variant'];
                $evaluated = $this->scoring->score($variant, $profile, $candidate['formula'], $candidate['sku']);
                if ($evaluated['scores']['C4'] === null) {
                    $latest = $candidate['sku']?->latestPrice;
                    $reason = $latest && $latest->observed_at->lt(now()->subDays((int) config('dermavera.price_max_age_days', 30))) ? 'stale_price' : ($latest ? 'ineligible_price_evidence' : 'missing_fresh_price');
                    $runtimeExcluded[$candidate['key']] = [$reason];
                    RecommendationResult::create(['recommendation_run_id' => $run->id, 'product_variant_id' => $variant->id, 'product_sku_id' => $candidate['sku']?->id, 'formula_version_id' => $candidate['formula']->id, 'price_observation_id' => $latest?->id, 'raw_scores' => $evaluated['scores'], 'normalized_scores' => [], 'contributions' => [], 'eligibility_status' => 'insufficient_evidence', 'explanation_codes' => [$reason], 'warnings' => $evaluated['warnings']]);

                    continue;
                }
                if ($evaluated['price']->observed_at->lt(now()->subDays((int) config('dermavera.price_max_age_days', 30)))) {
                    $runtimeExcluded[$candidate['key']] = ['stale_price'];
                    RecommendationResult::create(['recommendation_run_id' => $run->id, 'product_variant_id' => $variant->id, 'product_sku_id' => $candidate['sku']->id, 'formula_version_id' => $candidate['formula']->id, 'price_observation_id' => $evaluated['price']->id, 'raw_scores' => $evaluated['scores'], 'normalized_scores' => [], 'contributions' => [], 'eligibility_status' => 'insufficient_evidence', 'explanation_codes' => ['stale_price'], 'warnings' => $evaluated['warnings']]);

                    continue;
                }
                if (($profile['max_price'] ?? null) !== null && $evaluated['price']->amount > $profile['max_price']) {
                    $runtimeExcluded[$candidate['key']] = ['above_max_budget'];
                    RecommendationResult::create(['recommendation_run_id' => $run->id, 'product_variant_id' => $variant->id, 'product_sku_id' => $candidate['sku']->id, 'formula_version_id' => $candidate['formula']->id, 'price_observation_id' => $evaluated['price']->id, 'raw_scores' => $evaluated['scores'], 'normalized_scores' => [], 'contributions' => [], 'eligibility_status' => 'excluded', 'explanation_codes' => ['above_max_budget'], 'warnings' => $evaluated['warnings']]);

                    continue;
                }
                if (($profile['min_price'] ?? null) !== null && $evaluated['price']->amount < $profile['min_price']) {
                    $runtimeExcluded[$candidate['key']] = ['below_min_budget'];
                    RecommendationResult::create(['recommendation_run_id' => $run->id, 'product_variant_id' => $variant->id, 'product_sku_id' => $candidate['sku']->id, 'formula_version_id' => $candidate['formula']->id, 'price_observation_id' => $evaluated['price']->id, 'raw_scores' => $evaluated['scores'], 'normalized_scores' => [], 'contributions' => [], 'eligibility_status' => 'excluded', 'explanation_codes' => ['below_min_budget'], 'warnings' => $evaluated['warnings']]);

                    continue;
                }
                $matrix[$candidate['key']] = $evaluated['scores'];
                $metadata[$candidate['key']] = ['variant' => $variant, 'formula' => $candidate['formula'], ...$evaluated];
            }

            if ($matrix === []) {
                $run->update(['status' => 'no_safe_alternative', 'completed_at' => now(), 'calculation_snapshot' => ['eligible_variants' => [], 'eligible_count' => 0, 'score_mode' => 'no_ranking', 'excluded' => [...collect($selection['excluded'])->map(fn ($candidate) => $candidate['reasons'])->all(), ...$runtimeExcluded], 'dataset_version' => $dataset->version, 'dataset_hash' => $dataset->hash, 'algorithm_version' => $algorithmVersion, 'scoring_version' => CriterionScoringService::VERSION]]);
                $consultation->update(['status' => 'no_safe_alternative']);

                return $consultation->fresh(['safetyAssessment', 'runs.results.variant']);
            }

            $weightValues = $weights->weights->mapWithKeys(fn ($weight) => [$weight->criterion->code => (string) $weight->normalized_value])->all();
            $types = $weights->weights->mapWithKeys(fn ($weight) => [$weight->criterion->code => $weight->criterion->type])->all();
            $calculation = $this->saw->calculate($matrix, $weightValues, $types);
            $tieGroups = collect($calculation->ranking)
                ->groupBy(fn ($code) => number_format((float) $calculation->scores[$code], 4, '.', ''))
                ->filter(fn ($group) => $group->count() > 1)
                ->map(fn ($group) => $group->values()->all())
                ->values()
                ->all();
            $eligibleCount = count($calculation->ranking);
            $scoreMode = $eligibleCount === 1 ? 'relative_single_candidate' : 'comparative_saw';
            $displayRank = 0;
            $previousScore = null;
            foreach ($calculation->ranking as $index => $code) {
                $score = BigDecimal::of($calculation->scores[$code]);
                if ($previousScore === null || $previousScore->minus($score)->abs()->isGreaterThan('0.0001')) {
                    $displayRank = $index + 1;
                    $previousScore = $score;
                }

                RecommendationResult::create([
                    'recommendation_run_id' => $run->id, 'product_variant_id' => $metadata[$code]['variant']->id, 'product_sku_id' => $metadata[$code]['sku']->id, 'formula_version_id' => $metadata[$code]['formula']->id, 'price_observation_id' => $metadata[$code]['price']->id, 'rank' => $displayRank,
                    'raw_scores' => $calculation->matrix[$code], 'normalized_scores' => $calculation->normalized[$code],
                    'contributions' => $calculation->contributions[$code], 'final_score' => $calculation->scores[$code],
                    'eligibility_status' => 'eligible', 'explanation_codes' => $metadata[$code]['explanations'], 'warnings' => [...$decision->warnings, ...$metadata[$code]['warnings']],
                ]);
            }
            $run->update(['status' => 'completed', 'completed_at' => now(), 'calculation_snapshot' => ['input' => $profile, 'eligible_variants' => array_keys($matrix), 'eligible_count' => $eligibleCount, 'score_mode' => $scoreMode, 'tie_groups' => $tieGroups, 'excluded' => [...collect($selection['excluded'])->map(fn ($candidate) => $candidate['reasons'])->all(), ...$runtimeExcluded], 'X' => $calculation->matrix, 'R' => $calculation->normalized, 'W' => $weightValues, 'contributions' => $calculation->contributions, 'V' => $calculation->scores, 'ranking' => $calculation->ranking, 'dataset_version' => $dataset->version, 'dataset_hash' => $dataset->hash, 'algorithm_version' => $algorithmVersion, 'scoring_version' => CriterionScoringService::VERSION]]);
            $consultation->update(['status' => 'completed']);

            return $consultation->fresh(['safetyAssessment', 'datasetVersion', 'runs.results.variant.brand', 'runs.results.variant.skus.latestPrice']);
        });
    }
}
