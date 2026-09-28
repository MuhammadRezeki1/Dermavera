<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\ValidationException;

/**
 * @property int $id
 * @property string $catalog_code
 * @property string $name
 * @property string $slug
 * @property string $status
 * @property string $evidence_status
 * @property bool $is_active
 * @property Brand $brand
 * @property EloquentCollection<int, ProductSku> $skus
 * @property EloquentCollection<int, FormulaVersion> $formulaVersions
 * @property FormulaVersion|null $activeFormula
 */
class ProductVariant extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['rinse_off' => 'boolean', 'is_active' => 'boolean'];
    }

    /** @return BelongsTo<Brand, $this> */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /** @return HasMany<ProductSku, $this> */
    public function skus(): HasMany
    {
        return $this->hasMany(ProductSku::class);
    }

    /** @return HasMany<FormulaVersion, $this> */
    public function formulaVersions(): HasMany
    {
        return $this->hasMany(FormulaVersion::class);
    }

    /** @return HasOne<FormulaVersion, $this> */
    public function activeFormula(): HasOne
    {
        return $this->hasOne(FormulaVersion::class)->where('status', 'active')->latestOfMany();
    }

    /** @return HasMany<ProductEvidence, $this> */
    public function evidence(): HasMany
    {
        return $this->hasMany(ProductEvidence::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function booted(): void
    {
        static::saving(function (ProductVariant $variant) {
            if ($variant->is_active && ($variant->status !== 'verified' || ! in_array($variant->evidence_status, ['VERIFIED_OFFICIAL_INCI', 'VALIDATED_ID_FULL_INCI', 'VALIDATED_ID_VERSIONED'], true))) {
                throw ValidationException::withMessages(['is_active' => 'Produk publik wajib berstatus verified dengan bukti INCI yang diterima.']);
            }
            if ($variant->is_active && (! $variant->exists || ! $variant->formulaVersions()->where('status', 'active')->exists())) {
                throw ValidationException::withMessages(['is_active' => 'Produk publik wajib memiliki formula aktif.']);
            }
        });
    }
}
