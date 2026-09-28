<?php

namespace Tests\Feature;

use App\Models\FormulaVersion;
use App\Models\PriceObservation;
use App\Models\ProductVariant;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogDatasetTest extends TestCase
{
    use RefreshDatabase;

    public function test_documented_catalog_is_seeded_without_missing_records(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->assertDatabaseCount('brands', 5);
        $this->assertDatabaseCount('product_variants', 37);
        $this->assertDatabaseCount('product_skus', 40);
        $this->assertDatabaseCount('formula_versions', 40);
        $this->assertDatabaseCount('price_observations', 37);
        $this->assertSame(35, ProductVariant::where('is_active', true)->count());
        $this->assertFalse(ProductVariant::where('catalog_code', 'B03')->firstOrFail()->is_active);
        $this->assertFalse(ProductVariant::where('catalog_code', 'M01')->firstOrFail()->is_active);
        $this->assertTrue(ProductVariant::where('catalog_code', 'B03N')->firstOrFail()->is_active);
        $this->assertSame(3, ProductVariant::whereIn('catalog_code', ['M02', 'M03', 'M04'])->where('is_active', true)->count());
        $this->assertSame('NA18241208586', ProductVariant::where('catalog_code', 'M02')->firstOrFail()->skus()->value('bpom_no'));
        $this->assertSame('55000.00', PriceObservation::whereHas('sku.variant', fn ($query) => $query->where('catalog_code', 'N03'))->value('unit_price_per_100'));
        $this->assertNotSame(ProductVariant::where('catalog_code', 'B03')->value('id'), ProductVariant::where('catalog_code', 'B03N')->value('id'));
        $this->assertSame(2, ProductVariant::where('catalog_code', 'K01')->first()->formulaVersions()->count());
        $inci = strtoupper(FormulaVersion::whereHas('variant', fn ($q) => $q->where('catalog_code', 'B03N'))->value('inci_normalized'));
        $this->assertStringContainsString('ASCORBIC ACID', $inci);
        $this->assertStringNotContainsString('ASCORBYL GLUCOSIDE', $inci);
    }
}
