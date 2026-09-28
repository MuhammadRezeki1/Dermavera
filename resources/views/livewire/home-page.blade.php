<div data-motion-root="home">
    <section class="hero shell">
        <div class="hero-copy">
            <span class="eyebrow"><i></i> SKINCARE DECISION SYSTEM</span>
            <h1 data-hero-title>Bukan sekadar bersih.<br><em>Temukan yang sesuai.</em></h1>
            <p class="hero-lead">Rekomendasi facial wash pria berbasis kondisi kulit, keamanan formula, dan data yang dapat kamu periksa sendiri.</p>
            <p class="hero-scope">Untuk populasi penelitian pria usia 18–30 tahun. Rentang ini bukan batas usia wajib memakai facial wash.</p>
            <div class="actions"><a class="button button-primary button-lg" href="{{ route('consultation.consent') }}" wire:navigate>Mulai analisis kulit <span aria-hidden="true">→</span></a><a class="text-link" href="{{ route('method') }}" wire:navigate>Lihat cara kerja <span aria-hidden="true">↗</span></a></div>
            @guest
                <a class="hero-account-link" href="{{ route('login') }}" wire:navigate>Sudah punya akun? <span>Masuk untuk menyimpan riwayat</span></a>
            @else
                <a class="hero-account-link" href="{{ route('history') }}" wire:navigate>Lihat kembali rekomendasi tersimpan <span>→</span></a>
            @endguest
            <div class="hero-proof"><span><b>37</b> produk diaudit</span><span><b>6</b> kriteria SAW</span><span><b>35</b> alternatif aktif</span></div>
        </div>
        <x-hero-product-showcase :products="$heroProducts" />
    </section>

    <section class="metric-band" aria-label="Ringkasan data Dermavera"><div class="shell metric-grid"><div data-reveal-group="metrics"><strong>37</strong><span>record produk</span></div><div data-reveal-group="metrics"><strong>35</strong><span>varian aktif</span></div><div data-reveal-group="metrics"><strong>C1—C6</strong><span>kriteria transparan</span></div><div data-reveal-group="metrics"><strong>SAW</strong><span>metode keputusan</span></div></div></section>

    <section class="section shell concerns-section" aria-labelledby="concern-title">
        <div class="section-index" data-reveal-group="concern-heading">01 / KEBUTUHAN KULIT</div>
        <div class="section-heading split-heading" data-reveal-group="concern-heading"><div><span class="eyebrow"><i></i> MULAI DARI KONDISIMU</span><h2 id="concern-title">Kulit punya cara<br><em>sendiri untuk bicara.</em></h2></div><p>Pilih keluhan utama saat konsultasi. Kondisi tambahan tetap dibaca sebagai modifier agar hasil tidak menyederhanakan profilmu.</p></div>
        <div class="concern-grid">
            @foreach([
                ['Berminyak','Kontrol sebum','M9 1C7 4 3 7 3 11a6 6 0 0 0 12 0C15 7 11 4 9 1Z M6 11c0 2 1 3 3 3'],
                ['Jerawat ringan','Dukungan acne','M8 1c4 0 7 4 7 8 0 4-3 6-7 6S1 12 5 15c4 3 9 1 10-3 2-3-1-9-7-11 1-2 4-1 6-1Z'],
                ['Komedo','Kebersihan pori','M3 3h12v12H3zM6 0v6m6-6v6M0 6h18'],
                ['Kering & tertarik','Humektan & lembut','M9 1S3 7 3 11a6 6 0 0 0 12 0C15 7 9 1 9 1Z'],
                ['Kusam','Brightening konservatif','M9 0v3m0 12v3M0 9h3m12 0h3M2.6 2.6l2.1 2.1m8.6 8.6 2.1 2.1m0-12.8-2.1 2.1M4.7 13.3l-2.1 2.1M13 9a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z']
            ] as [$name,$copy,$path])
            <a class="concern-card" data-reveal-group="cards" href="{{ route('consultation.consent') }}" wire:navigate><span class="concern-no">0{{ $loop->iteration }}</span><svg viewBox="0 0 18 18" aria-hidden="true"><path d="{{ $path }}"/></svg><div><strong>{{ $name }}</strong><small>{{ $copy }}</small></div><span class="arrow">↗</span></a>
            @endforeach
        </div>
    </section>

    <section class="section process-section"><div class="shell">
        <div class="section-index" data-reveal-group="process-heading">02 / PROSES</div>
        <div class="section-heading split-heading light" data-reveal-group="process-heading"><div><span class="eyebrow"><i></i> CARA KERJA</span><h2>Keamanan dahulu.<br><em>Peringkat kemudian.</em></h2></div><p>Setiap hasil bergerak melalui jalur keputusan yang sama. Risiko tidak pernah ditukar dengan harga murah atau kemasan yang praktis.</p></div>
        <div class="steps">@foreach([['01','Profil yang relevan','Usia, keluhan, jenis kulit, sensitivitas, alergi, dan terapi.'],['02','Safety gate','Red flag dan konflik formula dipisahkan sebelum kalkulasi.'],['03','Hasil yang terbuka','Top 3, C1–C6, harga bertanggal, BPOM, sumber, dan snapshot.']] as [$n,$title,$copy])<article data-reveal-group="steps"><span class="step-number">{{ $n }}</span><div class="step-symbol">{{ $loop->iteration === 1 ? '◎' : ($loop->iteration === 2 ? '◇' : '≋') }}</div><h3>{{ $title }}</h3><p>{{ $copy }}</p></article>@endforeach</div>
    </div></section>

    <section class="section shell featured-section">
        <div class="section-index" data-reveal-group="catalog-heading">03 / KATALOG</div>
        <div class="section-heading row-heading" data-reveal-group="catalog-heading"><div><span class="eyebrow"><i></i> PILIHAN TERVERIFIKASI</span><h2>Kenali produk.<br><em>Baca buktinya.</em></h2></div><a class="text-link" href="{{ route('catalog') }}" wire:navigate>Lihat 37 produk <span>→</span></a></div>
        <div class="product-grid home-products">@foreach($featured as $product)<a class="product-card" data-reveal-group="products" href="{{ route('products.show',$product) }}" wire:navigate><span class="product-code">{{ $product->catalog_code }}</span><div class="product-media"><x-product-image :variant="$product" /></div><div class="product-card-body"><small>{{ strtoupper($product->brand->name) }}</small><h3>{{ $product->name }}</h3><span class="evidence-badge">INCI TERDOKUMENTASI</span></div></a>@endforeach</div>
    </section>

    <section class="section shell"><div class="transparency-panel" data-reveal-group="transparency"><span class="panel-mark">i</span><div><span class="eyebrow">CATATAN TRANSPARANSI</span><h2>Data yang tidak diketahui<br><em>tetap kami sebut tidak diketahui.</em></h2></div><p>Dermavera tidak menebak pH, konsentrasi bahan, atau kecocokan absolut. Formula dan harga dapat berubah—karena itu versi, sumber, dan tanggal observasi selalu menyertai hasil.</p><a href="{{ route('method') }}" wire:navigate>Pelajari metodologi →</a></div></section>
</div>
