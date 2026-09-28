<?php

namespace App\Services;

use App\Domain\Recommendation\Contracts\SafetyGateContract;
use App\Domain\Recommendation\DTO\SafetyDecision;

class SafetyGateService implements SafetyGateContract
{
    private const RED_FLAGS = [
        'nodul_kistik', 'jerawat_berat_memburuk', 'jaringan_parut', 'luka_infeksi_luas',
        'bengkak_alergi_berat', 'nyeri_terbakar_menetap', 'penyakit_kulit_dalam_pengobatan',
    ];

    /** @param array<string, mixed> $profile */
    public function assess(array $profile): SafetyDecision
    {
        $selected = array_values(array_intersect(self::RED_FLAGS, $profile['red_flags'] ?? []));
        if ($selected !== []) {
            return new SafetyDecision('refer', [], array_map(fn ($flag) => "red_flag_{$flag}", $selected));
        }

        $constraints = [];
        $warnings = [];
        if (! empty($profile['allergies'])) {
            $constraints[] = 'HC-01';
        }
        if (($profile['sensitive'] ?? false) || ($profile['barrier_impaired'] ?? false)) {
            $constraints[] = 'HC-02';
            $constraints[] = 'HC-03';
        }
        if ($profile['acne_therapy'] ?? false) {
            $constraints[] = 'HC-04';
            $warnings[] = 'therapy_requires_professional_confirmation';
        }

        return new SafetyDecision('eligible', array_values(array_unique($constraints)), ['safety_screen_complete'], $warnings);
    }
}
