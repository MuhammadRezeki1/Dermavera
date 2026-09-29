<?php

namespace App\Services;

use App\Models\FormulaVersion;
use App\Models\PriceObservation;
use App\Models\ProductSku;
use App\Models\ProductVariant;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Illuminate\Support\Str;

class CriterionScoringService
{
    public const VERSION = 'criteria-1.4.0';

    /** @var array<string, array{relevant:list<string>, supportive:list<string>, limited:list<string>}> */
    private const COMPLAINT_INGREDIENT_GROUPS = [
        'berminyak' => [
            'relevant' => ['KAOLIN', 'BENTONITE', 'SALICYLIC ACID'],
            'supportive' => ['CHARCOAL', 'ZINC'],
            'limited' => ['NIACINAMIDE'],
        ],
        'komedo' => [
            'relevant' => ['SALICYLIC ACID'],
            'supportive' => ['TEA TREE', 'O-CYMEN-5-OL'],
            'limited' => ['CHARCOAL', 'ZINC'],
        ],
        'jerawat_ringan' => [
            'relevant' => ['SALICYLIC ACID'],
            'supportive' => ['TEA TREE', 'O-CYMEN-5-OL'],
            'limited' => ['CHARCOAL', 'ZINC'],
        ],
        'kusam' => [
            'relevant' => ['NIACINAMIDE', 'ASCORB', '4-BUTYLRESORCINOL'],
            'supportive' => ['LICORICE', 'TRANEXAMIC'],
            'limited' => ['SALICYLIC ACID'],
        ],
        'kering' => [
            'relevant' => ['GLYCERIN', 'PANTHENOL', 'SODIUM PCA'],
            'supportive' => ['SORBITOL', 'BETA-GLUCAN', 'BETAINE'],
            'limited' => ['ALLANTOIN'],
        ],
    ];

    /** @var array<string, array{relevant:list<string>, supportive:list<string>, limited:list<string>}> */
    private const SKIN_INGREDIENT_GROUPS = [
        'berminyak' => [
            'relevant' => ['KAOLIN', 'BENTONITE', 'SALICYLIC ACID'],
            'supportive' => ['CHARCOAL', 'ZINC'],
            'limited' => ['TEA TREE'],
        ],
        'kombinasi' => [
            'relevant' => ['KAOLIN', 'BENTONITE', 'SALICYLIC ACID'],
            'supportive' => ['CHARCOAL', 'ZINC'],
            'limited' => ['TEA TREE'],
        ],
        'kering' => [
            'relevant' => ['GLYCERIN', 'PANTHENOL', 'SODIUM PCA'],
            'supportive' => ['SORBITOL', 'BETA-GLUCAN', 'BETAINE'],
            'limited' => ['ALLANTOIN'],
        ],
        'normal' => [
            'relevant' => ['GLYCERIN', 'PANTHENOL', 'SODIUM PCA'],
            'supportive' => ['SORBITOL', 'BETA-GLUCAN', 'BETAINE'],
            'limited' => ['ALLANTOIN'],
        ],
    ];

    /** @var array{relevant:list<string>, supportive:list<string>, limited:list<string>} */
    private const FORMULA_INGREDIENT_GROUPS = [
        'relevant' => ['GLYCERIN', 'PANTHENOL', 'ALLANTOIN', 'BETA-GLUCAN', 'SODIUM PCA', 'NIACINAMIDE', 'SALICYLIC ACID'],
        'supportive' => ['SORBITOL', 'BETAINE', 'ASCORB', 'LICORICE', 'TEA TREE'],
        'limited' => ['CHARCOAL', 'ZINC'],
    ];

    private const FRAGRANCE_TERMS = ['FRAGRANCE', 'PARFUM', 'ESSENTIAL OIL'];

    private const EXFOLIANT_TERMS = ['SALICYLIC ACID', 'GLYCOLIC ACID', 'LACTIC ACID', 'GLUCONOLACTONE'];

