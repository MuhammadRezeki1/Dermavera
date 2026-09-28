<?php

namespace App\Services;

use App\Models\WeightSet;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WeightSetService
{
    private const CRITERIA = ['C1', 'C2', 'C3', 'C4', 'C5', 'C6'];

    public function normalize(WeightSet $set): void
    {
        DB::transaction(function () use ($set) {
            $weights = $set->weights()->with('criterion')->get()->sortBy('criterion.code')->values();
            if ($weights->isEmpty()) {
                throw ValidationException::withMessages(['weights' => 'Versi bobot belum memiliki nilai kriteria.']);
            }

            $total = BigDecimal::zero();
            foreach ($weights as $weight) {
                $raw = BigDecimal::of((string) $weight->raw_value);
                if ($raw->isLessThanOrEqualTo(0)) {
                    throw ValidationException::withMessages(['weights' => "Bobot mentah {$weight->criterion->code} harus lebih dari nol."]);
                }
                $total = $total->plus($raw);
            }

            $allocated = BigDecimal::zero();
            foreach ($weights as $index => $weight) {
                $normalized = $index === $weights->count() - 1
                    ? BigDecimal::one()->minus($allocated)
                    : BigDecimal::of((string) $weight->raw_value)->dividedBy($total, 10, RoundingMode::HalfUp);
                $normalized = $normalized->toScale(10, RoundingMode::HalfUp);
                $allocated = $allocated->plus($normalized);
                $weight->update(['normalized_value' => (string) $normalized]);
            }
        });
    }

    public function activate(WeightSet $set): void
    {
        DB::transaction(function () use ($set) {
            $this->normalize($set);
            $set->load('weights.criterion');
            $codes = $set->weights->pluck('criterion.code')->sort()->values()->all();
            $sum = $set->weights->reduce(fn (BigDecimal $carry, $weight) => $carry->plus((string) $weight->normalized_value), BigDecimal::zero());
            if ($codes !== self::CRITERIA || $sum->minus('1')->abs()->isGreaterThan('0.0001')) {
                throw ValidationException::withMessages(['weights' => 'Versi bobot wajib memiliki tepat C1–C6 dan total 1 ± 0,0001.']);
            }
            WeightSet::where('is_active', true)->whereKeyNot($set->id)->update(['is_active' => false]);
            $set->update(['is_active' => true]);
        });
    }
}
