<div class="page shell" data-motion-root="method">
    <header class="page-header">
        <span class="eyebrow"><i></i> METODOLOGI TERBUKA</span>
        <h1>Safety gate dahulu.<br><em>SAW setelahnya.</em></h1>
        <p>Risiko tidak pernah dikompensasi oleh harga atau kemasan. Hanya alternatif yang lolos pemeriksaan keamanan yang masuk ke matriks pemeringkatan.</p>
    </header>

    <section class="pipeline" aria-label="Tahapan perhitungan">
        @foreach(['Validasi input','Safety gate','Matriks X','Normalisasi R','Ranking V'] as $label)
            <div><strong>{{ $loop->iteration }}</strong><span>{{ $label }}</span></div>
        @endforeach
    </section>

    <section class="content-card">
        <span class="card-kicker">VERSI AKTIF</span>
        <h2>Perhitungan yang sedang digunakan</h2>
        <p>
            Algoritma <strong>{{ $algorithmVersion }}</strong>, rubrik <strong>{{ $scoringVersion }}</strong>,
            dan dataset <strong>{{ $dataset?->version ?? 'belum aktif' }}</strong>.
            Hash dataset: <code>{{ $dataset ? substr($dataset->hash, 0, 12) : '—' }}</code>.
        </p>
    </section>

    <section class="content-card">
        <span class="card-kicker">BOBOT AKTIF</span>
        <h2>Enam kriteria keputusan</h2>
        <p>Bobot normal dihitung otomatis dari nilai mentah pada versi bobot aktif. Perubahan bobot mengharuskan snapshot dataset baru.</p>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Kode</th><th>Kriteria</th><th>Jenis</th><th>Bobot normal</th></tr></thead>
                <tbody>
                    @foreach($criteria as $criterion)
                        <tr>
                            <td><strong>{{ $criterion->code }}</strong></td>
                            <td>{{ $criterion->name }}</td>
                            <td>{{ ucfirst($criterion->type) }}</td>
                            <td>{{ number_format((float) ($weightSet?->weights->firstWhere('criterion_id', $criterion->id)?->normalized_value ?? 0), 7, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <div class="two-columns">
        <section class="content-card">
            <span class="card-kicker">RUMUS</span>
            <h2>Normalisasi</h2>
            <p>Benefit: <code>rᵢⱼ = xᵢⱼ / max(xⱼ)</code></p>
            <p>Cost harga: <code>rᵢⱼ = min(xⱼ) / xᵢⱼ</code></p>
            <p>Nilai akhir: <code>Vᵢ = Σ(wⱼ × rᵢⱼ)</code></p>
            <p>C4 menggunakan harga per 100 ml/g sebagai cost numerik dan tidak memakai skala ordinal 1–5. Perbandingan ml dengan g dicatat sebagai asumsi penelitian karena konsentrasi massa jenis tidak selalu tersedia.</p>
        </section>
        <section class="content-card danger-card">
            <span class="card-kicker">HARD CONSTRAINT</span>
            <h2>Red flag</h2>
            <p>Jerawat nodul atau kistik, kondisi berat atau cepat memburuk, jaringan parut, infeksi luas, alergi berat, serta nyeri terbakar menetap menghentikan ranking biasa.</p>
        </section>
    </div>

    <section class="content-card">
        <span class="card-kicker">KONSISTENSI</span>
        <h2>Tie-break deterministik</h2>
        <ol>
            <li>C6 kualitas bukti, legalitas, dan keamanan kontekstual tertinggi</li>
            <li>C1 keluhan, C2 kondisi, lalu C3 formulasi tertinggi</li>
            <li>Harga unit lebih rendah</li>
            <li>Nama varian ascending</li>
        </ol>
        <p>Snapshot menyimpan input, alternatif layak, alasan eksklusi, X, R, W, kontribusi, V, versi dataset, versi rubrik, versi algoritma, dan timestamp.</p>
    </section>
</div>
