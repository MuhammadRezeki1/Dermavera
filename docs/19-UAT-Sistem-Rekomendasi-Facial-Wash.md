# User Acceptance Test (UAT) Sistem Rekomendasi Facial Wash

**Versi dokumen:** 1.1  
**Tanggal:** 28 September 2026  
**Build yang diuji:** `saw-1.3.0` / `criteria-1.3.0` / dataset `1.4.5`  
**Lingkungan:** Docker Compose, PostgreSQL, browser desktop dan mobile  
**Penanggung jawab:** ____________________  
**Validator/penguji:** ____________________

## 1. Tujuan dan ruang lingkup

UAT ini memeriksa apakah alur pengguna, safety gate, perhitungan SAW, keterlacakan data, panel admin, ekspor, dan tampilan hasil sudah sesuai kebutuhan penelitian.

UAT membedakan tiga keadaan kandidat:

1. **Layak diranking:** masuk matriks X, normalisasi R, kontribusi, dan nilai V.
2. **Dikeluarkan:** diperiksa tetapi melanggar hard constraint atau preferensi harga pengguna; tidak masuk matriks.
3. **Data tidak cukup:** tidak dapat dinilai karena bukti harga/formula belum memenuhi syarat; tidak dipaksakan masuk ranking.

UAT tidak menguji klaim diagnosis atau kecocokan absolut produk.

Alur akun yang diuji bersifat **guest-first**: login tidak wajib sebelum SAW. Guest dapat melihat hasil, kemudian memilih menyimpan hasil ke akun tanpa menghitung ulang. Riwayat akun menampilkan card ringkas dengan foto dan konteks pilihan #1.

## 2. Data awal dan kriteria penerimaan

- Katalog baseline: 37 record produk, dengan 35 varian aktif/terverifikasi sesuai dataset yang dipakai aplikasi.
- Bobot aktif: C1/C2/C3/C6 = `5/26` dan C4/C5 = `3/26`.
- Batas kesegaran harga: 30 hari melalui `PRICE_MAX_AGE_DAYS` atau `config/dermavera.php`.
- Harga dinilai dari observasi terbaru untuk SKU, lalu diperiksa status ketersediaan, bukti ranking, dan usia observasinya.
- Harga C4 memakai harga unit per 100 ml/g; C4 adalah cost.
- Keluaran hasil menyimpan input, dataset, algoritma, matriks X, normalisasi R, bobot W, kontribusi, skor V, ranking, dan alasan non-ranking.
- Nilai skor tunggal diberi label **skor relatif**, bukan klaim bahwa produk memperoleh nilai absolut terbaik.

## 3. Skenario UAT utama

