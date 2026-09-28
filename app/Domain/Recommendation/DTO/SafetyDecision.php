<?php

namespace App\Domain\Recommendation\DTO;

final readonly class SafetyDecision
{
    /**
     * @param  list<string>  $hardConstraints
     * @param  list<string>  $explanationCodes
     * @param  list<string>  $warnings
     */
    public function __construct(
        public string $outcome,
        public array $hardConstraints = [],
        public array $explanationCodes = [],
        public array $warnings = [],
    ) {}

    public function shouldRefer(): bool
    {
        return $this->outcome === 'refer';
    }
}
