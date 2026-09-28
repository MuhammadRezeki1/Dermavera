<?php

namespace App\Console\Commands;

use App\Services\RecommendationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SmokeRecommendationCommand extends Command
{
    protected $signature = 'dermavera:smoke-recommendation';

    protected $description = 'Memvalidasi pipeline rekomendasi aktif tanpa menyimpan data konsultasi uji.';

    public function handle(RecommendationService $recommendations): int
    {
        DB::beginTransaction();

        try {
            $consultation = $recommendations->evaluate([
                'age' => 24,
                'primary_complaint' => 'jerawat_ringan',
                'secondary_concerns' => [],
                'skin_type' => 'berminyak',
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
            $run = $consultation->runs()->latest('id')->firstOrFail();
            $eligible = $run->results()->where('eligibility_status', 'eligible')->orderBy('rank')->with('variant')->get();
            $this->table(['Rank', 'Kode', 'Produk', 'Skor'], $eligible->take(3)->map(fn ($result) => [$result->rank, $result->variant->catalog_code, $result->variant->name, $result->final_score])->all());
            $this->line("Dataset: {$consultation->datasetVersion->version}; status: {$consultation->status}; alternatif layak: {$eligible->count()}.");

            return $consultation->status === 'completed' && $eligible->count() >= 3
                ? self::SUCCESS
                : self::FAILURE;
        } finally {
            DB::rollBack();
        }
    }
}
