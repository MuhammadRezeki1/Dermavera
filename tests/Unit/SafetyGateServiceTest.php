<?php

namespace Tests\Unit;

use App\Services\SafetyGateService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class SafetyGateServiceTest extends TestCase
{
    public static function redFlags(): array
    {
        return array_map(fn ($flag) => [$flag], ['nodul_kistik', 'jerawat_berat_memburuk', 'jaringan_parut', 'luka_infeksi_luas', 'bengkak_alergi_berat', 'nyeri_terbakar_menetap', 'penyakit_kulit_dalam_pengobatan']);
    }

    #[DataProvider('redFlags')]
    public function test_every_red_flag_stops_normal_ranking(string $flag): void
    {
        $decision = (new SafetyGateService)->assess(['red_flags' => [$flag]]);
        $this->assertTrue($decision->shouldRefer());
        $this->assertSame('refer', $decision->outcome);
    }

    public function test_safety_overlays_become_hard_constraints_before_scoring(): void
    {
        $decision = (new SafetyGateService)->assess(['red_flags' => [], 'allergies' => ['MENTHOL'], 'sensitive' => true, 'barrier_impaired' => true, 'acne_therapy' => true]);
        $this->assertSame(['HC-01', 'HC-02', 'HC-03', 'HC-04'], $decision->hardConstraints);
    }
}
