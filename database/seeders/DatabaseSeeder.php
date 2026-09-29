<?php

namespace Database\Seeders;

use App\Models\DatasetVersion;
use App\Services\DatasetVersionService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([CriterionSeeder::class, ValidatedWeightSeeder::class, SafetyRuleSeeder::class, ProductCatalogSeeder::class, PriceObservationSeeder::class, DemoUserSeeder::class]);
        $service = app(DatasetVersionService::class);
        $dataset = DatasetVersion::where('version', '1.4.7')->first() ?: $service->snapshot('1.4.7', 'Dataset dengan pemetaan kelompok ingredient relevan/pendukung/bukti terbatas, penalti kontekstual, dan peringkat setara untuk skor SAW identik.');
        if ($dataset->status !== 'active') {
            $service->activate($dataset);
        }
    }
}
