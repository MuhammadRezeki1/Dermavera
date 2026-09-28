# Product Requirements Document (PRD)

## 1. Ringkasan produk

Web membantu pria usia 18-30 tahun menyaring dan mengurutkan varian facial wash berdasarkan keluhan, jenis/kondisi kulit, formulasi, harga, kemasan, serta kualitas/keamanan. Sistem menerapkan **aturan keamanan terlebih dahulu**, kemudian metode **Simple Additive Weighting (SAW)** terhadap alternatif yang lolos.

Antarmuka memakai animasi fungsional melalui Anime.js untuk memperjelas perpindahan langkah, status proses, perubahan ranking, dan hubungan skor dengan kriteria. Animasi tidak menjadi bagian dari logika keputusan.

## 2. Masalah

Pengguna menghadapi banyak varian dengan klaim serupa, formula berbeda, harga dinamis, serta informasi pH/konsentrasi yang sering tidak lengkap. Pemilihan hanya berdasarkan merek, popularitas, atau jumlah bahan aktif dapat menghasilkan pilihan yang tidak sesuai dan berpotensi menimbulkan iritasi.

## 3. Tujuan

- Menghasilkan peringkat varian facial wash yang transparan dan dapat dijelaskan.
- Menggunakan data produk yang bersumber dan bertanggal.
- Mencegah alternatif berisiko masuk ranking melalui safety gate.
- Menyediakan jejak audit bobot, aturan, data, dan hasil perhitungan.
- Mendukung pembahasan penelitian tanpa mengklaim diagnosis medis.

## 4. Bukan tujuan

- Mendiagnosis penyakit kulit.
- Menggantikan konsultasi dokter.
- Menjamin produk pasti cocok.
- Menganggap lebih banyak bahan aktif selalu lebih baik.
- Menebak pH, konsentrasi, izin edar, atau komposisi yang tidak tersedia.
- Menggeneralisasi hasil kepada usia di luar 18-30 tahun.

## 5. Pengguna dan peran

| Peran | Kebutuhan utama |
|---|---|
| Pengunjung | Melihat edukasi, katalog, sumber, dan batasan |
| Responden guest | Mengisi profil kulit dan melihat rekomendasi tanpa membuat akun terlebih dahulu |
| Pengguna terautentikasi | Mengisi profil kulit, menerima rekomendasi, melihat alasan/peringatan, dan menyimpan hasil ke riwayat |
| Admin/peneliti | Mengelola merek, varian, ingredients, aturan, bobot, harga, sumber, dan versi dataset |
| Validator ahli | Memberi validasi di luar aplikasi penelitian; hasil ringkas dimasukkan admin dengan bukti dan versi |

## 6. Ruang lingkup data

- Populasi penelitian: pria usia 18-30 tahun.
- Kelompok analisis opsional: 18-24 dan 25-30 tahun.
- Merek: Kahf, MS Glow For Men, Garnier Men, NIVEA Men, Men's Biore.
- Unit alternatif: formula/varian produk; perbedaan ukuran menjadi SKU, bukan alternatif baru, kecuali daftar komposisi berbeda dan belum dapat dipastikan sebagai formula sama.
- Kondisi dasar: normal, berminyak, kering, kombinasi.
- Overlay: sensitif/rentan iritasi, alergi, barrier terganggu, terapi acne, dan red flag.

## 7. Alur pengguna

1. Membaca persetujuan, batasan, dan peringatan.
2. Memasukkan usia 18-30 tahun.
3. Memilih keluhan utama dan kondisi kulit.
4. Menjawab overlay sensitivitas, alergi, toleransi, terapi, dan red flag.
5. Memasukkan rentang harga dan preferensi kemasan bila diinginkan.
6. Sistem menjalankan validasi input dan safety gate.
7. Jika red flag: sistem menghentikan ranking biasa dan menampilkan saran pemeriksaan tenaga kesehatan.
8. Jika aman: sistem menyaring alternatif layak, menghitung SAW, dan menampilkan tiga teratas.
9. Pengguna melihat skor per kriteria, alasan kecocokan, keterbatasan data, sumber, dan tanggal verifikasi.
10. Pengguna guest ditawari masuk/daftar setelah hasil tampil; pilihan ini bersifat opsional.
11. Jika guest memilih menyimpan hasil, sistem mengaitkan konsultasi ke akun tanpa menghitung ulang SAW.

