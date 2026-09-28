<?php

namespace App\Services;

use App\Domain\Recommendation\Contracts\SawCalculatorContract;
use App\Domain\Recommendation\DTO\SawResult;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use InvalidArgumentException;

class SawCalculator implements SawCalculatorContract
{
    private const SCALE = 10;

    private const TIE_TOLERANCE = '0.0001';

    private const ALLOWED_TYPES = ['benefit', 'cost'];

    /**
     * @param  array<string, array<string, int|float|string>>  $matrix
     * @param  array<string, int|float|string>  $weights
     * @param  array<string, string>  $types
     */
    public function calculate(array $matrix, array $weights, array $types): SawResult
    {
        if ($matrix === []) {
            throw new InvalidArgumentException('Matriks keputusan tidak boleh kosong.');
        }

        if ($weights === []) {
            throw new InvalidArgumentException('Bobot kriteria tidak boleh kosong.');
        }

        $criteria = array_keys($weights);

        $typeCriteria = array_keys($types);
        sort($criteria);
        sort($typeCriteria);
        if ($criteria !== $typeCriteria) {
            throw new InvalidArgumentException('Daftar tipe harus sama persis dengan daftar kriteria berbobot.');
        }

        $decimalWeights = [];
        $sum = BigDecimal::zero();
        foreach ($weights as $criterion => $weight) {
            if (! in_array($types[$criterion], self::ALLOWED_TYPES, true)) {
                throw new InvalidArgumentException("Tipe {$criterion} harus benefit atau cost.");
            }

            $decimalWeights[$criterion] = $this->decimal($weight, "Bobot {$criterion}");
            if ($decimalWeights[$criterion]->isLessThanOrEqualTo(0)) {
                throw new InvalidArgumentException("Bobot {$criterion} harus lebih dari nol.");
            }

            $sum = $sum->plus($decimalWeights[$criterion]);
        }

        if ($sum->minus('1')->abs()->isGreaterThan('0.0001')) {
            throw new InvalidArgumentException('Total bobot harus bernilai 1.');
        }

        $extrema = [];
        foreach ($criteria as $criterion) {
            $values = [];
            foreach ($matrix as $key => $row) {
                $rowCriteria = array_keys($row);
                sort($rowCriteria);
                if ($rowCriteria !== $criteria) {
                    throw new InvalidArgumentException("Kriteria alternatif {$key} harus sama persis dengan kriteria berbobot.");
                }

                $values[] = $this->decimal($row[$criterion], "Nilai {$criterion} pada alternatif {$key}");
            }

            foreach ($values as $value) {
                if ($value->isLessThanOrEqualTo(0)) {
                    throw new InvalidArgumentException("Nilai {$criterion} harus lebih dari nol.");
                }
            }
            $extrema[$criterion] = $types[$criterion] === 'cost'
                ? array_reduce($values, fn ($a, $b) => $a === null || $b->isLessThan($a) ? $b : $a)
                : array_reduce($values, fn ($a, $b) => $a === null || $b->isGreaterThan($a) ? $b : $a);
        }

        $normalized = $contributions = $scores = [];
        foreach ($matrix as $key => $row) {
            $total = BigDecimal::zero();
            foreach ($criteria as $criterion) {
                $value = $this->decimal($row[$criterion], "Nilai {$criterion} pada alternatif {$key}");
                $ratio = $types[$criterion] === 'cost'
                    ? $extrema[$criterion]->dividedBy($value, self::SCALE, RoundingMode::HalfUp)
                    : $value->dividedBy($extrema[$criterion], self::SCALE, RoundingMode::HalfUp);
                $contribution = $ratio->multipliedBy($decimalWeights[$criterion])->toScale(self::SCALE, RoundingMode::HalfUp);
                $normalized[$key][$criterion] = (string) $ratio;
                $contributions[$key][$criterion] = (string) $contribution;
                $total = $total->plus($contribution);
            }
            $scores[$key] = (string) $total->toScale(self::SCALE, RoundingMode::HalfUp);
        }

        $ranking = $this->rank($matrix, $scores);

        return new SawResult($matrix, $normalized, $contributions, $scores, $ranking);
    }

    /**
     * Membentuk kelompok seri dari skor tertinggi sebagai jangkar. Pendekatan ini
     * membuat toleransi seri bersifat transitif dan aman digunakan untuk sorting.
     *
     * @param  array<string, array<string, int|float|string>>  $matrix
     * @param  array<string, string>  $scores
     * @return list<string>
     */
    private function rank(array $matrix, array $scores): array
    {
        $ranking = array_keys($matrix);
        usort($ranking, fn ($a, $b) => BigDecimal::of($scores[$b])->compareTo(BigDecimal::of($scores[$a])) ?: strnatcasecmp((string) $a, (string) $b));

        $groups = [];
        foreach ($ranking as $key) {
            $lastIndex = array_key_last($groups);
            if ($lastIndex === null || BigDecimal::of($scores[$groups[$lastIndex][0]])->minus($scores[$key])->abs()->isGreaterThan(self::TIE_TOLERANCE)) {
                $groups[][] = $key;

                continue;
            }

            $groups[$lastIndex][] = $key;
        }

        foreach ($groups as &$group) {
            usort($group, fn ($a, $b) => $this->compareTieBreak($a, $b, $matrix));
        }
        unset($group);

        return array_merge(...$groups);
    }

    /** @param array<string, array<string, int|float|string>> $matrix */
    private function compareTieBreak(string|int $a, string|int $b, array $matrix): int
    {
        foreach (['C6', 'C1', 'C2', 'C3'] as $criterion) {
            if (isset($matrix[$a][$criterion], $matrix[$b][$criterion])) {
                $comparison = $this->decimal($matrix[$b][$criterion], "Nilai {$criterion}")
                    ->compareTo($this->decimal($matrix[$a][$criterion], "Nilai {$criterion}"));
                if ($comparison !== 0) {
                    return $comparison;
                }
            }
        }

        if (isset($matrix[$a]['C4'], $matrix[$b]['C4'])) {
            $comparison = $this->decimal($matrix[$a]['C4'], 'Nilai C4')
                ->compareTo($this->decimal($matrix[$b]['C4'], 'Nilai C4'));
            if ($comparison !== 0) {
                return $comparison;
            }
        }

        return strnatcasecmp((string) $a, (string) $b);
    }

    private function decimal(int|float|string $value, string $label): BigDecimal
    {
        if (is_float($value) && (! is_finite($value))) {
            throw new InvalidArgumentException("{$label} harus berupa angka finite.");
        }

        try {
            return BigDecimal::of((string) $value);
        } catch (\Throwable $exception) {
            throw new InvalidArgumentException("{$label} harus berupa angka yang valid.", previous: $exception);
        }
    }
}
