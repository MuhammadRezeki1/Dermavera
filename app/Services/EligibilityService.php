<?php

namespace App\Services;

use App\Models\FormulaVersion;
use App\Models\ProductSku;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class EligibilityService
{
    /**
     * @param  Collection<int, ProductVariant>  $variants
     * @param  array{allergies?:list<string>,sensitive?:bool,barrier_impaired?:bool,acne_therapy?:bool}  $profile
     * @return array{eligible:Collection<int, array{key:string,variant:ProductVariant,formula:FormulaVersion,sku:ProductSku|null}>,excluded:array<string, array{key:string,variant:ProductVariant,formula:FormulaVersion|null,sku:ProductSku|null,reasons:list<string>}>}
     */
    public function filter(Collection $variants, array $profile): array
    {
        $eligible = collect();
        $excluded = [];
        foreach ($variants as $variant) {
            $formulas = $variant->formulaVersions->where('status', 'active');
            if ($formulas->isEmpty()) {
                $formulas = collect([null]);
            }
            foreach ($formulas as $formula) {
                $sku = $formula?->sku;
                $key = $variant->catalog_code.($formulas->count() > 1 && $sku ? '-'.(int) $sku->size_value.Str::upper($sku->size_unit) : '');
                $candidate = ['key' => $key, 'variant' => $variant, 'formula' => $formula, 'sku' => $sku];
                if (! $formula || trim($formula->inci_raw) === '' || ! in_array($formula->verification_status, ['VERIFIED_OFFICIAL_INCI', 'VALIDATED_ID_FULL_INCI', 'VALIDATED_ID_VERSIONED'], true)) {
                    $excluded[$key] = $candidate + ['reasons' => ['HC-05', 'insufficient_formula_evidence']];

                    continue;
                }
                if (! $formula->bpom_number) {
                    $excluded[$key] = $candidate + ['reasons' => ['HC-06', 'bpom_not_verified']];

                    continue;
                }
                $inci = Str::upper($formula->inci_normalized ?: $formula->inci_raw);
                $formula->loadMissing('formulaIngredients.ingredient');
                $ingredients = $formula->formulaIngredients->pluck('ingredient')->filter();
                $ingredientNames = $ingredients->map(fn ($ingredient) => Str::upper($ingredient->inci_name))->values();
                $contains = fn (string $term): bool => $ingredientNames->isNotEmpty()
                    ? $ingredientNames->contains(fn (string $name) => Str::contains($name, $term))
                    : str_contains($inci, $term);
                $allergies = array_filter(array_map(fn ($value) => Str::upper(trim($value)), $profile['allergies'] ?? []));
                $matched = array_values(array_filter($allergies, fn ($allergy) => $contains($allergy)));
                if ($matched !== []) {
                    $excluded[$key] = $candidate + ['reasons' => ['HC-01', 'allergen_match', ...$matched]];

                    continue;
                }
                $hasScrub = $ingredients->contains(fn ($ingredient) => $ingredient->is_physical_scrub)
                    || collect(['PUMICE', 'PERLITE', 'HYDRATED SILICA', 'MICROCRYSTALLINE CELLULOSE', 'CORN STARCH', 'POLYETHYLENE', 'SYNTHETIC WAX'])->contains(fn ($term) => $contains($term));
                $isCompromised = ($profile['sensitive'] ?? false) || ($profile['barrier_impaired'] ?? false);
                if ($isCompromised && $hasScrub) {
                    $excluded[$key] = $candidate + ['reasons' => ['HC-02', 'physical_scrub_conflict']];

                    continue;
                }
                $irritantCount = collect(['MENTHOL', 'PARFUM', 'FRAGRANCE', 'SALICYLIC ACID', 'GLYCOLIC ACID', 'LACTIC ACID', 'TEA TREE'])->filter(fn ($term) => $contains($term))->count();
                if ($isCompromised && $irritantCount >= 3) {
                    $excluded[$key] = $candidate + ['reasons' => ['HC-03', 'multiple_irritant_context']];

                    continue;
                }
                if (($profile['acne_therapy'] ?? false) && $irritantCount >= 2) {
                    $excluded[$key] = $candidate + ['reasons' => ['HC-04', 'therapy_irritation_review']];

                    continue;
                }
                $eligible->push($candidate);
            }
        }

        return ['eligible' => $eligible, 'excluded' => $excluded];
    }
}
