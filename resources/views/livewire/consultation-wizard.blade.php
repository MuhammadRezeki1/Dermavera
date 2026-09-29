<div class="wizard-page shell" data-motion-root="consultation">
    @php
        $steps = [
            'consent' => ['Persetujuan', 'Mulai aman'],
            'profile' => ['Profil', 'Kenali kulit'],
            'safety' => ['Keamanan', 'Periksa risiko'],
            'preference' => ['Preferensi', 'Saring pilihan'],
        ];
    @endphp

    <header class="wizard-header">
        <a href="{{ route('home') }}" wire:navigate @click.prevent="exitModalOpen = true" class="back-link">← Keluar</a>
        <div class="stepper" aria-label="Kemajuan konsultasi">
            @foreach($steps as $key => [$label, $hint])
                <div @class(['current' => $step === $key, 'done' => array_search($key, array_keys($steps)) < array_search($step, array_keys($steps))])>
                    <span>{{ $loop->iteration }}</span><small><b>{{ $label }}</b>{{ $hint }}</small>
                </div>
                @if(!$loop->last)<i></i>@endif
            @endforeach
        </div>
        <span class="wizard-time">± 3 MENIT</span>
    </header>

    <section class="wizard-shell">
        <aside class="wizard-aside">
            <span class="eyebrow"><i></i> ANALISIS KULIT</span>
            <h2>Jawabanmu membentuk<br><em>urutan rekomendasi.</em></h2>
            <p>Jawab sesuai kondisi saat ini. Tidak ada jawaban yang “lebih baik”.</p>
            <div class="privacy-note"><span>◇</span><div><strong>Data minimum</strong><small>Hanya informasi yang dibutuhkan untuk keputusan ini yang dipakai.</small></div></div>
        </aside>

        <div class="wizard-card" data-step-panel>
            @if($errors->any())
                <div class="error-summary" role="alert" tabindex="-1" data-validation-error>
                    <strong>Periksa kembali jawaban berikut:</strong>
                    <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            @if($step === 'consent')
                <span class="step-label">LANGKAH 1 / 4</span><h1>Sebelum kita mulai.</h1>
                <p>Dermavera membandingkan produk kosmetik. Sistem ini tidak mendiagnosis penyakit kulit dan tidak menggantikan tenaga kesehatan.</p>
                <div class="consent-points"><p><span>✓</span> Data konsultasi disimpan seminimal mungkin.</p><p><span>✓</span> Red flag menghentikan ranking biasa demi keamanan.</p><p><span>✓</span> Komposisi dan harga tetap perlu dicek ulang.</p></div>
                <label class="check-card"><input type="checkbox" wire:model="consent"><span class="custom-check"></span><span><strong>Saya memahami dan menyetujui batasan tersebut.</strong><small>Saya berada dalam populasi penelitian usia 18–30 tahun atau memahami batas generalisasinya.</small></span></label>
                <div class="wizard-actions"><span></span><button class="button button-primary" wire:click="accept">Setuju &amp; lanjutkan →</button></div>

            @elseif($step === 'profile')
                <span class="step-label">LANGKAH 2 / 4</span><h1>Ceritakan kondisi kulitmu.</h1>
                <div class="form-group"><label for="age">Usia</label><input id="age" type="number" min="18" max="30" wire:model="age" inputmode="numeric" placeholder="Contoh: 23"><small>Rentang 18–30 adalah ruang lingkup penelitian.</small></div>
                <fieldset><legend>Keluhan utama <small>Pilih satu yang paling mengganggu.</small></legend><div class="choice-grid choice-icons">@foreach(['berminyak' => ['◌', 'Minyak berlebih'], 'komedo' => ['◎', 'Komedo'], 'jerawat_ringan' => ['✦', 'Jerawat ringan'], 'kusam' => ['◐', 'Kusam'], 'kering' => ['◇', 'Kering / tertarik']] as $value => [$icon, $label])<label class="choice-card"><input type="radio" value="{{ $value }}" wire:model="primaryComplaint"><span><i>{{ $icon }}</i>{{ $label }}</span></label>@endforeach</div></fieldset>
                <fieldset><legend>Keluhan tambahan <small>Opsional, pilih semua yang sesuai.</small></legend><div class="secondary-grid">@foreach(['berminyak' => 'Berminyak', 'komedo' => 'Komedo', 'jerawat_ringan' => 'Jerawat', 'kusam' => 'Kusam', 'kering' => 'Kering'] as $value => $label)<label><input type="checkbox" value="{{ $value }}" wire:model="secondaryConcerns"><span>{{ $label }}</span></label>@endforeach</div></fieldset>
                <fieldset><legend>Jenis / kondisi dasar</legend><div class="choice-grid four">@foreach(['normal' => 'Normal', 'berminyak' => 'Berminyak', 'kering' => 'Kering', 'kombinasi' => 'Kombinasi'] as $value => $label)<label class="choice-card"><input type="radio" value="{{ $value }}" wire:model="skinType"><span>{{ $label }}</span></label>@endforeach</div></fieldset>
                <div class="wizard-actions"><a class="button button-secondary" href="{{ route('consultation.consent') }}" wire:navigate>← Kembali</a><button class="button button-primary" wire:click="saveProfile">Lanjutkan →</button></div>

            @elseif($step === 'safety')
                <span class="step-label">LANGKAH 3 / 4</span><h1>Pemeriksaan keamanan.</h1>
                <p>Jawab seluruh pertanyaan sesuai kondisi saat ini. Informasi ini dipakai sebelum skor produk dihitung.</p>
                <div class="safety-callout"><span>!</span><p>Bila ada gejala berat, ranking biasa akan dihentikan dan sistem menyarankan evaluasi tenaga kesehatan.</p></div>
                @foreach([['sensitive', 'Apakah kulit mudah perih, merah, atau rentan iritasi?'], ['barrierImpaired', 'Apakah skin barrier sedang terganggu—mengelupas, sangat kering, atau terbakar?'], ['acneTherapy', 'Apakah sedang memakai terapi acne atau obat kulit?']] as [$property, $question])
                    <fieldset class="yes-no"><legend>{{ $question }}</legend><label><input type="radio" value="1" wire:model="{{ $property }}"><span>Ya</span></label><label><input type="radio" value="0" wire:model="{{ $property }}"><span>Tidak</span></label></fieldset>
                @endforeach
                <div class="form-group"><label for="allergies">Alergi ingredient yang sudah diketahui</label><textarea id="allergies" wire:model="allergiesText" placeholder="Contoh: fragrance, salicylic acid — kosongkan bila tidak ada"></textarea></div>
                <fieldset><legend>Kondisi red flag <small>Pilih semua yang sesuai.</small></legend><div class="redflag-list">@foreach(['nodul_kistik' => 'Jerawat nodul / kistik', 'jerawat_berat_memburuk' => 'Jerawat berat / cepat memburuk', 'jaringan_parut' => 'Jaringan parut berkembang', 'luka_infeksi_luas' => 'Luka / infeksi luas', 'bengkak_alergi_berat' => 'Bengkak / dugaan alergi berat', 'nyeri_terbakar_menetap' => 'Nyeri / terbakar menetap', 'penyakit_kulit_dalam_pengobatan' => 'Penyakit kulit dalam pengobatan'] as $value => $label)<label><input type="checkbox" value="{{ $value }}" wire:model="redFlags"><span>{{ $label }}</span></label>@endforeach</div></fieldset>
                <div class="wizard-actions"><a class="button button-secondary" href="{{ route('consultation.profile') }}" wire:navigate>← Kembali</a><button class="button button-primary" wire:click="saveSafety">Lanjutkan →</button></div>

            @else
                <span class="step-label">LANGKAH 4 / 4</span><h1>Sempurnakan pilihanmu.</h1>
                <p>Preferensi ini membantu urutan akhir, tetapi tidak pernah membatalkan aturan keselamatan.</p>
                @guest
                    <div class="wizard-login-note"><strong>Login tidak wajib.</strong><span>Lihat rekomendasi sekarang, lalu simpan hasilnya ke akun jika kamu ingin membuka riwayat dari perangkat lain.</span></div>
                @endguest
                <div class="form-row"><div class="form-group"><label for="min-price">Harga minimum</label><div class="input-prefix"><span>Rp</span><input id="min-price" type="number" min="0" wire:model="minPrice" placeholder="0"></div></div><div class="form-group"><label for="max-price">Harga maksimum</label><div class="input-prefix"><span>Rp</span><input id="max-price" type="number" min="0" wire:model="maxPrice" placeholder="75.000"></div></div></div>
                <fieldset><legend>Preferensi kemasan</legend><div class="choice-grid four">@foreach(['' => 'Tidak ada', 'tube' => 'Tube', 'pump' => 'Pump', 'bottle' => 'Botol'] as $value => $label)<label class="choice-card"><input type="radio" value="{{ $value }}" wire:model="packagingPreference"><span>{{ $label }}</span></label>@endforeach</div></fieldset>
                <div class="processing" wire:loading wire:target="finish,confirmFinish" role="status"><span></span> Menjalankan safety gate, menyaring formula, dan menghitung SAW…</div>
                <div class="wizard-actions"><a class="button button-secondary" href="{{ route('consultation.safety') }}" wire:navigate>← Kembali</a><button class="button button-primary" wire:click="finish" wire:loading.attr="disabled">Lihat rekomendasi →</button></div>
            @endif
        </div>
    </section>

    <div x-cloak x-show="exitModalOpen" x-transition.opacity class="confirm-backdrop" role="presentation" @click.self="exitModalOpen = false">
        <section class="confirm-dialog" role="dialog" aria-modal="true" aria-labelledby="exit-dialog-title" @click.stop>
            <span class="confirm-kicker">KONFIRMASI</span><h2 id="exit-dialog-title">Keluar dari konsultasi?</h2>
            <p>Draft jawabanmu akan tetap tersimpan sementara. Kamu yakin ingin kembali ke beranda?</p>
            <div class="confirm-actions"><button type="button" class="button button-secondary" @click="exitModalOpen = false">Tidak</button><a class="button button-primary" href="{{ route('home') }}" wire:navigate @click="exitModalOpen = false">Ya, keluar</a></div>
        </section>
    </div>

    @if($showConfirmation)
        @php
            $complaintLabels = ['berminyak' => 'Minyak berlebih', 'komedo' => 'Komedo', 'jerawat_ringan' => 'Jerawat ringan', 'kusam' => 'Kusam', 'kering' => 'Kering / tertarik'];
            $skinLabels = ['normal' => 'Normal', 'berminyak' => 'Berminyak', 'kering' => 'Kering', 'kombinasi' => 'Kombinasi'];
            $concernLabels = ['berminyak' => 'Berminyak', 'komedo' => 'Komedo', 'jerawat_ringan' => 'Jerawat', 'kusam' => 'Kusam', 'kering' => 'Kering'];
            $redFlagLabels = ['nodul_kistik' => 'Jerawat nodul / kistik', 'jerawat_berat_memburuk' => 'Jerawat berat / cepat memburuk', 'jaringan_parut' => 'Jaringan parut berkembang', 'luka_infeksi_luas' => 'Luka / infeksi luas', 'bengkak_alergi_berat' => 'Bengkak / dugaan alergi berat', 'nyeri_terbakar_menetap' => 'Nyeri / terbakar menetap', 'penyakit_kulit_dalam_pengobatan' => 'Penyakit kulit dalam pengobatan'];
            $packagingLabels = ['' => 'Tidak ada', 'tube' => 'Tube', 'pump' => 'Pump', 'bottle' => 'Botol'];
        @endphp
        <div class="confirm-backdrop confirm-backdrop-livewire" role="presentation">
            <section class="confirm-dialog confirm-dialog-wide" role="dialog" aria-modal="true" aria-labelledby="review-dialog-title">
                <span class="confirm-kicker">DRAFT KONSULTASI</span><h2 id="review-dialog-title">Apakah isianmu sudah sesuai?</h2>
                <p class="confirm-intro">Periksa kembali ringkasan di bawah sebelum kami menghitung rekomendasi.</p>
                <div class="draft-summary">
                    <div><small>Persetujuan</small><strong>Disetujui</strong></div><div><small>Usia</small><strong>{{ $age ?? '—' }} tahun</strong></div>
                    <div><small>Keluhan utama</small><strong>{{ $complaintLabels[$primaryComplaint] ?? '—' }}</strong></div><div><small>Keluhan tambahan</small><strong>{{ collect($secondaryConcerns)->map(fn ($value) => $concernLabels[$value] ?? $value)->join(', ') ?: 'Tidak ada' }}</strong></div>
                    <div><small>Kondisi kulit</small><strong>{{ $skinLabels[$skinType] ?? '—' }}</strong></div><div><small>Kulit sensitif</small><strong>{{ $sensitive ? 'Ya' : 'Tidak' }}</strong></div>
                    <div><small>Skin barrier terganggu</small><strong>{{ $barrierImpaired ? 'Ya' : 'Tidak' }}</strong></div><div><small>Terapi acne / obat kulit</small><strong>{{ $acneTherapy ? 'Ya' : 'Tidak' }}</strong></div>
                    <div><small>Alergi ingredient</small><strong>{{ $allergiesText ?: 'Tidak ada' }}</strong></div><div><small>Red flag</small><strong>{{ collect($redFlags)->map(fn ($value) => $redFlagLabels[$value] ?? $value)->join(', ') ?: 'Tidak ada' }}</strong></div>
                    <div><small>Rentang harga</small><strong>Rp {{ $minPrice !== null ? number_format($minPrice, 0, ',', '.') : '—' }} — {{ $maxPrice !== null ? 'Rp '.number_format($maxPrice, 0, ',', '.') : 'tanpa batas' }}</strong></div><div><small>Preferensi kemasan</small><strong>{{ $packagingLabels[$packagingPreference] ?? 'Tidak ada' }}</strong></div>
                </div>
                <div class="confirm-actions"><button type="button" class="button button-secondary" wire:click="cancelReview">Tidak, periksa lagi</button><button type="button" class="button button-primary" wire:click="confirmFinish" wire:loading.attr="disabled">Ya, sudah sesuai</button></div>
            </section>
        </div>
    @endif
</div>
