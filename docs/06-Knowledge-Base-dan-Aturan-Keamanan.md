# Knowledge Base dan Aturan Keamanan

## 1. Bentuk knowledge base

Knowledge base bukan daftar “bahan bagus/buruk” yang berdiri sendiri. Ia menyimpan hubungan bersyarat antara **profil pengguna + formula lengkap + cara pakai + bukti + konsekuensi skor/keamanan**.

Tipe fakta:

- Fakta pengguna: usia, keluhan, kondisi dasar, overlay sensitif, alergi, terapi, toleransi.
- Fakta produk: INCI, konsentrasi bila diumumkan, pH bila terverifikasi, surfaktan, humektan, eksfolian, pewangi, menthol, scrub, legalitas, kemasan, harga.
- Fakta bukti: sumber, tanggal, status verifikasi, level kepastian.
- Aturan: hard stop, exclude, penalty, score, warning, referral.

## 2. Urutan inferensi

1. `REFERRAL`: cek red flag.
2. `EXCLUDE`: cek alergi/konflik/ketidaklengkapan kritis.
3. `PENALTY`: kurangi kecocokan karena iritan potensial atau bukti lemah.
4. `SCORE`: beri nilai 1-5 per kriteria.
5. `RANK`: jalankan SAW.
6. `EXPLAIN`: tampilkan alasan dan keterbatasan.

## 3. Red flag - hentikan ranking biasa

- Jerawat nodul/kistik.
- Jerawat berat atau cepat memburuk.
- Jaringan parut berkembang.
- Luka atau infeksi luas.
- Bengkak atau dugaan alergi berat.
- Nyeri/rasa terbakar menetap.
- Penyakit kulit yang sedang diobati dan membutuhkan evaluasi.

Output: saran pemeriksaan dokter kulit. Sistem tidak mendiagnosis.

## 4. Hard constraint

| Kode | Kondisi | Aksi |
|---|---|---|
| HC-01 | Alergi terkonfirmasi terhadap ingredient | Keluarkan formula yang mengandung ingredient |
| HC-02 | Sensitif/barrier terganggu + scrub fisik kuat | Keluarkan atau tahan untuk review ahli |
| HC-03 | Sensitif/barrier terganggu + beberapa eksfolian/menthol/pewangi | Keluarkan bila rule tervalidasi; minimal beri penalty tinggi |
| HC-04 | Sedang terapi acne | Jangan menyimpulkan otomatis; evaluasi potensi iritasi dan sarankan konfirmasi tenaga kesehatan |
| HC-05 | Formula/INCI minimum belum terverifikasi | Jangan aktifkan alternatif untuk publik |
| HC-06 | Legalitas produk tidak dapat diverifikasi pada tahap produksi | Tahan publikasi |

Harga dan kemasan tidak dapat membatalkan HC-01 sampai HC-06.

## 5. Mapping bahan dari validasi ahli

| Kebutuhan | Bahan/fitur | Peran validasi | Cara sistem memperlakukan |
|---|---|---|---|
| Berminyak/komedo/jerawat ringan | Salicylic acid/BHA | Pendukung | Nilai positif bila formula dapat ditoleransi |
| Berminyak/komedo/jerawat ringan | Niacinamide, zinc, tea tree, clay/charcoal | Pendukung | Bukan bukti tunggal produk pasti cocok |
| Semua kondisi | Surfaktan lembut, humektan | Pendukung | Nilai tolerabilitas/formulasi |
| Kering/sensitif | Glycerin, ceramide, hyaluronic acid | Relevan | Positif bila formula keseluruhan mendukung |
| Kering/sensitif | Panthenol, centella | Pendukung | Tambahan, bukan jaminan |
| Kering/sensitif | Tanpa pewangi | Relevan | Positif terutama pada riwayat sensitif |
| Kering/sensitif | Scrub fisik | Bukti produk bilas terbatas/risiko toleransi | Penalty atau exclude sesuai severity |
| Kusam/bekas jerawat | Niacinamide, vitamin C/turunan, AHA | Relevan | Nilai positif konservatif pada rinse-off |
| Kusam/bekas jerawat | Licorice, BHA, surfaktan lembut | Pendukung | Jangan mengklaim terapi hiperpigmentasi |

## 6. Ketentuan konsentrasi dan pH

- `declared`: konsentrasi angka/rentang tersedia dari sumber tepercaya.
- `not_declared`: ingredient ada di INCI tetapi tanpa angka; jangan memberi klaim khusus berbasis dosis.
- `unknown`: daftar komposisi tidak lengkap; formula tidak boleh masuk ranking publik.
- Urutan INCI hanya ordinal dan tidak diubah menjadi persen.
- pH yang tidak diumumkan tetap `NULL`; tidak diisi dengan nilai umum 4,5-5,5.
- pH dinilai bersama surfaktan, humektan, pewangi, menthol, eksfolian, frekuensi, cara pakai, dan barrier.

## 7. Contoh aturan terstruktur

```json
{
  "code": "KB-ACNE-01",
  "priority": 300,
  "if": {
    "complaint": ["acne_mild", "comedones"],
    "ingredient_present": "SALICYLIC ACID",
    "barrier_impaired": false,
    "allergy_match": false
  },
  "then": {
    "criterion": "C3",
    "score_delta": 1,
    "max_score": 5,
    "explanation_code": "contains_supportive_acne_ingredient"
  }
}
```

## 8. Konflik aturan

Urutan prioritas: `REFERRAL > EXCLUDE > PENALTY > SCORE`. Jika dua score rule bertentangan, gunakan rule dengan konteks lebih spesifik; bila sama, ambil nilai paling konservatif dan catat konflik untuk review admin.

## 9. Data yang masih wajib divalidasi sebelum produksi

- Foto label belakang terkini untuk seluruh produk berstatus `PENDING_LABEL_VERIFICATION`.
- Nomor notifikasi BPOM dan kecocokan nama/formula/kemasan.
- Mapping skor 1-5 setiap varian terhadap konteks pengguna.
- Definisi operasional kualitas dan kemasan yang tidak subjektif.
- Uji reliabilitas antar-validator bila validator ditambah.

## 10. Perilaku audit kandidat v3.2

Knowledge base dan service evaluasi harus menjelaskan tiga keluaran tanpa mencampurnya:

- `excluded`: aturan keamanan/hard constraint atau batas harga memblokir kandidat;
- `insufficient_evidence`: keputusan belum dapat dibuat karena bukti harga/formula tidak memenuhi syarat;
- `eligible`: kandidat dapat dinilai oleh C1-C6 dan masuk SAW.

Harga stale bukan berarti harga produk tidak ada untuk selamanya. Artinya observasi tersebut terlalu lama untuk dipakai pada ranking saat ini. Admin memperbaikinya dengan menambah observasi baru yang memuat sumber dan tanggal, kemudian mempublikasikan snapshot dataset yang baru. Riwayat lama tidak dihapus.

Keluhan `kusam` dapat menjadi keluhan utama untuk aturan C1. Penggunaan `kusam` sebagai keluhan tambahan harus memiliki aturan modifier yang eksplisit dan diuji; menyimpan field input saja tidak cukup.
