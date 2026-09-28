<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        body{font-family:DejaVu Sans,sans-serif;color:#172033;font-size:10px}h1{font-size:20px}h2{font-size:15px;border-bottom:1px solid #ddd;padding-bottom:5px}table{width:100%;border-collapse:collapse;margin:10px 0}th,td{border:1px solid #ddd;padding:4px;text-align:left}.note{background:#f3f4f6;padding:10px}.danger{background:#fee2e2;padding:12px}
    </style>
</head>
<body>
    <h1>Hasil Rekomendasi Dermavera</h1>
    <p>UUID: {{ $consultation->uuid }}<br>Dataset: {{ $consultation->datasetVersion?->version }}<br>Algoritma: {{ $consultation->algorithm_version }}<br>Waktu: {{ $consultation->created_at->toIso8601String() }}</p>
    @if ($consultation->safetyAssessment->outcome === 'refer')
        <div class="danger"><strong>Referral:</strong> ranking dihentikan karena red flag.</div>
    @else
        @php($scoreLabel = (($run?->calculation_snapshot['eligible_count'] ?? $run?->results->where('eligibility_status', 'eligible')->count()) === 1) ? 'Skor relatif' : 'Skor SAW')
        <h2>Peringkat</h2>
        <p class="note">{{ $scoreLabel }}: bila hanya satu kandidat yang lolos, nilai tidak dapat dibandingkan dengan alternatif lain dan bukan persentase kecocokan absolut.</p>
        <table>
            <thead><tr><th>Rank</th><th>Produk</th><th>Harga / tanggal</th><th>C1</th><th>C2</th><th>C3</th><th>C4/100</th><th>C5</th><th>C6</th><th>{{ $scoreLabel }}</th></tr></thead>
            <tbody>
                @foreach ($run?->results->where('eligibility_status', 'eligible') ?? [] as $r)
                    <tr>
                        <td>{{ $r->rank }}</td>
                        <td>{{ $r->variant->brand->name }} — {{ $r->variant->name }}<br>{{ $r->sku?->size_value }} {{ $r->sku?->size_unit }}</td>
                        <td>Rp{{ number_format((float) $r->price?->amount, 0, ',', '.') }}<br>{{ $r->price?->observed_at?->format('d-m-Y') }}</td>
                        @foreach (['C1', 'C2', 'C3', 'C4', 'C5', 'C6'] as $criterion)
                            <td>{{ $r->raw_scores[$criterion] }}</td>
                        @endforeach
                        <td>{{ $r->final_score }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
    <div class="note">Rekomendasi membantu membandingkan produk kosmetik dan bukan diagnosis medis. Komposisi serta harga dapat berubah; periksa kembali label kemasan dan tanggal observasi.</div>
</body>
</html>