    /**
     * @param  array{primary_complaint:string,skin_type:string,sensitive?:bool,barrier_impaired?:bool,acne_therapy?:bool,packaging_preference?:string}  $profile
     * @return array{scores:array{C1:int,C2:int,C3:int,C4:string|null,C5:int,C6:int},explanations:list<string>,warnings:list<string>,sku:ProductSku|null,price:PriceObservation|null}
     */
    public function score(ProductVariant $variant, array $profile, ?FormulaVersion $formula = null, ?ProductSku $sku = null): array
    {
        $formula ??= $variant->activeFormula;
        $inci = Str::upper($formula->inci_normalized ?: $formula->inci_raw);
        $formula->loadMissing('formulaIngredients.ingredient');
        $ingredients = $formula->formulaIngredients->pluck('ingredient')->filter();
        $ingredientNames = $ingredients
            ->map(fn ($ingredient) => Str::upper($ingredient->inci_name))
            ->filter()
            ->values();
        $contains = fn (array $terms): bool => $ingredientNames->isNotEmpty()
            ? $ingredientNames->contains(fn (string $name) => Str::contains($name, $terms))
            : Str::contains($inci, $terms);
        $hasFlag = fn (string $property, array $terms): bool => $ingredients->isNotEmpty()
            ? $ingredients->contains(fn ($ingredient) => (bool) $ingredient->{$property})
            : $contains($terms);
        $hasFragrance = $hasFlag('is_fragrance', self::FRAGRANCE_TERMS);
        $hasMenthol = $hasFlag('is_menthol', ['MENTHOL']);
        $hasPhysicalScrub = $hasFlag('is_physical_scrub', ['PUMICE', 'PERLITE', 'HYDRATED SILICA', 'MICROCRYSTALLINE CELLULOSE', 'POLYETHYLENE', 'SYNTHETIC WAX']);
        $hasExfoliant = $hasFlag('is_exfoliant', self::EXFOLIANT_TERMS);
        $complaint = $profile['primary_complaint'];

        $primaryGroups = $this->classifyIngredientGroups($ingredientNames, self::COMPLAINT_INGREDIENT_GROUPS[$complaint] ?? ['relevant' => [], 'supportive' => [], 'limited' => []]);
        $c1 = 3 + $this->groupScore($primaryGroups, 2, 1);

        $secondarySupport = [];
        foreach (array_unique($profile['secondary_concerns'] ?? []) as $secondaryConcern) {
            if ($secondaryConcern === $complaint || ! isset(self::COMPLAINT_INGREDIENT_GROUPS[$secondaryConcern])) {
                continue;
            }

            $secondaryGroups = $this->classifyIngredientGroups($ingredientNames, self::COMPLAINT_INGREDIENT_GROUPS[$secondaryConcern]);
            if ($this->hasPositiveGroup($secondaryGroups)) {
                $c1 += 1;
                $secondarySupport[] = $secondaryConcern;
            }
        }

        $skinGroups = $this->classifyIngredientGroups($ingredientNames, self::SKIN_INGREDIENT_GROUPS[$profile['skin_type']] ?? ['relevant' => [], 'supportive' => [], 'limited' => []]);
        $c2 = 3 + ($this->hasPositiveGroup($skinGroups) ? 1 : 0);

        $sensitiveContext = (bool) ($profile['sensitive'] ?? false);
        $barrierContext = (bool) ($profile['barrier_impaired'] ?? false);
        $therapyContext = (bool) ($profile['acne_therapy'] ?? false);
        if (($sensitiveContext || $barrierContext) && $hasFragrance) {
            $c2--;
        }
        if (($sensitiveContext || $barrierContext) && $hasMenthol) {
            $c2--;
        }
        if (($sensitiveContext || $barrierContext) && $hasPhysicalScrub) {
            $c2--;
        }
        if ($barrierContext && $hasExfoliant) {
            $c2--;
        }

        $formulaGroups = $this->classifyIngredientGroups($ingredientNames, self::FORMULA_INGREDIENT_GROUPS);
        $c3 = 3 + ($formulaGroups['relevant'] ? 1 : 0) + ($formulaGroups['supportive'] ? 1 : 0);
        if (($sensitiveContext || $barrierContext) && $hasMenthol) {
            $c3--;
        }

        $sku ??= $variant->skus->filter(fn ($item) => $item->latestEligiblePrice && $item->size_value > 0)->sortByDesc(fn ($item) => $item->latestEligiblePrice->observed_at)->first();
        $price = $sku?->latestEligiblePrice;
        $unitPrice = $price ? (string) ($price->unit_price_per_100 ?? BigDecimal::of($price->amount)
            ->dividedBy((string) $sku->size_value, 8, RoundingMode::HalfUp)
            ->multipliedBy('100')
            ->toScale(2, RoundingMode::HalfUp)) : null;

        $c5 = $sku->packaging_score ?? 3;
        if ($sku && ($profile['packaging_preference'] ?? '') !== '') {
            $c5 += $sku->package_type === $profile['packaging_preference'] ? 1 : -1;
        }
        $c6 = 2;
        if ($formula->bpom_number) {
            $c6++;
        }
        if ($formula->source_id && $formula->verified_at) {
            $c6++;
        }
        if (in_array($formula->verification_status, ['VERIFIED_OFFICIAL_INCI', 'VALIDATED_ID_FULL_INCI'], true)) {
            $c6++;
        }
        if (($sensitiveContext || $barrierContext) && ($hasMenthol || $hasFragrance || $hasPhysicalScrub)) {
            $c6--;
        }
        if (($barrierContext || $therapyContext) && $hasExfoliant) {
            $c6--;
        }

        return [
            'scores' => ['C1' => max(1, min(5, $c1)), 'C2' => max(1, min(5, $c2)), 'C3' => max(1, min(5, $c3)), 'C4' => $unitPrice, 'C5' => max(1, min(5, $c5)), 'C6' => max(1, min(5, $c6))],
            'explanations' => $this->explanations($complaint, $inci, $profile, $secondarySupport),
            'warnings' => $this->warnings($inci, $profile),
            'sku' => $sku,
            'price' => $price,
        ];
    }

