# Rancangan UI UX dan Daftar Halaman

## 1. Tujuan desain

Antarmuka membantu pengguna menyelesaikan konsultasi, memahami alasan rekomendasi, dan menelusuri sumber tanpa mengira sistem sebagai alat diagnosis. Arah visual menggunakan konsep **clinical clean modern**: profesional, ringan, tidak menyerupai toko daring, dan tidak terlalu menyerupai aplikasi rumah sakit.

Anime.js dipakai untuk menjelaskan perubahan state, bukan sebagai hiasan. Informasi penting harus tetap tersedia jika animasi dinonaktifkan.

## 2. Template dan teknologi

- Layout pengguna: Laravel Livewire Starter Kit dengan **header layout**.
- Komponen: Flux UI dan Tailwind CSS.
- Animasi: Anime.js 4 melalui Vite.
- Panel admin: Filament 5 dengan tema bersih dan fungsional.
- Pola navigasi desktop: navbar atas.
- Pola navigasi mobile: tombol menu yang membuka panel navigasi.

## 3. Design tokens

| Token | Nilai | Penggunaan |
|---|---|---|
| Navy | `#14213D` | Navbar, heading utama, footer |
| Indigo | `#5B4CDB` | CTA, menu aktif, skor utama |
| Mint | `#20C7B5` | Status cocok, indikator aman |
| Background | `#F6F7FB` | Latar halaman |
| Surface | `#FFFFFF` | Kartu dan dialog |
| Text | `#172033` | Teks utama |
| Muted | `#64748B` | Teks sekunder |
| Warning | `#F59E0B` | Perhatian dan data belum lengkap |
| Danger | `#EF4444` | Red flag dan error penting |
| Border | `#E2E8F0` | Garis kartu dan input |
| Radius | `14-18 px` | Kartu, input, modal |
| Font | Inter atau system sans | Seluruh aplikasi |
| Max width | `1180 px` | Konten desktop |

## 4. Motion tokens

| Token | Nilai | Contoh penggunaan |
|---|---:|---|
| `motion-fast` | 160-200 ms | Hover, centang, tooltip |
| `motion-base` | 280-360 ms | Card selection, modal |
| `motion-page` | 420-600 ms | Hero dan perpindahan section |
| `motion-score` | 800-1000 ms | Counter dan criterion bar |
| `stagger-list` | 60-90 ms | Card produk atau ingredients |
| `distance-small` | 8-16 px | Fade-slide komponen kecil |
| `distance-page` | 24-36 px | Masuk halaman atau section |

Gunakan easing lembut `out(3)` untuk elemen masuk dan `inOut(3)` untuk perpindahan layout. Jangan memakai animasi berulang pada konten utama.

## 5. Struktur navbar

```text
DERMASELECT   Beranda   Konsultasi   Katalog   Metode   Tentang   [Mulai]
```

- Navbar bersifat sticky dengan latar semi-transparan dan blur ringan.
- Garis indikator menu aktif bergeser mengikuti route.
- Tombol Mulai Konsultasi selalu terlihat pada desktop.
- Pada mobile, menu dibuka melalui tombol yang memiliki label aksesibel.
- Fokus keyboard tidak dipindahkan hanya karena animasi.

## 6. Sitemap pengguna

| Route | Halaman | Isi utama | Animasi utama |
|---|---|---|---|
| `/` | Beranda | Hero, manfaat, batasan, CTA | Text reveal, hero image, stagger card |
| `/metode` | Metode | Safety gate, C1-C6, SAW | Section reveal saat scroll |
| `/katalog` | Katalog | Filter merek, keluhan, status data | Auto Layout saat filter berubah |
| `/produk/{slug}` | Detail varian | Formula, actives, sumber, harga | Ingredient chips dan modal sumber |
| `/konsultasi/mulai` | Persetujuan | Scope, privasi, disclaimer | Fade-slide ringkas |
| `/konsultasi/profil` | Profil | Usia, keluhan, kondisi dasar | Step transition dan progress |
| `/konsultasi/keamanan` | Safety | Sensitif, alergi, terapi, red flag | Selection feedback; red flag tanpa delay |
| `/konsultasi/preferensi` | Preferensi | Harga dan kemasan | Step transition |
| `/hasil/{uuid}` | Hasil | Top 3, skor, alasan, peringatan | Ranking reveal, count-up, criterion bars |
| `/riwayat` | Riwayat | Card hasil milik pengguna: foto pilihan #1, brand, harga, ukuran, profil singkat, status, dan dataset | Stagger card satu kali |

## 7. Rancangan halaman beranda

Urutan konten:

