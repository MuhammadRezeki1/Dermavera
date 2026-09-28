# Paket Dokumentasi Sistem Rekomendasi Facial Wash

**Peneliti:** Muhammad Rezeki  
**Program Studi:** Teknik Informatika, UIN Sultan Syarif Kasim Riau  
**Judul:** Sistem Rekomendasi Pemilihan Produk Facial Wash pada Pria Menggunakan Metode Simple Additive Weighting  
**Stack yang direncanakan:** Laravel 13, Livewire 4, Tailwind CSS, Flux UI, Anime.js 4, Filament 5, PostgreSQL  
**Tanggal pembaruan rancangan:** 28 September 2026  
**Versi paket:** 3.3

## Isi paket

| No. | File | Fungsi |
|---:|---|---|
| 1 | `01-PRD-Sistem-Rekomendasi-Facial-Wash.md` | Tujuan, ruang lingkup, pengguna, fitur, batasan, dan acceptance criteria |
| 2 | `02-Kebutuhan-Fungsional-dan-Nonfungsional.md` | Daftar kebutuhan sistem yang dapat diuji |
| 3 | `03-Arsitektur-Sistem-Laravel-PostgreSQL.md` | Arsitektur aplikasi, modul, keamanan, dan deployment |
| 4 | `04-Use-Case-dan-Activity-Diagram.md` | Aktor, use case, alur konsultasi, dan alur admin |
| 5 | `05-ERD-dan-Kamus-Data.md` | ERD, tabel, relasi, constraint, dan audit data |
| 6 | `06-Knowledge-Base-dan-Aturan-Keamanan.md` | Fakta, aturan, red flag, dan status kelengkapan pengetahuan |
| 7 | `07-Kriteria-Subkriteria-dan-Bobot-SAW.md` | Definisi C1-C6, bobot dokter, skala 1-5, benefit/cost |
| 8 | `08-Alur-Perhitungan-SAW.md` | Safety gate, pembentukan matriks, normalisasi, nilai akhir, tie-break |
| 9 | `09-Rancangan-UI-UX-dan-Daftar-Halaman.md` | Design system dan rancangan halaman responsif |
| 10 | `10-Rancangan-API-Route-dan-Struktur-Modul.md` | Route web/API, service, policy, job, dan struktur proyek |
| 11 | `11-Rencana-Pengujian-dan-UAT.md` | Unit, feature, integrasi, keamanan, UAT, dan validasi perhitungan |
| 12 | `12-Traceability-Matrix-Persyaratan.md` | Hubungan masalah-rumusan-kebutuhan-data-uji-output |
| 13 | `13-Katalog-Produk-Varian-dan-Ingredients.md` | 5 merek, 37 record (35 aktif), INCI, bahan penting, sumber, dan status verifikasi |
| 14 | `14-Rencana-Implementasi-Laravel-PostgreSQL.md` | Tahap pembangunan, seed data, migrasi, dan definition of done |
| 15 | `15-Spesifikasi-Animasi-dan-Interaksi-AnimeJS.md` | Kontrak animasi, durasi, pemicu, aksesibilitas, integrasi Livewire, dan pengujian |
| 16 | `16-Audit-BPOM-33-Varian.md` | Nomor BPOM, kecocokan nama/ukuran, versi aktif, dan pemisahan B03 Acne Skincare dari B03N Acne Bright Care |
| 17 | `17-BAB-IV-Pembahasan-Final.md` | Naskah sumber BAB IV yang mengikuti struktur template tugas akhir |
| 18 | `BAB-IV-Pembahasan-Sistem-Rekomendasi-Facial-Wash.docx` | BAB IV final yang dapat diedit dan mengikuti format template kampus |
| 19 | `BAB-IV-Pembahasan-Sistem-Rekomendasi-Facial-Wash.pdf` | Versi PDF BAB IV final untuk pemeriksaan dan pencetakan |
| 20 | `Draf-BAB-IV-Final-Perancangan-Sistem.docx` | Kompilasi seluruh spesifikasi teknis yang dapat diedit; digunakan sebagai dokumen pendukung |
| 21 | `Draf-BAB-IV-Final-Perancangan-Sistem.pdf` | Versi PDF kompilasi spesifikasi teknis |
| 22 | `18-Referensi-Harga-37-Record.md` | Observasi harga bertanggal untuk 37 record, status bukti, sumber, dan keputusan kelayakan ranking |
| 23 | `19-UAT-Sistem-Rekomendasi-Facial-Wash.md` | Skenario UAT aktual, validasi satu/beberapa kandidat, safety gate, harga, ekspor, aksesibilitas, dan tunnel publik |

## Keputusan desain yang wajib dipertahankan

