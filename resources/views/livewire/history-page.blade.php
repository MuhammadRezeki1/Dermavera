<div class="page shell">
    <header class="page-header"><span class="eyebrow"><i></i> RIWAYAT PRIBADI</span><h1>Rekomendasi<br><em>yang tersimpan.</em></h1><p>Ringkasan hasil konsultasimu. Buka card untuk melihat matriks, alasan, dan seluruh peringkat.</p></header>

    <div class="history-grid">
        @forelse($consultations as $item)
            @php
                $run = $item->runs->last();
                $winner = $run?->results->first();
                $input = $item->input_snapshot ?? [];
                $complaintLabels = ['berminyak' => 'Minyak berlebih', 'komedo' => 'Komedo', 'jerawat_ringan' => 'Jerawat ringan', 'kusam' => 'Kusam', 'kering' => 'Kering / tertarik'];
                $statusLabels = ['completed' => 'Selesai', 'referred' => 'Perlu rujukan', 'no_safe_alternative' => 'Belum ada alternatif aman', 'processing' => 'Diproses'];
            @endphp
            <a class="history-card content-card" href="{{ route('results.show', $item) }}" wire:navigate>
                <div class="history-card-media">
                    @if ($winner)
                        <x-product-image :variant="$winner->variant" />
                    @else
                        <span class="history-card-empty">∅</span>
                    @endif
                    <span class="history-card-date">{{ $item->created_at->format('d M Y') }}</span>
                </div>
                <div class="history-card-body">
                    <div class="history-card-kicker"><span>{{ $statusLabels[$item->status] ?? ucfirst(str_replace('_', ' ', $item->status)) }}</span><span>Dataset {{ $item->datasetVersion?->version ?? '—' }}</span></div>
                    @if ($winner)
                        <small>{{ strtoupper($winner->variant->brand->name) }} · PILIHAN #1</small>
                        <h2>{{ $winner->variant->name }}</h2>
                        <div class="history-card-price"><strong>Rp{{ number_format((float) ($winner->price?->amount ?? 0), 0, ',', '.') }}</strong><span>{{ $winner->sku?->size_value ? rtrim(rtrim(number_format((float) $winner->sku->size_value, 2, ',', '.'), '0'), ',').' '.$winner->sku->size_unit : 'Ukuran —' }}</span></div>
                    @else
                        <small>HASIL KONSULTASI</small>
                        <h2>Belum ada pilihan yang dapat ditampilkan</h2>
                    @endif
                    <div class="history-card-meta"><span>{{ $complaintLabels[$input['primary_complaint'] ?? ''] ?? 'Profil tersimpan' }}</span><span>{{ $input['age'] ?? '—' }} tahun</span><span>{{ $item->created_at->format('H:i') }} WIB</span></div>
                    <span class="history-card-action">Buka hasil lengkap <b>→</b></span>
                </div>
            </a>
        @empty
            <div class="empty-state"><span>∅</span><h2>Belum ada riwayat</h2><p>Mulai konsultasi untuk mendapatkan rekomendasi pertamamu.</p><a class="button button-primary" href="{{ route('consultation.consent') }}">Mulai konsultasi</a></div>
        @endforelse
    </div>

    {{ $consultations->links() }}
</div>
