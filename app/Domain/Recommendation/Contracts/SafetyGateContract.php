<?php

namespace App\Domain\Recommendation\Contracts;

use App\Domain\Recommendation\DTO\SafetyDecision;

interface SafetyGateContract
{
    /** @param array<string, mixed> $profile */
    public function assess(array $profile): SafetyDecision;
}
