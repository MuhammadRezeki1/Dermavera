<?php

namespace App\Console\Commands;

use App\Models\DatasetVersion;
use App\Models\ProductVariant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ExportVariantIngredientsCommand extends Command
{
    protected $signature = 'dermavera:export-variant-ingredients {--output=storage/app/exports/dermavera-variant-ingredients.csv}';

    protected $description = 'Mengekspor variant aktif dan seluruh ingredient formula aktif dalam format CSV.';

    public function handle(): int
    {
        $relativePath = ltrim((string) $this->option('output'), '/\\');
        $path = str_starts_with($relativePath, 'storage'.DIRECTORY_SEPARATOR)
            || str_starts_with($relativePath, 'storage/')
            ? base_path($relativePath)
            : storage_path($relativePath);

        File::ensureDirectoryExists(dirname($path));

        $dataset = DatasetVersion::where('status', 'active')->latest('activated_at')->first();
        $variants = ProductVariant::query()
            ->with(['brand', 'activeFormula.formulaIngredients' => fn ($query) => $query->orderBy('ordinal'), 'activeFormula.formulaIngredients.ingredient'])
            ->orderBy('catalog_code')
            ->get();

        $handle = fopen($path, 'wb');
        if ($handle === false) {
            $this->error("Tidak dapat membuat file {$path}.");

            return self::FAILURE;
        }

        fwrite($handle, "\xEF\xBB\xBF");
        fputcsv($handle, [
            'dataset_version',
            'dataset_hash',
            'variant_code',
            'brand',
            'variant',
            'variant_status',
            'variant_active',
            'formula_version',
            'formula_status',
            'bpom_number',
            'verification_status',
            'formula_inci_raw',
            'ingredient_order',
            'ingredient_id',
            'inci_name',
            'display_name',
            'function_group',
            'is_fragrance',
            'is_menthol',
            'is_physical_scrub',
            'is_exfoliant',
            'concentration_value',
            'concentration_unit',
            'concentration_status',
        ]);

        $rowCount = 0;
        foreach ($variants as $variant) {
            $formula = $variant->activeFormula;
            $ingredients = $formula?->formulaIngredients ?? collect();

            if ($ingredients->isEmpty()) {
                fputcsv($handle, [
                    $dataset?->version,
                    $dataset?->hash,
                    $variant->catalog_code,
                    $variant->brand?->name,
                    $variant->name,
                    $variant->status,
                    $variant->is_active ? '1' : '0',
                    $formula?->version_label,
                    $formula?->status,
                    $formula?->bpom_number,
                    $formula?->verification_status,
                    $formula?->inci_raw,
                ]);
                $rowCount++;

                continue;
            }

            foreach ($ingredients as $formulaIngredient) {
                $ingredient = $formulaIngredient->ingredient;
                fputcsv($handle, [
                    $dataset?->version,
                    $dataset?->hash,
                    $variant->catalog_code,
                    $variant->brand?->name,
                    $variant->name,
                    $variant->status,
                    $variant->is_active ? '1' : '0',
                    $formula?->version_label,
                    $formula?->status,
                    $formula?->bpom_number,
                    $formula?->verification_status,
                    $formula?->inci_raw,
                    $formulaIngredient->ordinal,
                    $ingredient?->id,
                    $ingredient?->inci_name,
                    $ingredient?->display_name,
                    $ingredient?->function_group,
                    $ingredient?->is_fragrance ? '1' : '0',
                    $ingredient?->is_menthol ? '1' : '0',
                    $ingredient?->is_physical_scrub ? '1' : '0',
                    $ingredient?->is_exfoliant ? '1' : '0',
                    $formulaIngredient->concentration_value,
                    $formulaIngredient->concentration_unit,
                    $formulaIngredient->concentration_status,
                ]);
                $rowCount++;
            }
        }

        fclose($handle);

        $this->info("Ekspor selesai: {$path}");
        $this->info("Variant: {$variants->count()}; baris ingredient: {$rowCount}; dataset: ".($dataset?->version ?? '—'));

        return self::SUCCESS;
    }
}
