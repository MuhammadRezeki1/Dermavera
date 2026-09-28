<div class="page catalog-page" data-motion-root="catalog">
    <header class="catalog-masthead"><div class="shell"><span class="eyebrow"><i></i> KATALOG BERBASIS BUKTI</span><div class="catalog-title-row"><h1>Temukan formula<br><em>yang masuk akal.</em></h1><p>Telusuri produk dengan foto asli, formula terdokumentasi, harga observasi, dan jejak sumber yang dapat dibuka.</p></div></div></header>

    <div class="shell catalog-layout">
        <aside class="filter-panel" aria-label="Filter katalog">
            <div class="filter-heading"><strong>FILTER</strong><span>{{ $products->total() }} hasil</span></div>
            <label class="search-field"><span>Cari produk</span><div><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg><input type="search" wire:model.live.debounce.300ms="search" placeholder="Nama atau kode"></div></label>
            <label><span>Merek</span><select wire:model.live="brand"><option value="">Semua merek</option>@foreach ($brands as $brandOption)<option value="{{ $brandOption->slug }}">{{ $brandOption->name }}</option>@endforeach</select></label>
            <label><span>Kebutuhan kulit</span><select wire:model.live="concern"><option value="">Semua kebutuhan</option><option value="acne">Jerawat</option><option value="oil">Minyak & komedo</option><option value="bright">Kusam & noda</option><option value="gentle">Lembut / sensitif</option></select></label>
            <label><span>Bukti formula</span><select wire:model.live="status"><option value="">Semua status</option><option value="VERIFIED_OFFICIAL_INCI">Official INCI</option><option value="VALIDATED_ID_FULL_INCI">Validated ID</option><option value="VALIDATED_ID_VERSIONED">Versioned</option></select></label>
            <label><span>Harga maksimum</span><select wire:model.live="maxPrice"><option value="">Semua harga</option><option value="30000">≤ Rp30.000</option><option value="40000">≤ Rp40.000</option><option value="50000">≤ Rp50.000</option><option value="75000">≤ Rp75.000</option></select></label>
            <p class="filter-note">Harga merupakan observasi bertanggal dan dapat berubah.</p>
        </aside>

        <section class="catalog-results" aria-live="polite" aria-busy="{{ $products->isEmpty() ? 'false' : 'false' }}">
            <div class="catalog-toolbar"><span><b>{{ $products->total() }}</b> produk aktif</span><span>Diurutkan berdasarkan kode audit</span></div>
            <div class="catalog-loading" wire:loading.flex wire:target="search,brand,status,concern,maxPrice"><span></span><span></span><span></span></div>
            <div class="product-grid catalog-grid" wire:loading.class="is-loading" wire:target="search,brand,status,concern,maxPrice">
                @forelse ($products as $product)
                    @php($prices = $product->skus->pluck('latestPrice')->filter())
                    <a class="product-card" href="{{ route('products.show', $product) }}" wire:navigate wire:key="product-{{ $product->id }}">
                        <span class="product-code">{{ $product->catalog_code }}</span>
                        <div class="product-media"><x-product-image :variant="$product" /></div>
                        <div class="product-card-body">
                            <div class="product-brand-row"><small>{{ strtoupper($product->brand->name) }}</small><span>{{ $product->skus->map(fn ($sku) => rtrim(rtrim($sku->size_value, '0'), '.').' '.$sku->size_unit)->join(' / ') }}</span></div>
                            <h2>{{ $product->name }}</h2>
                            <p class="skin-fit">{{ $product->target_claim ?: 'Profil kecocokan tersedia di detail produk.' }}</p>
                            <div class="product-price-row">@if($prices->isNotEmpty())<strong>Rp{{ number_format((float) $prices->min('amount'), 0, ',', '.') }}</strong><small>observasi {{ $prices->max('observed_at')->format('d M Y') }}</small>@else<strong>Harga belum tersedia</strong>@endif</div>
                            <div class="product-proof"><span>BPOM {{ $product->activeFormula?->bpom_number ?? '—' }}</span><span>{{ str_contains($product->evidence_status, 'VERIFIED') ? 'INCI resmi' : 'INCI tervalidasi' }}</span></div>
                        </div>
                    </a>
                @empty
                    <div class="empty-state"><span>∅</span><h2>Belum ada produk yang cocok dengan filter</h2><p>Coba ubah merek, kebutuhan, status formula, atau rentang harga.</p></div>
                @endforelse
            </div>
            <div class="pagination">{{ $products->links() }}</div>
        </section>
    </div>
</div>
