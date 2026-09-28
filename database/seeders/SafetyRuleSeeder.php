<?php

namespace Database\Seeders;

use App\Models\Rule;
use Illuminate\Database\Seeder;

class SafetyRuleSeeder extends Seeder
{
    public function run(): void
    {
        $rules = [
            ['RF-01', 'REFERRAL', 1000, 'critical', 'Red flag klinis menghentikan ranking biasa.'],
            ['HC-01', 'EXCLUDE', 900, 'high', 'Alergi terkonfirmasi terhadap ingredient mengecualikan formula.'],
            ['HC-02', 'EXCLUDE', 800, 'high', 'Kulit sensitif/barrier terganggu dengan scrub fisik kuat dikeluarkan.'],
            ['HC-03', 'EXCLUDE', 700, 'high', 'Kombinasi eksfolian, menthol, dan pewangi dinilai konservatif.'],
            ['HC-04', 'EXCLUDE', 600, 'high', 'Terapi acne memerlukan evaluasi potensi iritasi dan konfirmasi profesional.'],
            ['HC-05', 'EXCLUDE', 500, 'high', 'Formula minimum yang belum terverifikasi tidak dipublikasikan.'],
            ['HC-06', 'EXCLUDE', 400, 'high', 'Legalitas yang belum dapat diverifikasi menahan publikasi.'],
        ];
        foreach ($rules as [$code, $category, $priority, $severity, $explanation]) {
            Rule::updateOrCreate(['code' => $code], compact('category', 'priority', 'severity', 'explanation') + ['version' => '1.0.0', 'is_active' => true]);
        }
    }
}
