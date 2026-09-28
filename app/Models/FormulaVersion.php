<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

/**
 * @property int $id
 * @property int $product_variant_id
 * @property int|null $product_sku_id
 * @property int|null $source_id
 * @property string $inci_raw
 * @property string|null $inci_normalized
 * @property string $verification_status
 * @property string $status
 * @property string|null $bpom_number
 * @property Carbon|null $verified_at
 * @property ProductSku|null $sku
 */
class FormulaVersion extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['verified_at' => 'datetime', 'ph_min' => 'decimal:2', 'ph_max' => 'decimal:2'];
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

    /** @return BelongsTo<Source, $this> */
    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class);
    }

    /** @return HasMany<FormulaIngredient, $this> */
    public function formulaIngredients(): HasMany
    {
        return $this->hasMany(FormulaIngredient::class);
    }

    protected static function booted(): void
    {
        static::saving(function (FormulaVersion $formula) {
            if ($formula->status === 'active' && (! $formula->product_sku_id || ! $formula->source_id || ! $formula->verified_at || ! $formula->bpom_number || trim((string) $formula->inci_raw) === '')) {
                throw ValidationException::withMessages(['status' => 'Formula aktif wajib memiliki INCI, sumber, waktu verifikasi, dan nomor BPOM.']);
            }
        });
    }
}
