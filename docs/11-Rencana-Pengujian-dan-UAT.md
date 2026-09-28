# Rencana Pengujian dan UAT

## 1. Strategi

Gunakan PHPUnit/Pest untuk unit dan feature test, PostgreSQL test database untuk fitur bergantung constraint, dan browser test untuk alur kritis. SQLite tidak cukup sebagai satu-satunya test DB karena perilaku constraint/JSON dapat berbeda.

## 2. Unit test

| ID | Komponen | Kasus |
|---|---|---|
| UT-01 | WeightNormalizer | 5,5,5,3,3,5 menjadi bobot tervalidasi dan total 1 |
| UT-02 | SawCalculator | Benefit normalization |
| UT-03 | SawCalculator | Cost normalization |
| UT-04 | SawCalculator | Contoh dummy menghasilkan nilai referensi |
| UT-05 | SafetyGate | Setiap red flag menghasilkan `refer` |
| UT-06 | Eligibility | Alergi cocok dengan INCI menghasilkan `excluded` |
| UT-07 | Evidence | INCI tidak lengkap menghasilkan `insufficient_evidence` |
| UT-08 | TieBreaker | Ranking stabil pada skor sama |
| UT-09 | Price | Konversi harga per 100 ml/g |
| UT-10 | Explanation | Kode alasan hanya berasal dari rule yang dijalankan |
| UT-11 | Motion payload | Nilai akhir counter/bar sama dengan skor server |
| UT-12 | Motion preference | Reduced motion menghasilkan durasi nol atau transisi minimal |

## 3. Feature test

- Usia 17/31 menerima pesan di luar scope tanpa menyatakan dilarang memakai facial wash.
- Semua pertanyaan keamanan wajib.
- Red flag tidak membuat `recommendation_results` biasa.
- Konsultasi aman menyimpan X, R, W, V, ranking, dan version ID.
- Pengguna tidak dapat membuka hasil milik akun lain.
- Admin tanpa izin tidak dapat mengaktifkan dataset.
- Produk pending tidak muncul di ranking publik.
- Formula lama tetap terhubung ke hasil lama setelah update.
- Navigasi Livewire berulang tidak menggandakan listener Anime.js.
- Red flag dapat dibaca segera tanpa menunggu timeline.
- Filter katalog tidak mengubah urutan semantik hasil atau fokus keyboard.
- Guest dapat menyelesaikan konsultasi tanpa login dan menerima hasil.
- Klaim hasil guest ke akun tidak membuat run atau perhitungan SAW baru.
- Card riwayat memuat foto dan ringkasan pilihan #1 tanpa membocorkan riwayat akun lain.

## 4. Security test

- CSRF, session fixation, brute-force/rate limit.
- IDOR pada UUID hasil.
- Mass assignment pada role/status/verified.
- XSS dari nama produk, sumber, dan catatan admin.
- URL sumber `javascript:` ditolak.
- Ekspor tidak memuat identitas/STR validator tanpa kewenangan.

## 5. UAT pengguna

| ID | Pernyataan UAT | Skala |
|---|---|---|
| UAT-01 | Instruksi pengisian mudah dipahami | 1-5 |
| UAT-02 | Istilah jenis dan keluhan kulit cukup jelas | 1-5 |
| UAT-03 | Peringatan keamanan mudah dipahami | 1-5 |
| UAT-04 | Alasan rekomendasi mudah ditelusuri | 1-5 |
| UAT-05 | Perbandingan tiga produk membantu keputusan | 1-5 |
| UAT-06 | Informasi sumber dan tanggal meningkatkan kepercayaan | 1-5 |
| UAT-07 | Tampilan nyaman di ponsel | 1-5 |
| UAT-08 | Pengguna memahami sistem bukan diagnosis | Ya/Tidak |
| UAT-09 | Perpindahan langkah terasa jelas dan tidak membingungkan | 1-5 |
| UAT-10 | Animasi hasil membantu memahami urutan rekomendasi | 1-5 |
| UAT-11 | Tampilan tetap nyaman bagi pengguna yang mengurangi gerakan | 1-5 |

## 6. Validasi ahli tahap lanjutan

- Review daftar pertanyaan pengguna.
- Review hard constraint dan red flag.
- Review mapping skor produk-konteks.
- Review wording output dan disclaimer.
- Review 10-20 kasus vinyet dan bandingkan hasil sistem dengan penilaian ahli.

