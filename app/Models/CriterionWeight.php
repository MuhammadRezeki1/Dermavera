<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** @property numeric-string $normalized_value @property Criterion $criterion */
class CriterionWeight extends Model
{
    protected $guarded = [];

    /** @return BelongsTo<Criterion, $this> */
    public function criterion(): BelongsTo
    {
        return $this->belongsTo(Criterion::class);
    }

    /** @return BelongsTo<WeightSet, $this> */
    public function weightSet(): BelongsTo
    {
        return $this->belongsTo(WeightSet::class);
    }
}
