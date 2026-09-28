<div class="page shell result-page" data-motion-root="results">
    @php
        $criterionLabels = ['C1' => 'Keluhan utama', 'C2' => 'Kondisi kulit', 'C3' => 'Kualitas formula', 'C4' => 'Harga unit', 'C5' => 'Kemasan', 'C6' => 'Bukti & keamanan'];
    @endphp
    @if ($consultation->safetyAssessment->outcome === 'refer')
        <section class="safety-banner danger" role="alert" tabindex="-1"><span aria-hidden="true">!</span><div><small>SAFETY GATE · RUJUKAN</small><h1>Keluhan memerlukan evaluasi tenaga kesehatan.</h1><p>Ranking produk tidak ditampilkan karena jawaban memuat red flag. Dermavera tidak mendiagnosis; pertimbangkan pemeriksaan dokter kulit.</p></div></section>
    @else
        <section class="result-intro">
            <div class="safety-banner safe"><span aria-hidden="true">✓</span><div><strong>Lolos pemeriksaan awal</strong><p>Alternatif berisiko telah dikeluarkan sebelum kalkulasi.</p></div></div>
            <header><span class="eyebrow"><i></i> HASIL PERSONAL</span><h1>{{ $top->isEmpty() ? 'Belum ada alternatif yang dapat diranking.' : 'Pilihan yang paling dekat dengan profilmu.' }}</h1><p>Dataset {{ $consultation->datasetVersion?->version }} · Algoritma {{ $consultation->algorithm_version }} · {{ $consultation->created_at->format('d M Y, H:i') }} WIB</p></header>
        </section>

        @php
            $input = $consultation->input_snapshot ?? [];
            $complaintLabels = ['berminyak' => 'Minyak berlebih', 'komedo' => 'Komedo', 'jerawat_ringan' => 'Jerawat ringan', 'kusam' => 'Kusam', 'kering' => 'Kering / tertarik'];
            $skinLabels = ['normal' => 'Normal', 'berminyak' => 'Berminyak', 'kering' => 'Kering', 'kombinasi' => 'Kombinasi'];
            $concernLabels = ['berminyak' => 'Berminyak', 'komedo' => 'Komedo', 'jerawat_ringan' => 'Jerawat ringan', 'kusam' => 'Kusam', 'kering' => 'Kering'];
            $yesNo = fn ($value) => $value ? 'Ya' : 'Tidak';
        @endphp
        <section class="result-input-card" data-result-card aria-labelledby="result-input-title">
            <div class="result-input-heading"><span class="eyebrow"><i></i> PROFIL TERSIMPAN</span><h2 id="result-input-title">Input konsultasi yang dipakai.</h2><p>Profil ini disimpan bersama hasil agar riwayat dapat dibaca ulang dengan konteks yang sama.</p></div>
            <div class="result-input-grid">
                <div><small>Usia</small><strong>{{ $input['age'] ?? '—' }} tahun</strong></div>
                <div><small>Keluhan utama</small><strong>{{ $complaintLabels[$input['primary_complaint'] ?? ''] ?? ($input['primary_complaint'] ?? '—') }}</strong></div>
                <div><small>Jenis kulit</small><strong>{{ $skinLabels[$input['skin_type'] ?? ''] ?? ($input['skin_type'] ?? '—') }}</strong></div>
                <div><small>Keluhan tambahan</small><strong>{{ collect($input['secondary_concerns'] ?? [])->map(fn ($item) => $concernLabels[$item] ?? $item)->join(', ') ?: 'Tidak ada' }}</strong></div>
                <div><small>Kulit sensitif</small><strong>{{ $yesNo($input['sensitive'] ?? false) }}</strong></div>
                <div><small>Barrier terganggu</small><strong>{{ $yesNo($input['barrier_impaired'] ?? false) }}</strong></div>
                <div><small>Terapi acne</small><strong>{{ $yesNo($input['acne_therapy'] ?? false) }}</strong></div>
                <div><small>Rentang harga</small><strong>Rp{{ number_format((float) ($input['min_price'] ?? 0), 0, ',', '.') }} – Rp{{ number_format((float) ($input['max_price'] ?? 0), 0, ',', '.') }}</strong></div>
                <div><small>Alergi tercatat</small><strong>{{ collect($input['allergies'] ?? [])->join(', ') ?: 'Tidak ada' }}</strong></div>
                <div><small>Preferensi kemasan</small><strong>{{ $input['packaging_preference'] ?: 'Tidak ada' }}</strong></div>
            </div>
        </section>

        @guest
            <section class="save-result-card" data-result-card aria-labelledby="save-result-title">
                <div><span class="eyebrow"><i></i> HASIL SUDAH SIAP</span><h2 id="save-result-title">Simpan hasil ini ke riwayatmu?</h2><p>Kamu sudah bisa melihat hasil tanpa login. Masuk atau daftar untuk membuka hasil ini kembali dari perangkat lain.</p></div>
                <div class="save-result-actions"><a class="button button-primary" href="{{ route('results.claim.start', $consultation) }}">Masuk / daftar dan simpan</a><span>Tidak perlu menghitung ulang.</span></div>
            </section>
        @endguest

        @if ($top->isEmpty())
            <section class="empty-state content-card"><span>∅</span><h2>Data belum cukup untuk ranking yang aman</h2><p>Produk tidak dipaksakan masuk hasil. Penyebabnya dapat berupa harga yang tidak segar, formula tidak lengkap, atau semua alternatif bertentangan dengan hard constraint.</p><a class="button button-secondary" href="{{ route('catalog') }}">Telusuri katalog</a></section>
        @else
            @php
                $winner = $top->first();
                $singleCandidate = $eligible->count() === 1;
                $winnerIsTied = ($winner->tie_count ?? 1) > 1;
                $scoreLabel = $singleCandidate ? 'SKOR RELATIF' : 'SKOR AKHIR';
            @endphp
            <article class="winner-card" data-result-card>
                <div class="winner-media"><span class="winner-rank">PILIHAN #1{{ $winnerIsTied ? ' · SERI' : '' }}</span><x-product-image :variant="$winner->variant" :eager="true" /></div>
                <div class="winner-copy"><span class="eyebrow"><i></i> {{ strtoupper($winner->variant->brand->name) }}</span><h2>{{ $winner->variant->name }}</h2><p class="winner-reason">{{ $singleCandidate ? 'Satu-satunya alternatif yang lolos pemeriksaan dan dapat dihitung.' : ($winnerIsTied ? 'Skornya seri dengan kandidat lain; urutan berikutnya memakai tie-break deterministik.' : 'Pilihan teratas setelah safety gate dan normalisasi enam kriteria.') }}</p>
                    <div class="winner-score"><div><span data-score="{{ $winner->final_score }}">{{ number_format((float)$winner->final_score, 4, ',', '.') }}</span><small>{{ $scoreLabel }}</small></div><div><strong>{{ $winner->sku ? rtrim(rtrim(number_format((float)$winner->sku->size_value, 2, ',', '.'), '0'), ',').' '.$winner->sku->size_unit : '—' }}</strong><small>UKURAN</small></div><div><strong>{{ $winner->price ? 'Rp'.number_format((float)$winner->price->amount, 0, ',', '.') : '—' }}</strong><small>HARGA OBSERVASI</small></div></div>
                    @if ($singleCandidate)<div class="notice"><div aria-hidden="true">i</div><div><strong>Skor ini bersifat relatif.</strong><p>Hanya satu kandidat yang lolos, sehingga nilai 1,0000 tidak dapat dibandingkan dengan produk lain dan bukan persentase kecocokan absolut.</p></div></div>@endif
                    <div class="criterion-list" aria-label="Kontribusi kriteria pemenang">@foreach($winner->contributions as $code => $value)<div><span><strong>{{ $code }} · {{ $criterionLabels[$code] ?? $code }}</strong><small>{{ number_format((float)$value, 4, ',', '.') }}</small></span><div class="criterion-track"><i data-width="{{ min(100,(float)$value*500) }}" style="width:{{ min(100,(float)$value*500) }}%"></i></div></div>@endforeach</div>
                    <details open><summary>Mengapa dipilih?</summary><ul>@foreach($explanations->resolve($winner->explanation_codes) as $reason)<li>{{ $reason['text'] }}</li>@endforeach</ul>@if($winner->warnings)<h3>Perhatian</h3><ul>@foreach($explanations->resolve($winner->warnings) as $warning)<li>{{ $warning['text'] }}</li>@endforeach</ul>@endif</details>
                    <div class="result-source-row"><span>BPOM <b>{{ $winner->formula?->bpom_number ?? '—' }}</b></span><span>Formula <b>{{ $winner->formula?->verification_status ?? '—' }}</b></span>@if($winner->formula?->source?->url)<a href="{{ $winner->formula->source->url }}" target="_blank" rel="noopener noreferrer">Buka sumber ↗</a>@endif</div>
                </div>
            </article>

            <section class="runner-section"><div class="section-heading row-heading"><div><span class="eyebrow"><i></i> ALTERNATIF KUAT</span><h2>{{ $top->count() > 1 ? 'Dua pilihan berikutnya.' : 'Belum ada runner-up aman.' }}</h2></div><p>{{ $top->count() > 1 ? 'Skor berdekatan tidak berarti produknya identik. Baca formula, harga, dan catatan tiap alternatif.' : 'Safety gate hanya meloloskan satu alternatif untuk profil ini. Kandidat lain dapat dilihat pada daftar eksklusi di bawah.' }}</p></div>
                <div class="runner-grid">@foreach($top->skip(1) as $result)<article class="runner-card" data-result-card><span class="rank">#{{ $result->rank }}{{ ($result->tie_count ?? 1) > 1 ? ' · SERI' : '' }}</span><div class="runner-product"><x-product-image :variant="$result->variant" /><div><small>{{ strtoupper($result->variant->brand->name) }}</small><h3>{{ $result->variant->name }}</h3></div></div><div class="runner-score"><strong data-score="{{ $result->final_score }}">{{ number_format((float)$result->final_score,4,',','.') }}</strong><span>{{ $scoreLabel }}</span></div><div class="runner-meta"><span>{{ $result->price ? 'Rp'.number_format((float)$result->price->amount,0,',','.') : 'Harga —' }}</span><span>BPOM {{ $result->formula?->bpom_number ?? '—' }}</span></div><details><summary>Lihat alasan & C1–C6</summary><div class="criterion-list">@foreach($result->contributions as $code=>$value)<div><span><strong>{{ $code }}</strong><small>{{ $criterionLabels[$code] ?? $code }} · {{ number_format((float)$value,4,',','.') }}</small></span><div class="criterion-track"><i data-width="{{ min(100,(float)$value*500) }}" style="width:{{ min(100,(float)$value*500) }}%"></i></div></div>@endforeach</div><ul>@foreach($explanations->resolve($result->explanation_codes) as $reason)<li>{{ $reason['text'] }}</li>@endforeach</ul></details></article>@endforeach</div>
            </section>

            <section class="comparison-card"><div><span class="eyebrow"><i></i> PERBANDINGAN CEPAT</span><h2>Top 3 dalam satu pandangan.</h2></div><div class="table-wrap"><table><thead><tr><th>Produk</th><th>Skor</th><th>Ukuran</th><th>Harga</th><th>BPOM</th><th>Bukti</th></tr></thead><tbody>@foreach($top as $result)<tr><td><b>#{{ $result->rank }} {{ $result->variant->name }}</b><small>{{ $result->variant->brand->name }}</small></td><td>{{ number_format((float)$result->final_score,4,',','.') }}</td><td>{{ $result->sku ? rtrim(rtrim(number_format((float)$result->sku->size_value,2,',','.'),'0'),',').' '.$result->sku->size_unit : '—' }}</td><td>{{ $result->price ? 'Rp'.number_format((float)$result->price->amount,0,',','.') : '—' }}</td><td>{{ $result->formula?->bpom_number ?? '—' }}</td><td>@if($result->formula?->source?->url)<a href="{{ $result->formula->source->url }}" target="_blank" rel="noopener noreferrer">Sumber ↗</a>@else—@endif</td></tr>@endforeach</tbody></table></div></section>

            @if ($eligible->count() > 3)
                <section class="eligible-section" data-result-card aria-labelledby="eligible-title">
                    <div class="section-heading row-heading">
                        <div><span class="eyebrow"><i></i> KANDIDAT LAYAK</span><h2 id="eligible-title">Pilihan lain yang tetap sesuai profil.</h2></div>
                        <p><strong>{{ $eligible->count() }} produk</strong> lolos pemeriksaan keamanan dan ikut dihitung dengan SAW. Top 3 sudah ditampilkan di atas; daftar ini memuat {{ $eligible->count() - 3 }} kandidat layak berikutnya berdasarkan peringkat.</p>
                    </div>
                    <div class="eligible-list" role="list" aria-label="Semua kandidat layak selain Top 3">
                        @foreach($eligible->skip(3) as $result)
                            <article class="eligible-row" role="listitem">
                                <div class="eligible-rank"><span>#{{ $result->rank ?? $loop->iteration + 3 }}</span><small>PERINGKAT</small></div>
                                <div class="eligible-product"><x-product-image :variant="$result->variant" /><div><small>{{ strtoupper($result->variant->brand->name) }}</small><h3>{{ $result->variant->name }}</h3><span>{{ $result->formula?->verification_status === 'verified' ? 'Formula terverifikasi' : 'Formula tercatat' }}</span></div></div>
                                <div class="eligible-score"><strong>{{ number_format((float)$result->final_score,4,',','.') }}</strong><small>SKOR SAW</small></div>
                                <div class="eligible-meta"><span>{{ $result->price ? 'Rp'.number_format((float)$result->price->amount,0,',','.') : 'Harga —' }}</span><span>{{ $result->sku ? rtrim(rtrim(number_format((float)$result->sku->size_value,2,',','.'),'0'),',').' '.$result->sku->size_unit : 'Ukuran —' }}</span><span>BPOM {{ $result->formula?->bpom_number ?? '—' }}</span></div>
                                <div class="eligible-action">@if($result->formula?->source?->url)<a href="{{ $result->formula->source->url }}" target="_blank" rel="noopener noreferrer">Bukti ↗</a>@endif</div>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif
        @endif
    @endif

    @php
        $excludedReasons = ['HC-01' => 'Alergen cocok dengan profil', 'HC-02' => 'Scrub fisik tidak cocok untuk kulit sensitif / barrier terganggu', 'HC-03' => 'Konteks beberapa bahan iritan terlalu tinggi', 'HC-04' => 'Perlu peninjauan iritasi selama terapi acne', 'HC-05' => 'Bukti formula belum cukup', 'HC-06' => 'BPOM belum terverifikasi', 'physical_scrub_conflict' => 'Mengandung scrub fisik', 'multiple_irritant_context' => 'Kombinasi iritan, pewangi, atau menthol', 'therapy_irritation_review' => 'Potensi iritasi saat terapi acne', 'insufficient_formula_evidence' => 'Formula belum tervalidasi', 'bpom_not_verified' => 'Nomor BPOM belum tersedia / tervalidasi'];
        $excludedResults = $run?->results->where('eligibility_status', 'excluded') ?? collect();
        $excludedReasons += ['above_max_budget' => 'Harga di atas batas maksimum', 'below_min_budget' => 'Harga di bawah batas minimum', 'stale_price' => 'Harga sudah melewati masa kesegaran data', 'ineligible_price_evidence' => 'Bukti harga belum layak untuk ranking', 'missing_fresh_price' => 'Belum ada harga segar untuk ranking'];
    @endphp
    @if ($excludedResults->isNotEmpty())
        <section class="excluded-panel" data-result-card aria-labelledby="excluded-title">
            <div class="excluded-heading"><span class="eyebrow"><i></i> SAFETY GATE</span><h2 id="excluded-title">Mengapa pilihan lain tidak masuk ranking?</h2><p>{{ $excludedResults->count() }} kandidat diperiksa, tetapi tidak diberi ranking karena bertentangan dengan input keamananmu. Kandidat hanya ditampilkan di sini untuk menjelaskan alasan safety gate dan tidak menjadi rekomendasi.</p></div>
            <details class="excluded-details"><summary>Lihat {{ $excludedResults->count() }} kandidat yang dikeluarkan beserta foto</summary><div class="excluded-grid">@foreach($excludedResults as $excluded)<article><x-product-image :variant="$excluded->variant" class="excluded-product-image" /><div><span>{{ $excluded->variant->catalog_code }}</span><small>{{ strtoupper($excluded->variant->brand->name) }}</small></div><strong>{{ $excluded->variant->name }}</strong><p>{{ collect($excluded->explanation_codes ?? [])->map(fn ($reason) => $excludedReasons[$reason] ?? $reason)->join(' · ') }}</p><em class="excluded-status">TIDAK MASUK RANKING</em></article>@endforeach</div></details>
        </section>
    @endif

    <section class="audit-card content-card"><div><span class="card-kicker">SNAPSHOT & AUDIT</span><h2>Hasil ini dapat ditelusuri kembali.</h2><p>Input, kandidat layak, alasan eksklusi, matriks X, normalisasi R, bobot W, kontribusi, skor V, tie-break, dataset, dan algoritma disimpan pada run ini.</p></div><div class="audit-stats"><span><b>{{ count($run?->calculation_snapshot['eligible_variants'] ?? []) }}</b> kandidat layak</span><span><b>{{ $excludedResults->count() }}</b> dikeluarkan</span></div><div class="actions"><a class="button button-secondary" href="{{ route('results.csv',$consultation) }}">Ekspor CSV</a><a class="button button-primary" href="{{ route('results.pdf',$consultation) }}">Ekspor PDF</a></div></section>
    <div class="notice"><div aria-hidden="true">i</div><div><h2>Bukan diagnosis</h2><p>Hasil adalah dukungan keputusan kosmetik, bukan jaminan produk pasti cocok. Lakukan uji tempel, hentikan pemakaian bila timbul reaksi, dan periksa kembali label serta harga.</p></div></div>
</div>
