<?php

namespace App\Services;

class ExplanationService
{
    private const TEXT = [
        'formula_verified' => 'Formula dan sumber produk telah diverifikasi untuk versi yang dinilai.',
        'rinse_off_scoring_conservative' => 'Produk bilas dinilai konservatif karena waktu kontaknya singkat.',
        'brightening_supportive_ingredient' => 'Formula memuat bahan pendukung keluhan kusam; hasil bukan klaim terapi.',
        'contains_supportive_acne_ingredient' => 'Formula memuat bahan pendukung untuk jerawat ringan atau komedo.',
        'contains_humectant_support' => 'Formula memuat humektan yang mendukung kelembapan.',
        'secondary_concern_support' => 'Formula memiliki dukungan tambahan untuk salah satu keluhan sekunder yang dipilih.',
        'contains_fragrance' => 'Formula mencantumkan fragrance/parfum.',
        'contains_menthol' => 'Formula mencantumkan menthol.',
        'sensitive_skin_use_caution' => 'Kulit sensitif perlu uji tempel dan pemantauan toleransi.',
        'dry_skin_irritant_caution' => 'Kulit kering atau barrier terganggu perlu berhati-hati terhadap fragrance atau menthol.',
        'therapy_requires_professional_confirmation' => 'Konfirmasikan pembersih yang digunakan selama terapi acne kepada tenaga kesehatan.',
        'missing_fresh_price' => 'Belum ada observasi harga yang layak untuk SKU ini.',
        'stale_price' => 'Observasi harga sudah melewati batas kesegaran 30 hari dan tidak dipakai untuk ranking.',
        'ineligible_price_evidence' => 'Harga disimpan sebagai riwayat, tetapi status buktinya belum layak untuk ranking.',
    ];

    /**
     * @param  list<string>  $codes
     * @return list<array{code:string,text:string}>
     */
    public function resolve(array $codes): array
    {
        return array_values(array_map(function ($code) {
            $text = self::TEXT[$code] ?? $code;
            if ($code === 'stale_price') {
                $days = (int) config('dermavera.price_max_age_days', 30);
                $text = "Observasi harga sudah melewati batas kesegaran {$days} hari dan tidak dipakai untuk ranking.";
            }

            return ['code' => $code, 'text' => $text];
        }, array_unique($codes)));
    }
}
