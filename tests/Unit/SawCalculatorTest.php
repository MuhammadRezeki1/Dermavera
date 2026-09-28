<?php

namespace Tests\Unit;

use App\Services\SawCalculator;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class SawCalculatorTest extends TestCase
{
    private array $weights = ['C1' => '0.1923076923', 'C2' => '0.1923076923', 'C3' => '0.1923076923', 'C4' => '0.1153846154', 'C5' => '0.1153846154', 'C6' => '0.1923076923'];

    private array $types = ['C1' => 'benefit', 'C2' => 'benefit', 'C3' => 'benefit', 'C4' => 'cost', 'C5' => 'benefit', 'C6' => 'benefit'];

    #[Test]
    public function it_reproduces_the_documented_dummy_calculation(): void
    {
        $matrix = ['A' => ['C1' => 5, 'C2' => 4, 'C3' => 4, 'C4' => 45000, 'C5' => 4, 'C6' => 5], 'B' => ['C1' => 4, 'C2' => 5, 'C3' => 5, 'C4' => 60000, 'C5' => 3, 'C6' => 5], 'C' => ['C1' => 3, 'C2' => 3, 'C3' => 4, 'C4' => 35000, 'C5' => 5, 'C6' => 4]];
        $result = (new SawCalculator)->calculate($matrix, $this->weights, $this->types);
        $this->assertSame(['A', 'B', 'C'], $result->ranking);
        $this->assertEqualsWithDelta(.874359, (float) $result->scores['A'], 1e-6);
        $this->assertEqualsWithDelta(.867308, (float) $result->scores['B'], 1e-6);
        $this->assertEqualsWithDelta(.769231, (float) $result->scores['C'], 1e-6);
        $this->assertEqualsWithDelta(.7777778, (float) $result->normalized['A']['C4'], 1e-6);
    }

    #[Test]
    public function it_applies_the_documented_tie_break_order(): void
    {
        $matrix = ['Zulu' => ['C1' => 4, 'C2' => 4, 'C3' => 4, 'C4' => 50000, 'C5' => 4, 'C6' => 5], 'Alpha' => ['C1' => 5, 'C2' => 4, 'C3' => 4, 'C4' => 50000, 'C5' => 4, 'C6' => 5]];
        $result = (new SawCalculator)->calculate($matrix, $this->weights, $this->types);
        $this->assertSame('Alpha', $result->ranking[0]);
    }

    #[Test]
    public function cost_values_must_be_positive(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new SawCalculator)->calculate(['A' => ['C1' => 1, 'C2' => 1, 'C3' => 1, 'C4' => 0, 'C5' => 1, 'C6' => 1]], $this->weights, $this->types);
    }

    #[Test]
    public function it_rejects_unknown_or_missing_criterion_types(): void
    {
        $types = $this->types;
        $types['C4'] = 'costt';

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Tipe C4 harus benefit atau cost.');
        (new SawCalculator)->calculate($this->singleAlternative(), $this->weights, $types);
    }

    #[Test]
    public function it_rejects_non_positive_weights_even_when_the_total_is_one(): void
    {
        $weights = $this->weights;
        $weights['C1'] = '-0.1000000000';
        $weights['C2'] = '0.4846153846';

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Bobot C1 harus lebih dari nol.');
        (new SawCalculator)->calculate($this->singleAlternative(), $weights, $this->types);
    }

    #[Test]
    public function it_rejects_rows_with_missing_or_extra_criteria(): void
    {
        $matrix = $this->singleAlternative();
        unset($matrix['A']['C6']);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('harus sama persis');
        (new SawCalculator)->calculate($matrix, $this->weights, $this->types);
    }

    #[Test]
    public function equal_alternatives_are_ordered_naturally(): void
    {
        $row = $this->singleAlternative()['A'];
        $result = (new SawCalculator)->calculate(['Produk 10' => $row, 'Produk 2' => $row, 'Produk 1' => $row], $this->weights, $this->types);

        $this->assertSame(['Produk 1', 'Produk 2', 'Produk 10'], $result->ranking);
        $this->assertSame('1.0000000000', $result->scores['Produk 1']);
    }

    /** @return array<string, array<string, int>> */
    private function singleAlternative(): array
    {
        return ['A' => ['C1' => 5, 'C2' => 5, 'C3' => 5, 'C4' => 50000, 'C5' => 5, 'C6' => 5]];
    }
}
