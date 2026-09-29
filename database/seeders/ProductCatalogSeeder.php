<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\FormulaVersion;
use App\Models\Ingredient;
use App\Models\ProductEvidence;
use App\Models\ProductSku;
use App\Models\ProductVariant;
use App\Models\Source;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use RuntimeException;

class ProductCatalogSeeder extends Seeder
{
    private const BPOM = [
        'K01' => 'NA18251206114', 'K02' => 'NA18251206015', 'K03' => 'NA18251206004', 'K04' => 'NA18251206553', 'K05' => 'NA18221200857', 'K06' => 'NA18251202492', 'K07' => 'NA18251202219',
        'M01' => 'NA18241206929', 'M02' => 'NA18241208586', 'M03' => 'NA18241206802', 'M04' => 'NA18241206800', 'G01' => 'NA18171203281', 'G02' => 'NA18241201785', 'G03' => 'NA18201205798', 'G04' => 'NA18211201259', 'G05' => 'NA18201205796', 'G06' => 'NA18241200587', 'G07' => 'NA18261201331', 'G08' => 'NA18221208721', 'G09' => 'NA18201205266',
        'N01' => 'NA18201204536', 'N02' => 'NA18211207460', 'N03' => 'NA18241210118', 'N04' => 'NA18201203923', 'N05' => 'NA18221201768', 'N06' => 'NA18251211307', 'N07' => 'NA18241210118', 'N08' => 'NA18211207459',
        'B01' => 'NA18261200825', 'B02' => 'NA18231200236', 'B03' => 'NA18231200237', 'B03N' => 'NA18241202965', 'B04' => 'NA18201203966', 'B05' => 'NA18221201164', 'B06' => 'NA18221201165', 'B07' => 'NA18221201191', 'B08' => 'NA18191200283',
    ];

    private const VERSIONED = ['G01', 'B03', 'B03N', 'B05', 'B06'];

    /**
     * Ingredients that are unambiguous physical abrasives in this dataset.
     * Context-dependent materials such as cellulose, silica, and corn starch
     * are evaluated by EligibilityService together with the product claim.
     */
    private const PHYSICAL_SCRUB_INGREDIENTS = ['PUMICE', 'PERLITE', 'HYDRATED SILICA', 'MICROCRYSTALLINE CELLULOSE', 'POLYETHYLENE', 'SYNTHETIC WAX'];

    private const SIZES = ['K01' => [[50, 'ml'], [100, 'ml']], 'K02' => [[50, 'ml'], [100, 'ml']], 'G07' => [[50, 'ml'], [100, 'ml']], 'N03' => [[50, 'ml']], 'N07' => [[100, 'ml']], 'M01' => [[100, 'ml']], 'M02' => [[100, 'ml']], 'M03' => [[100, 'ml']], 'M04' => [[100, 'ml']], 'N01' => [[100, 'ml']], 'N02' => [[100, 'ml']], 'N04' => [[100, 'ml']], 'N05' => [[100, 'ml']], 'N06' => [[100, 'ml']], 'N08' => [[100, 'ml']], 'B01' => [[100, 'g']], 'B02' => [[100, 'g']], 'B03' => [[100, 'g']], 'B03N' => [[100, 'g']], 'B04' => [[100, 'g']], 'B05' => [[100, 'g']], 'B06' => [[100, 'g']], 'B07' => [[100, 'g']], 'B08' => [[100, 'g']]];

