<?php

namespace App\Domain\Recommendation\Contracts;

use App\Domain\Recommendation\DTO\SawResult;

interface SawCalculatorContract
{
    /**
     * @param  array<string, array<string, int|float|string>>  $matrix
     * @param  array<string, int|float|string>  $weights
     * @param  array<string, string>  $types
     */
    public function calculate(array $matrix, array $weights, array $types): SawResult;
}
