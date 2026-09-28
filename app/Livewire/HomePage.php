<?php

namespace App\Livewire;

use App\Models\ProductVariant;
use Illuminate\View\View;
use Livewire\Component;

class HomePage extends Component
{
    public function render(): View
    {
        $showcase = ProductVariant::with(['brand', 'skus', 'activeFormula'])
            ->where('is_active', true)
            ->orderBy('id')
            ->get();

        $heroProducts = $showcase->map(function (ProductVariant $product): array {
            $sku = $product->skus->firstWhere('is_active', true) ?? $product->skus->first();
            $formula = $product->activeFormula;
            $image = config('product-images.'.$product->catalog_code);
            $size = 'Ukuran tidak tercatat';

            if ($sku) {
                $value = rtrim(rtrim(number_format((float) $sku->size_value, 2, '.', ''), '0'), '.');
                $size = $value.' '.$sku->size_unit;
            }

            return [
                'code' => $product->catalog_code,
                'brand' => strtoupper($product->brand->name),
                'name' => $product->name,
                'size' => $size,
                'status' => $formula?->bpom_number
                    ? 'BPOM '.$formula->bpom_number
                    : str_replace('_', ' ', $product->evidence_status),
                'src' => asset('images/product-cutout/'.pathinfo($image['file'], PATHINFO_FILENAME).'.png'),
                'alt' => $product->name.' — kemasan depan',
            ];
        })->values();

        return view('livewire.home-page', [
            'featured' => $showcase->take(4),
            'heroProducts' => $heroProducts,
        ])->layout('layouts.public', ['title' => 'Dermavera — Rekomendasi Facial Wash']);
    }
}