## 8. Fitur MVP

- Landing page dan edukasi metodologi.
- Kuesioner profil kulit berbasis pilihan.
- Safety gate.
- Ranking SAW dan detail alasan.
- Katalog varian serta status sumber data.
- Riwayat rekomendasi pseudonim.
- Penyimpanan hasil guest ke akun secara opsional setelah rekomendasi tampil.
- Riwayat privat berbentuk card dengan foto dan ringkasan pilihan #1.
- Panel admin Filament untuk CRUD dan versioning.
- Umpan balik animasi untuk wizard, loading, hasil ranking, filter katalog, modal, dan notifikasi.
- Ekspor perhitungan ke PDF/CSV untuk audit penelitian.
- Log perubahan bobot, aturan, dan formula produk.

## 9. Aturan inti

- Bobot tervalidasi: C1 0,1923; C2 0,1923; C3 0,1923; C4 0,1154; C5 0,1154; C6 0,1923.
- C1, C2, C3, C5, C6 adalah benefit. C4 dapat dihitung sebagai cost numerik atau benefit kategori; pilih satu pendekatan dan kunci dalam versi algoritma.
- Skala kecocokan 1-5 diperbolehkan.
- Produk tanpa data formula minimum tidak boleh dipublikasikan sebagai alternatif aktif.
- Konsentrasi yang tidak diumumkan tidak memperoleh klaim efektivitas khusus.
- Data pH yang tidak tersedia tidak boleh diimputasi dengan angka tebakan.

## 10. Output rekomendasi

Setiap kartu hasil memuat: nama varian, merek, skor akhir 0-1, posisi, ringkasan C1-C6, faktor pengurang, peringatan, status data, harga dan tanggal observasi, tautan sumber, serta disclaimer “bukan diagnosis”.

## 11. Acceptance criteria utama

- Red flag tidak pernah menghasilkan ranking biasa.
- Produk yang melanggar hard constraint tidak dapat muncul walau murah atau kemasannya baik.
- Perhitungan dapat direproduksi dari snapshot input, bobot, dataset, dan versi aturan.
- Total bobot tersimpan bernilai 1 dengan toleransi pembulatan.
- Semua produk aktif memiliki minimal satu sumber, tanggal verifikasi, dan status komposisi.
- Setiap perubahan formula membuat versi baru, bukan menimpa riwayat lama.
- Pengguna dapat membaca alasan peringkat, bukan hanya angka akhir.
- Sistem tetap dapat dipakai dengan keyboard dan ketika `prefers-reduced-motion` aktif.
- Angka akhir selalu berasal dari respons server; animasi hanya bergerak menuju nilai tersebut dan tidak membuat nilai baru di browser.
- Pengguna tidak wajib login untuk menyelesaikan konsultasi atau melihat hasil.
- Klaim hasil guest ke akun mempertahankan UUID, snapshot, dataset, algoritma, dan hasil SAW yang sudah tersimpan.

## 12. Metrik penelitian

- Akurasi hitung terhadap perhitungan manual: 100% pada kasus uji.
- Konsistensi ranking pada input identik: 100%.
- UAT fungsi utama: minimal 80% pernyataan “sesuai/sangat sesuai”, dengan metode dan responden dilaporkan.
- Waktu respons ranking target: p95 kurang dari 2 detik pada dataset penelitian.
- Cakupan unit test service perhitungan dan safety gate: minimal 90% cabang penting.

## 13. Risiko

