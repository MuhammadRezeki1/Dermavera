<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property numeric-string $amount
 * @property numeric-string|null $unit_price_per_100
 * @property Carbon $observed_at
 * @property bool $is_available
 * @property bool $ranking_eligible
 */
class PriceObservation extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2', 'normal_price' => 'decimal:2', 'unit_price_per_100' => 'decimal:2', 'observed_at' => 'datetime', 'promo' => 'boolean', 'is_available' => 'boolean', 'ranking_eligible' => 'boolean'];
    }

    /** @return BelongsTo<ProductSku, $this> */
    public function sku(): BelongsTo
    {
        return $this->belongsTo(ProductSku::class, 'product_sku_id');
    }

    /** @return BelongsTo<Source, $this> */
    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class);
    }
}
