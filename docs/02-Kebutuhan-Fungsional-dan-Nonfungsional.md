# Kebutuhan Fungsional dan Nonfungsional

## A. Kebutuhan fungsional

| ID | Kebutuhan | Prioritas | Kriteria penerimaan ringkas |
|---|---|---|---|
| FR-01 | Sistem menampilkan persetujuan dan batasan | Must | Pengguna menyetujui sebelum konsultasi |
| FR-02 | Sistem memvalidasi usia penelitian 18-30 | Must | Di luar rentang diberi pesan ruang lingkup, bukan larangan memakai facial wash |
| FR-03 | Sistem merekam keluhan utama | Must | Minimal satu keluhan dipilih |
| FR-04 | Sistem merekam jenis/kondisi kulit | Must | Satu kondisi dasar; sensitif sebagai overlay |
| FR-05 | Sistem merekam alergi, barrier, terapi, dan red flag | Must | Semua pertanyaan keamanan dijawab |
| FR-06 | Sistem menjalankan safety gate | Must | Kasus red flag berhenti sebelum SAW |
| FR-07 | Sistem menyaring alternatif berdasarkan hard constraint | Must | Alternatif tidak aman tidak masuk matriks |
| FR-08 | Sistem menghitung SAW | Must | Normalisasi dan nilai akhir sama dengan hitung manual |
| FR-09 | Sistem menampilkan tiga hasil teratas | Must | Ada skor, alasan, peringatan, sumber |
| FR-10 | Sistem menjelaskan kontribusi C1-C6 | Must | Tampil nilai mentah, normalisasi, bobot, kontribusi |
| FR-11 | Sistem menyimpan snapshot konsultasi | Must | Input dan versi dataset/algoritma tidak berubah retrospektif |
| FR-12 | Admin mengelola merek dan varian | Must | CRUD dengan soft delete dan audit log |
| FR-13 | Admin mengelola INCI dan bahan aktif | Must | Setiap komposisi memiliki versi dan sumber |
| FR-14 | Admin mengelola mapping kondisi-bahan/produk | Must | Skala 1-5 dan alasan wajib |
| FR-15 | Admin mengelola bobot | Must | Total harus 1; aktivasi satu versi pada satu waktu |
| FR-16 | Admin mengelola harga bertanggal | Should | Sumber dan observed_at wajib |
| FR-17 | Admin mempublikasikan produk setelah lolos verifikasi | Must | Status `verified` diperlukan |
| FR-18 | Admin melihat audit dan ekspor hasil | Should | CSV/PDF menyertakan versi dan timestamp |
| FR-19 | Sistem menampilkan katalog dan detail sumber | Should | Tautan sumber resmi dan tanggal akses tersedia |
| FR-20 | Sistem menyediakan disclaimer dan rujukan | Must | Muncul pada input berisiko dan hasil |
| FR-21 | Sistem memberi transisi visual antarlangkah | Should | Perubahan pertanyaan, progress, dan validasi terlihat tanpa mengubah jawaban |
| FR-22 | Sistem menganimasikan hasil terverifikasi | Should | Ranking, skor, dan bar bergerak menuju nilai yang diterima dari server |
| FR-23 | Sistem menganimasikan filter katalog | Could | Elemen masuk, keluar, dan berpindah tanpa kehilangan fokus atau urutan semantik |
| FR-31 | Sistem mengizinkan konsultasi tanpa login | Must | Guest dapat menyetujui batasan, mengisi wizard, menjalankan SAW, dan membaca hasil melalui session yang berwenang |
| FR-32 | Sistem menawarkan penyimpanan hasil setelah rekomendasi | Should | Guest melihat CTA masuk/daftar setelah hasil; login tidak menjadi syarat sebelum SAW |
| FR-33 | Sistem mengklaim hasil guest ke akun | Must | Setelah autentikasi, `user_id` konsultasi diisi, retensi guest dilepas, dan SAW tidak dihitung ulang |
| FR-34 | Sistem menampilkan riwayat dalam card ringkas | Should | Card berisi foto pilihan #1, brand, harga, ukuran, keluhan, usia, tanggal, status, dan dataset |

## B. Kebutuhan nonfungsional