    /**
     * @param  array{relevant:list<string>,supportive:list<string>,limited:list<string>}  $groups
     * @return array{relevant:bool,supportive:bool,limited:bool}
     */
    private function classifyIngredientGroups($ingredientNames, array $groups): array
    {
        $contains = fn (array $terms): bool => $ingredientNames->contains(fn (string $name) => Str::contains($name, $terms));

        return [
            'relevant' => $contains($groups['relevant']),
            'supportive' => $contains($groups['supportive']),
            'limited' => $contains($groups['limited']),
        ];
    }

    /** @param array{relevant:bool,supportive:bool,limited:bool} $groups */
    private function groupScore(array $groups, int $relevantPoints, int $supportivePoints): int
    {
        return $groups['relevant'] ? $relevantPoints : ($groups['supportive'] ? $supportivePoints : 0);
    }

    /** @param array{relevant:bool,supportive:bool,limited:bool} $groups */
    private function hasPositiveGroup(array $groups): bool
    {
        return $groups['relevant'] || $groups['supportive'];
    }

    /**
     * @param  array<string, mixed>  $profile
     * @return list<string>
     */
    private function explanations(string $complaint, string $inci, array $profile, array $secondarySupport = []): array
    {
        $codes = ['formula_verified', 'rinse_off_scoring_conservative'];
        if ($complaint === 'kusam' && Str::contains($inci, ['NIACINAMIDE', 'ASCORB'])) {
            $codes[] = 'brightening_supportive_ingredient';
        }
        if (in_array($complaint, ['jerawat_ringan', 'komedo'], true) && Str::contains($inci, ['SALICYLIC ACID', 'TEA TREE', 'O-CYMEN-5-OL'])) {
            $codes[] = 'contains_supportive_acne_ingredient';
        }
        if (Str::contains($inci, ['GLYCERIN', 'SORBITOL', 'PANTHENOL'])) {
            $codes[] = 'contains_humectant_support';
        }
        if ($secondarySupport !== []) {
            $codes[] = 'secondary_concern_support';
        }

        return $codes;
    }

    /**
     * @param  array<string, mixed>  $profile
     * @return list<string>
     */
    private function warnings(string $inci, array $profile): array
    {
        $warnings = [];
        if (Str::contains($inci, ['FRAGRANCE', 'PARFUM'])) {
            $warnings[] = 'contains_fragrance';
        }
        if (Str::contains($inci, ['MENTHOL'])) {
            $warnings[] = 'contains_menthol';
        }
        if (($profile['sensitive'] ?? false) && $warnings !== []) {
            $warnings[] = 'sensitive_skin_use_caution';
        }
        if ((($profile['skin_type'] ?? null) === 'kering' || ($profile['primary_complaint'] ?? null) === 'kering' || ($profile['barrier_impaired'] ?? false)) && $warnings !== []) {
            $warnings[] = 'dry_skin_irritant_caution';
        }

        return $warnings;
    }
}
