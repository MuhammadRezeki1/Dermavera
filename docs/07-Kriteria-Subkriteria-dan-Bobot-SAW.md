# Kriteria, Subkriteria, dan Bobot SAW

## 1. Bobot hasil validasi ahli

Satu dokter Sp.D.V.E./Sp.KK memberi tingkat kepentingan: 5, 5, 5, 3, 3, 5. Normalisasi menggunakan `w_j = nilai_j / jumlah nilai`; jumlah nilai =26.

| Kode | Kriteria | Nilai ahli | Bobot normal | Jenis |
|---|---|---:|---:|---|
| C1 | Kecocokan dengan keluhan kulit | 5 | 0,1923077 | Benefit |
| C2 | Kecocokan dengan jenis/kondisi kulit | 5 | 0,1923077 | Benefit |
| C3 | Kandungan dan keseluruhan formulasi | 5 | 0,1923077 | Benefit |
| C4 | Harga | 3 | 0,1153846 | Cost numerik atau benefit kategori |
| C5 | Fungsi dan kebersihan kemasan | 3 | 0,1153846 | Benefit |
| C6 | Kualitas dan keamanan | 5 | 0,1923077 | Benefit |
|  | **Total** | **26** | **1,0000000** |  |

Angka empat desimal untuk tampilan adalah 0,1923; 0,1923; 0,1923; 0,1154; 0,1154; 0,1923. Perhitungan database memakai presisi penuh.

## 2. Catatan metodologis

- Bobot ini berasal dari **satu validator ahli**, sehingga bukan konsensus.
- Safety gate bukan C7 dan tidak diberi bobot. Ia mendahului dan membatasi alternatif.
- Harga dan kemasan tetap dinilai, tetapi tidak dapat mengompensasikan alergi, sensitivitas berat, barrier terganggu, atau red flag.

## 3. Definisi operasional skala 1-5

| Skor | Label | Definisi umum |
|---:|---|---|
| 1 | Sangat tidak sesuai | Konflik kuat atau tidak memenuhi kebutuhan, tetapi belum termasuk hard exclusion |
| 2 | Tidak sesuai | Dukungan lemah dan terdapat faktor pengurang |
| 3 | Cukup/netral | Bukti campuran atau informasi terbatas tetapi masih layak dinilai |
| 4 | Sesuai | Dukungan jelas dengan risiko terkendali |
| 5 | Sangat sesuai | Dukungan kuat, relevan, dan tidak ada faktor pengurang material |

Skor 1 bukan pengganti `excluded`. Produk yang melanggar hard constraint dikeluarkan dari matriks.

## 4. Subkriteria

### C1 - Keluhan kulit

| Subkriteria | Indikator |
|---|---|
| Minyak berlebih | Klaim sasaran + formula pendukung + toleransi |
| Komedo | Dukungan pembersihan/keratolitik; bukan klaim menyembuhkan |
| Jerawat ringan | Bahan pendukung acne dan formula yang dapat ditoleransi |
| Kusam/noda akibat kotoran | Klaim pembersihan/brightening yang proporsional untuk rinse-off |
| Kering/tertarik setelah cuci | Formula lembut/humektan dan tanpa konflik |

Keluhan primer diberi prioritas; keluhan sekunder menjadi modifier. Jerawat sedang-berat/red flag tidak masuk ranking biasa.

### C2 - Jenis/kondisi kulit

Kondisi dasar: normal, berminyak, kering, kombinasi. Sensitif adalah overlay. Penilaian mempertimbangkan kecenderungan minyak/kering, barrier, sensitivitas, aktivitas, lingkungan, dan terapi.

### C3 - Kandungan dan formulasi

Komponen: surfaktan; humektan/emolien; bahan pendukung keluhan; scrub/eksfolian; fragrance/essential oil/menthol; kelengkapan INCI; konsentrasi bila diumumkan; karakter rinse-off. Banyaknya bahan aktif bukan indikator mandiri.

### C4 - Harga

**Pilihan yang direkomendasikan:** cost numerik menggunakan harga per 100 ml/g dari observasi terbaru yang lolos validasi. Jika ukuran berbeda:

`unit_price = price / size * 100`

Harga harus bertanggal dan diberi batas kedaluwarsa data, misalnya 30 hari.

### C5 - Kemasan