| ID | Aspek | Target |
|---|---|---|
| NFR-01 | Keamanan | CSRF, rate limit, validasi server, prepared query/Eloquent, kebijakan akses |
| NFR-02 | Privasi | Data minimum, pseudonim, tanpa diagnosis, retensi dapat dikonfigurasi |
| NFR-03 | Reproducibility | Snapshot bobot, aturan, formula, harga, dan versi algoritma |
| NFR-04 | Kinerja | p95 ranking <2 detik pada beban penelitian |
| NFR-05 | Ketersediaan | Backup harian database saat produksi |
| NFR-06 | Integritas | Foreign key, unique constraint, check constraint, transaksi |
| NFR-07 | Aksesibilitas | Keyboard, label form, kontras WCAG AA, pesan error jelas |
| NFR-08 | Responsif | 360 px hingga desktop tanpa horizontal overflow |
| NFR-09 | Maintainability | Service terpisah untuk safety, scoring, recommendation, dan explanation |
| NFR-10 | Observability | Structured log, audit log, exception tracking, health check |
| NFR-11 | Portabilitas | Docker Compose lokal; konfigurasi melalui `.env` |
| NFR-12 | Bahasa | Bahasa Indonesia konsisten; istilah INCI tidak diterjemahkan sembarang |
| NFR-13 | Motion accessibility | `prefers-reduced-motion` menonaktifkan gerakan besar dan membuat durasi mendekati nol |
| NFR-14 | Kinerja animasi | Utamakan transform/opacity; tidak ada long task animasi >50 ms pada perangkat uji |
| NFR-15 | Integritas hasil | Anime.js tidak menghitung ulang safety gate, skor, bobot, atau ranking |
| NFR-16 | Stabilitas Livewire | Listener dan instance animasi dibersihkan ketika komponen dirender ulang atau dilepas |
| NFR-17 | Privasi hasil guest | UUID hasil guest hanya dapat dibuka dari session guest yang membuatnya; setelah diklaim, akses mengikuti kepemilikan akun |
| NFR-18 | Konsistensi klaim | Klaim guest mempertahankan snapshot, dataset, algoritma, dan hasil yang sudah dihitung |

## C. Validasi input penting

- Usia bilangan bulat 18-30 untuk sampel penelitian.
- Harga minimum tidak boleh melebihi maksimum.
- Satu kondisi kulit dasar; overlay boleh lebih dari satu.
- Setiap konsultasi harus mengisi semua pertanyaan keselamatan.
- Nilai matriks hanya integer 1-5.
- Bobot numerik >0 dan jumlah 1,0000 dengan toleransi 0,0001.
- URL sumber valid; tanggal akses dan jenis sumber wajib.
- `inci_raw` tidak boleh diubah otomatis; `inci_normalized` boleh dibersihkan dengan catatan normalisasi.
- Nilai skor yang dianimasikan harus sama persis dengan payload server setelah animasi selesai.
- Peringatan red flag harus langsung tersedia di DOM dan tidak boleh menunggu animasi untuk dibaca.

## D. Matriks hak akses

| Aksi | Pengunjung | Pengguna | Admin |
|---|:---:|:---:|:---:|
| Melihat edukasi/katalog | Ya | Ya | Ya |
| Mengisi konsultasi | Ya/pseudonim | Ya | Ya |
| Menyelesaikan konsultasi tanpa akun | Ya, dengan session | Ya | Ya |
| Mengklaim hasil guest ke akun | Tidak | Ya, untuk hasil miliknya | Ya sesuai kewenangan |
| Melihat riwayat pribadi | Tidak | Ya | Ya sesuai kewenangan |
| Mengubah produk/aturan/bobot | Tidak | Tidak | Ya |
| Mengaktifkan versi dataset | Tidak | Tidak | Ya + konfirmasi |
| Mengekspor data penelitian | Tidak | Data sendiri | Ya, data minimum |
| Melihat identitas validator | Tidak | Tidak | Terbatas |

## E. Kebutuhan tambahan implementasi v3.3

| ID | Kebutuhan | Prioritas | Kriteria penerimaan ringkas |
|---|---|---|---|
| FR-24 | Sistem membedakan mode skor | Must | `relative_single_candidate` untuk satu kandidat dan `comparative_saw` untuk minimal dua kandidat; mode tersimpan dalam snapshot |
| FR-25 | Sistem menampilkan alasan kandidat tidak diranking | Must | Kandidat `excluded` ditampilkan di rincian Safety Gate dengan alasan dan foto bila tersedia; `insufficient_evidence` tetap tersimpan untuk audit dan tidak menjadi link rekomendasi |
| FR-26 | Sistem mengelola kesegaran harga | Must | `observed_at`, sumber, ketersediaan, dan kelayakan ranking diperiksa; batas default 30 hari dapat dikonfigurasi |
| FR-27 | Sistem mempertahankan histori observasi | Must | Admin menambah observasi baru; data lama tidak ditimpa dan perubahan dipakai setelah dataset aktif diperbarui |
| FR-28 | Sistem menampilkan audit kandidat | Must | Jumlah kandidat layak, dikeluarkan, dan data tidak cukup konsisten dengan baris hasil dan snapshot |
| FR-29 | Sistem menampilkan status skor relatif | Must | Nilai `1,0000` pada satu kandidat diberi konteks relatif dan tidak ditulis sebagai nilai absolut |
| FR-30 | Sistem mendukung akses publik melalui reverse proxy | Should | URL asset mengikuti HTTPS/proxy dan halaman tidak mengalami mixed content; konfigurasi Quick Tunnel tidak dianggap produksi |

### E.1 Catatan gap yang harus diuji

Field `secondary_concerns` sudah menjadi bagian input snapshot dan modifier C1 sudah diterapkan pada `criteria-1.3.0`; UAT-09 tetap perlu membuktikan perubahan skor pada input yang sama dengan dan tanpa keluhan tambahan.
