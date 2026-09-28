<?php

namespace Database\Seeders;

use App\Models\Criterion;
use Illuminate\Database\Seeder;

class CriterionSeeder extends Seeder
{
    public function run(): void
    {
        $criteria = [
            ['C1', 'Kecocokan dengan keluhan kulit', 'benefit', 'Kesesuaian bahan pendukung dengan keluhan utama pengguna.'],
            ['C2', 'Kecocokan dengan jenis/kondisi kulit', 'benefit', 'Kesesuaian formula dengan jenis kulit dan penalti untuk sensitivitas kontekstual.'],
            ['C3', 'Kandungan dan keseluruhan formulasi', 'benefit', 'Dukungan humektan atau bahan aktif relevan dalam keseluruhan formula rinse-off.'],
            ['C4', 'Harga per 100 ml/g', 'cost', 'Harga unit positif per 100 ml atau 100 g; nilai lebih rendah lebih baik.'],
            ['C5', 'Fungsi dan kebersihan kemasan', 'benefit', 'Skor fungsi kemasan dan kesesuaiannya dengan preferensi pengguna.'],
            ['C6', 'Kualitas bukti, legalitas, dan keamanan kontekstual', 'benefit', 'Kelengkapan BPOM, sumber formula, status verifikasi, dan penalti iritan kontekstual.'],
        ];
        $definitions = [
            1 => ['Sangat tidak sesuai', 'Terdapat konflik kuat yang telah ditetapkan dalam rubrik kriteria.'],
            2 => ['Tidak sesuai', 'Dukungan rendah atau terdapat penalti kontekstual terhadap kebutuhan pengguna.'],
            3 => ['Cukup/netral', 'Tidak ditemukan bukti pendukung maupun konflik yang cukup untuk menaikkan atau menurunkan skor.'],
            4 => ['Sesuai', 'Terdapat satu dukungan relevan yang terverifikasi menurut rubrik kriteria.'],
            5 => ['Sangat sesuai', 'Terdapat dukungan kuat atau beberapa dukungan relevan yang terverifikasi menurut rubrik kriteria.'],
        ];
        foreach ($criteria as [$code, $name, $type, $description]) {
            $criterion = Criterion::updateOrCreate(['code' => $code], compact('name', 'type', 'description'));
            if ($code === 'C4') {
                \DB::table('criterion_scales')->where('criterion_id', $criterion->id)->delete();

                continue;
            }
            foreach ($definitions as $score => [$label, $operationalDefinition]) {
                \DB::table('criterion_scales')->updateOrInsert(['criterion_id' => $criterion->id, 'score' => $score], ['label' => $label, 'operational_definition' => $operationalDefinition, 'created_at' => now(), 'updated_at' => now()]);
            }
        }
    }
}
