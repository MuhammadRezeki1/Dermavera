<?php

namespace App\Livewire;

use App\Models\ProductVariant;
use Illuminate\View\View;
use Livewire\Component;

class ProductDetailPage extends Component
{
    public ProductVariant $variant;

    public function mount(ProductVariant $variant): void
    {
        abort_unless($variant->is_active, 404);
        $this->variant = $variant;
    }

    public function render(): View
    {
        $this->variant->load(['brand', 'formulaVersions.source', 'formulaVersions.formulaIngredients.ingredient', 'skus.latestPrice.source', 'evidence']);

        return view('livewire.product-detail-page')->layout('layouts.public', ['title' => $this->variant->name]);
    }
}
