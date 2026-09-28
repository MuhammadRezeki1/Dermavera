<div class="page product-page shell">
    <a href="{{ route('catalog') }}" wire:navigate class="back-link">← Kembali ke katalog</a>
    <header class="product-detail-header">
        <div class="product-detail-media"><span class="product-code">{{ $variant->catalog_code }}</span><x-product-image :variant="$variant" :eager="true" /></div>
        <div class="product-detail-copy">
            <span class="eyebrow"><i></i> {{ strtoupper($variant->brand->name) }}</span>
            <h1>{{ $variant->name }}</h1>
            <p>{{ $variant->target_claim }}</p>
            <div class="detail-badges"><span>✓ Formula aktif</span><span>✓ Produk bilas</span><span>✓ Sumber terlacak</span></div>
            <div class="actions"><a class="button button-primary" href="{{ route('consultation.consent') }}" wire:navigate>Cek kecocokan produk →</a><a class="text-link" href="{{ config('product-images.'.$variant->catalog_code.'.source') }}" target="_blank" rel="noopener noreferrer">Sumber foto ↗</a></div>
        </div>
    </header>

    <div class="detail-grid">
        <section class="content-card formula-card">
            <div class="card-kicker">FORMULA & INCI</div>
            <h2>Komposisi yang terdokumentasi</h2>
            @foreach ($variant->formulaVersions as $formula)
                <article class="formula">
                    <div class="formula-heading"><div><strong>{{ $formula->version_label }}</strong><small>{{ $formula->package_size }} · BPOM {{ $formula->bpom_number }}</small></div><span>{{ strtoupper($formula->status) }}</span></div>
                    <p>{{ $formula->inci_raw }}</p>
                    <div class="limitation"><b>BATAS DATA</b><span>pH: {{ $formula->ph_min ?? 'UNKNOWN' }}</span><span>Konsentrasi tidak ditebak dari urutan INCI.</span></div>
                </article>
            @endforeach
        </section>

        <aside>
            <section class="content-card price-card">
                <div class="card-kicker">SKU & HARGA</div><h2>Observasi terbaru</h2>
                @foreach ($variant->skus as $sku)
                    @php($price = $sku->latestPrice)
                    <div class="sku-price"><div><strong>{{ rtrim(rtrim($sku->size_value, '0'), '.') }} {{ $sku->size_unit }}</strong><small>BPOM {{ $sku->bpom_no }}</small></div>
                        @if ($price)
                            <span class="price-value">Rp{{ number_format((float) $price->amount, 0, ',', '.') }}</span>
                            <small>Normal Rp{{ number_format((float) $price->normal_price, 0, ',', '.') }} · Rp{{ number_format((float) $price->unit_price_per_100, 0, ',', '.') }}/100</small>
                            <small>Diamati {{ $price->observed_at->format('d M Y') }}</small>
                            <span class="evidence-badge {{ $price->ranking_eligible ? '' : 'muted' }}">{{ $price->ranking_eligible ? 'LAYAK RANKING' : 'RIWAYAT / TIDAK LAYAK' }}</span>
                            <a href="{{ $price->source->url }}" target="_blank" rel="noopener noreferrer">{{ $price->seller }} ↗</a>
                        @else<small>Belum ada observasi harga untuk ukuran ini.</small>@endif
                    </div>
                @endforeach
            </section>
            <section class="content-card source-card"><div class="card-kicker">JEJAK SUMBER</div><h2>Bukti formula</h2>@foreach ($variant->formulaVersions->unique('source_id') as $formula)@if ($formula->source)<a href="{{ $formula->source->url }}" target="_blank" rel="noopener noreferrer">{{ $formula->source->title }} ↗</a><small>Diakses {{ $formula->source->accessed_at->format('d M Y') }}</small>@endif @endforeach</section>
        </aside>
    </div>
    <div class="notice"><div aria-hidden="true">i</div><div><h2>Interpretasi yang hati-hati</h2><p>Keberadaan satu bahan tidak otomatis membuktikan efektivitas atau kecocokan. Formula dinilai sebagai produk bilas dan selalu dikaitkan dengan profil pengguna. Harga adalah observasi bertanggal, bukan harga tetap.</p></div></div>
</div>