1. Navbar atas.
2. Hero dengan judul, penjelasan singkat, dan dua CTA.
3. Pilihan masalah kulit: berminyak, jerawat ringan, kering, kusam, sensitif.
4. Penjelasan tiga langkah penggunaan.
5. Penjelasan singkat safety gate dan SAW.
6. Cuplikan katalog.
7. Batasan dan disclaimer.
8. Footer sumber dan kontak akademik.

Anime.js menampilkan judul per kata, CTA, gambar produk, dan kartu kondisi secara berurutan. Animasi hanya berjalan sekali ketika halaman pertama kali masuk.

## 8. Rancangan wizard konsultasi

Setiap halaman konsultasi memakai struktur tetap:

- judul tahap;
- progress stepper;
- satu kelompok pertanyaan utama;
- penjelasan istilah bila diperlukan;
- tombol Kembali dan Selanjutnya;
- ringkasan error di atas dan error dekat pertanyaan.

Interaksi pilihan:

- kartu terpilih membesar maksimal 1,03;
- border berubah menjadi indigo;
- ikon centang muncul dengan scale singkat;
- tombol Selanjutnya aktif setelah validasi server;
- pilihan tidak terpilih tetap terbaca dan tidak dibuat terlalu redup.

Jika validasi gagal, pertanyaan bergerak horizontal 4-6 px sebanyak dua kali dan fokus dipindahkan ke pesan error. Red flag tidak memakai efek dramatis; peringatan ditampilkan langsung dan tegas.

## 9. State proses rekomendasi

Status proses yang diperbolehkan:

1. Memeriksa kelengkapan jawaban.
2. Menjalankan pemeriksaan keamanan.
3. Menyaring alternatif yang layak.
4. Menghitung nilai SAW.
5. Menyusun penjelasan hasil.

Status harus mengikuti proses nyata. Jangan menampilkan status palsu atau menahan hasil hanya untuk menyelesaikan animasi. Jika respons server selesai lebih cepat, timeline dipercepat menuju status akhir.

## 10. Susunan halaman hasil

1. Banner status keamanan.
2. Ringkasan input pengguna.
3. Tiga kartu ranking.
4. Grafik kontribusi C1-C6.
5. Alasan sesuai dan faktor pengurang.
6. Bahan penting serta status konsentrasi.
7. Harga dan tanggal observasi.
8. Sumber serta status verifikasi.
9. Disclaimer dan kapan perlu menemui dokter.

Untuk pengguna guest, setelah ringkasan input dan sebelum daftar ranking, tampilkan panel **Hasil sudah siap** dengan CTA **Masuk/daftar dan simpan**. Panel harus menjelaskan bahwa login tidak wajib, hasil tidak dihitung ulang setelah klaim, dan guest tetap dapat melanjutkan membaca hasil.

Urutan animasi hasil:

1. Banner keamanan tersedia segera.
2. Judul hasil fade-in.
3. Kartu peringkat pertama, kedua, dan ketiga muncul berurutan.
4. Angka bergerak dari 0 menuju skor final server.
5. Criterion bar terisi menuju nilai kontribusi final.
6. Badge peringkat pertama muncul tanpa loop.

## 11. Katalog dan detail produk

- Grid empat kolom pada layar besar, dua kolom pada tablet, dan satu kolom pada mobile.
- Filter memakai chip atau select dengan label jelas.
- Saat filter berubah, Auto Layout menggerakkan kartu ke posisi baru dan melakukan fade pada elemen masuk atau keluar.
- Nama, status formula, ukuran, dan sumber tetap menjadi informasi utama.
- Draggable hanya boleh digunakan untuk carousel atau perbandingan mobile, bukan untuk mengubah ranking SAW.

## 12. Komponen

- `TopNavbar`, `MobileNavigation`, `HeroSection`, `SkinConcernCard`.
- `QuestionCard`, `ChoiceCard`, `SafetyAlert`, `ProgressStepper`.
- `ProcessingStages`, `RecommendationCard`, `CriterionBar`, `ScoreCounter`.
- `EvidenceBadge`, `DataFreshnessBadge`, `IngredientChip`, `IngredientTable`.
- `PriceObservation`, `SourceList`, `LimitationNotice`, `Toast`.

## 13. Microcopy penting

- “Rentang 18-30 tahun adalah ruang lingkup penelitian, bukan batas usia wajib menggunakan facial wash.”
- “Rekomendasi membantu membandingkan produk kosmetik dan bukan diagnosis medis.”
- “Komposisi dan harga dapat berubah. Periksa kembali label kemasan.”
- “Nilai pH atau konsentrasi tidak dicantumkan sumber; sistem tidak menebaknya.”
- “Keluhan yang dipilih memerlukan evaluasi tenaga kesehatan; ranking produk tidak ditampilkan.”