    public function run(): void
    {
        $path = base_path('docs/13-Katalog-Produk-Varian-dan-Ingredients.md');
        $markdown = file_get_contents($path) ?: throw new RuntimeException('Dokumen katalog tidak dapat dibaca.');
        $start = strpos($markdown, '## 3. Kahf');
        if ($start === false) {
            throw new RuntimeException('Bagian katalog produk tidak ditemukan.');
        }
        $catalog = substr($markdown, $start);
        preg_match_all('/^###\s+(K\d{2}|M\d{2}|G\d{2}|N\d{2}|B\d{2}N?)\s+-\s+(.+)$/m', $catalog, $headings, PREG_OFFSET_CAPTURE);
        if (count($headings[1]) !== 37) {
            throw new RuntimeException('Katalog wajib berisi tepat 37 record produk.');
        }

        $brands = [
            'K' => Brand::updateOrCreate(['slug' => 'kahf'], ['name' => 'Kahf', 'official_url' => 'https://www.kahfeveryday.com/en/shop/face-care/', 'is_active' => true]),
            'M' => Brand::updateOrCreate(['slug' => 'ms-glow-for-men'], ['name' => 'MS Glow For Men', 'official_url' => 'https://web.ms-glow.id', 'is_active' => true]),
            'G' => Brand::updateOrCreate(['slug' => 'garnier-men'], ['name' => 'Garnier Men', 'official_url' => 'https://www.garnier.co.id/tentang-brands/garnier-men', 'is_active' => true]),
            'N' => Brand::updateOrCreate(['slug' => 'nivea-men'], ['name' => 'NIVEA Men', 'official_url' => 'https://www.nivea.co.id/produk/pria/wajah', 'is_active' => true]),
            'B' => Brand::updateOrCreate(['slug' => 'mens-biore'], ['name' => "Men's Biore", 'official_url' => 'https://www.kao.com/id/id/products/mensbiore/', 'is_active' => true]),
        ];

        foreach ($headings[1] as $index => $codeMatch) {
            $code = $codeMatch[0];
            $verifiedAt = in_array($code, ['M02', 'M03', 'M04'], true) ? '2026-09-26 00:00:00+07:00' : '2026-09-24 00:00:00+07:00';
            $name = trim($headings[2][$index][0]);
            $offset = $headings[0][$index][1];
            $end = $headings[0][$index + 1][1] ?? strlen($catalog);
            $section = substr($catalog, $offset, $end - $offset);
            $status = in_array($code, self::VERSIONED, true) ? 'VALIDATED_ID_VERSIONED' : (str_starts_with($code, 'B') || str_starts_with($code, 'M') ? 'VALIDATED_ID_FULL_INCI' : 'VERIFIED_OFFICIAL_INCI');
            $variant = ProductVariant::updateOrCreate(['catalog_code' => $code], ['brand_id' => $brands[$code[0]]->id, 'name' => $name, 'slug' => Str::slug($code.'-'.$name), 'target_claim' => $this->importantIngredients($section), 'rinse_off' => true, 'status' => 'verified', 'evidence_status' => $status, 'is_active' => false]);
            preg_match('/\*\*Sumber full INCI Indonesia:\*\*\s*\[[^\]]+\]\((https:\/\/[^)]+)\)/', $section, $inciUrlMatch);
            preg_match('/\[[^\]]+\]\((https:\/\/[^)]+)\)/', $section, $urlMatch);
            $urlMatch = $inciUrlMatch ?: $urlMatch;
            $url = $urlMatch[1] ?? $brands[$code[0]]->official_url;
            $sourceType = str_contains($url, 'ms-glow.id') ? 'official_product_page' : (str_contains($section, 'Sumber resmi') && ! isset($inciUrlMatch[1]) ? 'official_product_page' : 'retailer_supplemental');
            $source = Source::query()->where('url', $url)->whereDate('accessed_at', substr($verifiedAt, 0, 10))->first()
                ?? Source::create(['url' => $url, 'accessed_at' => $verifiedAt, 'title' => "Sumber {$code} {$name}", 'publisher' => $brands[$code[0]]->name, 'type' => $sourceType]);
            ProductEvidence::updateOrCreate(['product_variant_id' => $variant->id, 'source_id' => $source->id, 'evidence_type' => 'composition'], ['verification_status' => $status, 'notes' => 'Diimpor dari dokumen katalog versi 3.2.']);

            if (preg_match('/\*\*Sumber resmi dan BPOM:\*\*\s*\[[^\]]+\]\((https:\/\/[^)]+)\)/', $section, $officialUrlMatch)) {
                $officialSource = Source::query()
                    ->where('url', $officialUrlMatch[1])
                    ->whereDate('accessed_at', substr($verifiedAt, 0, 10))
                    ->first();
                if (! $officialSource) {
                    $officialSource = Source::create([
                        'url' => $officialUrlMatch[1],
                        'accessed_at' => $verifiedAt,
                        'title' => "Identitas dan BPOM {$code} {$name}",
                        'publisher' => $brands[$code[0]]->name,
                        'type' => 'official_product_page',
                    ]);
                } else {
                    $officialSource->update([
                        'title' => "Identitas dan BPOM {$code} {$name}",
                        'publisher' => $brands[$code[0]]->name,
                        'type' => 'official_product_page',
                    ]);
                }
                ProductEvidence::updateOrCreate(
                    ['product_variant_id' => $variant->id, 'source_id' => $officialSource->id, 'evidence_type' => 'identity_bpom'],
                    ['verification_status' => $status, 'notes' => 'Nama, ukuran, klaim utama, dan nomor izin edar dari laman resmi merek.'],
                );
            }

            $sizes = self::SIZES[$code] ?? [[100, str_starts_with($code, 'B') ? 'g' : 'ml']];
            foreach ($sizes as [$value,$unit]) {
                $sku = ProductSku::updateOrCreate(['product_variant_id' => $variant->id, 'size_value' => $value, 'size_unit' => $unit], ['bpom_no' => $this->bpomForSku($code, $value), 'package_type' => 'tube', 'packaging_score' => 3, 'is_active' => true]);
                $inci = $this->inciFor($markdown, $section, $code, $value);
                $formula = FormulaVersion::updateOrCreate(['product_variant_id' => $variant->id, 'version_label' => "ID-{$value}{$unit}-2026"], ['product_sku_id' => $sku->id, 'source_id' => $source->id, 'inci_raw' => $inci, 'inci_normalized' => $this->normalizeInci($inci), 'ph_min' => null, 'ph_max' => null, 'verified_at' => $verifiedAt, 'verification_status' => $status, 'status' => 'active', 'bpom_number' => $this->bpomForSku($code, $value), 'registered_name' => $name, 'package_size' => "{$value} {$unit}"]);
                $this->syncIngredients($formula, $inci);
            }
            $variant->update(['is_active' => ! in_array($code, ['B03', 'M01'], true)]);
        }
    }