Sebaiknya tambah 1-2 validator. Jika tidak memungkinkan, nyatakan keterbatasan generalisasi dan jangan menghitung kesepakatan antarpenilai.

## 7. Uji akurasi SAW

Pilih minimal lima kasus input. Ekspor matriks dari sistem, hitung manual/spreadsheet, dan bandingkan setiap R serta V dengan toleransi `1e-6`. Bukti uji memuat tanggal, commit hash, dataset version, dan penanggung jawab.

## 8. Definition of passed

- Seluruh test safety dan calculation lulus 100%.
- Tidak ada severity high/critical yang terbuka.
- Semua produk publik berstatus verified.
- UAT memenuhi target yang ditetapkan metodologi.
- Hasil dapat direproduksi dari snapshot.
- Tidak ada animasi yang menutupi, menunda, atau mengubah informasi keselamatan.
- Pengujian keyboard dan reduced motion lulus pada alur konsultasi sampai hasil.

## 9. Pengujian khusus Anime.js

- Uji akhir properti: opacity, transform, width, dan angka kembali pada state yang diharapkan.
- Uji cleanup: scope direvert saat navigasi atau komponen dilepas.
- Uji resize: split text/layout diperbarui tanpa duplikasi node.
- Uji performa: animasi utama memakai transform dan opacity, serta tidak menurunkan respons input secara nyata.
- Uji fallback: aplikasi tetap dapat menyelesaikan konsultasi ketika modul animasi gagal dimuat.
- Uji visual mobile 360 px, tablet, dan desktop.

## 10. Paket UAT aktual v3.3

Skenario penerimaan yang dapat langsung diisi penguji dipindahkan/diperluas pada [`19-UAT-Sistem-Rekomendasi-Facial-Wash.md`](19-UAT-Sistem-Rekomendasi-Facial-Wash.md). Dokumen tersebut menjadi lembar UAT utama dan mencakup:

- profil valid, batas usia penelitian, consent, dan red flag;
- safety gate serta alasan `excluded`;
- perbedaan `eligible`, `excluded`, dan `insufficient_evidence`;
- keluhan utama `kusam` dan verifikasi modifier `secondary_concerns`;
- satu kandidat dengan **skor relatif** versus beberapa kandidat dengan SAW komparatif;
- harga per 100 ml/g, observed date, stale price, `ranking_eligible`, dan pembaruan oleh admin;
- foto kandidat `excluded` yang redup di dalam rincian Safety Gate dan tidak dapat dipilih;
- riwayat, ekspor CSV/PDF, aksesibilitas, reduced motion, dan akses melalui tunnel publik.
- guest-first, CTA penyimpanan hasil, klaim UUID ke akun, dan card riwayat visual.

### 10.1 Kasus regresi yang wajib ditambahkan

| ID | Risiko regresi | Verifikasi |
|---|---|---|
| REG-01 | Skor satu kandidat kembali ditampilkan sebagai skor absolut | Pastikan label **skor relatif** dan `score_mode` snapshot |
| REG-02 | Kandidat yang dikeluarkan hilang dari audit | Pastikan foto, status, dan alasan tetap tampil tanpa link rekomendasi |
| REG-03 | Harga lama dipakai setelah observasi baru tidak layak | Uji observasi terbaru, status bukti, dan batas 30 hari |
| REG-04 | Jumlah pada banner tidak sama dengan baris hasil | Bandingkan ringkasan status, result rows, dan calculation snapshot |
| REG-05 | Keluhan tambahan hanya tersimpan tetapi tidak memengaruhi skor | Jalankan UAT-09; `criteria-1.3.0` harus menghasilkan modifier C1 yang dapat dibuktikan |
| REG-06 | Tunnel HTTPS menghasilkan asset HTTP | Periksa CSS, JS, gambar, dan Livewire melalui URL publik |
| REG-07 | Login menjadi wajib sebelum hasil | Jalankan konsultasi sebagai guest dan pastikan CTA login baru muncul setelah hasil |
| REG-08 | Klaim guest menghitung ulang hasil | Bandingkan UUID, run, dataset, algoritma, dan snapshot sebelum/sesudah login |
