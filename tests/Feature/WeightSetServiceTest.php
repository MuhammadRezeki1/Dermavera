<?php

namespace Tests\Feature;

use App\Models\Criterion;
use App\Models\WeightSet;
use App\Services\WeightSetService;
use Brick\Math\BigDecimal;
use Database\Seeders\CriterionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class WeightSetServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_activation_normalizes_raw_weights_and_requires_c1_through_c6(): void
    {
        $this->seed(CriterionSeeder::class);
        $set = WeightSet::create(['name' => 'Uji bobot', 'version' => 'test-1', 'source' => 'Pengujian', 'validated_at' => now(), 'is_active' => false]);

        foreach (['C1' => 5, 'C2' => 5, 'C3' => 5, 'C4' => 3, 'C5' => 3, 'C6' => 5] as $code => $raw) {
            $set->weights()->create(['criterion_id' => Criterion::where('code', $code)->value('id'), 'raw_value' => $raw, 'normalized_value' => 1]);
        }

        app(WeightSetService::class)->activate($set);

        $sum = $set->fresh('weights')->weights->reduce(
            fn (BigDecimal $carry, $weight) => $carry->plus((string) $weight->normalized_value),
            BigDecimal::zero(),
        );
        $this->assertSame('1.0000000000', (string) $sum->toScale(10));
        $this->assertSame('0.1923076923', (string) $set->fresh('weights.criterion')->weights->firstWhere('criterion.code', 'C1')->normalized_value);
        $this->assertTrue($set->fresh()->is_active);
    }

    public function test_incomplete_weight_set_cannot_be_activated(): void
    {
        $this->seed(CriterionSeeder::class);
        $set = WeightSet::create(['name' => 'Tidak lengkap', 'version' => 'test-2', 'source' => 'Pengujian', 'validated_at' => now(), 'is_active' => false]);
        $set->weights()->create(['criterion_id' => Criterion::where('code', 'C1')->value('id'), 'raw_value' => 5, 'normalized_value' => 1]);

        $this->expectException(ValidationException::class);
        app(WeightSetService::class)->activate($set);
    }
}
