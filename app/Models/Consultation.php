<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property string $uuid
 * @property int|null $user_id
 * @property string $algorithm_version
 * @property string $status
 * @property DatasetVersion|null $datasetVersion
 * @property SafetyAssessment $safetyAssessment
 * @property Collection<int, RecommendationRun> $runs
 */
class Consultation extends Model
{
    use MassPrunable;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['input_snapshot' => 'array', 'consent_at' => 'datetime', 'expires_at' => 'datetime'];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<DatasetVersion, $this> */
    public function datasetVersion(): BelongsTo
    {
        return $this->belongsTo(DatasetVersion::class);
    }

    /** @return HasOne<SafetyAssessment, $this> */
    public function safetyAssessment(): HasOne
    {
        return $this->hasOne(SafetyAssessment::class);
    }

    /** @return HasMany<RecommendationRun, $this> */
    public function runs(): HasMany
    {
        return $this->hasMany(RecommendationRun::class);
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /** @return Builder<Consultation> */
    public function prunable(): Builder
    {
        return static::whereNull('user_id')->whereNotNull('expires_at')->where('expires_at', '<=', now());
    }
}