    private function inciFor(string $markdown, string $section, string $code, int $size): string
    {
        if (in_array($code, ['K01', 'K02'], true)) {
            $key = "{$code}-{$size}ML";
            if (preg_match('/^####\s+'.preg_quote($key, '/').'\s+-.+?(?=^####|^###|^##)/ms', $markdown, $block) && preg_match('/\*\*INCI Watsons[^:]*:\*\*\s*(.+?)(?=\n\n|$)/s', $block[0], $inci)) {
                return trim($inci[1]);
            }
        }
        $patterns = $code === 'G01'
            ? ['/\*\*Kandidat formula B[^:]*:\*\*\s*(.+?)(?=\n\n|$)/s']
            : ['/\*\*INCI Indonesia[^:]*:\*\*\s*(.+?)(?=\n\n|$)/s', '/\*\*INCI resmi:\*\*\s*(.+?)(?=\n\n|$)/s', '/\*\*INCI pendukung:\*\*\s*(.+?)(?=\n\n|$)/s', '/\*\*INCI yang saat ini[^:]*:\*\*\s*(.+?)(?=\n\n|$)/s'];
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $section, $match)) {
                return trim($match[1]);
            }
        }
        throw new RuntimeException("Full INCI {$code} tidak ditemukan.");
    }

    private function importantIngredients(string $section): ?string
    {
        preg_match('/\*\*Bahan penting[^:]*:\*\*\s*(.+)/', $section, $m);

        return isset($m[1]) ? trim($m[1]) : null;
    }

    private function normalizeInci(string $inci): string
    {
        return implode(', ', array_map(fn ($v) => trim((string) preg_replace('/\s+/', ' ', $v)), preg_split('/,\s*/', $inci) ?: []));
    }

    private function bpomForSku(string $code, int $size): string
    {
        if ($code === 'K01' && $size === 50) {
            return 'NA18201203048';
        } if ($code === 'K02' && $size === 50) {
            return 'NA18211205162';
        }

        return self::BPOM[$code];
    }

    private function syncIngredients(FormulaVersion $formula, string $inci): void
    {
        $formula->formulaIngredients()->delete();
        foreach (preg_split('/,\s*/', $inci) ?: [] as $index => $raw) {
            $name = Str::upper(trim(preg_replace('/\s*\([^)]*\)/', '', $raw)));
            if ($name === '') {
                continue;
            }
            $classificationName = rtrim($name, '.');
            $ingredient = Ingredient::updateOrCreate(
                ['inci_name' => $name],
                [
                    'display_name' => Str::title(Str::lower($name)),
                    'function_group' => 'unclassified',
                    'is_fragrance' => Str::contains($name, ['FRAGRANCE', 'PARFUM']),
                    'is_menthol' => Str::contains($name, 'MENTHOL'),
                    'is_physical_scrub' => in_array($classificationName, self::PHYSICAL_SCRUB_INGREDIENTS, true),
                    'is_exfoliant' => Str::contains($name, ['SALICYLIC ACID', 'GLYCOLIC ACID', 'LACTIC ACID', 'GLUCONOLACTONE']),
                ],
            );
            $formula->formulaIngredients()->create(['ingredient_id' => $ingredient->id, 'ordinal' => $index + 1, 'concentration_status' => 'not_declared']);
        }
    }
}
