<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** @property int $id @property string $status @property array<string,mixed>|null $calculation_snapshot @property EloquentCollection<int, RecommendationResult> $results */
class RecommendationRun extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['calculation_snapshot' => 'array', 'started_at' => 'datetime', 'completed_at' => 'datetime'];
    }

    /** @return HasMany<RecommendationResult, $this> */
    public function results(): HasMany
    {
        return $this->hasMany(RecommendationResult::class);
    }
}
