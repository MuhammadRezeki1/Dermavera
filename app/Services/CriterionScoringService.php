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
    public const VERSION = 'criteria-1.3.0';

    /** @var array<string, list<string>> */
    private const COMPLAINT_SUPPORT_TERMS = [
        'berminyak' => ['KAOLIN', 'BENTONITE', 'CHARCOAL', 'ZINC'],
        'komedo' => ['SALICYLIC ACID', 'TEA TREE', 'O-CYMEN-5-OL'],
        'jerawat_ringan' => ['SALICYLIC ACID', 'TEA TREE', 'O-CYMEN-5-OL'],
        'kusam' => ['NIACINAMIDE', 'ASCORB', '4-BUTYLRESORCINOL', 'LICORICE', 'TRANEXAMIC'],
        'kering' => ['GLYCERIN', 'SORBITOL', 'PANTHENOL', 'BETA-GLUCAN', 'SODIUM PCA'],
    ];

    private const DRY_SKIN_IRRITANT_TERMS = ['FRAGRANCE', 'PARFUM', 'ESSENTIAL OIL', 'MENTHOL'];

    /**
     * @param  array{primary_complaint:string,skin_type:string,sensitive?:bool,barrier_impaired?:bool,acne_therapy?:bool,packaging_preference?:string}  $profile
     * @return array{scores:array{C1:int,C2:int,C3:int,C4:string|null,C5:int,C6:int},explanations:list<string>,warnings:list<string>,sku:ProductSku|null,price:PriceObservation|null}
     */
    public function score(ProductVariant $variant, array $profile, ?FormulaVersion $formula = null, ?ProductSku $sku = null): array
    {
        $formula ??= $variant->activeFormula;
        $inci = Str::upper($formula->inci_normalized ?: $formula->inci_raw);
        $formula->loadMissing('formulaIngredients.ingredient');
        $ingredientNames = $formula->formulaIngredients
            ->map(fn ($item) => Str::upper($item->ingredient->inci_name))
            ->filter()
            ->values();
        $contains = fn (array $terms): bool => $ingredientNames->isNotEmpty()
            ? $ingredientNames->contains(fn (string $name) => Str::contains($name, $terms))
            : Str::contains($inci, $terms);
        $complaint = $profile['primary_complaint'];

        $c1 = 3;
        if ($complaint === 'berminyak' && $contains(['KAOLIN', 'BENTONITE', 'CHARCOAL', 'ZINC'])) {
            $c1++;
        }
        if (in_array($complaint, ['jerawat_ringan', 'komedo'], true) && $contains(['SALICYLIC ACID', 'TEA TREE', 'O-CYMEN-5-OL'])) {
            $c1 += 2;
        }
        if ($complaint === 'kusam' && $contains(['NIACINAMIDE', 'ASCORB', '4-BUTYLRESORCINOL', 'LICORICE', 'TRANEXAMIC'])) {
            $c1 += 2;
        }
        if ($complaint === 'kering' && $contains(['GLYCERIN', 'SORBITOL', 'PANTHENOL', 'BETA-GLUCAN', 'SODIUM PCA'])) {
            $c1 += 1;
        }

        $secondarySupport = [];
        foreach (array_unique($profile['secondary_concerns'] ?? []) as $secondaryConcern) {
            if ($secondaryConcern === $complaint || ! isset(self::COMPLAINT_SUPPORT_TERMS[$secondaryConcern])) {
                continue;
            }

            if ($contains(self::COMPLAINT_SUPPORT_TERMS[$secondaryConcern])) {
                $c1 += 1;
                $secondarySupport[] = $secondaryConcern;
            }
        }

        $c2 = 3;
        if (in_array($profile['skin_type'], ['berminyak', 'kombinasi'], true) && $contains(['KAOLIN', 'BENTONITE', 'CHARCOAL', 'SALICYLIC ACID'])) {
            $c2++;
        }
        if (in_array($profile['skin_type'], ['kering', 'normal'], true) && $contains(['GLYCERIN', 'SORBITOL', 'PANTHENOL', 'BETAINE', 'SODIUM PCA'])) {
            $c2++;
        }
        $dryContext = ($profile['skin_type'] ?? null) === 'kering'
            || $complaint === 'kering'
            || ($profile['barrier_impaired'] ?? false)
            || ($profile['sensitive'] ?? false);
        if ($dryContext && $contains(self::DRY_SKIN_IRRITANT_TERMS)) {
            $c2--;
        }
        if (($profile['sensitive'] ?? false) && $contains(['MENTHOL', 'FRAGRANCE', 'PARFUM', 'ESSENTIAL OIL'])) {
            $c2 -= 2;
        }

        $c3 = 3;
        if ($contains(['GLYCERIN', 'SORBITOL', 'PANTHENOL', 'ALLANTOIN', 'BETA-GLUCAN'])) {
            $c3++;
        }
        if ($contains(['NIACINAMIDE', 'SALICYLIC ACID', 'ASCORB', 'LICORICE', 'TEA TREE'])) {
            $c3++;
        }
        if ($contains(['MENTHOL']) && ($profile['sensitive'] ?? false)) {
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
        if (($profile['sensitive'] ?? false) && $contains(['MENTHOL', 'FRAGRANCE', 'PARFUM'])) {
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
