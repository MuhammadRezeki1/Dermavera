<?php

namespace App\Domain\Recommendation\DTO;

final readonly class SawResult
{
    /**
     * @param  array<string, array<string, int|float|string>>  $matrix
     * @param  array<string, array<string, string>>  $normalized
     * @param  array<string, array<string, string>>  $contributions
     * @param  array<string, string>  $scores
     * @param  list<string>  $ranking
     */
    public function __construct(
        public array $matrix,
        public array $normalized,
        public array $contributions,
        public array $scores,
        public array $ranking,
    ) {}
}
