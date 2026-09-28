<?php

namespace Database\Seeders;

use App\Models\Criterion;
use App\Models\WeightSet;
use App\Services\WeightSetService;
use Illuminate\Database\Seeder;

class ValidatedWeightSeeder extends Seeder
{
    public function run(): void
    {
        WeightSet::query()->update(['is_active' => false]);
        $set = WeightSet::updateOrCreate(['version' => '1.0.0'], ['name' => 'Bobot satu validator ahli', 'source' => 'Satu dokter Sp.D.V.E./Sp.KK; nilai 5,5,5,3,3,5', 'validated_at' => '2026-09-24 00:00:00+07:00', 'is_active' => false]);
        foreach (['C1' => 5, 'C2' => 5, 'C3' => 5, 'C4' => 3, 'C5' => 3, 'C6' => 5] as $code => $raw) {
            \DB::table('criterion_weights')->updateOrInsert(['weight_set_id' => $set->id, 'criterion_id' => Criterion::where('code', $code)->value('id')], ['raw_value' => $raw, 'normalized_value' => number_format($raw / 26, 10, '.', ''), 'created_at' => now(), 'updated_at' => now()]);
        }
        app(WeightSetService::class)->activate($set);
    }
}
