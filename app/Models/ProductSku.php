<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property int $product_variant_id
 * @property numeric-string $size_value
 * @property string $size_unit
 * @property int $packaging_score
 * @property string $package_type
 * @property PriceObservation|null $latestPrice
 * @property PriceObservation|null $latestEligiblePrice
 */
class ProductSku extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['size_value' => 'decimal:2', 'is_active' => 'boolean'];
    }

    /** @return BelongsTo<ProductVariant, $this> */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    /** @return HasMany<PriceObservation, $this> */
    public function prices(): HasMany
    {
        return $this->hasMany(PriceObservation::class);
    }

    /** @return HasOne<PriceObservation, $this> */
    public function latestPrice(): HasOne
    {
        return $this->hasOne(PriceObservation::class)->latestOfMany('observed_at');
    }

    /** @return HasOne<PriceObservation, $this> */
    public function latestEligiblePrice(): HasOne
    {
        return $this->hasOne(PriceObservation::class)
            ->where('is_available', true)
            ->where('ranking_eligible', true)
            ->latestOfMany('observed_at');
    }
}