| Risiko | Mitigasi |
|---|---|
| Formula berubah | Versioning, foto label, tanggal verifikasi, nonaktifkan versi lama |
| Harga berubah | `observed_at`, riwayat harga, jangan tampilkan sebagai harga pasti |
| Satu validator | Nyatakan keterbatasan; tambah 1-2 validator bila memungkinkan |
| Data pH/konsentrasi tidak tersedia | `NULL/UNKNOWN`, tanpa imputasi |
| SAW mengompensasi risiko | Safety gate sebelum SAW |
| Klaim terlalu medis | Bahasa “dukungan kecocokan”, bukan terapi/diagnosis |
| Animasi berlebihan atau mengganggu | Batas durasi, reduced motion, fokus keyboard, dan larangan animasi dekoratif pada peringatan klinis |
| Livewire merender ulang DOM | Scope Anime.js per komponen dan cleanup sebelum navigasi/render ulang |

## 14. Pembaruan PRD berdasarkan implementasi v3.3

### 14.1 Status kandidat yang wajib dibedakan

Sistem tidak hanya memiliki keluaran “masuk” atau “tidak masuk”. Setiap kandidat yang diperiksa harus dapat berada pada salah satu status berikut:

| Status | Makna | Perlakuan pada SAW | Tampilan |
|---|---|---|---|
| `eligible` | Lolos safety gate, bukti minimum, dan preferensi harga | Masuk matriks X dan ranking | Kartu rekomendasi normal |
| `excluded` | Melanggar hard constraint atau batas harga | Tidak masuk matriks | Tampil di rincian Safety Gate, redup, foto bila ada, bukan tautan rekomendasi |
| `insufficient_evidence` | Bukti harga/formula tidak cukup, produk tidak tersedia, atau sudah kedaluwarsa | Tidak masuk matriks | Disimpan untuk audit; tidak ditampilkan sebagai kartu ranking. Harga dapat dilihat melalui detail katalog dengan label riwayat/tidak layak |

### 14.2 Mode skor

- Dua kandidat atau lebih memakai `comparative_saw` dan skor dibandingkan dalam himpunan kandidat layak pada konsultasi tersebut.
- Satu kandidat memakai `relative_single_candidate` dan UI wajib menulis **skor relatif**.
- Nilai `1,0000` pada mode tunggal berarti kandidat menjadi nilai acuan satu-satunya setelah normalisasi, bukan jaminan kecocokan absolut.
- Snapshot menyimpan `eligible_count` dan `score_mode` agar interpretasi riwayat tidak berubah.

### 14.3 Harga dan pemeliharaan data

Harga bukan atribut permanen produk. Admin memasukkan observasi baru dengan sumber dan `observed_at`; observasi lama tetap tersimpan. Observasi yang lebih tua dari `PRICE_MAX_AGE_DAYS` (default 30 hari), tidak tersedia, atau tidak memiliki bukti ranking tidak boleh masuk ranking. Setelah perubahan data yang memengaruhi hasil, admin membuat/mengaktifkan versi dataset baru.

### 14.4 Penerimaan tambahan

- Snapshot dan audit run menyimpan jumlah kandidat layak, dikeluarkan, dan data tidak cukup; halaman hasil menampilkan ranking serta panel eksklusi yang relevan tanpa menjadikan kandidat non-ranking sebagai rekomendasi.
- Alasan eksklusi dapat dibaca tanpa membuka kandidat sebagai rekomendasi.
- Foto kandidat `excluded` tidak mengubah status dan tidak dapat diklik menuju alur rekomendasi; tidak ada galeri non-ranking terpisah.
- Hasil CSV/PDF membawa status kandidat, alasan, harga bertanggal, dataset, algoritma, dan mode skor.
- Pengaruh `secondary_concerns` terhadap C1 sudah diterapkan dan tetap diverifikasi melalui UAT-09.

### 14.5 Alur akun dan riwayat

- Login tidak menjadi prasyarat konsultasi.
- Hasil guest diakses melalui session yang memiliki UUID konsultasi dan retensi guest yang dapat dikonfigurasi.
- CTA penyimpanan muncul setelah hasil tersedia, bukan sebagai paywall sebelum SAW.
- Setelah autentikasi, endpoint claim mengubah konsultasi guest menjadi milik akun, menghapus masa kedaluwarsa, dan mengarahkan pengguna kembali ke hasil yang sama.
- Halaman riwayat menampilkan ringkasan card; halaman hasil tetap menjadi sumber detail audit, matriks, alasan, dan ekspor.
