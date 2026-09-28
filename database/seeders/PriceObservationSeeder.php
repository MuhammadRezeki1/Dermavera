<?php

namespace Database\Seeders;

use App\Models\PriceObservation;
use App\Models\ProductSku;
use App\Models\ProductVariant;
use App\Models\Source;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use RuntimeException;

class PriceObservationSeeder extends Seeder
{
    private const RANKING_ELIGIBLE_STATUSES = [
        'aktif',
        'promo aktif',
        'official store aktif',
        'promo ritel',
        'promo aktif exact sku',
    ];

    public function run(): void
    {
        $path = base_path('docs/18-Referensi-Harga-37-Record.md');
        $markdown = file_get_contents($path) ?: throw new RuntimeException('Dokumen referensi harga tidak dapat dibaca.');
        $rows = $this->parseRows($markdown);

        if (count(array_unique(array_column($rows, 'code'))) !== 37) {
            throw new RuntimeException('Dokumen harga wajib memuat 37 kode produk unik; observasi tambahan per SKU diperbolehkan.');
        }

        $catalogCodes = ProductVariant::query()->orderBy('catalog_code')->pluck('catalog_code')->all();
        $priceCodes = collect($rows)->pluck('code')->unique()->sort()->values()->all();
        if ($catalogCodes !== $priceCodes) {
            throw new RuntimeException('Kode produk dokumen harga tidak sama dengan katalog produk.');
        }

        $currentObservationIds = [];

        foreach ($rows as $row) {
            $variant = ProductVariant::query()->where('catalog_code', $row['code'])->firstOrFail();
            $sku = ProductSku::query()
                ->where('product_variant_id', $variant->id)
                ->where('size_value', $row['size'])
                ->where('size_unit', $row['unit'])
                ->first();
            if (! $sku) {
                throw new RuntimeException("SKU {$row['code']} {$row['size']} {$row['unit']} tidak ditemukan.");
            }

            $observedAt = CarbonImmutable::parse($row['date'].' 12:00:00', 'Asia/Jakarta');
            $source = Source::updateOrCreate(
                ['url' => $row['url'], 'accessed_at' => $observedAt],
                [
                    'title' => "Referensi harga {$row['code']} — {$row['seller']}",
                    'publisher' => $row['seller'],
                    'type' => $this->sourceType($row['status']),
                    'published_at' => $row['date'],
                    'checksum' => hash('sha256', implode('|', $row)),
                    'evidence_path' => 'docs/18-Referensi-Harga-37-Record.md',
                ],
            );

            $calculatedPer100 = round(($row['amount'] / $row['size']) * 100, 2);
            if (abs($calculatedPer100 - $row['price_per_100']) > 0.01) {
                throw new RuntimeException("Harga per 100 untuk {$row['code']} tidak konsisten.");
            }

            $normalizedStatus = Str::lower($row['status']);
            $isAvailable = ! Str::contains($normalizedStatus, ['historis', 'arsip', 'last chance', 'referensi sekunder', 'stok habis']);
            $rankingEligible = $variant->is_active
                && $isAvailable
                && in_array($normalizedStatus, self::RANKING_ELIGIBLE_STATUSES, true);

            $observation = PriceObservation::updateOrCreate(
                ['product_sku_id' => $sku->id, 'source_id' => $source->id, 'observed_at' => $observedAt],
                [
                    'amount' => $row['amount'],
                    'currency' => 'IDR',
                    'seller' => $row['seller'],
                    'promo' => $row['amount'] < $row['normal_price'],
                    'normal_price' => $row['normal_price'],
                    'unit_price_per_100' => $calculatedPer100,
                    'evidence_status' => $row['status'],
                    'is_available' => $isAvailable,
                    'ranking_eligible' => $rankingEligible,
                    'notes' => "Diimpor dari dokumen harga record {$row['code']}; status bukti dipertahankan apa adanya.",
                ],
            );
            $currentObservationIds[] = $observation->id;
        }

        // Observasi lama tetap dipertahankan untuk audit, tetapi tidak boleh
        // memengaruhi ranking setelah dokumen harga aktif diperbarui.
        PriceObservation::query()
            ->whereNotIn('id', $currentObservationIds)
            ->where('ranking_eligible', true)
            ->update(['ranking_eligible' => false]);
    }

    /**
     * @return list<array{code:string,size:int,unit:string,amount:int,normal_price:int,price_per_100:int,date:string,seller:string,url:string,status:string}>
     */
    private function parseRows(string $markdown): array
    {
        $rows = [];
        foreach (preg_split('/\R/', $markdown) ?: [] as $line) {
            if (! preg_match('/^\|\s*(K\d{2}|M\d{2}|G\d{2}|N\d{2}|B\d{2}N?)\s*\|/', $line)) {
                continue;
            }
            $cells = array_map('trim', explode('|', trim($line, "| \t")));
            if (count($cells) !== 9) {
                throw new RuntimeException('Format baris harga tidak valid: '.$line);
            }
            [$code, , $sizeCell, $amountCell, $normalCell, $per100Cell, $date, $sourceCell, $status] = $cells;
            if (! preg_match('/^(\d+)\s*(ml|g)$/i', $sizeCell, $sizeMatch)) {
                throw new RuntimeException("Ukuran {$code} tidak valid.");
            }
            if (! preg_match('/^\[([^]]+)]\((https:\/\/[^)]+)\)$/', $sourceCell, $sourceMatch)) {
                throw new RuntimeException("Sumber harga {$code} tidak valid.");
            }

            $rows[] = [
                'code' => $code,
                'size' => (int) $sizeMatch[1],
                'unit' => Str::lower($sizeMatch[2]),
                'amount' => $this->rupiah($amountCell),
                'normal_price' => $this->rupiah($normalCell),
                'price_per_100' => $this->rupiah($per100Cell),
                'date' => $date,
                'seller' => $sourceMatch[1],
                'url' => $sourceMatch[2],
                'status' => $status,
            ];
        }

        return $rows;
    }

    private function rupiah(string $value): int
    {
        $digits = preg_replace('/\D/', '', $value);
        if (! $digits) {
            throw new RuntimeException("Nilai harga {$value} tidak valid.");
        }

        return (int) $digits;
    }

    private function sourceType(string $status): string
    {
        $status = Str::lower($status);

        return match (true) {
            Str::contains($status, 'official store') => 'official_store_price',
            Str::contains($status, 'marketplace') => 'marketplace_price',
            Str::contains($status, 'referensi sekunder') => 'secondary_price_reference',
            default => 'retailer_price',
        };
    }
}