## 14. Responsif dan aksesibilitas

- Target sentuh minimal 44 x 44 px.
- Tidak mengandalkan warna; sertakan ikon, teks, dan status.
- Fokus keyboard terlihat dan urutan tab mengikuti urutan visual.
- Grafik memiliki tabel atau teks alternatif.
- `prefers-reduced-motion: reduce` menonaktifkan perpindahan besar, stagger, parallax, dan animasi angka panjang.
- Konten tidak disembunyikan dari pembaca layar selama animasi.
- Animasi error tidak menjadi satu-satunya cara menyampaikan masalah.

## 15. Empty loading error state

- Loading: indikator ringkas dan status faktual berdasarkan proses server.
- Tidak ada hasil: tampilkan alasan, jangan mengendurkan safety gate.
- Data kedaluwarsa: tampilkan badge dan keluarkan dari ranking bila melampaui aturan.
- Server error: tampilkan reference ID tanpa stack trace.
- Anime.js gagal: tampilan berhenti pada state akhir CSS dan fungsi utama tetap berjalan.
- Hasil guest: tampilkan CTA penyimpanan setelah hasil tersedia; jangan mengubahnya menjadi redirect login wajib.

## 16. Batas penggunaan animasi

- Tidak ada autoplay yang terus berulang pada halaman hasil.
- Tidak ada parallax besar pada wizard atau red flag.
- Tidak ada animasi yang memindahkan tombol ketika akan diklik.
- Tidak ada simulasi skor yang berbeda dari hasil server.
- Tidak ada loading minimum buatan hanya untuk mempertontonkan animasi.

## 17. Sumber desain teknis

- [Flux UI Header Layout](https://fluxui.dev/layouts/header)
- [Flux UI Navbar](https://fluxui.dev/components/navbar)
- [Anime.js Documentation](https://animejs.com/documentation/)
- [Anime.js Timeline](https://animejs.com/documentation/timeline/)
- [Anime.js Scroll Observer](https://animejs.com/documentation/events/onscroll/)
- [Anime.js SVG](https://animejs.com/documentation/svg/)
- [Anime.js Layout](https://animejs.com/documentation/layout/)
- [Anime.js Scope](https://animejs.com/documentation/scope/)

## 18. Pembaruan halaman hasil v3.3

Halaman `/hasil/{uuid}` wajib memiliki empat blok status yang dapat dibaca tanpa animasi:

1. Status safety gate dan disclaimer.
2. Ringkasan profil konsultasi, termasuk keluhan utama dan keluhan tambahan.
3. Ringkasan jumlah kandidat: layak, dikeluarkan, dan data tidak cukup.
4. Ranking serta rincian audit Safety Gate.

Jika hanya satu kandidat layak, label angka berubah menjadi **SKOR RELATIF** dan diberi keterangan bahwa `1,0000` adalah hasil normalisasi terhadap satu kandidat. Jika dua kandidat atau lebih layak, label boleh menggunakan **SKOR AKHIR/SAW KOMPARATIF**.

Rincian kandidat Safety Gate:

- menampilkan kode, merek, nama, ukuran, foto produk jika tersedia, status, dan alasan;
- dibuat sedikit redup/tersaturasi rendah agar jelas bukan rekomendasi;
- tidak memiliki link kartu atau aksi pemilihan;
- menampilkan kandidat `excluded` di dalam panel Safety Gate, bukan pada galeri non-ranking terpisah;
- tetap dapat dibaca keyboard dan pembaca layar.

Panel data harga menampilkan nilai harga, unit 100 ml/g, sumber, `observed_at`, serta pesan bila melewati batas 30 hari. Admin memperbarui harga melalui observasi baru, bukan mengubah riwayat lama.

## 19. Referensi UAT

Kasus visual dan interaksi hasil dirujuk pada `docs/19-UAT-Sistem-Rekomendasi-Facial-Wash.md`, terutama UAT-09, UAT-11, UAT-13, UAT-15, UAT-19, dan UAT-20.

## 20. Riwayat dan akses guest

- Konsultasi guest disimpan dengan UUID dan daftar akses pada session browser.
- Login/daftar hanya ditawarkan setelah hasil SAW selesai.
- Endpoint claim mengaitkan konsultasi guest ke akun dan menghapus masa kedaluwarsa tanpa membuat run baru.
- Card riwayat menampilkan informasi sekilas agar mudah dipindai; pengguna membuka card untuk detail audit lengkap.
- Card tidak menampilkan seluruh 37 kandidat, hanya pilihan #1 dan konteks ringkas supaya halaman tidak padat.
