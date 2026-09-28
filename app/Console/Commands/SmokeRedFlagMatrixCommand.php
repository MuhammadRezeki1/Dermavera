<?php

namespace App\Console\Commands;

use App\Services\RecommendationService;
use App\Services\SafetyGateService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

class SmokeRedFlagMatrixCommand extends Command
{
    protected $signature = 'dermavera:smoke-redflag-matrix';

    protected $description = 'Memvalidasi seluruh kombinasi red flag dan penghentian pipeline SAW.';

    /** @var list<string> */
    private const RED_FLAGS = [
        'nodul_kistik',
        'jerawat_berat_memburuk',
        'jaringan_parut',
        'luka_infeksi_luas',
        'bengkak_alergi_berat',
        'nyeri_terbakar_menetap',
        'penyakit_kulit_dalam_pengobatan',
    ];

    /** @var list<string> */
    private const PRIMARY_COMPLAINTS = ['berminyak', 'komedo', 'jerawat_ringan', 'kusam', 'kering'];

    /** @var list<string> */
    private const SKIN_TYPES = ['normal', 'berminyak', 'kering', 'kombinasi'];

    public function handle(RecommendationService $recommendations, SafetyGateService $safety): int
    {
        $combinationCount = (1 << count(self::RED_FLAGS)) - 1;
        $gateChecks = 0;
        $pipelineChecks = 0;
        $failures = [];

        foreach (self::PRIMARY_COMPLAINTS as $primaryComplaint) {
            foreach (self::SKIN_TYPES as $skinType) {
                for ($contextMask = 0; $contextMask < 16; $contextMask++) {
                    $profileContext = [
                        'primary_complaint' => $primaryComplaint,
                        'skin_type' => $skinType,
                        'sensitive' => (bool) ($contextMask & 1),
                        'barrier_impaired' => (bool) ($contextMask & 2),
                        'acne_therapy' => (bool) ($contextMask & 4),
                        'allergies' => ($contextMask & 8) ? ['fragrance'] : [],
                    ];

                    for ($redFlagMask = 1; $redFlagMask <= $combinationCount; $redFlagMask++) {
                        $flags = $this->flagsFromMask($redFlagMask);
                        $decision = $safety->assess([...$profileContext, 'red_flags' => $flags]);
                        $gateChecks++;

                        $expectedCodes = array_map(fn (string $flag) => "red_flag_{$flag}", $flags);
                        if ($decision->outcome !== 'refer' || ! $decision->shouldRefer() || $decision->explanationCodes !== $expectedCodes) {
                            $failures[] = "gate:{$primaryComplaint}:{$skinType}:context={$contextMask}:mask={$redFlagMask}";
                        }
                    }
                }
            }
        }

        DB::beginTransaction();

        try {
            for ($redFlagMask = 1; $redFlagMask <= $combinationCount; $redFlagMask++) {
                $flags = $this->flagsFromMask($redFlagMask);
                $consultation = $recommendations->evaluate([
                    'age' => 24,
                    'primary_complaint' => 'jerawat_ringan',
                    'secondary_concerns' => ['kusam', 'komedo'],
                    'skin_type' => 'berminyak',
                    'sensitive' => true,
                    'barrier_impaired' => true,
                    'acne_therapy' => true,
                    'allergies' => ['fragrance'],
                    'red_flags' => $flags,
                    'min_price' => null,
                    'max_price' => null,
                    'packaging_preference' => '',
                    'consent_at' => now(),
                ]);
                $pipelineChecks++;
                $assessment = $consultation->safetyAssessment;

                if (
                    $consultation->status !== 'referred'
                    || ! $assessment?->red_flag
                    || $assessment->outcome !== 'refer'
                    || $consultation->runs()->exists()
                ) {
                    $failures[] = "pipeline:mask={$redFlagMask}";
                }
            }

            $this->line("Safety gate: {$gateChecks} checks; gagal: " . count(array_filter($failures, fn (string $failure) => str_starts_with($failure, 'gate:'))));
            $this->line("Pipeline SAW: {$pipelineChecks} checks; gagal: " . count(array_filter($failures, fn (string $failure) => str_starts_with($failure, 'pipeline:'))));
            $this->line('Kombinasi red flag diuji: ' . $combinationCount . ' (seluruh kombinasi non-kosong).');

            if ($failures !== []) {
                $this->error('Ditemukan kegagalan:');
                $this->table(['Contoh kegagalan'], array_map(fn (string $failure) => [$failure], array_slice($failures, 0, 10)));

                return self::FAILURE;
            }

            $this->info('LULUS: seluruh red flag memicu rujukan dan tidak membuat ranking SAW.');

            return self::SUCCESS;
        } finally {
            DB::rollBack();
        }
    }

    /** @return list<string> */
    private function flagsFromMask(int $mask): array
    {
        $flags = [];
        foreach (self::RED_FLAGS as $index => $flag) {
            if (($mask & (1 << $index)) !== 0) {
                $flags[] = $flag;
            }
        }

        return $flags;
    }
}
