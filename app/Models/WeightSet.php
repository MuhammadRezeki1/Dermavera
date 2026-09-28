<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** @property int $id @property bool $is_active @property EloquentCollection<int, CriterionWeight> $weights */
class WeightSet extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['validated_at' => 'datetime', 'is_active' => 'boolean'];
    }

    /** @return HasMany<CriterionWeight, $this> */
    public function weights(): HasMany
    {
        return $this->hasMany(CriterionWeight::class);
    }
}
