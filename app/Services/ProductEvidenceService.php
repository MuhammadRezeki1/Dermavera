<?php

namespace App\Services;

use App\Models\FormulaVersion;

class ProductEvidenceService
{
    public function publishable(FormulaVersion $formula): bool
    {
        return trim($formula->inci_raw) !== ''
            && $formula->bpom_number !== null
            && $formula->source_id !== null
            && $formula->verified_at !== null
            && in_array($formula->verification_status, ['VERIFIED_OFFICIAL_INCI', 'VALIDATED_ID_FULL_INCI', 'VALIDATED_ID_VERSIONED'], true);
    }
}
