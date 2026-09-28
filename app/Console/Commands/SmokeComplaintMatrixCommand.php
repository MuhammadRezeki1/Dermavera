<?php

namespace App\Console\Commands;

use App\Services\RecommendationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SmokeComplaintMatrixCommand extends Command
{
    protected $signature = 'dermavera:smoke-complaint-matrix';

    protected $description = 'Memvalidasi seluruh kombinasi keluhan utama dan keluhan tambahan.';

    /** @var list<string> */
    private const COMPLAINTS = ['berminyak', 'komedo', 'jerawat_ringan', 'kusam', 'kering'];

    public function handle(RecommendationService $recommendations): int
    {
        $secondaryConcerns = self::COMPLAINTS;
        $combinationCount = 1 << count($secondaryConcerns);
        $checks = 0;
        $completed = 0;
        $noSafeAlternative = 0;
        $failures = [];

        DB::beginTransaction();

        try {
            foreach (self::COMPLAINTS as $primaryComplaint) {
                for ($mask = 0; $mask < $combinationCount; $mask++) {
                    $selectedSecondary = [];
                    foreach ($secondaryConcerns as $index => $concern) {
                        if (($mask & (1 << $index)) !== 0) {
                            $selectedSecondary[] = $concern;
                        }
                    }

                    $consultation = $recommendations->evaluate([
                        'age' => 24,
                        'primary_complaint' => $primaryComplaint,
                        'secondary_concerns' => $selectedSecondary,
                        'skin_type' => $primaryComplaint === 'kering' ? 'kering' : 'berminyak',
                        'sensitive' => false,
                        'barrier_impaired' => false,
                        'acne_therapy' => false,
                        'allergies' => [],
                        'red_flags' => [],
                        'min_price' => null,
                        'max_price' => null,
                        'packaging_preference' => '',
                        'consent_at' => now(),
                    ]);
                    $checks++;
                    $assessment = $consultation->safetyAssessment;
                    $snapshot = $consultation->input_snapshot;

                    if ($consultation->status === 'completed') {
                        $completed++;
                    } elseif ($consultation->status === 'no_safe_alternative') {
                        $noSafeAlternative++;
                    }

                    if (
                        ! in_array($consultation->status, ['completed', 'no_safe_alternative'], true)
                        || $assessment?->red_flag
                        || $assessment->outcome !== 'eligible'
                        || ($snapshot['primary_complaint'] ?? null) !== $primaryComplaint
                        || ($snapshot['secondary_concerns'] ?? []) !== $selectedSecondary
                        || ! $consultation->runs()->exists()
                    ) {
                        $failures[] = "primary={$primaryComplaint}:secondary=" . implode(',', $selectedSecondary ?: ['none']);
                    }
                }
            }

            $this->line("Kombinasi keluhan diuji: {$checks} (5 utama x 32 subset tambahan). ");
            $this->line("Selesai normal: {$completed}; tanpa alternatif aman: {$noSafeAlternative}; gagal: " . count($failures));

            if ($failures !== []) {
                $this->error('Ditemukan kegagalan:');
                $this->table(['Contoh kegagalan'], array_map(fn (string $failure) => [$failure], array_slice($failures, 0, 10)));

                return self::FAILURE;
            }

            $this->info('LULUS: seluruh kombinasi keluhan diproses tanpa salah klasifikasi red flag.');

            return self::SUCCESS;
        } finally {
            DB::rollBack();
        }
    }
}
