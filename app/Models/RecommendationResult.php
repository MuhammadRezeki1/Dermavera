<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int|null $rank
 * @property array<string, int|float|string|null> $raw_scores
 * @property array<string, string> $normalized_scores
 * @property array<string, string> $contributions
 * @property numeric-string|null $final_score
 * @property string $eligibility_status
 * @property list<string> $explanation_codes
 * @property list<string> $warnings
 * @property ProductVariant $variant
 */
class RecommendationResult extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['raw_scores' => 'array', 'normalized_scores' => 'array', 'contributions' => 'array', 'explanation_codes' => 'array', 'warnings' => 'array', 'final_score' => 'decimal:10'];
    }

    /** @return BelongsTo<ProductVariant, $this> */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    /** @return BelongsTo<ProductSku, $this> */
    public function sku(): BelongsTo
    {
        return $this->belongsTo(ProductSku::class, 'product_sku_id');
    }

    /** @return BelongsTo<FormulaVersion, $this> */
    public function formula(): BelongsTo
    {
        return $this->belongsTo(FormulaVersion::class, 'formula_version_id');
    }

    /** @return BelongsTo<PriceObservation, $this> */
    public function price(): BelongsTo
    {
        return $this->belongsTo(PriceObservation::class, 'price_observation_id');
    }
}
