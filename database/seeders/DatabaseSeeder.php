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
        $dataset = DatasetVersion::where('version', '1.4.5')->first() ?: $service->snapshot('1.4.5', 'Dataset 37 record produk dengan observasi harga retailer/resmi terbaru 28 September 2026, tie group, penalti iritan kulit kering, dan status stok N08.');
        if ($dataset->status !== 'active') {
            $service->activate($dataset);
        }
    }
}