| Skor | Definisi |
|---:|---|
| 1 | Rusak/tidak higienis/tidak memberi perlindungan layak |
| 2 | Fungsi kurang baik atau informasi sulit dibaca |
| 3 | Tube/botol standar, fungsi cukup |
| 4 | Higienis, mudah mengontrol keluaran, label jelas |
| 5 | Sangat higienis/fungsional dengan proteksi dan kontrol dosis sangat baik |

Kemasan dinilai secara objektif melalui bentuk, integritas seal, kontrol keluaran, keterbacaan label, dan perlindungan isi; bukan estetika semata.

### C6 - Kualitas dan keamanan

Komponen: legalitas awal, kelengkapan label, petunjuk/peringatan, status formula, bukti sumber, risiko iritasi kontekstual, serta keterlacakan produsen/distributor. Status halal dicatat sebagai preferensi/kepatuhan terpisah, bukan pengganti legalitas kosmetik.

## 5. Pemetaan scoring ingredients B01 dan B03N

Kedua record berikut telah memiliki full INCI Indonesia dan dapat masuk scoring final. Angka 1-5 tetap ditentukan oleh kombinasi profil pengguna, bukan ditetapkan sebagai skor produk yang sama untuk semua orang.

| Produk | Dukungan positif | Faktor pengurang kontekstual | Arah scoring |
|---|---|---|---|
| B01 Men's Biore Bright Expert | Niacinamide untuk keluhan kusam; Glycerin dan Sorbitol untuk dukungan kelembapan; Polyquaternium-7 sebagai conditioning agent | Fragrance dan BHT; basis sabun/fatty acid perlu dinilai konservatif pada kulit sangat kering atau barrier terganggu | Naikkan C1 untuk kusam dan C2/C3 bila pengguna normal atau tidak sensitif; jangan memberi bonus khusus anti-acne |
| B03N Men's Biore Acne Bright Care | Tea Tree Leaf Oil dan o-Cymen-5-ol untuk dukungan kulit rentan berjerawat; Ascorbic Acid untuk klaim pencerahan; Glycerin, Propylene Glycol, dan Sorbitol untuk kelembapan | Menthol, fragrance, dan Tea Tree Oil dapat menurunkan tolerabilitas pada kulit sensitif/barrier terganggu | Naikkan C1 untuk jerawat ringan dan bekas/kusam; turunkan C2/C3 pada pengguna sensitif; tetap jalankan hard constraint alergi |

Aturan khusus:

- B01 tidak mendapat skor anti-acne hanya karena nama Bright Expert atau Niacinamide.
- B03N tidak dianggap mengandung Ascorbyl Glucoside; label mencantumkan Ascorbic Acid.
- B03 Acne Skincare lama dan B03N Acne Bright Care tetap merupakan dua formula berbeda.
- Produk rinse-off dinilai konservatif; keberadaan satu bahan tidak otomatis menghasilkan skor 5.

## 6. Pseudocode pembentukan skor

```text
for each eligible_variant:
    C1 = complaint_rules(user, variant, evidence)
    C2 = skin_condition_rules(user, variant, evidence)
    C3 = formulation_rules(user, formula, evidence)
    C4 = observed_unit_price
    C5 = packaging_rules(sku, evidence)
    C6 = quality_safety_rules(user, variant, evidence)
```

Tidak boleh ada nilai yang diisi semata-mata agar matriks lengkap. Jika kriteria minimum tidak dapat dinilai, alternatif berstatus `insufficient_evidence`.

## 7. Pembaruan implementasi v3.3

- Keluhan `kusam` sebagai keluhan utama memberi dukungan C1 bila formula memiliki bahan brightening/pembersihan yang terdokumentasi. Karena produk facial wash bersifat rinse-off, dukungan dijelaskan secara konservatif dan bukan klaim terapi.
- `secondary_concerns` disimpan bersama snapshot input dan memengaruhi C1 melalui modifier dukungan keluhan. UAT tetap membandingkan input yang sama dengan dan tanpa keluhan tambahan.
- Formula dinilai dari gabungan data formula yang terpetakan dan INCI raw/normalized. Data yang tidak lengkap tidak boleh diisi dengan asumsi.
- Harga C4 berasal dari observasi terbaru SKU untuk audit, sedangkan `latestEligiblePrice` hanya memilih observasi yang tersedia dan layak ranking. Status stok habis/tidak layak atau observasi yang melewati batas kesegaran membuat kandidat `insufficient_evidence`, bukan harga lama yang diam-diam dipakai.
- Jika hanya satu alternatif masuk matriks, normalisasi tetap sah secara matematis tetapi interpretasinya **relatif terhadap himpunan satu kandidat**.