| ID | Skenario | Langkah ringkas | Hasil yang diharapkan | Status |
|---|---|---|---|---|
| UAT-01 | Membuka konsultasi | Buka `/konsultasi/mulai`, baca persetujuan, lanjutkan | Persetujuan, ruang lingkup penelitian, privasi, dan disclaimer tampil; pengguna tidak dapat melewati persetujuan | ☐ Lulus ☐ Gagal |
| UAT-02 | Input profil valid | Isi usia 18-30, keluhan utama, keluhan tambahan, jenis kulit, lalu lanjut | Semua nilai tersimpan dan tampil kembali pada ringkasan hasil | ☐ Lulus ☐ Gagal |
| UAT-03 | Batas usia penelitian | Uji usia 17 dan 31 | Sistem memberi pesan di luar populasi penelitian tanpa menyatakan pengguna dilarang memakai facial wash | ☐ Lulus ☐ Gagal |
| UAT-04 | Skrining keamanan | Isi sensitif, alergi, barrier, terapi acne, dan red flag | Semua pertanyaan wajib dijawab; pesan validasi jelas dan dapat dibaca keyboard | ☐ Lulus ☐ Gagal |
| UAT-05 | Red flag | Isi kasus dengan red flag | Ranking biasa tidak dibuat; tampil rujukan/peringatan dan disclaimer | ☐ Lulus ☐ Gagal |
| UAT-06 | Safety gate formula | Gunakan profil barrier terganggu/sensitif pada produk dengan scrub fisik | Kandidat diberi status `excluded`, alasan HC-02 tampil, dan tidak masuk matriks SAW | ☐ Lulus ☐ Gagal |
| UAT-07 | Alergi | Masukkan alergi yang cocok dengan INCI/bahan produk | Kandidat konflik dikeluarkan dan alasan alergi dapat ditelusuri | ☐ Lulus ☐ Gagal |
| UAT-08 | Profil dengan keluhan kusam | Pilih `kusam` sebagai keluhan utama | C1 mempertimbangkan bahan pendukung brightening yang terdokumentasi; penjelasan tidak mengklaim terapi medis | ☐ Lulus ☐ Gagal |
| UAT-09 | Keluhan tambahan kusam | Pilih keluhan utama selain kusam dan `kusam` sebagai keluhan tambahan | Sistem memberi modifier C1 sesuai aturan yang disahkan; perubahan dapat dibuktikan pada snapshot/skor dan tidak dianggap lulus hanya karena field tersimpan | ☐ Lulus ☐ Gagal |
| UAT-10 | Banyak kandidat layak | Gunakan profil yang menghasilkan minimal dua kandidat layak | Hasil memakai label/perilaku **SAW komparatif**, ranking menurun, tie-break deterministik | ☐ Lulus ☐ Gagal |
| UAT-11 | Satu kandidat layak | Gunakan profil yang hanya menyisakan satu kandidat | Kartu menampilkan **skor relatif**, penjelasan bahwa normalisasi terhadap kandidat tunggal menghasilkan 1,0000, dan tidak menyebutnya bukti terbaik universal | ☐ Lulus ☐ Gagal |
| UAT-12 | Semua kandidat tidak layak | Gunakan profil yang membuat matriks kosong | Sistem menampilkan `no_safe_alternative`, alasan, dan tidak membuat ranking palsu | ☐ Lulus ☐ Gagal |
| UAT-13 | Kandidat dikeluarkan | Buka rincian Safety Gate | Semua kandidat yang dikeluarkan tampil di dalam rincian Safety Gate, dengan foto bila tersedia, tampilan redup, tidak dapat diklik sebagai rekomendasi, dan alasan eksklusi | ☐ Lulus ☐ Gagal |
| UAT-14 | Bukti harga tidak cukup | Gunakan kandidat tanpa bukti harga ranking atau dengan harga tidak tersedia | Kandidat berstatus `insufficient_evidence` pada result/snapshot, tidak masuk matriks, dan tidak menjadi kartu ranking; detail katalog boleh menampilkan label riwayat/tidak layak | ☐ Lulus ☐ Gagal |
| UAT-15 | Harga kedaluwarsa | Siapkan observasi lebih tua dari batas 30 hari | Kandidat tidak diranking; status dan alasan stale tersimpan pada hasil/snapshot serta tanggal observasi tetap dapat diaudit | ☐ Lulus ☐ Gagal |
| UAT-16 | Pembaruan harga oleh admin | Admin menambah observasi harga baru dengan sumber dan tanggal hari ini | Observasi lama tetap menjadi riwayat; observasi baru dapat dipakai setelah dataset aktif/snapshot yang sesuai dipublikasikan | ☐ Lulus ☐ Gagal |
| UAT-17 | Ukuran 100 ml/100 g | Uji SKU 100 ml dan 100 g dengan harga berbeda | C4 menggunakan harga per 100 satuan; unit tidak tertukar antara ml dan g | ☐ Lulus ☐ Gagal |
| UAT-18 | Riwayat dan privasi | Simpan hasil sebagai pengguna, lalu buka akun lain/UUID hasil lain | Pengguna hanya melihat riwayat sendiri; hasil tetap dapat dibaca ulang dengan snapshot yang sama | ☐ Lulus ☐ Gagal |
| UAT-19 | Ekspor | Klik ekspor CSV dan PDF pada hasil | File berhasil diunduh; ranking eligible memuat input, skor, harga/sumber/tanggal, dataset, dan algoritma. Status non-ranking tetap tersedia pada snapshot/audit | ☐ Lulus ☐ Gagal |
| UAT-20 | Aksesibilitas hasil | Uji keyboard, pembaca layar dasar, viewport 360 px, dan reduced motion | Fokus terlihat, tidak ada informasi yang hanya disampaikan lewat warna/animasi, dan isi tetap tersedia saat motion dikurangi | ☐ Lulus ☐ Gagal |
| UAT-21 | Tunnel publik | Buka URL publik melalui perangkat/jaringan berbeda | CSS, JS, gambar, Livewire, navigasi konsultasi, dan halaman hasil tetap menggunakan HTTPS serta dapat dimuat | ☐ Lulus ☐ Gagal |
| UAT-22 | Konsultasi tanpa login | Selesaikan wizard sebagai guest sampai tombol hasil | SAW dijalankan tanpa redirect login; hasil dapat dibaca dan terdapat CTA masuk/daftar untuk menyimpan | ☐ Lulus ☐ Gagal |
| UAT-23 | Klaim hasil guest | Dari CTA hasil, masuk/daftar lalu kembali ke hasil | Konsultasi guest berpindah ke akun, tetap memakai UUID/snapshot/dataset/run yang sama, dan tidak menghitung ulang SAW | ☐ Lulus ☐ Gagal |
| UAT-24 | Card riwayat | Buka `/riwayat` setelah hasil diklaim | Setiap card menampilkan foto pilihan #1, brand, harga, ukuran, keluhan utama, usia, tanggal, status, dataset, dan link hasil lengkap | ☐ Lulus ☐ Gagal |

