# Rencana Implementasi Laravel - PostgreSQL

## 1. Urutan kerja

| Sprint | Deliverable |
|---:|---|
| 0 | Bekukan scope, kamus istilah, 33 alternatif awal, record B03N, status data, dan kriteria |
| 1 | Inisialisasi Laravel Livewire Starter Kit, PostgreSQL, auth, role/policy |
| 2 | Migrasi ERD, model, factory, seed master, audit log |
| 3 | Filament CRUD merek-varian-SKU-formula-ingredient-source-harga |
| 4 | Knowledge base, safety gate, unit test red flag/hard constraint |
| 5 | Scoring C1-C6, SAW, tie-break, explanation, unit test |
| 6 | Wizard konsultasi, halaman hasil responsif, dan integrasi Anime.js |
| 7 | Versioning dataset, ekspor, riwayat, privacy controls |
| 8 | UAT, validasi kasus ahli, perbaikan, deployment, dokumentasi Bab IV/V |

## 2. Perintah awal yang disarankan

```bash
laravel new facial-wash-recommender
# Pilih Livewire starter kit dan PostgreSQL pada installer.
cd facial-wash-recommender
composer require filament/filament
php artisan filament:install --panels
npm install
npm install animejs
npm run build
php artisan migrate
```

Ikuti dokumentasi versi yang benar saat eksekusi. Jangan menyalin nomor versi secara membabi buta.

## 3. Konfigurasi `.env` lokal

```dotenv
APP_NAME="Facial Wash Recommender"
APP_ENV=local
APP_DEBUG=true
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=facial_wash
DB_USERNAME=facial_wash_app
DB_PASSWORD=change_me
```

Jangan commit `.env`. Produksi menggunakan `APP_DEBUG=false`, password kuat, TLS, dan secret manager bila tersedia.

## 4. Urutan migrasi

1. Users/roles.
2. Brands, variants, SKUs.
3. Ingredients dan formula versions.
4. Sources, evidence, price observations.
5. Criteria, scales, weights.
6. Rules dan product scores.
7. Dataset versions.
8. Consultations, safety, runs, results.
9. Audit logs.

## 5. Seed data

- `CriterionSeeder`: C1-C6.
- `ValidatedWeightSeeder`: 5,5,5,3,3,5 dan normalisasi.
- `BrandSeeder`: lima merek.
- `ProductCatalogSeeder`: hanya data berstatus verified.
- `SafetyRuleSeeder`: red flag dan hard constraint.
- `AdminSeeder`: lokal saja; kredensial produksi dibuat aman.

Produk berstatus `PENDING_LABEL_VERIFICATION` boleh masuk staging/admin sebagai draft tetapi tidak masuk dataset aktif.

## 6. Algoritma service

```text
validate input
create consultation snapshot
safety = safetyGate.assess(profile)
if safety.refer: persist and return referral
eligible = eligibility.filter(activeVariants, profile)
if empty: return no-safe-alternative
matrix = scoring.build(eligible, profile, dataset)
result = saw.calculate(matrix, activeWeights)
ranked = tieBreaker.sort(result)
persist complete audit snapshot
return explanation.build(ranked)
```

Browser menerima hasil yang sudah final. Anime.js hanya menjalankan transisi tampilan setelah payload hasil diterima.

## 7. CI pipeline

```text
composer validate
php-cs-fixer/pint
phpstan/larastan
php artisan test
npm ci && npm run build
migration test against PostgreSQL
dependency/security audit
```

## 8. Dataset release checklist

- [ ] Semua nama varian cocok dengan kemasan.
- [ ] INCI raw ditranskripsi dua kali/peer checked.
- [ ] Sumber dan tanggal akses tersedia.
- [ ] Formula/ukuran yang berbeda tidak tergabung keliru.
- [ ] Harga punya source dan observed_at.
- [ ] BPOM diverifikasi melalui kanal resmi.
- [ ] Mapping skor disetujui validator.
- [ ] Tidak ada pH/konsentrasi tebakan.
- [ ] Hash dataset dibuat dan versi diaktifkan.

## 9. Definition of done sistem

- Safety gate dan SAW teruji.
- 37 record produk terdokumentasi; status aktif/terverifikasi tetap dipisahkan dari record historis atau data yang belum lengkap.
- Hasil transparan dan dapat direproduksi.
- Admin dapat memperbarui formula tanpa merusak hasil historis.
- UAT selesai dan bukti dimasukkan ke Bab IV/V.
- Disclaimer dan rujukan dokter tampil sesuai kondisi.
- Animasi konsultasi dan hasil memiliki fallback tanpa motion, cleanup Livewire, serta pengujian reduced motion.
- Hasil akhir counter dan bar sama persis dengan skor server.

## 10. Urutan implementasi Anime.js

1. Instal `animejs` melalui npm dan impor modul melalui Vite.
2. Buat motion tokens untuk durasi, easing, stagger, dan batas gerakan.
3. Implementasikan `createScope()` per komponen Livewire.
4. Mulai dari transisi stepper, validasi, loading, lalu hasil ranking.
5. Tambahkan Auto Layout untuk filter katalog setelah alur inti stabil.
6. Tambahkan SVG loading hanya jika tidak menambah keterlambatan semu.
7. Implementasikan reduced motion dan cleanup sebelum melakukan polish.
8. Jalankan feature test, browser test, serta audit keyboard dan mobile.

## 11. Sumber implementasi resmi

- [Anime.js Documentation](https://animejs.com/documentation/)
- [Anime.js Module Imports](https://animejs.com/documentation/getting-started/module-imports/)
- [Anime.js Scope](https://animejs.com/documentation/scope/)
- [Anime.js Scroll Observer](https://animejs.com/documentation/events/onscroll/)
- [Anime.js Layout](https://animejs.com/documentation/layout/)
- [Laravel Starter Kits](https://laravel.com/starter-kits)
- [Flux UI Header Layout](https://fluxui.dev/layouts/header)
- [Filament Installation](https://filamentphp.com/docs/5.x/introduction/installation)

## 12. Checklist rilis untuk pengujian v3.3

- [ ] Jalankan `php artisan test` dengan PostgreSQL.
- [ ] Jalankan UAT-01 sampai UAT-24 dari `docs/19-UAT-Sistem-Rekomendasi-Facial-Wash.md`.
- [ ] Bandingkan R, kontribusi, dan V dengan spreadsheet pada toleransi `1e-6`.
- [ ] Uji profil satu kandidat dan pastikan label skor relatif.
- [ ] Uji kandidat `excluded`/`insufficient_evidence`; `excluded` tampil pada panel Safety Gate, sedangkan `insufficient_evidence` tetap tersimpan pada snapshot dan tidak menjadi kartu ranking.
- [ ] Uji konsultasi guest, CTA simpan, claim setelah login/daftar, dan card riwayat.
- [ ] Uji pembaruan harga melalui observasi baru dan aktivasi dataset.
- [ ] Uji hasil publik melalui HTTPS/reverse proxy; tidak boleh ada mixed content.
- [ ] Isi defect log dan jangan menandai UAT lulus bila ada defect high/critical.

## 13. Catatan gap saat ini

`secondary_concerns` sudah memiliki modifier pada `criteria-1.3.0`, tetapi tetap harus dibuktikan dengan UAT-09. Alur guest/claim dan card riwayat juga harus ditutup dengan UAT-22 sampai UAT-24 sebelum rilis penelitian.
