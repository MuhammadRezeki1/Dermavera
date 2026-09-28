# Dermavera

Sistem pendukung keputusan pemilihan facial wash pria usia 18–30 tahun menggunakan safety gate dan Simple Additive Weighting (SAW). Aplikasi ini bukan alat diagnosis atau pengganti dokter.

## Fitur

- Wizard konsultasi empat tahap dengan validasi server.
- Red flag dan hard constraint dijalankan sebelum SAW.
- 37 record produk (35 alternatif aktif dan 2 historis) dari dokumentasi versi 3.2.
- Observasi harga bertanggal dari dokumen 18; bukti historis tetap tersimpan tetapi tidak dipaksakan masuk ranking.
- SAW presisi desimal, tie-break deterministik, dan snapshot X/R/W/V.
- Hasil membedakan kandidat `eligible`, `excluded`, dan `insufficient_evidence`; satu kandidat diberi label skor relatif.
- Katalog, detail INCI/sumber/BPOM, konsultasi guest-first, penyimpanan hasil ke akun secara opsional, riwayat privat berbentuk card, ekspor CSV/PDF.
- Panel admin Filament untuk master data, formula, harga, aturan, bobot, dataset, dan audit.
- UI Livewire + Flux + Tailwind dengan Anime.js, fallback tanpa JavaScript, keyboard, dan reduced motion.

## Menjalankan lokal

Persyaratan: PHP 8.3+, Composer 2, Node.js 24+, PostgreSQL 17 (atau SQLite hanya untuk pengembangan/test).

```bash
cp .env.example .env
composer install
php artisan key:generate
npm ci
npm run build
php artisan migrate --seed
php artisan dermavera:create-admin
composer run dev
```

Untuk Docker, salin `.env.example` menjadi `.env`, ganti `DB_PASSWORD`, lalu:

```bash
docker compose up --build -d
docker compose exec app php artisan migrate --seed --force
docker compose exec app php artisan dermavera:create-admin
```

Aplikasi tersedia di `http://localhost:8080`, panel admin di `/admin`, dan health check di `/up`.

Konsultasi tidak mewajibkan login. Pengguna guest dapat melihat hasil terlebih dahulu, lalu memilih **Masuk/daftar dan simpan**. Hasil guest yang diklaim ke akun dipindahkan ke riwayat pengguna tanpa menjalankan ulang SAW; hasil guest yang tidak diklaim tetap terikat pada session dan retensi guest.

PostgreSQL proyek diekspos khusus pada `127.0.0.1:5433` agar tidak berbenturan dengan PostgreSQL lokal di port `5432`. Parameter pgAdmin:

- Name: `Dermavera Docker`
- Host: `127.0.0.1`
- Port: `5433`
- Maintenance database: `dermavera`
- Username: `dermavera_app`
- Password: nilai `DB_PASSWORD` di `.env`

Definisi server siap impor tersedia di `docker/pgadmin/servers.json`. Pada instalasi saat ini definisi tersebut memakai `PasswordExecCommand`, sehingga pgAdmin membaca password langsung dari `.env` tanpa menyimpannya di berkas JSON.

## Data dan rilis dataset

Harga tidak diinventarisasi sebagai nilai tetap. `PriceObservationSeeder` membaca `docs/18-Referensi-Harga-37-Record.md`, memvalidasi kode, ukuran SKU, harga per 100 ml/g, sumber, tanggal, dan status bukti. Observasi lebih tua dari 30 hari atau berstatus historis/arsip/last chance tidak ikut ranking. Untuk mengimpor ulang dan mengunci snapshot baru:

```bash
php artisan db:seed --class=PriceObservationSeeder --force
php artisan dermavera:dataset:snapshot 1.1.0 --activate --notes="Audit label dan harga selesai"
php artisan dermavera:smoke-recommendation
```

Formula lama tidak ditimpa. K01/K02 menyimpan formula per ukuran; B03 Acne Skincare dan B03N Acne Bright Care adalah record terpisah.

## Verifikasi

```bash
composer validate --strict
vendor/bin/pint --test
vendor/bin/phpstan analyse
php artisan test
npm run build
```

Test menggunakan SQLite in-memory untuk kecepatan. Sebelum produksi, jalankan migration/integration test tambahan pada PostgreSQL sesuai `docs/11-Rencana-Pengujian-dan-UAT.md`.

Lembar UAT yang dapat langsung diisi tersedia pada [`docs/19-UAT-Sistem-Rekomendasi-Facial-Wash.md`](docs/19-UAT-Sistem-Rekomendasi-Facial-Wash.md). Dokumen tersebut mencakup mode skor relatif, kandidat non-ranking, kesegaran harga, ekspor, aksesibilitas, dan pengujian URL publik HTTPS.