## 4. Validasi perhitungan SAW

### 4.1 Bobot

Input bobot mentah `[5, 5, 5, 3, 3, 5]` harus menghasilkan:

```text
jumlah = 26
C1 = C2 = C3 = C6 = 5/26 = 0,1923076923
C4 = C5 = 3/26 = 0,1153846154
total = 1,0000000000
```

### 4.2 Kasus satu kandidat

Dengan satu baris matriks X, setiap nilai benefit menjadi `x/max(x)=1` dan harga cost menjadi `min(x)/x=1`. Nilai V menjadi `1,0000000000` karena itu adalah skor relatif terhadap himpunan kandidat yang hanya berisi satu baris.

Kriteria lulus: UI memakai label **skor relatif** dan menjelaskan konteksnya.

### 4.3 Kasus beberapa kandidat

Gunakan contoh berikut untuk membandingkan hasil aplikasi dengan spreadsheet:

| Alternatif | C1 | C2 | C3 | C4 | C5 | C6 |
|---|---:|---:|---:|---:|---:|---:|
| A | 5 | 4 | 4 | 45000 | 4 | 5 |
| B | 4 | 5 | 5 | 60000 | 3 | 5 |
| C | 3 | 3 | 4 | 35000 | 5 | 4 |

Rumus:

```text
Benefit: R_ij = X_ij / max(X_j)
Cost:    R_ij = min(X_j) / X_ij
V_i = jumlah(W_j * R_ij)
```

Toleransi perbandingan: `1e-6` untuk R, kontribusi, dan V. Ranking harus konsisten pada input yang sama.

## 5. Form pencatatan hasil

| ID test | Tanggal/jam | Browser/perangkat | Dataset | Hasil aktual | Bukti/screenshot | Status | Catatan defect |
|---|---|---|---|---|---|---|---|
| | | | | | | | |
| | | | | | | | |
| | | | | | | | |

## 6. Kriteria penerimaan UAT

- UAT-01 sampai UAT-08, UAT-10 sampai UAT-24 lulus tanpa defect high/critical.
- UAT-09 wajib ditutup dengan implementasi atau dicatat sebagai gap yang disetujui pembimbing/validator; tidak boleh diam-diam dianggap lulus.
- Perhitungan manual dan aplikasi sama dalam toleransi `1e-6`.
- Safety gate tidak boleh dilonggarkan hanya agar ranking terisi.
- Semua kandidat non-ranking tetap dapat diaudit, tetapi tidak dipresentasikan sebagai rekomendasi.
- Hasil historis tetap dapat direproduksi setelah harga, formula, bobot, atau dataset diperbarui.
- UAT publik hanya menggunakan data demo; Quick Tunnel tidak dianggap deployment produksi.

## 7. Defect log

| ID | Severity | Deskripsi | Langkah reproduksi | Perbaikan | Status |
|---|---|---|---|---|---|
| D-001 | | | | | ☐ Open ☐ Fixed ☐ Accepted |
| D-002 | | | | | ☐ Open ☐ Fixed ☐ Accepted |

## 8. Persetujuan

| Peran | Nama | Tanda tangan | Tanggal |
|---|---|---|---|
| Pengembang | | | |
| Penguji/UAT | | | |
| Validator/pembimbing | | | |

## 9. Catatan implementasi dan gap yang masih perlu dicatat

Catatan ini berasal dari audit implementasi. Kasus berikut tetap perlu diuji dan tidak boleh ditandai lulus hanya karena UI sudah menampilkan field terkait:

1. `secondary_concerns` sudah tersimpan pada input dan modifier C1 sudah diterapkan pada `criteria-1.3.0`; UAT-09 harus membandingkan profil yang sama dengan dan tanpa keluhan tambahan.
2. Ekspor CSV saat ini terutama menulis baris `eligible`. Jika kebutuhan penelitian mewajibkan kandidat `excluded` dan `insufficient_evidence` ikut diekspor, UAT-19 memerlukan perluasan export contract dan pengujian ulang.
3. Pemilihan harga mengikuti observasi terbaru untuk audit, lalu memeriksa `is_available`, `ranking_eligible`, dan usia untuk ranking. Observasi stok habis seperti N08 disimpan dengan harga Rp32.700 tetapi tidak memengaruhi ranking.
4. CTA claim guest dan card riwayat perlu diuji pada browser yang sama setelah login/daftar; hasil tidak boleh dibuka oleh session guest lain.

UAT dinyatakan selesai setelah empat catatan tersebut berstatus **Fixed** atau **Accepted** dengan persetujuan pembimbing/validator.