1. **Merek bukan alternatif akhir.** Merek menjadi kelompok, sedangkan setiap varian/formula menjadi alternatif SAW tersendiri.
2. **Rentang 18-30 tahun adalah batas populasi penelitian**, bukan pernyataan bahwa usia di luar rentang tersebut tidak boleh atau tidak perlu memakai facial wash.
3. **Safety gate berjalan sebelum SAW.** Sensitivitas, alergi, skin barrier terganggu, terapi acne, dan red flag klinis tidak boleh dikompensasikan oleh harga atau kemasan.
4. **Tidak menebak pH atau konsentrasi bahan.** Nilai yang tidak tersedia disimpan sebagai `NULL/UNKNOWN` dan dinilai konservatif.
5. **Harga bersifat observasi bertanggal**, bukan fakta tetap. Setiap harga harus memiliki sumber dan `observed_at`.
6. **Hanya satu validator ahli.** Tulisan ilmiah harus menggunakan frasa “satu validator ahli”, bukan “konsensus dokter”.
7. **Jawaban dokter hanya boleh diringkas tanpa kutipan langsung.** Data mentah dan identitas yang tidak diperlukan tidak ditampilkan ke publik.
8. **Sistem adalah pendukung keputusan kosmetik, bukan alat diagnosis atau pengganti dokter.**
9. **Konsultasi bersifat guest-first.** Login tidak wajib untuk menghitung dan melihat hasil; login/daftar ditawarkan setelah hasil agar pengguna dapat menyimpan hasil ke riwayat tanpa menghitung ulang.
10. **Riwayat pribadi hanya untuk akun.** Card riwayat menampilkan ringkasan visual pilihan #1 dan informasi konteks; detail lengkap tetap dibuka pada halaman hasil yang sama.

## Keputusan antarmuka dan animasi

Gunakan **Laravel Livewire Starter Kit resmi** dengan layout header untuk pengguna, Flux UI dan Tailwind untuk komponen, **Anime.js 4** untuk animasi fungsional, serta **Filament** untuk panel admin. Anime.js hanya mengatur presentasi dan umpan balik visual; hasil safety gate, matriks, normalisasi, skor, dan ranking tetap dihitung di sisi server. Sumber resmi: [Laravel Starter Kits](https://laravel.com/starter-kits), [Flux UI](https://fluxui.dev/), [Anime.js](https://animejs.com/documentation/), dan [Filament](https://filamentphp.com/docs/5.x/introduction/installation).

## Cara memakai paket

- Gunakan file 01-12, 15, dan 19 sebagai spesifikasi serta rencana pengujian.
- Gunakan status terbaru pada file 13 dan audit BPOM pada file 16. Seluruh 37 record kini memiliki full INCI Indonesia. B03 dan M01 disimpan sebagai riwayat; M02-M04 adalah tiga alternatif aktif MS Glow For Men terbaru.
- B01 dan B03N sudah dapat masuk scoring ingredients final berdasarkan foto label produk Indonesia; tetap simpan nomor BPOM, URL sumber, tanggal akses, dan versi formula.
- Gunakan file 18 untuk seed harga. Simpan observasi per SKU sebagai riwayat, tetapi hanya status bukti yang layak, tersedia, dan berumur maksimal 30 hari yang boleh masuk ranking; N08 menyimpan harga meskipun stok habis dan tidak ikut ranking.
- Implementasikan safety gate dan unit test terlebih dahulu, baru SAW.
- Animasi harus menghormati `prefers-reduced-motion`, tidak menutupi error, dan tidak menunda hasil lebih dari waktu proses sebenarnya.
- BAB IV final pada file 17 serta dokumen DOCX/PDF pendamping memuat hasil analisis dan perancangan yang sudah tersedia. Setelah sistem selesai, tambahkan tangkapan layar, hasil pengujian, UAT, serta pemeringkatan produk aktual.
- File `Draf-BAB-IV-Final-Perancangan-Sistem` merupakan kompilasi spesifikasi pendukung, bukan pengganti naskah BAB IV yang mengikuti template kampus.

## Pembaruan implementasi v3.3

- Hasil membedakan `eligible`, `excluded`, dan `insufficient_evidence`; kandidat non-ranking tetap disimpan untuk audit, sedangkan hanya `excluded` yang diringkas pada panel Safety Gate hasil.
- Jika hanya satu kandidat layak, label hasil menggunakan **skor relatif**. Nilai `1,0000` tidak boleh ditafsirkan sebagai skor absolut atau bukti produk terbaik secara universal.
- Keluhan `kusam` tersedia sebagai keluhan utama pada scoring. `secondary_concerns` sudah memengaruhi C1 melalui modifier dukungan keluhan pada `criteria-1.3.0`.
- Harga adalah observasi per SKU dengan `observed_at`, sumber, `is_available`, dan `ranking_eligible`. Batas default kesegaran data adalah 30 hari.
- Perubahan harga/formula tidak menimpa riwayat. Admin membuat observasi/versi baru, kemudian mempublikasikan snapshot dataset yang sesuai.
- Halaman hasil menampilkan foto kandidat `excluded` secara redup di dalam rincian Safety Gate tanpa menjadikannya tautan rekomendasi; tidak ada galeri non-ranking terpisah.
- Halaman hasil guest menawarkan penyimpanan ke akun setelah hasil tersedia. Proses klaim memperbarui `consultations.user_id` dan `expires_at` tanpa menghitung ulang.
- Halaman `/riwayat` menampilkan card ringkas berisi foto pilihan #1, brand, harga, ukuran, keluhan utama, usia, tanggal, status, dan dataset.
- Dataset aktif aplikasi: `1.4.5`; algoritma `saw-1.3.0`; scoring `criteria-1.3.0`.
- File UAT utama untuk pengujian aktual adalah `docs/19-UAT-Sistem-Rekomendasi-Facial-Wash.md`.
