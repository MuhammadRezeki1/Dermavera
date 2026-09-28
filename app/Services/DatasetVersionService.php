<?php

namespace App\Services;

use App\Models\Criterion;
use App\Models\DatasetVersion;
use App\Models\ProductVariant;
use App\Models\Rule;
use App\Models\WeightSet;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class DatasetVersionService
{
    public function snapshot(string $version, ?string $notes = null): DatasetVersion
    {
        $snapshot = $this->currentSnapshot();
        $hash = $this->hash($snapshot);

        return DatasetVersion::create(compact('version', 'notes', 'snapshot', 'hash') + ['status' => 'draft']);
    }

    /** @return array<string, mixed> */
    public function currentSnapshot(): array
    {
        return [
            'algorithm_version' => (string) config('dermavera.algorithm_version'),
            'scoring_version' => CriterionScoringService::VERSION,
            'criteria' => Criterion::orderBy('code')->get()->toArray(),
            'products' => ProductVariant::with([
                'brand',
                'skus' => fn ($query) => $query->orderBy('id'),
                'skus.latestPrice.source',
                'skus.latestEligiblePrice.source',
                'formulaVersions' => fn ($query) => $query->orderBy('id'),
                'formulaVersions.source',
                'formulaVersions.formulaIngredients' => fn ($query) => $query->orderBy('ordinal'),
                'formulaVersions.formulaIngredients.ingredient',
            ])->orderBy('catalog_code')->get()->toArray(),
            'rules' => Rule::where('is_active', true)->orderByDesc('priority')->get()->toArray(),
            'weights' => WeightSet::with([
                'weights' => fn ($query) => $query->orderBy('criterion_id'),
                'weights.criterion',
            ])->where('is_active', true)->first()?->toArray(),
        ];
    }

    public function assertCurrent(DatasetVersion $dataset): void
    {
        if (! hash_equals($dataset->hash, $this->hash($dataset->snapshot))) {
            throw new RuntimeException('Snapshot dataset tersimpan tidak cocok dengan hash-nya.');
        }

        if (! hash_equals($dataset->hash, $this->hash($this->currentSnapshot()))) {
            throw new RuntimeException('Dataset aktif sudah tidak sesuai dengan data master. Buat dan aktifkan snapshot baru.');
        }
    }

    public function activate(DatasetVersion $dataset): void
    {
        DB::transaction(function () use ($dataset) {
            $this->assertCurrent($dataset);
            DatasetVersion::where('status', 'active')->whereKeyNot($dataset->id)->get()->each->update(['status' => 'superseded']);
            $dataset->update(['status' => 'active', 'activated_at' => now()]);
        });
    }

    /** @param array<string, mixed> $snapshot */
    private function hash(array $snapshot): string
    {
        return hash('sha256', json_encode($snapshot, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));
    }
}
