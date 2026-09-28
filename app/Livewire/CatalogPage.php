<?php

namespace App\Livewire;

use App\Models\Brand;
use App\Models\ProductVariant;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class CatalogPage extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $brand = '';

    #[Url]
    public string $status = '';

    #[Url]
    public string $concern = '';

    #[Url]
    public ?int $maxPrice = null;

    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'brand', 'status', 'concern', 'maxPrice'], true)) {
            $this->resetPage();
            $this->dispatch('catalog-updated');
        }
    }

    public function render(): View
    {
        $products = ProductVariant::with(['brand', 'activeFormula', 'skus.latestPrice'])->where('is_active', true)
            ->when($this->search, fn ($q) => $q->where(fn ($x) => $x->where('name', 'like', '%'.$this->search.'%')->orWhere('catalog_code', 'like', '%'.$this->search.'%')))
            ->when($this->brand, fn ($q) => $q->whereHas('brand', fn ($x) => $x->where('slug', $this->brand)))
            ->when($this->status, fn ($q) => $q->where('evidence_status', $this->status))
            ->when($this->concern, fn ($q) => $q->where(fn ($x) => $x
                ->where('target_claim', 'like', '%'.$this->concern.'%')
                ->orWhere('name', 'like', '%'.$this->concern.'%')))
            ->when($this->maxPrice, fn ($q) => $q->whereHas('skus.prices', fn ($x) => $x->where('amount', '<=', $this->maxPrice)))
            ->orderBy('catalog_code')->paginate(12);

        return view('livewire.catalog-page', ['products' => $products, 'brands' => Brand::where('is_active', true)->orderBy('name')->get()])->layout('layouts.public', ['title' => 'Katalog Facial Wash']);
    }
}
