# Katalog Produk, Varian, Ingredients, dan Status Bukti

**Tanggal verifikasi web:** 26 September 2026  
**Cakupan:** lima merek yang disebut dalam proposal  
**Unit alternatif:** varian/formula; SKU ukuran dicatat terpisah bila formula pada sumber berbeda

## 1. Aturan kualitas data

| Status | Makna | Boleh masuk ranking publik? |
|---|---|:---:|
| `VERIFIED_OFFICIAL_INCI` | Daftar komposisi lengkap tersedia pada laman resmi merek | Ya, setelah review label/BPOM |
| `OFFICIAL_CLAIMS_ONLY` | Nama dan bahan/fitur utama ada di situs resmi, tetapi full INCI tidak tersedia | Tidak |
| `SUPPLEMENTAL_INCI_REQUIRES_LABEL` | INCI berasal dari retailer/transkripsi pendukung | Tidak sebelum dicocokkan dengan kemasan |
| `SOURCE_CONFLICT_REQUIRES_LABEL` | Ada perbedaan nama varian, daftar INCI, atau klaim antar-sumber | Tidak; formula belum boleh dipilih |
| `PENDING_LABEL_VERIFICATION` | Full INCI belum dapat dipastikan | Tidak |
| `PENDING_LOCAL_LABEL_CONFIRMATION` | Produk dan BPOM Indonesia terkonfirmasi, tetapi full INCI lokal belum didukung foto/teks label | Tidak |
| `VALIDATED_ID_FULL_INCI` | Nama/SKU dan full INCI ditemukan pada sumber Indonesia yang dapat diaudit | Ya, dengan menyimpan URL dan tanggal akses |
| `VALIDATED_ID_VERSIONED` | Full INCI ditemukan pada sumber Indonesia, tetapi ada indikasi formula lama/reformulasi atau konflik nama/klaim | Ya sebagai record formula berversi; jangan digabung |
| `CONFIRMED_ID_CROSSMARKET_INCI` | Produk dikonfirmasi resmi beredar di Indonesia, tetapi full INCI yang ditemukan berasal dari pasar lain | Hanya untuk kandidat internal; belum untuk formula final |

“Bahan penting” di bawah bukan konsentrasi dan bukan jaminan kecocokan. pH dan konsentrasi yang tidak diumumkan harus disimpan sebagai `NULL/UNKNOWN`.

## Audit final lintas situs Indonesia

### Hierarki sumber yang dipakai

Audit final tidak membatasi validasi pada Watsons. Urutan prioritasnya adalah: (1) situs resmi merek Indonesia dengan full INCI; (2) official store produsen di marketplace Indonesia; (3) retailer nasional/flagship store Indonesia; (4) penjual marketplace Indonesia yang menampilkan full INCI, identitas SKU, dan/atau nomor BPOM; lalu (5) sumber luar Indonesia hanya sebagai pembanding. Jika sumber resmi Indonesia dan retailer berbeda, formula resmi Indonesia menjadi record utama dan formula retailer disimpan sebagai versi lain—bukan dicampur.

### Hasil audit 35 alternatif aktif dan 2 record historis

| Kelompok bukti | Jumlah | ID | Keputusan |
|---|---:|---|---|
| Full INCI dari situs resmi merek Indonesia | 23 | K01–K07, G02–G09, N01–N08 | Dapat dipakai sebagai formula utama dengan URL dan tanggal akses |
| Full INCI dari situs/retailer/foto label Indonesia dan identitas varian cocok | 11 | M01-M04, B01, B02, B03, B03N, B04, B07, B08 | Dapat dipakai sebagai formula Indonesia tingkat retailer/label; simpan nama seller/BPOM dan versi formula |
| Full INCI Indonesia tersedia, tetapi ada konflik versi/nama/klaim | 3 | G01, B05, B06 | Simpan sebagai formula berversi; jangan menggabungkan kandidat |
| Produk Indonesia terkonfirmasi, tetapi full INCI lokal belum tampil | 0 | - | Tidak ada record yang masih menunggu full INCI |
| **Total record produk** | **37** |  | 35 alternatif aktif dan dua record historis; seluruh 37 record memiliki full INCI Indonesia |

Dengan hasil ini, foto kemasan **bukan syarat untuk mengisi ulang seluruh katalog**. Foto label B01 dan B03N telah menyelesaikan dua formula yang sebelumnya belum lengkap. Foto tambahan hanya diperlukan bila peneliti ingin mengunci batch/reformulasi terbaru G01, B05, atau B06. Foto depan produk tetap berguna untuk antarmuka aplikasi.

### Sumber Indonesia tambahan yang mengubah hasil audit

- M01: [Blibli—Energizer Facial Wash MS Glow For Men](https://www.blibli.com/p/energizer-facial-wash-ms-glow-for-men/ps--PUB-70185-00066) menampilkan full INCI; [Apotek Mandjur Flagship Store](https://www.blibli.com/p/ms-glow-men-energizer-facial-wash-100-ml-pembersih-wajah-3-in-1-pria/ps--MAR-60030-03435) mengonfirmasi ukuran 100 ml, produsen, dan BPOM NA18191234497.
- G01: [Blibli—Garnier Men Acno Fight Foam 6 in 1](https://www.blibli.com/p/garnier-men-acno-fight-foam-6-in-1-anti-acne-100ml/ps--TOL-70509-02297) dan [Watsons Indonesia](https://www.watsons.co.id/id/garnier-men-acno-fight-foam-6-in-1-anti-acne-100ml/p/BP_32839) sama-sama memuat formula Indonesia, tetapi versinya berbeda.
- B01–B08: [Katalog resmi KAO Indonesia](https://www.kao.com/id/id/products/mensbiore/) mengonfirmasi seluruh delapan nama varian yang diaudit.
- B01: [Alfagift Indonesia—Bright Expert](https://alfagift.id/p/mens-biore-pembersih-wajah-foam-gentle-bright-expert-100-g-801692) menampilkan foto kemasan depan-belakang yang mendukung transkripsi full INCI; [KAO Indonesia](https://www.kao.com/id/id/products/mensbiore/mbi_bright_expert_00/) mengonfirmasi klaim 5x Vitamin B3 dan Aqua Hydration.
- B03N: [Shopee Indonesia—Acne Bright Care 20 g](https://shopee.co.id/Men%27s-Biore-Facial-Wash-Acne-Bright-Care-Non-Scrub-Tea-Tree-Oil-With-Antibacterial-Agent-Vit-C-20gr-i.396834019.40516980839) menampilkan foto belakang kemasan dengan full INCI dan BPOM `NA18241202965`; record disimpan terpisah dari B03 lama.
- B02: [Gazelook Indonesia—Oil Balance](https://gazelook.id/product/mens-biore-gentle-clean-oil-balance-foam/) menampilkan full INCI Indonesia sekunder; [official store di Blibli](https://www.blibli.com/p/men-s-biore-oil-balance-facial-foam-100-g/ps--KAK-25212-00177) mengonfirmasi identitas produknya.
- B03: [Astro Indonesia—Acne Bright Care/Acne Skincare](https://www.astronauts.id/p/mens_biore_acne_bright_care_gentle_facial_foam_100_gr) menampilkan full INCI serta BPOM `NA18211200323`; daftar yang sama juga tampil pada [Gazelook Indonesia—Acne Skincare](https://gazelook.id/product/mens-biore-gentle-clean-acne-skincare-foam/). Record ini diperlakukan sebagai formula Acne Skincare lama karena nomor BPOM-nya, bukan sebagai bukti formula Acne Bright Care baru.
- B04: [Farmaku Flagship Store di Blibli—Bright Oil Clear](https://www.blibli.com/p/biore-mens-facial-foam-bright-oil-clear-tube-100-gr/ps--GAA-60023-10315) menampilkan full INCI dan BPOM NA18201203966.
- B07: [Farmaku Flagship Store di Blibli—Deep Pore Clean/Deep Fresh](https://www.blibli.com/p/biore-mens-facial-foam-deep-fresh-100-g/is--GAA-60023-03310-00001) menampilkan full INCI dan BPOM NA18221201191.
- B08: [Farmaku Flagship Store di Blibli—Acne Bacterior](https://www.blibli.com/p/biore-mens-facial-foam-anti-bacterior-pop-up-100-g/is--GAA-60023-05245-00001) menampilkan full INCI dan BPOM NA18191200283.

## Audit khusus Watsons Indonesia

### Prinsip penggunaan sumber

Watsons Indonesia digunakan sebagai sumber retailer Indonesia untuk memeriksa nama produk, ukuran, nomor BPOM yang ditampilkan, dan daftar ingredients. Namun, data Watsons tidak otomatis menggantikan situs resmi merek. Jika daftar Watsons dan situs resmi berbeda, kedua daftar disimpan sebagai versi formula terpisah dan statusnya dinaikkan menjadi konflik. Formula tidak boleh digabung.

Status audit Watsons:

| Status | Makna |
|---|---|
| `WATSONS_MATCH` | Nama/ukuran dan daftar ingredients Watsons sesuai dengan formula pembanding |
| `WATSONS_MINOR_DIFFERENCE` | Set ingredients hampir sama, tetapi ada perbedaan urutan atau unsur kecil yang dapat menunjukkan versi formula berbeda |
| `WATSONS_CONFLICT` | Nama, ukuran, atau daftar ingredients berbeda secara material dari formula pembanding |
| `WATSONS_NO_INCI` | Halaman produk ada, tetapi full INCI tidak ditampilkan |
| `NOT_FOUND_EXACT_WATSONS` | Tidak ditemukan halaman Watsons Indonesia yang cocok persis dengan varian/SKU penelitian |

### Ringkasan hasil audit per varian

| ID | Varian | Status Watsons | Keputusan data |
|---|---|---|---|
| K01 | Kahf Oil and Acne Care | WATSONS_CONFLICT | Pisahkan formula 50 ml dan 100 ml; Watsons menampilkan komposisi berbeda |
| K02 | Kahf Skin Energizing and Brightening | WATSONS_CONFLICT | Pisahkan formula 50 ml dan 100 ml; daftar 100 ml memuat Phenylethyl Resorcinol, Sclareolide, dan Silica |
| K03 | Kahf Acne and Pore Cleanse Scrub | WATSONS_CONFLICT | Watsons memuat Morinda Citrifolia Callus Culture Lysate dan urutan komposisi berbeda |
| K04 | Kahf Brightening and Dark Spot Scrub | NOT_FOUND_EXACT_WATSONS | Hasil pencarian lama mengarah ke halaman yang kini 410/Gone; pertahankan sumber resmi merek |
| K05 | Kahf Triple Action Oil and Comedo Defense | WATSONS_MATCH | Daftar Watsons sesuai formula resmi yang dicatat |
| K06 | Kahf Acne Care AminoGel | WATSONS_MINOR_DIFFERENCE | Set bahan utama sama, tetapi urutan bagian akhir berbeda; simpan sebagai verifikasi retailer |
| K07 | Kahf Bright Revitalizing AminoGel | WATSONS_MATCH | Daftar Watsons sesuai formula resmi yang dicatat |
| M01 | MS Glow For Men Energizer | NOT_FOUND_EXACT_WATSONS | Tidak ditemukan halaman Watsons Indonesia yang cocok |
| G01 | Garnier Men AcnoFight Scrub in Foam | WATSONS_CONFLICT | Formula Watsons berbeda dari daftar peneliti/INCIdecoder |
| G02 | Garnier Men AcnoFight Wasabi | WATSONS_CONFLICT | Watsons memuat Methylisothiazolinone dan tidak memuat beberapa bahan pada situs resmi |
| G03 | Garnier Men Oil Control Cooling Foam | WATSONS_CONFLICT | Watsons memakai nama lama Turbolight dan formula lama/lebih pendek |
| G04 | Garnier Men Oil Control Super Duo | NOT_FOUND_EXACT_WATSONS | Tidak ditemukan halaman exact |
| G05 | Garnier Men Matcha Deep Clean Gel | NOT_FOUND_EXACT_WATSONS | Tidak ditemukan halaman exact |
| G06 | Garnier Men 3in1 Charcoal | WATSONS_MATCH | Formula Watsons sesuai daftar resmi yang dicatat |
| G07 | Garnier Men Oil Control Icy Scrub | WATSONS_CONFLICT | Watsons menampilkan daftar pendek yang tidak memuat Pumice, Perlite, Lemon Extract, Charcoal, dan Menthol pada formula resmi |
| G08 | Garnier Men TurboBright Super Duo | NOT_FOUND_EXACT_WATSONS | Tidak ditemukan halaman exact; produk Natural Turbo Light Double White tidak dianggap sama |
| G09 | Garnier Men Shaving & Cleansing Brightening Foam | NOT_FOUND_EXACT_WATSONS | Tidak ditemukan halaman exact |
| N01 | NIVEA Men Deep Acne Attack Scrub Mud | WATSONS_CONFLICT | Watsons menambahkan Mineral Salts dan Parfum dibanding daftar yang sebelumnya dicatat |
| N02 | NIVEA Men Pore Minimizing Scrub | WATSONS_CONFLICT | Watsons menampilkan formula lama dengan Polyethylene dan Benzophenone-3 |
| N03 | NIVEA Men Extra Bright 50 ml | WATSONS_NO_INCI | Halaman 50 ml tersedia, tetapi ingredients tidak ditampilkan |
| N04 | NIVEA Men Acne Defense Foam | WATSONS_CONFLICT | Daftar Watsons jauh lebih pendek dan tidak memuat Magnolia serta Menthol yang ada pada sumber resmi |
| N05 | NIVEA Men Anti-Shine+Purify Mud Foam | WATSONS_CONFLICT | Daftar Watsons tampak tidak lengkap dan urutannya berbeda |
| N06 | NIVEA Men Extra Bright Mud Foam | WATSONS_CONFLICT | Watsons menampilkan reformulasi dengan Glutathione dan tanpa Menthol/Cellulose dari formula lama |
| N07 | NIVEA Men Extra Bright 100 ml | WATSONS_CONFLICT | Watsons memakai nama Dark Spot Minimizer dan daftar formula berbeda |
| N08 | NIVEA Men Pore Minimizing Foam | NOT_FOUND_EXACT_WATSONS | Halaman White Oil Clear Anti-Shine merupakan produk/versi lain dan tidak dianggap exact |
| B01 | Men's Biore Bright Expert | NOT_FOUND_EXACT_WATSONS | Tidak ditemukan halaman exact Watsons Indonesia |
| B02 | Men's Biore Oil Balance | NOT_FOUND_EXACT_WATSONS | Tidak ditemukan halaman exact Watsons Indonesia |
| B03 | Men's Biore Acne Skincare | NOT_FOUND_EXACT_WATSONS | Tidak ditemukan halaman exact Watsons Indonesia |
| B04 | Men's Biore Bright Oil Clear | WATSONS_CONFLICT | Watsons hanya menampilkan produk lama Men Scrub Black White dengan INCI yang tampak terpotong |
| B05 | Men's Biore Bright Energy | WATSONS_CONFLICT | Watsons memakai nama White Energy; daftar tidak memuat Vitamin B3 yang diklaim varian Bright Energy saat ini |
| B06 | Men's Biore Cool Oil Clear | WATSONS_CONFLICT | Full INCI tersedia, tetapi tidak memuat Menthol walau klaim resmi menyebut Icy Menthol |
| B07 | Men's Biore Deep Pore Clean | NOT_FOUND_EXACT_WATSONS | Tidak ditemukan halaman exact Watsons Indonesia |
| B08 | Men's Biore Acne Bacterior | NOT_FOUND_EXACT_WATSONS | Tidak ditemukan halaman exact Watsons Indonesia |

**Rekap:** 3 varian `WATSONS_MATCH`, 1 `WATSONS_MINOR_DIFFERENCE`, 16 `WATSONS_CONFLICT`, 1 `WATSONS_NO_INCI`, dan 12 `NOT_FOUND_EXACT_WATSONS`.

### Koreksi penting: ukuran Kahf harus dipisahkan

Watsons Indonesia menunjukkan bahwa ukuran berbeda tidak selalu memakai daftar yang sama.

#### K01-50ML - Oil and Acne Care Face Wash

**Sumber Watsons Indonesia:** [Kahf Oil and Acne Care Face Wash 50 ml](https://www.watsons.co.id/id/kahf-kahf-oil-and-acne-care-face-wash-50ml/p/BP_37953)  
**BPOM yang ditampilkan:** NA18201203048.

**INCI Watsons 50 ml:** Aqua, Glycerin, Myristic Acid, Potassium Hydroxide, Stearic Acid, Butylene Glycol, Lauric Acid, Glyceryl Stearate, PEG-3 Distearate, Decyl Glucoside, Zinc Gluconate, Phenoxyethanol, Saccharide Isomerate, Kaolin, Salicylic Acid, Fragrance, Menthol, Allantoin, Disodium EDTA, Hydroxypropyl Methylcellulose, Ethylhexylglycerin, Menthyl Lactate, PEG-40 Hydrogenated Castor Oil, PPG-26-Buteth-26, Salvia Officinalis (Sage) Leaf Extract, Citric Acid, Sodium Citrate, Cupressus Sempervirens Fruit Extract, Ammonium Acrylates Copolymer, PEG-40 Castor Oil, Propanediol, Potassium Sorbate, Sodium Benzoate, Trideceth-9, Laureth-21, 1,2-Hexanediol, Aminomethyl Propanediol, Caprylhydroxamic Acid, Dimethicone, Polysorbate 20, Sodium Dehydroacetate, CI 77891, CI 19140, CI 77266, CI 42090.

#### K01-100ML - Oil and Acne Care Face Wash

**Sumber Watsons Indonesia:** [Kahf Oil and Acne Care Face Wash 100 ml](https://www.watsons.co.id/id/kahf-oil-and-acne-care-face-wash-100-ml/p/BP_18031)  
**BPOM yang ditampilkan:** NA18251206114.

**INCI Watsons 100 ml:** Aqua, Glycerin, Myristic Acid, Butylene Glycol, Stearic Acid, Potassium Hydroxide, Lauric Acid, Decyl Glucoside, Glyceryl Stearate, PEG-3 Distearate, Sodium PCA, Trehalose, Phenoxyethanol, Kaolin, Salicylic Acid, Menthol, Saccharide Isomerate, Allantoin, Fragrance, Zinc Gluconate, Propanediol, Disodium EDTA, Ethylhexylglycerin, Hydroxypropyl Methylcellulose, Salvia Officinalis (Sage) Leaf Extract, Citric Acid, Sodium Citrate, Cupressus Sempervirens Fruit Extract, Ammonium Acrylates Copolymer, PEG-40 Castor Oil, Bioflavonoids, Potassium Azeloyl Diglycinate, Potassium Sorbate, Sodium Benzoate, PEG-40 Hydrogenated Castor Oil, Trideceth-9, Laureth-21, 1,2-Hexanediol, Aminomethyl Propanol, Caprylhydroxamic Acid, Dimethicone, Polysorbate 20, Sodium Dehydroacetate, Caprylyl Glycol, Chlorphenesin, CI 77891, CI 19140, CI 77266, CI 42090.

#### K02-50ML - Skin Energizing and Brightening Face Wash

**Sumber Watsons Indonesia:** [Kahf Skin Energizing and Brightening Face Wash 50 ml](https://www.watsons.co.id/id/kahf-kahf-skin-energizing-and-brightening-face-wash-50ml/p/BP_37952)  
**BPOM yang ditampilkan:** NA18211205162.

**INCI Watsons 50 ml:** Aqua, Glycerin, Myristic Acid, Butylene Glycol, Potassium Hydroxide, Stearic Acid, Lauric Acid, Glyceryl Stearate, Decyl Glucoside, Olive Oil PEG-7 Esters, Phenoxyethanol, Saccharide Isomerate, Fragrance, Niacinamide, Menthol, Allantoin, Disodium EDTA, Ethylhexylglycerin, Hydroxypropyl Methylcellulose, Menthyl Lactate, PEG-40 Hydrogenated Castor Oil, PPG-26-Buteth-26, Propylene Glycol, Citric Acid, Mentha Viridis Leaf Extract, Sodium Citrate, Citrus Grandis Fruit Extract.

#### K02-100ML - Skin Energizing and Brightening Face Wash

**Sumber Watsons Indonesia:** [Kahf Skin Energizing and Brightening Face Wash 100 ml](https://www.watsons.co.id/id/kahf-skin-energizing-and-brightening-face-wash-100ml/p/BP_18040)  
**BPOM yang ditampilkan:** NA18201203049.

**INCI Watsons 100 ml:** Aqua, Glycerin, Myristic Acid, Butylene Glycol, Stearic Acid, Potassium Hydroxide, Lauric Acid, Glyceryl Stearate, Decyl Glucoside, Niacinamide, Olive Oil PEG-7 Esters, Phenoxyethanol, Fragrance, Allantoin, Menthol, Saccharide Isomerate, Disodium EDTA, PEG-40 Hydrogenated Castor Oil, Ethylhexylglycerin, Hydroxypropyl Methylcellulose, Menthyl Lactate, PPG-26-Buteth-26, Propylene Glycol, Pentylene Glycol, Trideceth-9, Mentha Viridis (Spearmint) Leaf Extract, Citrus Grandis (Grapefruit) Fruit Extract, Phenylethyl Resorcinol, Sclareolide, Citric Acid, Sodium Citrate, Silica.

### Tautan Watsons Indonesia yang digunakan dalam audit

- [Kahf Acne and Pore Cleanse Scrub 100 ml](https://www.watsons.co.id/id/kahf-acne-and-pore-cleanse-scrub-face-wash-100-ml/p/BP_42102)
- [Kahf Triple Action Oil and Comedo Defense 100 ml](https://www.watsons.co.id/id/kahf-triple-action-oil-and-comede-defense-face-wash-100-ml/p/BP_27218)
- [Kahf Acne Care Amino Gel 100 ml](https://www.watsons.co.id/id/kahf-acne-care-amino-gel-face-wash-100ml/p/BP_59342)
- [Kahf Bright Revitalizing Amino Gel 100 ml](https://www.watsons.co.id/id/kahf-bright-revitalizing-amino-gel-face-wash-100ml/p/BP_59341)
- [Garnier Men Acno Fight 6 in 1 100 ml](https://www.watsons.co.id/id/garnier-men-acno-fight-foam-6-in-1-anti-acne-100ml/p/BP_32839)
- [Garnier Men Acno Fight Wasabi 100 ml](https://www.watsons.co.id/id/garnier-men-acno-fight-wasabi-brightening-foam-100ml/p/BP_30711)
- [Garnier Men Turbolight Oil Control Cooling Foam 100 ml](https://www.watsons.co.id/id/garnier-men-turbolight-oil-control-cooling-foam-100ml/p/BP_23523)
- [Garnier Men Oil Control 3in1 Charcoal 100 ml](https://www.watsons.co.id/id/garnier-men-turbo-light-oil-control-3-1-charcoal-100ml/p/BP_35082)
- [Garnier Men Turbolight Oil Control Icy Scrub 100 ml](https://www.watsons.co.id/id/garnier-men-turbolight-oil-control-icy-scrub-skin-care-100ml/p/BP_23525)
- [NIVEA Men Deep Acne Attack 100 ml](https://www.watsons.co.id/id/nivea-nivea-men-deep-acne-oil-clear-facial-clear-100ml-melawan-jerawat/p/BP_23353)
- [NIVEA Men Bright Oil Clear Pore Minimizing Scrub 100 ml](https://www.watsons.co.id/id/nivea-nivea-men-bright-oil-clear-pore-minimizing-scrub-100ml/p/BP_33081)
- [NIVEA Men Extra Bright Dark Spot Minimizer 50 ml](https://www.watsons.co.id/id/nivea-nivea-men-extra-bright-dark-spot-minimizer-foam-50ml/p/BP_37405)
- [NIVEA Men Acne Defense Foam 100 ml](https://www.watsons.co.id/id/nivea-nivea-men-facial-foam-oil-clear-acne-defense-100ml/p/BP_95179)
- [NIVEA Men Bright 8H Oil Clear Mud Foam 100 ml](https://www.watsons.co.id/id/nivea-nivea-men-bright-8h-oil-clear-anti-shine-purify-mud-foam-100ml/p/BP_32755)
- [NIVEA Men Extra Bright Mud Foam 100 ml](https://www.watsons.co.id/id/nivea-nivea-men-extra-bright-mud-foam-100ml/p/BP_59450)
- [NIVEA Men Extra Bright Dark Spot Minimizer 100 ml](https://www.watsons.co.id/id/nivea-nivea-men-facial-foam-extra-bright-dark-spot-minimizer-100ml/p/BP_95178)
- [Men's Biore White Energy 100 g](https://www.watsons.co.id/id/biore-men-facial-foam-white-energy-100-g/p/BP_32998)
- [Men's Biore Cool Oil Clear 100 g](https://www.watsons.co.id/id/biore-men-facial-foam-cool-oil-100-g/p/BP_32997)
- [Men's Biore Men Scrub Black White 100 g](https://www.watsons.co.id/id/biore-men-scrub-black-white-100g/p/BP_23397)

## 2. Ringkasan 35 alternatif aktif dan 2 record historis

| ID | Merek | Varian/SKU | Ukuran | Status INCI |
|---|---|---|---|---|
| K01 | Kahf | Oil and Acne Care Face Wash | 50/100 ml | VERIFIED_OFFICIAL_INCI; pisahkan formula per ukuran |
| K02 | Kahf | Skin Energizing and Brightening Face Wash | 50/100 ml | VERIFIED_OFFICIAL_INCI; pisahkan formula per ukuran |
| K03 | Kahf | Acne and Pore Cleanse Scrub Face Wash | 100 ml | VERIFIED_OFFICIAL_INCI |
| K04 | Kahf | Brightening and Dark Spot Scrub Face Wash | cek kemasan | VERIFIED_OFFICIAL_INCI |
| K05 | Kahf | Triple Action Oil and Comedo Defense Face Wash | cek kemasan | VERIFIED_OFFICIAL_INCI |
| K06 | Kahf | Acne Care AminoGel Face Wash | cek kemasan | VERIFIED_OFFICIAL_INCI |
| K07 | Kahf | Bright Revitalizing AminoGel Face Wash | cek kemasan | VERIFIED_OFFICIAL_INCI |
| M01 | MS Glow For Men | Energizer Facial Wash | 100 ml | VALIDATED_ID_FULL_INCI |
| M02 | MS Glow For Men | Ultra Bright Facial Wash | 100 ml | VALIDATED_ID_FULL_INCI |
| M03 | MS Glow For Men | Hydra Boost Facial Wash | 100 ml | VALIDATED_ID_FULL_INCI |
| M04 | MS Glow For Men | Acnotion Facial Wash | 100 ml | VALIDATED_ID_FULL_INCI |
| G01 | Garnier Men | AcnoFight Scrub in Foam | 100 ml | VALIDATED_ID_VERSIONED |
| G02 | Garnier Men | AcnoFight Wasabi Brightening Foam | 100 ml | VERIFIED_OFFICIAL_INCI |
| G03 | Garnier Men | Oil Control Anti-Shine Brightening Cooling Foam | 100 ml | VERIFIED_OFFICIAL_INCI |
| G04 | Garnier Men | Oil Control Super Duo Foam | cek kemasan | VERIFIED_OFFICIAL_INCI |
| G05 | Garnier Men | Oil Control Matcha Deep Clean Foaming Gel | cek kemasan | VERIFIED_OFFICIAL_INCI |
| G06 | Garnier Men | Oil Control 3in1 Charcoal Foam | cek kemasan | VERIFIED_OFFICIAL_INCI |
| G07 | Garnier Men | Oil Control Icy Scrub | 50/100 ml | VERIFIED_OFFICIAL_INCI |
| G08 | Garnier Men | TurboBright Super Duo Foam | cek kemasan | VERIFIED_OFFICIAL_INCI |
| G09 | Garnier Men | TurboBright Shaving & Cleansing Brightening Foam | cek kemasan | VERIFIED_OFFICIAL_INCI |
| N01 | NIVEA Men | Deep Acne Attack Scrub Mud Facial Foam | 100 ml | VERIFIED_OFFICIAL_INCI |
| N02 | NIVEA Men | Bright Oil Clear Pore Minimizing Scrub | 100 ml | VERIFIED_OFFICIAL_INCI |
| N03 | NIVEA Men | Extra Bright Facial Foam | 50 ml | VERIFIED_OFFICIAL_INCI |
| N04 | NIVEA Men | Acne Oil Clear Acne Defense Foam | 100 ml | VERIFIED_OFFICIAL_INCI |
| N05 | NIVEA Men | Bright Oil Clear Anti-Shine+Purify Mud Foam | 100 ml | VERIFIED_OFFICIAL_INCI |
| N06 | NIVEA Men | Extra Bright Mud Foam | 100 ml | VERIFIED_OFFICIAL_INCI |
| N07 | NIVEA Men | Extra Bright Facial Foam | 100 ml | VERIFIED_OFFICIAL_INCI |
| N08 | NIVEA Men | Bright Oil Clear Pore Minimizing Foam | 100 ml | VERIFIED_OFFICIAL_INCI |
| B01 | Men's Biore | Facial Foam Bright Expert | 100 g | VALIDATED_ID_FULL_INCI |
| B02 | Men's Biore | Facial Foam Oil Balance | 100 g | VALIDATED_ID_FULL_INCI |
| B03 | Men's Biore | Facial Foam Acne Skincare | 100 g | VALIDATED_ID_VERSIONED |
| B03N | Men's Biore | Acne Bright Care Gentle Facial Foam | 100 g | VALIDATED_ID_VERSIONED |
| B04 | Men's Biore | Scrub Facial Wash Bright Oil Clear | 100 g | VALIDATED_ID_FULL_INCI |
| B05 | Men's Biore | Scrub Facial Wash Bright Energy | 100 g | VALIDATED_ID_VERSIONED |
| B06 | Men's Biore | Scrub Facial Wash Cool Oil Clear | 100 g | VALIDATED_ID_VERSIONED |
| B07 | Men's Biore | Scrub Facial Wash Deep Pore Clean | 100 g | VALIDATED_ID_FULL_INCI |
| B08 | Men's Biore | Scrub Facial Wash Acne Bacterior | 100 g | VALIDATED_ID_FULL_INCI |

## 3. Kahf - 7 varian

Katalog: [Kahf Face Care](https://www.kahfeveryday.com/en/shop/face-care/).

### K01 - Oil and Acne Care Face Wash

**Bahan penting untuk pemetaan:** Zinc Gluconate, Kaolin, Salicylic Acid, Allantoin, Sage, Menthol/fragrance.  
**Sumber resmi:** [Kahf Oil and Acne Care](https://www.kahfeveryday.com/en/product/kahf-oil-and-acne-care-face-wash/)

**INCI resmi:** Aqua, Glycerin, Myristic Acid, Potassium Hydroxide, Stearic Acid, Butylene Glycol, Lauric Acid, Glyceryl Stearate, PEG-3 Distearate, Decyl Glucoside, Zinc Gluconate, Phenoxyethanol, Saccharide Isomerate, Kaolin, Salicylic Acid, Fragrance, Menthol, Allantoin, Disodium EDTA, Hydroxypropyl Methylcellulose, Ethylhexylglycerin, Menthyl Lactate, PEG-40 Hydrogenated Castor Oil, PPG-26-Buteth-26, Salvia Officinalis (Sage) Leaf Extract, Citric Acid, Sodium Citrate, Cupressus Sempervirens Fruit Extract, Ammonium Acrylates Copolymer, PEG-40 Castor Oil, Propanediol, Potassium Sorbate, Sodium Benzoate, Trideceth-9, Laureth-21, 1,2-Hexanediol, Aminomethyl Propanediol, Caprylhydroxamic Acid, Dimethicone, Polysorbate 20, Sodium Dehydroacetate.

### K02 - Skin Energizing and Brightening Face Wash

**Bahan penting:** Niacinamide, Kaolin, Allantoin, Grapefruit Extract, Menthol/fragrance.  
**Sumber resmi:** [Kahf Skin Energizing and Brightening](https://www.kahfeveryday.com/en/product/kahf-skin-energizing-and-brightening-face-wash/)

**INCI resmi:** Aqua, Glycerin, Myristic Acid, Potassium Hydroxide, Stearic Acid, Butylene Glycol, Lauric Acid, Glyceryl Stearate, PEG-3 Distearate, Decyl Glucoside, Phenoxyethanol, Saccharide Isomerate, Potassium Cocoyl Glycinate, Kaolin, Niacinamide, Fragrance, Menthol, Potassium Cocoate, Allantoin, Disodium EDTA, Hydroxypropyl Methylcellulose, Ethylhexylglycerin, Menthyl Lactate, PEG-40 Hydrogenated Castor Oil, PPG-26-Buteth-26, Propylene Glycol, Citric Acid, Mentha Viridis (Spearmint) Leaf Extract, Sodium Citrate, Citrus Grandis (Grapefruit) Fruit Extract.

### K03 - Acne and Pore Cleanse Scrub Face Wash

**Bahan penting:** Perlite/hydrated silica/microcrystalline wax (scrub), Salicylic Acid, Niacinamide, Centella, Green Tea, Licorice, Allantoin, Menthol/fragrance.  
**Sumber resmi:** [Kahf Acne and Pore Cleanse Scrub](https://www.kahfeveryday.com/en/product/kahf-acne-and-pore-cleanse-scrub-face-wash/)

**INCI resmi:** Aqua, Glycerin, Myristic Acid, Stearic Acid, Potassium Hydroxide, Propylene Glycol, Butylene Glycol, Lauric Acid, Decyl Glucoside, Glycol Distearate, Perlite, Glyceryl Stearate, Olive Oil PEG-7 Esters, Allantoin, Salicylic Acid, Saccharide Isomerate, Polyquaternium-7, Centella Asiatica Extract, Polygonum Cuspidatum Root Extract, Scutellaria Baicalensis Root Extract, Camellia Sinensis Leaf Extract, Glycyrrhiza Glabra (Licorice) Root Extract, Chamomilla Recutita (Matricaria) Flower Extract, Rosmarinus Officinalis (Rosemary) Leaf Extract, Niacinamide, Fragrance, Menthol, PEG-3 Distearate, Phenoxyethanol, Hydrated Silica, Hydroxypropyl Methylcellulose, Microcrystalline Wax, Disodium EDTA, Ethylhexylglycerin, Menthyl Lactate, PEG-40 Hydrogenated Castor Oil, PPG-26-Buteth-26, Citric Acid, Sodium Citrate, Sodium Benzoate, Trideceth-9, Polysorbate 20, CI 74260, CI 77289, CI 19140, CI 42090.

### K04 - Brightening and Dark Spot Scrub Face Wash

**Bahan penting:** Tranexamic Acid, Tranexamoyl Dipeptide-23, Caffeine, Niacinamide, Ginseng, Lactic Acid, Salicylic Acid, hydrated silica (scrub), Menthol/fragrance.  
**Sumber resmi:** [Kahf Brightening and Dark Spot Scrub](https://www.kahfeveryday.com/en/product/kahf-brightening-and-dark-spot-scrub-face-wash/)

**INCI resmi:** Glycerin, Aqua, Stearic Acid, Myristic Acid, Potassium Hydroxide, Lauric Acid, Decyl Glucoside, Glyceryl Stearate, PEG-3 Distearate, Propanediol, Tranexamic Acid, Tranexamoyl Dipeptide-23, Caffeine, Coffea Arabica Seed Extract, Niacinamide, Panax Ginseng Root Extract, Saccharide Isomerate, Allantoin, Tocopheryl Acetate, Glucose, Olive Oil PEG-7 Esters, Caramel, Butylene Glycol, Lactic Acid, Trideceth-9, 1,2-Hexanediol, Caprylyl Glycol, Sodium Benzoate, Potassium Sorbate, Benzyl Alcohol, Phenoxyethanol, Fragrance, Hydrated Silica, Salicylic Acid, Menthol, Disodium EDTA, Ethylhexylglycerin, Hydroxypropyl Methylcellulose, Menthyl Lactate, PEG-40 Hydrogenated Castor Oil, PPG-26-Buteth-26, Propylene Glycol, Citric Acid, Sodium Citrate.

### K05 - Triple Action Oil and Comedo Defense Face Wash

**Bahan penting:** Kaolin, Bentonite, Charcoal, Gluconolactone, Salicylic Acid, Lactic Acid, Tea Tree Oil, Menthol/fragrance.  
**Sumber resmi:** [Kahf Triple Action Oil and Comedo Defense](https://www.kahfeveryday.com/en/product/kahf-triple-action-oil-and-comedo-defense-face-wash/)

**INCI resmi:** Aqua, Cocamidopropyl Betaine, Glycerin, Decyl Glucoside, Sodium Methyl Cocoyl Taurate, Kaolin, Bentonite, Acrylates Copolymer, Propanediol, Sodium Chloride, Charcoal Powder, Gluconolactone, Salicylic Acid, Lactic Acid, Citric Acid, Melaleuca Alternifolia (Tea Tree) Leaf Oil, Saccharide Isomerate, Fragrance, Sodium Hydroxide, Menthol, Disodium EDTA, Allantoin, Ethylhexylglycerin, Phenoxyethanol, PEG-40 Hydrogenated Castor Oil, Sodium Dehydroacetate, Sodium Citrate, Laureth-21, CI 77266.

**Catatan transkripsi:** laman resmi menampilkan karakter yang tampak salah pada kata “Cocoyl”; nama di atas dinormalisasi sebagai `Sodium Methyl Cocoyl Taurate`, sedangkan `inci_raw` database perlu menyimpan teks sumber/foto label.

### K06 - Acne Care AminoGel Face Wash

**Bahan penting:** Panthenol, glycolipids, Succinic Acid, Beta-Glucan, amino acids, Allantoin, Salicylic Acid, Hypochlorous Acid, humektan.  
**Sumber resmi:** [Kahf Acne Care AminoGel](https://www.kahfeveryday.com/en/product/kahf-acne-care-aminogel-face-wash/)

**INCI resmi:** Aqua, Glycerin, Sodium C14-16 Olefin Sulfonate, Cocamidopropyl Betaine, Propanediol, Sodium Chloride, Panthenol, Disodium Cocoyl Glutamate, Acrylates/C10-30 Alkyl Acrylate Crosspolymer, Glycolipids, Succinic Acid, Saccharide Isomerate, Beta-Glucan, Avena Sativa Kernel Flour, Glycine, Serine, Glutamic Acid, Aspartic Acid, Leucine, Alanine, Lysine, Arginine, Tyrosine, Phenylalanine, Proline, Threonine, Valine, Isoleucine, Histidine, Allantoin, Almond Oil/Polyglyceryl-10 Esters, Polyglyceryl-4 Punicate, Phenoxyethanol, Salicylic Acid, Castoryl Maleate, Caprylyl Glycol, Pentylene Glycol, Hypochlorous Acid, Citric Acid, Sodium Citrate, PEG-40 Hydrogenated Castor Oil, PPG-26-Buteth-26, Sodium Hydroxide, Fragrance, Sodium Benzoate, Disodium EDTA, Sodium Metabisulfite, Polyquaternium-10, Ethylhexylglycerin, Menthyl Lactate.

### K07 - Bright Revitalizing AminoGel Face Wash

**Bahan penting:** Gluconolactone, Niacinamide, Sodium PCA, Trehalose, Panthenol, 3-O-Ethyl Ascorbic Acid, Glycolic Acid, Licorice, amino acids.  
**Sumber resmi:** [Kahf Bright Revitalizing AminoGel](https://www.kahfeveryday.com/en/product/kahf-bright-revitalizing-aminogel-face-wash/)

**INCI resmi:** Aqua, Glycerin, Sodium C14-16 Olefin Sulfonate, Cocamidopropyl Betaine, Propanediol, Sodium Chloride, Gluconolactone, Niacinamide, Sodium PCA, Trehalose, Disodium Cocoyl Glutamate, Panthenol, Saccharide Isomerate, 3-O-Ethyl Ascorbic Acid, Glycolipids, Glycolic Acid, Glycyrrhiza Glabra (Licorice) Root Extract, Polyquaternium-10, Acrylates/C10-30 Alkyl Acrylate Crosspolymer, Glycine, Serine, Glutamic Acid, Leucine, Alanine, Lysine, Arginine, Tyrosine, Phenylalanine, Proline, Threonine, Valine, Isoleucine, Histidine, Aspartic Acid, Castoryl Maleate, Allantoin, Propylene Glycol, Disodium EDTA, Sodium Metabisulfite, Phenoxyethanol, Potassium Hydroxide, Ethylhexylglycerin, Menthyl Lactate, Citric Acid, PEG-40 Hydrogenated Castor Oil, PPG-26-Buteth-26, Fragrance, Sodium Benzoate, Almond Oil/Polyglyceryl-10 Esters, Polyglyceryl-4 Punicate, Sodium Citrate.

## 4. MS Glow For Men - 4 record, 3 varian terbaru aktif

### M01 - Energizer Facial Wash 100 ml

**Bahan penting untuk pemetaan:** Niacinamide, oatmeal/Avena Strigosa, Kaolin, Aloe, Witch Hazel, Lemon Extract, Allantoin, Hydrated Silica, fragrance.  
**Sumber klaim resmi:** [MS Glow Official Store](https://web.ms-glow.id/p/msglow-official-store-id-1/ms-glow-for-men-paket-komplit).  
**Sumber INCI Indonesia:** [Blibli—Energizer Facial Wash](https://www.blibli.com/p/energizer-facial-wash-ms-glow-for-men/ps--PUB-70185-00066). [Apotek Mandjur Flagship Store](https://www.blibli.com/p/ms-glow-men-energizer-facial-wash-100-ml-pembersih-wajah-3-in-1-pria/ps--MAR-60030-03435) mengonfirmasi ukuran 100 ml, PT Kosmetika Global Indonesia, dan BPOM NA18191234497. Daftar yang sama juga tersedia di [INCIdecoder](https://incidecoder.com/products/ms-glow-facial-wash-for-men).  
**Status:** `VALIDATED_ID_FULL_INCI`; full INCI dan identitas produk telah ditemukan pada sumber Indonesia, meskipun bukan laman komposisi resmi merek.

**INCI Indonesia:** Water, Myristic Acid, Stearic Acid, Potassium Cocoyl Glycinate, Glycerin, Potassium Hydroxide, Lauric Acid, Glycol Distearate, Oatmeal, Glyceryl Stearate, 1,3-Butylene Glycol, Kaolin, Niacinamide, Aloe Barbadensis Leaf Extract, Hamamelis Virginiana Extract, Citrus Limon (Lemon) Fruit Extract, Phenoxyethanol, Triethylene Glycol, Hydrated Silica, Fragrance, Allantoin, Polyquaternium-7, Avena Strigosa Seed Extract, BHT, Disodium EDTA.

**Catatan audit:** oatmeal, kaolin, dan hydrated silica dapat berperan sebagai partikel/absorben, sedangkan fragrance, witch hazel, dan lemon extract relevan untuk skrining kulit sensitif. Normalisasi ejaan dilakukan dari teks retailer (`Glynate`, `disterate`, dan sejenisnya); `inci_raw` tetap perlu menyimpan teks sumber.

**Status katalog:** record historis; tidak lagi menjadi alternatif aktif setelah lini terbaru M02-M04 ditambahkan.

### M02 - Ultra Bright Facial Wash 100 ml

**Bahan penting untuk pemetaan:** Niacinamide, Glycolic Acid, Sodium Hyaluronate, Papain, Ferulic Acid, Tranexamic Acid, Ceramide AP/EOP/NP, citrus oils, Limonene.  
**Sumber resmi dan BPOM:** [MS Glow Official Store](https://web.ms-glow.id/p/msglow-official-store-id-1/ms-glow-for-men-ultra-bright-facial-wash), BPOM `NA18241208586`.  
**Sumber full INCI Indonesia:** [ASTRO Indonesia](https://www.astronauts.id/p/ms-glow-for-men-ultra-bright-facial-wash-100ml-45422).  
**Status:** `VALIDATED_ID_FULL_INCI`; nama, ukuran, klaim utama, dan BPOM dikonfirmasi laman resmi, sedangkan transkripsi INCI lengkap berasal dari retailer Indonesia.

**INCI Indonesia:** Aqua, Myristic Acid, Lauramidopropyl Betaine, Palmitic Acid, Lauric Acid, Potassium Hydroxide, Stearic Acid, Glycerin, Glycol Distearate, Niacinamide, Sodium Taurine Laurate, Glycolic Acid, Glyceryl Stearate, Trehalose, Palm Kernel/Coco Glucoside, Phenoxyethanol, PEG-6 Cocamide, Sodium Hyaluronate, Papain, Cocamidopropyl Hydroxysultaine, Polyquaternium-51, Polyacrylate Crosspolymer-6, Sodium Lauroyl Glutamate, Calendula Officinalis Flower Extract, Allantoin, Hydroxyacetophenone, Sodium Hydroxide, Maltodextrin, Leuconostoc/Radish Root Ferment Filtrate, Sucrose, Hydroxypropyl Methylcellulose, Sodium Chloride, Lactobacillus/Punica Granatum Fruit Ferment Extract, Ferulic Acid, Tranexamic Acid, Caprylyl Glycol, Sodium Benzoate, Microcrystalline Cellulose, Leuconostoc/Radish Root Ferment Filtrate, Potassium Sorbate, Disodium Cocoyl Glutamate, Hydrogenated Palm Glycerides Citrate, Glyceryl Oleate, Sodium PCA, PEG-100 Stearate, Urea, 1,2-Hexanediol, T-Butyl Alcohol, Citric Acid, Ceramide AP, Ceramide EOP, CI 77499, Succinoglycan, Citrus Aurantium Dulcis Peel Oil, Ceramide NP, Simmondsia Chinensis (Jojoba) Seed Oil, CI 77491, Zea Mays Starch, Tocopherol, Coconut Acid, Citrus Paradisi Peel Oil, CI 77492, Limonene.

### M03 - Hydra Boost Facial Wash 100 ml

**Bahan penting untuk pemetaan:** mild surfactants, Glycerin, Hyaluronic Acid dan turunannya, Madecassoside, Ceramide NP/NS/NG/AS/AP/EOP, Ectoin, fragrance.  
**Sumber resmi dan BPOM:** [MS Glow Official Store](https://web.ms-glow.id/go/p/msglow-official-store-id-1/ms-glow-for-men-hydra-boost-facial-wash), BPOM `NA18241206802`.  
**Sumber full INCI Indonesia:** [ASTRO Indonesia](https://www.astronauts.id/p/ms-glow-for-men-hydra-boost-facial-wash-100ml-45421).  
**Status:** `VALIDATED_ID_FULL_INCI`; nama, ukuran, klaim utama, dan BPOM dikonfirmasi laman resmi, sedangkan transkripsi INCI lengkap berasal dari retailer Indonesia.

**INCI Indonesia:** Aqua, Potassium Cocoyl Glycinate, Acrylates Copolymer, Lauryl Hydroxysultaine, Decyl Glucoside, Glycerin, Potassium Chloride, Lauryl Glucoside, PEG-40 Hydrogenated Castor Oil, Butylene Glycol, Glycine, Fomes Officinalis Extract, Phenoxyethanol, Potassium Sulfate, Hydrolyzed Royal Jelly Protein, Glucose, Hydroxypropyl Methylcellulose, Caprylyl Glycol, Hyaluronic Acid, Madecassoside, Acetyl Hexapeptide-8, Hydroxypropyltrimonium Hyaluronate, Hydrolyzed Rhodophyceae Extract, Sodium Chloride, Cellulose, Hydrolyzed Sodium Hyaluronate, Parfum, Hydrogenated Lecithin, Ascorbyl Palmitate, Kappaphycus Alvarezii Extract, Ceramide NP, Chondrus Crispus Extract, Lactose, Sodium Acetylated Hyaluronate, Sodium Hyaluronate Crosspolymer, Potassium Hydroxide, Jojoba Esters, Citric Acid, Potassium Hyaluronate, Ceramide NS, 1,2-Hexanediol, Ceramide NG, Sodium Hyaluronate, Xanthan Gum, Ceramide AS, Hydroxyacetophenone, Disodium EDTA, Tocopheryl Acetate, Ectoin, Hydrolyzed Hyaluronic Acid, Ceramide AP, Ceramide EOP, Phytosterols, CI 77007, CI 61570.

### M04 - Acnotion Facial Wash 100 ml

**Bahan penting untuk pemetaan:** Niacinamide, Centella Asiatica, Salix Alba Bark Extract, Menthol, Panthenol, Allantoin, Tea Tree Oil, fragrance.  
**Sumber resmi dan BPOM:** [MS Glow Official Store](https://web.ms-glow.id/p/msglow-official-store-id-1/ms-glow-for-men-acnotion-facial-wash), BPOM `NA18241206800`.  
**Sumber full INCI Indonesia:** [ASTRO Indonesia](https://www.astronauts.id/p/ms-glow-for-men-acnotion-facial-wash-100ml).  
**Status:** `VALIDATED_ID_FULL_INCI`; nama, ukuran, klaim utama, dan BPOM dikonfirmasi laman resmi, sedangkan transkripsi INCI lengkap berasal dari retailer Indonesia.

**INCI Indonesia:** Water, Cocamidopropyl Betaine, Sodium Methyl Cocoyl Taurate, Sodium Lauroyl Glutamate, Glycerin, Propanediol, Niacinamide, Sodium PCA, Phenoxyethanol, Caprylyl Glycol, Ethylhexylglycerin, Fragrance, Sodium Benzoate, Disodium EDTA, Prunus Serrulata Flower Extract, Stellaria Media (Chickweed) Extract, Dandelion Extract, Glycyrrhiza Glabra Root Extract, Centella Asiatica Leaf Extract, Sophora Flavescens Root Extract, Corallina Officinalis Extract, Hamamelis Virginiana (Witch Hazel) Extract, Sodium Methyl Cocoyl Taurate, Salix Alba Bark Extract, Aloe Barbadensis Leaf Extract, Cucumis Sativus Fruit Extract, Coccinia Indica Fruit Extract, Rubia Cordifolia Root Extract, Camellia Sinensis Leaf Extract, Santalum Album Extract, Curcuma Longa Root Extract, Ocimum Sanctum Leaf Extract, Lawsonia Inermis Extract, Moringa Pterygosperma Seed Extract, Allium Cepa Bulb Extract, Portulaca Oleracea Extract, Nelumbium Speciosum Flower Extract, Morus Alba Leaf Extract, Melia Azadirachta Flower Extract, Melia Azadirachta Leaf Extract, Melia Azadirachta Fruit Extract, Swertia Japonica Extract, Phellodendron Amurense Bark Extract, Equisetum Arvense Extract, Menthol, Ethylhexylglycerin, Tocopheryl Acetate, Panthenol, Allantoin, Propyl Gallate, Ascorbic Acid, Palmitic Acid, Citric Acid, Propionyl Carnitine, Curcuma Longa Root Oil, Melaleuca Alternifolia (Tea Tree) Leaf Oil.

## 5. Garnier Men - 9 varian

Katalog resmi menyebut 8 kelompok facial wash, sedangkan katalog produk aktif juga menampilkan Oil Control Icy Scrub; karena itu dataset mencatat 9 varian dan menandai status masing-masing. Sumber: [Garnier Men](https://www.garnier.co.id/tentang-brands/garnier-men).

### G01 - AcnoFight Scrub in Foam

**Klaim resmi Indonesia:** membantu melawan tanda masalah wajah akibat jerawat. [Sumber katalog resmi Garnier Indonesia](https://www.garnier.co.id/tentang-brands/garnier-men).  
**Status:** `VALIDATED_ID_VERSIONED`; full INCI Indonesia telah ditemukan, tetapi terdapat dua kandidat formula yang berbeda.

**Kandidat formula A - daftar yang diberikan peneliti:** Aqua/Water, Glycerin, Myristic Acid, Palmitic Acid, Stearic Acid, Potassium Hydroxide, Lauric Acid, Glyceryl Distearate, Glyceryl Stearate, Parfum/Fragrance, Kaolin, PEG-14M, CI 42090/Blue 1, CI 74160/Pigment Blue 15, Paraffinum Liquidum/Mineral Oil, Sorbitol, Vaccinium Myrtillus Fruit Extract (Herba Repair), Pumice, Salicylic Acid, Perlite, Phenoxyethanol, Propylene Glycol, Tetrasodium EDTA, Citrus Limon Fruit Extract/Lemon Fruit Extract, Menthol, Ethylcellulose.

**Sumber yang mendukung formula A:** [INCIdecoder - Garnier Men Acno Fight Anti Pimple Face Wash](https://incidecoder.com/products/garnier-men-acno-fight-anti-pimple-face-wash), unggahan pengguna tanggal 21 Juli 2022. Daftar yang sama juga ditemukan pada produk pasar India dengan EAN 8901526618262; karena itu daftar ini belum dapat langsung dianggap sebagai formula Indonesia.

**Kandidat formula B - transkripsi yang saat ini tampil pada Watsons Indonesia:** Aqua/Water, Glycerin, Myristic Acid, Palmitic Acid, Stearic Acid, Potassium Hydroxide, Lauric Acid, Polyethylene, Glyceryl Distearate, Glyceryl Stearate, Parfum/Fragrance, Kaolin, CI 42090/Blue 1, CI 42090/Blue 1 Lake, CI 77007/Ultramarines, Citrus Medica Limonum Extract/Lemon Fruit Extract, Menthol, Methylisothiazolinone, PEG-14M, Salicylic Acid, Synthetic Wax, Tetrasodium EDTA, Vaccinium Myrtillus Extract/Vaccinium Myrtillus Fruit Extract.

**Sumber formula B:** [Watsons Indonesia - Garnier Men Acno Fight Foam 6 in 1 Anti Acne 100 ml](https://www.watsons.co.id/id/garnier-men-acno-fight-foam-6-in-1-anti-acne-100ml/p/BP_32839) dan [Blibli—Garnier Men Acno Fight Foam 6 in 1](https://www.blibli.com/p/garnier-men-acno-fight-foam-6-in-1-anti-acne-100ml/ps--TOL-70509-02297). Dua sumber Indonesia tersebut mendukung formula B sebagai formula Indonesia yang dapat diaudit.

**Perbedaan penting:** formula A mencantumkan Mineral Oil, Sorbitol, Pumice, Perlite, Phenoxyethanol, Propylene Glycol, dan Ethylcellulose. Formula B mencantumkan Polyethylene, Synthetic Wax, Methylisothiazolinone, dan kombinasi pewarna yang berbeda. Untuk dataset Indonesia, formula B menjadi kandidat utama; formula A disimpan sebagai formula lintas pasar/versi lain. Jangan menggabungkan keduanya.

**Bahan penting untuk pemetaan setelah versi formula dipastikan:** Salicylic Acid; partikel scrub/abrasif (Pumice dan Perlite pada formula A atau Polyethylene dan Synthetic Wax pada formula B); Kaolin; Vaccinium Myrtillus Extract; Lemon Extract; Menthol; fragrance. Methylisothiazolinone hanya boleh ditandai jika formula B terbukti sesuai label penelitian.

### G02 - AcnoFight Wasabi Brightening Foam

**Bahan penting:** Kaolin, Wasabi Root Powder, Salicylic Acid, Lactic Acid, fragrance allergens.  
**Sumber resmi:** [AcnoFight Wasabi](https://www.garnier.co.id/tentang-brands/garnier-men/acno-fight/acnofight-wasabi-brightening-foam-facial-cleanser)

**INCI resmi:** Aqua/Water, Glycerin, Myristic Acid, Palmitic Acid, Stearic Acid, Potassium Hydroxide, Lauric Acid, Glyceryl Distearate, Glyceryl Stearate, Kaolin, PEG-14M, CI 19140/Yellow 5, CI 42090/Blue 1, CI 77499/Iron Oxides, Linalool, Wasabia Japonica Root Powder, Potassium Sorbate, Parfum/Fragrance, Salicylic Acid, Sodium Benzoate, Phenoxyethanol, Limonene, Disodium EDTA, Citronellol, Lactic Acid, Hexyl Cinnamal.

### G03 - Oil Control Anti-Shine Brightening Cooling Foam

**Bahan penting:** Kaolin, Lemon Fruit Extract, Menthol, fragrance.  
**Sumber resmi:** [Oil Control Cooling Foam](https://www.garnier.co.id/tentang-brands/garnier-men/oil-control/oil-control-anti-shine-brightening-cooling-foam)

**INCI resmi:** Aqua/Water, Glycerin, Myristic Acid, Palmitic Acid, Stearic Acid, Potassium Hydroxide, Lauric Acid, Butylene Glycol, Sorbitol, Glyceryl Stearate SE, Kaolin, Parfum/Fragrance, CI 77266/Black 2, Citrus Medica Limonum Extract/Lemon Fruit Extract, Menthol, Tetrasodium EDTA.

### G04 - Oil Control Super Duo Foam

**Bahan penting:** Salicylic Acid, Kaolin, Perlite, Ascorbyl Glucoside, Menthol, fragrance allergens.  
**Sumber resmi:** [Oil Control Super Duo](https://www.garnier.co.id/tentang-brands/garnier-men/oil-control/oil-control-super-duo-foam)

**INCI resmi:** Aqua/Water, Glycerin, Myristic Acid, Stearic Acid, Palmitic Acid, Potassium Hydroxide, Lauric Acid, Butylene Glycol, Sorbitol, Glyceryl Stearate SE, PEG-8, Parfum/Fragrance, CI 42090/Blue 1 Lake, Linalool, Salicylic Acid, Kaolin, Perlite, Alumina, Phenoxyethanol, Limonene, Ascorbyl Glucoside, Tetrasodium EDTA, Menthol, Polyquaternium-4, Hexyl Cinnamal, Benzyl Salicylate, Benzyl Alcohol.

**Catatan:** kode warna dinormalisasi ke CI 42090; simpan teks mentah sumber karena laman dapat memiliki salah ketik.

### G05 - Oil Control Matcha Deep Clean Foaming Gel

**Bahan penting:** Salicylic Acid, Green Tea Extract, Lemon Extract, Menthol, SLES/Co-Betaine surfactants.  
**Sumber resmi:** [Oil Control Matcha Gel](https://www.garnier.co.id/tentang-brands/garnier-men/oil-control/oil-control-matcha-deep-clean-foaming-gel)

**INCI resmi:** Aqua/Water, Sodium Laureth Sulfate, Glycerin, Coco-Betaine, Sodium Chloride, CI 19140/Yellow 5, CI 42090/Blue 1, Sodium Benzoate, Sodium Hydroxide, PEG-60 Hydrogenated Castor Oil, PPG-5-Ceteth-20, PEG-30 Dipolyhydroxystearate, Trideceth-6, Salicylic Acid, Camellia Sinensis Leaf Extract, Benzophenone-4, Menthol, Parfum/Fragrance, Acrylates/C10-30 Alkyl Acrylate Crosspolymer, Citrus Limon Fruit Extract/Lemon Fruit Extract, Citric Acid, Dextrin.

### G06 - Oil Control 3in1 Charcoal Foam

**Bahan penting:** Pumice/Perlite (scrub), Salicylic Acid, Kaolin, Charcoal, Ascorbyl Glucoside, Menthol, fragrance allergens.  
**Sumber resmi:** [Oil Control 3in1 Charcoal](https://www.garnier.co.id/tentang-brands/garnier-men/oil-control/oil-control-3in1-charcoal-foam)

**INCI resmi:** Aqua/Water, Glycerin, Myristic Acid, Stearic Acid, Palmitic Acid, Potassium Hydroxide, Lauric Acid, Butylene Glycol, Sorbitol, Glyceryl Stearate SE, PEG-8, Parfum/Fragrance, CI 77499/Iron Oxides, Linalool, Pumice, Salicylic Acid, Sodium Dehydroacetate, Perlite, Kaolin, Phenoxyethanol, Limonene, Ascorbyl Glucoside, Tetrasodium EDTA, Charcoal Powder, Citric Acid, Menthol, Polyquaternium-4, Hexyl Cinnamal, Polyglycerin-10, Polyglyceryl-10 Myristate, Polyglyceryl-10 Stearate, Benzyl Salicylate, Benzyl Alcohol.

### G07 - Oil Control Icy Scrub 50/100 ml

**Bahan penting:** Kaolin, Pumice, Perlite, Salicylic Acid, Lemon Extract, Charcoal, Menthol, fragrance.  
**Sumber resmi:** [Oil Control Icy Scrub](https://www.garnier.co.id/tentang-brands/garnier-men/oil-control/oil-control-icy-scrub)

**INCI resmi:** Aqua/Water, Glycerin, Myristic Acid, Palmitic Acid, Stearic Acid, Potassium Hydroxide, Lauric Acid, Butylene Glycol, Sorbitol, Glyceryl Stearate SE, Kaolin, Parfum/Fragrance, CI 74160/Pigment Blue 15, Paraffinum Liquidum/Mineral Oil, Pumice, Salicylic Acid, Sodium Dehydroacetate, Perlite, Phenoxyethanol, Tetrasodium EDTA, Citrus Limon Fruit Extract/Lemon Fruit Extract, Charcoal Powder, Citric Acid, Menthol, Ethylcellulose, Polyglycerin-10, Polyglyceryl-10 Myristate, Polyglyceryl-10 Stearate.

**Catatan:** laman resmi menulis `SALYLIC ACID`; dinormalisasi menjadi `Salicylic Acid`, tetapi teks mentah harus disimpan untuk audit.

### G08 - TurboBright Super Duo Foam

**Bahan penting:** Salicylic Acid, Kaolin, Charcoal, Ascorbyl Glucoside, Menthol, fragrance allergens.  
**Sumber resmi:** [TurboBright Super Duo](https://www.garnier.co.id/tentang-brands/garnier-men/turbobright/turbobright-super-duo-foam)

**INCI resmi:** Aqua/Water, Glycerin, Myristic Acid, Stearic Acid, Palmitic Acid, Potassium Hydroxide, Lauric Acid, Butylene Glycol, Sorbitol, Glyceryl Stearate SE, PEG-8, Parfum/Fragrance, CI 77499/Iron Oxides, Linalool, Salicylic Acid, Sodium Dehydroacetate, Kaolin, Phenoxyethanol, Limonene, Ascorbyl Glucoside, Tetrasodium EDTA, Charcoal Powder, Citric Acid, Menthol, Polyquaternium-4, Hexyl Cinnamal, Polyglycerin-10, Polyglyceryl-10 Myristate, Polyglyceryl-10 Stearate, Benzyl Salicylate, Benzyl Alcohol.

### G09 - TurboBright Shaving & Cleansing Brightening Foam

**Bahan penting:** Salicylic Acid, Kaolin, Perlite, Ascorbyl Glucoside, Menthol, fragrance allergens.  
**Sumber resmi:** [TurboBright Shaving & Cleansing](https://www.garnier.co.id/tentang-brands/garnier-men/turbobright/turbobright-shaving-cleansing-brightening-foam)

**INCI resmi:** Aqua/Water, Glycerin, Myristic Acid, Stearic Acid, Palmitic Acid, Potassium Hydroxide, Lauric Acid, Butylene Glycol, Sorbitol, Glyceryl Stearate SE, PEG-8, Parfum/Fragrance, CI 42090/Blue 1 Lake, Linalool, Salicylic Acid, Kaolin, Perlite, Alumina, Phenoxyethanol, Limonene, Ascorbyl Glucoside, Tetrasodium EDTA, Menthol, Polyquaternium-4, Hexyl Cinnamal, Benzyl Salicylate, Benzyl Alcohol.

## 6. NIVEA Men - 8 SKU pada katalog resmi

Katalog: [NIVEA Men Perawatan Wajah](https://www.nivea.co.id/produk/pria/wajah).

### N01 - Deep Acne Attack Scrub Mud Facial Foam 100 ml

**Bahan penting:** Cellulose scrub, Carbon/CI 77268:1, Carnitine, Garcinia, Magnolia, Salicylic Acid, Licorice, Menthol.  
**Sumber resmi:** [Deep Acne Attack](https://www.nivea.co.id/produk/nivea-men-deep-acne-attack-scrub-mud-facial-foam-100ml-89997770172930048.html)

**INCI resmi:** Aqua, Glycerin, Myristic Acid, Palmitic Acid, Stearic Acid, Potassium Hydroxide, Lauric Acid, Glyceryl Stearate, PEG-8, Cellulose, Cera Alba, Microcrystalline Cellulose, Acrylates Copolymer, Sodium Methyl Cocoyl Taurate, Menthol, Trisodium EDTA, Arachidic Acid, CI 16035, Calcium Carbonate, Oleic Acid, CI 77268:1, Carnitine, Garcinia Cambogia Fruit Extract, Magnolia Officinalis Bark Extract, Salicylic Acid, Sodium Lauryl Sulfate, Glycyrrhiza Glabra Root Extract.

### N02 - Bright Oil Clear Pore Minimizing Scrub 100 ml

**Bahan penting:** Magnolia, 4-Butylresorcinol, Licorice, Carnitine, Ginkgo, Ginseng, Sodium Ascorbyl Phosphate, Panthenol, Tocopheryl Acetate, cellulose/microcrystalline cellulose, fragrance.  
**Sumber resmi:** [NIVEA Indonesia](https://www.nivea.co.id/produk/nivea-men-bright-oil-clear-pore-minimizing-scrub-100ml-89997770029540048.html).  
**Status:** `VERIFIED_OFFICIAL_INCI`; kemasan tetap menjadi acuan untuk barang yang benar-benar dibeli.

**INCI resmi:** Aqua, Potassium Myristate, Propylene Glycol, Potassium Palmitate, Potassium Stearate, Glycerin, Potassium Laurate, PEG-150, PEG-8, Glyceryl Stearate, Magnolia Officinalis Bark Extract, 4-Butylresorcinol, Glycyrrhiza Glabra Root Extract, Carnitine, Glyceryl Glucoside, Ginkgo Biloba Leaf Extract, Panax Ginseng Root Extract, Aluminum Chlorohydrate, Sodium Ascorbyl Phosphate, Panthenol, Magnesium Chloride, Tocopheryl Acetate, Cellulose, Microcrystalline Cellulose, Cera Alba, BHT, Sodium Methyl Cocoyl Taurate, Potassium Arachidate, Potassium Oleate, Trisodium EDTA, Linalool, Parfum.

**Catatan perubahan:** daftar resmi saat ini berbeda dari transkripsi retailer lama yang mencantumkan Polyethylene dan Benzophenone-3. Simpan keduanya sebagai versi formula terpisah; jangan menimpa riwayat lama.

### N03 - Extra Bright Facial Foam 50 ml

**Bahan penting:** Sodium Ascorbyl Phosphate, Lactic Acid, 4-Butylresorcinol, fragrance.  
**Sumber resmi:** [Extra Bright 50 ml](https://www.nivea.co.id/produk/nivea-men-extra-bright-facial-foam-50ml-89997778883500048.html)

**INCI resmi:** Aqua, Myristic Acid, Propylene Glycol, Palmitic Acid, Stearic Acid, Potassium Hydroxide, Glycerin, Lauric Acid, PEG-150, PEG-8, Glyceryl Stearate, Sodium Ascorbyl Phosphate, Lactic Acid, 4-Butylresorcinol, Cera Alba, Sodium Chloride, Sodium Methyl Cocoyl Taurate, Arachidic Acid, Oleic Acid, Pentylene Glycol, Trisodium EDTA, Sodium Benzoate, Parfum.

### N04 - Acne Oil Clear Acne Defense Foam 100 ml

**Bahan penting:** Magnolia, Menthol, Carnitine, Glyceryl Glucoside, Green Tea.  
**Sumber resmi:** [Acne Defense Foam](https://www.nivea.co.id/produk/nivea-men-acne-oil-clear-acne-defense-foam-89997778883810048.html)

**INCI resmi:** Aqua, Glycerin, Myristic Acid, Palmitic Acid, Stearic Acid, Potassium Hydroxide, Lauric Acid, Glyceryl Stearate, PEG-8, Cera Alba, Acrylates Copolymer, Sodium Methyl Cocoyl Taurate, Magnolia Officinalis Bark Extract, Menthol, Trisodium EDTA, Arachidic Acid, Oleic Acid, Carnitine, Glyceryl Glucoside, Camellia Sinensis Leaf Extract, CI 47005, CI 42090, Parfum.

### N05 - Bright Oil Clear Anti-Shine+Purify Mud Foam 100 ml

**Bahan penting:** 4-Butylresorcinol, Licorice, Carnitine, Sodium Salicylate, Fucus, Carbon/CI 77266, Menthol.  
**Sumber resmi:** [Anti-Shine+Purify Mud Foam](https://www.nivea.co.id/produk/nivea-men-bright-oil-clear-anti-shinepluspurify-mud-foam-100ml-40058086948220048.html)

**INCI resmi:** Aqua, Glycerin, Potassium Myristate, Potassium Palmitate, Potassium Stearate, Potassium Laurate, Glyceryl Stearate, PEG-8, Cera Alba, Menthol, 4-Butylresorcinol, Glycyrrhiza Glabra Root Extract, Carnitine, Sodium Salicylate, Fucus Vesiculosus Extract, Magnesium Chloride, Calcium Carbonate, Acrylates Copolymer, Sodium Methyl Cocoyl Taurate, Potassium Arachidate, Potassium Oleate, Caprylic/Capric Triglyceride, Trisodium EDTA, Parfum, CI 77266, CI 61570, CI 16035, CI 10316.

### N06 - Extra Bright Mud Foam 100 ml

**Bahan penting:** Sodium Ascorbyl Phosphate, Lactic Acid, 4-Butylresorcinol, Menthol, cellulose, Carbon/CI 77268:1.  
**Sumber resmi:** [Extra Bright Mud Foam](https://www.nivea.co.id/produk/nivea-men-extra-bright-mud-foam-100ml-89997770017110048.html)

**INCI resmi:** Aqua, Glycerin, Myristic Acid, Palmitic Acid, Stearic Acid, Lauric Acid, Potassium Hydroxide, Glyceryl Stearate, Cera Alba, Sodium Ascorbyl Phosphate, Lactic Acid, 4-Butylresorcinol, Menthol, Cellulose, Cellulose Gum, Sodium Chloride, Sodium Methyl Cocoyl Taurate, Hydrogenated Coconut Acid, Arachidic Acid, Oleic Acid, Trisodium Ethylenediamine Disuccinate, Pentylene Glycol, Benzoic Acid, Sodium Benzoate, Parfum, CI 77268:1.

### N07 - Extra Bright Facial Foam 100 ml

**Bahan penting:** Sodium Ascorbyl Phosphate, Lactic Acid, 4-Butylresorcinol, fragrance.  
**Sumber resmi:** [Extra Bright 100 ml](https://www.nivea.co.id/produk/nivea-men-extra-bright-facial-foam-100ml-89997778883670048.html)

**INCI resmi:** Aqua, Myristic Acid, Propylene Glycol, Palmitic Acid, Stearic Acid, Potassium Hydroxide, Glycerin, Lauric Acid, PEG-150, PEG-8, Glyceryl Stearate, Sodium Ascorbyl Phosphate, Lactic Acid, 4-Butylresorcinol, Cera Alba, Sodium Chloride, Sodium Methyl Cocoyl Taurate, Hydrogenated Coconut Acid, Arachidic Acid, Oleic Acid, Pentylene Glycol, Trisodium EDTA, Sodium Benzoate, Parfum.

**Catatan penting:** sumber resmi 50 ml dan 100 ml berbeda pada `Hydrogenated Coconut Acid`. Sampai label fisik diverifikasi, simpan sebagai formula/SKU terpisah dan jangan mengasumsikan daftar identik.

### N08 - Bright Oil Clear Pore Minimizing Foam 100 ml

**Bahan penting:** 4-Butylresorcinol, Tocopheryl Acetate, Sodium Ascorbyl Phosphate, Magnolia, Panthenol, Ginseng, Menthol.  
**Sumber resmi:** [Pore Minimizing Foam](https://www.nivea.co.id/produk/nivea-men-bright-oil-clear-pore-minimizing-foam-100ml-89997770001890048.html)

**INCI resmi:** Aqua, Myristic Acid, Propylene Glycol, Palmitic Acid, Stearic Acid, Potassium Hydroxide, Glycerin, Lauric Acid, PEG-150, PEG-8, Glyceryl Stearate, Cera Alba, Sodium Thiosulfate, Sodium Methyl Cocoyl Taurate, Arachidic Acid, Trisodium EDTA, Oleic Acid, Menthol, Sodium Chloride, Hydrogenated Coconut Acid, Aluminum Chlorohydrate, Carnitine, Magnesium Chloride, 4-Butylresorcinol, Tocopheryl Acetate, Sodium Ascorbyl Phosphate, Magnolia Officinalis Bark Extract, Panthenol, Glucose, PEG-40 Hydrogenated Castor Oil, Trideceth-9, Pantolactone, Citric Acid, Lactic Acid, Panax Ginseng Root Extract, Sodium Benzoate, Potassium Sorbate, Sodium Sulfate, Caramel, Linalool, Tocopherol, Parfum, CI 42090.

## 7. Men's Biore - 8 alternatif awal dan 1 produk versi baru

Katalog resmi: [Kao Indonesia - Men's Biore](https://www.kao.com/id/id/products/mensbiore/). Full INCI Indonesia tersedia untuk seluruh record B01-B08 dan B03N. B03 diperlakukan sebagai formula Acne Skincare lama, sedangkan B03N merupakan Acne Bright Care formula baru. Nomor BPOM dan konflik versinya tersedia pada `16-Audit-BPOM-33-Varian.md`.

### B01 - Facial Foam Bright Expert 100 g

**Bahan penting:** Niacinamide, Glycerin, Sorbitol, Polyquaternium-7; fragrance dan BHT dicatat sebagai bahan perhatian kontekstual.  
**Klaim resmi Indonesia:** 5x Vitamin B3, Aqua Hydration, gentle foam. [Sumber resmi](https://www.kao.com/id/id/products/mensbiore/mbi_bright_expert_00/)  
**Sumber pendukung produk:** [Alfagift Indonesia](https://alfagift.id/p/mens-biore-pembersih-wajah-foam-gentle-bright-expert-100-g-801692) dan [KAO Personal Care di Blibli](https://www.blibli.com/p/men-s-biore-bright-expert-facial-foam-100-g/is--KAK-25212-00827-00001).  
**BPOM:** `NA18261200825`; versi nama lama `NA18231200235`.  
**Status:** `VALIDATED_ID_FULL_INCI`; full INCI ditranskripsi dari foto belakang kemasan Alfagift Indonesia dan dicocokkan dengan identitas serta klaim produk resmi Indonesia.

**INCI Indonesia:** Water (Aqua), Glycerin, Sorbitol, Stearic Acid, Palmitic Acid, Myristic Acid, Laureth-6 Carboxylic Acid, Potassium Hydroxide, Lauric Acid, Niacinamide, Polyquaternium-7, Fragrance (Parfum), Disodium EDTA, PEG-65M, BHT, Phenoxyethanol.

**Implikasi scoring:** mendukung kriteria pencerahan/kulit kusam melalui Niacinamide dan kelembapan melalui Glycerin serta Sorbitol. Tidak diberi nilai khusus anti-acne karena tidak mengandung Salicylic Acid atau Tea Tree Oil pada daftar ini.

### B02 - Facial Foam Oil Balance 100 g

**Bahan penting:** Sorbitol, Kaolin/Japanese Clay, menthol, fragrance; basis pembersih Lauryl Hydroxysultaine dan sabun asam lemak.  
**Klaim resmi Indonesia:** Japanese Clay, Aqua Hydration, gentle foam. [Sumber resmi](https://www.kao.com/id/id/products/mensbiore/mbi_nonscrub_bright_00/)  
**Sumber INCI Indonesia:** [Gazelook Indonesia](https://gazelook.id/product/mens-biore-gentle-clean-oil-balance-foam/). Identitas produk dikonfirmasi oleh [official store KAO di Blibli](https://www.blibli.com/p/men-s-biore-oil-balance-facial-foam-100-g/ps--KAK-25212-00177).  
**BPOM:** `NA18231200236`; versi lama `NA18211200324`.  
**Status:** `VALIDATED_ID_FULL_INCI`; simpan URL dan tanggal akses karena sumber INCI adalah basis data kosmetik Indonesia, bukan laman produsen.

**INCI Indonesia:** Aqua/Water, Sorbitol, Lauryl Hydroxysultaine, Lauric Acid, Potassium Hydroxide, Laureth-6 Carboxylic Acid, Myristic Acid, Ethylhexylglycerin, Acrylates/C10-30 Alkyl Acrylate Crosspolymer, Fragrance/Parfum/Aroma, Menthol, Palmitic Acid, Disodium EDTA, Kaolin, PEG-6, Phenoxyethanol, o-Cymen-5-ol/Isopropyl Methylphenol.

**Catatan audit:** formula ini tidak boleh disalin ke Bright Expert. B01 dan B02 tetap dua produk berbeda.

### B03 - Facial Foam Acne Skincare 100 g

**Bahan penting:** Glycerin, Sorbitol, Propylene Glycol, Tea Tree Leaf Oil, o-Cymen-5-ol, menthol, fragrance.  
**Klaim resmi Indonesia:** Tea Tree Oil, antibacterial agent, Aqua Hydration. [Sumber resmi](https://www.kao.com/id/id/products/mensbiore/mbi_nonscrub_acne_00/)  
**Sumber INCI Indonesia:** [Astro Indonesia](https://www.astronauts.id/p/mens_biore_acne_bright_care_gentle_facial_foam_100_gr) dan [Gazelook Indonesia](https://gazelook.id/product/mens-biore-gentle-clean-acne-skincare-foam/).  
**BPOM:** `NA18211200323`; registrasi Acne Skincare yang lebih baru `NA18231200237`.  
**Status:** `VALIDATED_ID_VERSIONED`; full INCI Indonesia tersedia dan Astro mencantumkan BPOM `NA18211200323`, sehingga record ini dikunci sebagai formula **Acne Skincare lama**. Judul pemasaran “Acne Bright Care” pada retailer tidak mengubah identitas formula berdasarkan nomor BPOM.

**INCI Indonesia:** Water (Aqua), Glycerin, Palmitic Acid, Stearic Acid, Laureth-4 Carboxylic Acid, Myristic Acid, Potassium Hydroxide, Propylene Glycol, Lauric Acid, Sorbitol, Decyl Glucoside, Fragrance (Parfum), Polyquaternium-7, Disodium EDTA, Menthol, Melaleuca Alternifolia (Tea Tree) Leaf Oil, o-Cymen-5-ol/Isopropyl Methylphenol, Methylparaben, Propylparaben.

**Koreksi identitas:** daftar di atas tidak boleh diberi nama Acne Bright Care formula baru. Formula B03 lama tetap mengandung Methylparaben dan Propylparaben, berbeda dari B03N.

### B03N - Acne Bright Care Gentle Facial Foam 100 g

**Klaim resmi Indonesia:** Tea Tree Oil dan Vitamin C untuk perawatan kulit rentan berjerawat dan bekas jerawat. [Sumber resmi](https://indonesiabiore.com/mensbiore/product/acne-bright-care-facial-foam)  
**BPOM:** `NA18241202965`; registrasi lebih baru `NA18261200824`.  
**Sumber label Indonesia:** [Shopee Indonesia—Acne Bright Care 20 g](https://shopee.co.id/Men%27s-Biore-Facial-Wash-Acne-Bright-Care-Non-Scrub-Tea-Tree-Oil-With-Antibacterial-Agent-Vit-C-20gr-i.396834019.40516980839).  
**Status:** `VALIDATED_ID_VERSIONED`; foto label Indonesia menampilkan full INCI dan BPOM `NA18241202965`. Registrasi `NA18261200824` disimpan sebagai nomor lebih baru, tetapi tidak dianggap bukti reformulasi tanpa label nomor tersebut.  

**INCI Indonesia:** Water (Aqua), Glycerin, Palmitic Acid, Stearic Acid, Laureth-4 Carboxylic Acid, Myristic Acid, Potassium Hydroxide, Propylene Glycol, Lauric Acid, Sorbitol, Decyl Glucoside, Fragrance (Parfum), Polyquaternium-7, Disodium EDTA, Menthol, o-Cymen-5-ol, Sodium Benzoate, Ascorbic Acid, Melaleuca Alternifolia (Tea Tree) Leaf Oil.

**Implikasi scoring:** Tea Tree Leaf Oil dan o-Cymen-5-ol mendukung penilaian kulit rentan berjerawat; Ascorbic Acid mendukung klaim pencerahan; Glycerin, Propylene Glycol, dan Sorbitol mendukung kelembapan. Menthol, fragrance, dan Tea Tree Oil menjadi faktor perhatian pada kulit sensitif atau skin barrier terganggu. **Ascorbyl Glucoside tidak digunakan** karena label mencantumkan Ascorbic Acid.

### B04 - Scrub Facial Wash Bright Oil Clear 100 g

**Bahan penting:** Cellulose dan Corn Starch (scrub), Charcoal, Green Tea, Bentonite, Betaine, Arginine, menthol, fragrance, alcohol.  
**Klaim resmi Indonesia:** Micro Bright Scrub dan Bamboo Charcoal. [Sumber resmi](https://www.kao.com/id/id/products/mensbiore/mbi_bright_oilclear_00/)  
**Sumber INCI Indonesia:** [Farmaku Flagship Store di Blibli](https://www.blibli.com/p/biore-mens-facial-foam-bright-oil-clear-tube-100-gr/ps--GAA-60023-10315), yang juga menampilkan BPOM NA18201203966. [INCIdecoder](https://incidecoder.com/products/mens-biore-scrub-facial-wash-bright-oil-clear) dipertahankan sebagai pembanding.  
**Status:** `VALIDATED_ID_FULL_INCI`.

**INCI Indonesia (transkripsi retailer):** Water, Glycerin, Palmitic Acid, Stearic Acid, Laureth-6 Carboxylic Acid, Propylene Glycol, Myristic Acid, Potassium Hydroxide, Sorbitol, Lauric Acid, Cellulose, Zea Mays (Corn) Starch, PEG-150, Decyl Glucoside, Fragrance/Parfum, Magnesium Potassium Fluorosilicate, Sodium MA/Vinyl Alcohol Copolymer, Disodium EDTA, Menthol, PEG-45M, Charcoal Powder, Magnesium/Potassium/Silicon/Fluoride/Hydroxide/Oxide, Silica, Alcohol, Butylene Glycol, Magnesium Oxide/CI 77711, BHT, Camellia Sinensis Leaf Extract, Caprylic/Capric Triglyceride, Arginine, Bentonite/CI 77004, Betaine, o-Cymen-5-ol, Iron.

**Koreksi sumber:** URL Watsons `BP_32997` yang sebelumnya ditempel untuk varian ini adalah halaman **Cool Oil Clear**, bukan Bright Oil Clear.

### B05 - Scrub Facial Wash Bright Energy 100 g

**Bahan penting dari formula retailer:** Cellulose dan Corn Starch (scrub), Charcoal, Green Tea, fragrance, alcohol.  
**Klaim resmi Indonesia:** Micro Bright Scrub dan Vitamin B3. [Sumber resmi](https://www.kao.com/id/id/products/mensbiore/mbi_doublescrub_white_00/)  
**Sumber INCI pendukung:** [Watsons Indonesia—White Energy](https://www.watsons.co.id/id/biore-men-facial-foam-white-energy-100-g/p/BP_32998).  
**Status:** `VALIDATED_ID_VERSIONED`; full INCI Indonesia tersedia, tetapi nama White Energy/Bright Energy dan klaim Vitamin B3 menunjukkan perbedaan versi.

**INCI pendukung:** Water, Glycerin, Sorbitol, Stearic Acid, Palmitic Acid, Dipropylene Glycol, Behenic Acid, Ethoxydiglycol, Myristic Acid, Potassium Hydroxide, Lauric Acid, Cellulose, Zea Mays (Corn) Starch, Fragrance, Magnesium Potassium Fluorosilicate, Disodium EDTA, Sodium MA/Vinyl Alcohol Copolymer, Etidronic Acid, Polyquaternium-7, Charcoal Powder, Alcohol, Butylene Glycol, Camellia Sinensis Leaf Extract, Methylparaben, Propylparaben, CI 77499.

**Konflik:** sumber retailer memakai nama **White Energy**, sedangkan katalog terkini memakai **Bright Energy**. INCI retailer juga tidak memuat Niacinamide/Vitamin B3 yang diklaim produk resmi. Perlakukan sebagai kemungkinan formula lama, bukan formula Bright Energy terkini.

### B06 - Scrub Facial Wash Cool Oil Clear 100 g

**Klaim resmi Indonesia:** Micro Power Scrub, Black Tea, Icy Menthol. [Sumber resmi](https://www.kao.com/id/id/products/mensbiore/mbi_doublescrub_cool_00/)  
**Sumber retailer yang diaudit:** [Watsons Indonesia](https://www.watsons.co.id/id/biore-men-facial-foam-cool-oil-100-g/p/BP_32997).  
**Status:** `VALIDATED_ID_VERSIONED`; full INCI Indonesia tersedia, tetapi klaim Icy Menthol tidak konsisten dengan daftar Watsons yang tampil saat audit.

**INCI yang saat ini tampil pada halaman Watsons:** Water/Aqua, Glycerin, Sorbitol, Stearic Acid, Palmitic Acid, Dipropylene Glycol, Behenic Acid, Ethoxydiglycol, Myristic Acid, Potassium Hydroxide, Lauric Acid, Cellulose, Zea Mays (Corn) Starch, Fragrance/Parfum, Magnesium Potassium Fluorosilicate, Sodium MA/Vinyl Alcohol Copolymer, Disodium EDTA, Etidronic Acid, Polyquaternium-7, Kaolin, Alcohol, Camellia Sinensis Leaf Extract, Arginine, Bentonite, Betaine, Methylparaben, Propylparaben, CI 11680, CI 74160, CI 77120, CI 77499.

**Konflik utama:** daftar yang dikirim peneliti untuk URL yang sama berbeda cukup besar—antara lain mencantumkan Laureth-4 Carboxylic Acid, Propylene Glycol, PEG-150, PEG-45M, PEG-8, Menthol, dan Aluminum Hydroxide. Daftar Watsons yang tampil sekarang justru tidak memuat Menthol, meskipun klaim resmi menyebut Icy Menthol. Kedua daftar harus disimpan sebagai kandidat formula berbeda dan tidak boleh digabung.

### B07 - Scrub Facial Wash Deep Pore Clean 100 g

**Bahan penting:** Cellulose dan Corn Starch (scrub), Bentonite/mineral clay, Betaine, Arginine, menthol, fragrance.  
**Klaim resmi Indonesia:** Micro Power Scrub dan Mineral Clay. [Sumber resmi](https://www.kao.com/id/id/products/mensbiore/mbi_doublescrub_fresh_00/)  
**Sumber INCI Indonesia:** [Farmaku Flagship Store di Blibli—Deep Pore Clean/Deep Fresh](https://www.blibli.com/p/biore-mens-facial-foam-deep-fresh-100-g/is--GAA-60023-03310-00001), yang menampilkan BPOM NA18221201191. [INCIdecoder](https://incidecoder.com/products/mens-biore-deep-pore-clean-scrub-facial-wash) dipertahankan sebagai kandidat versi lain.  
**Status:** `VALIDATED_ID_FULL_INCI` untuk formula retailer Indonesia; formula INCIdecoder disimpan terpisah.

**INCI Indonesia (transkripsi retailer):** Water/Aqua, Glycerin, Sorbitol, Stearic Acid, Palmitic Acid, Dipropylene Glycol, Behenic Acid, Ethoxydiglycol, Zea Mays (Corn) Starch, Magnesium Potassium Fluorosilicate, Sodium MA/Vinyl Alcohol Copolymer, Fragrance/Parfum, Disodium EDTA, Etidronic Acid, Menthol, Polyquaternium-7, BHT, Methylparaben, Propylparaben, CI 77499.

**INCI pendukung:** Water, Glycerin, Palmitic Acid, Stearic Acid, Laureth-6 Carboxylic Acid, Myristic Acid, Potassium Hydroxide, Propylene Glycol, Cellulose, Zea Mays (Corn) Starch, Sorbitol, Lauric Acid, PEG-150, Fragrance, Magnesium/Potassium/Silicon/Fluoride/Hydroxide/Oxide, Decyl Glucoside, Sodium MA/Vinyl Alcohol Copolymer, Disodium EDTA, Menthol, PEG-45M, PEG-6, Aluminum Hydroxide, BHT, Caprylic/Capric Triglyceride, Arginine, Bentonite, Betaine, o-Cymen-5-ol, CI 77499, CI 77891.

### B08 - Scrub Facial Wash Acne Bacterior 100 g

**Bahan penting:** Cellulose dan Corn Starch (scrub), Tea Tree Oil, Green Tea, o-Cymen-5-ol, Bentonite, Betaine, Arginine, menthol, fragrance.  
**Klaim resmi Indonesia:** Micro Natural Scrub, antibacterial agent, Tea Tree Oil. [Sumber resmi](https://www.kao.com/id/id/products/mensbiore/mbi_doublescrub_antibacterior_00/)  
**Sumber INCI Indonesia:** [Farmaku Flagship Store di Blibli—Acne Bacterior](https://www.blibli.com/p/biore-mens-facial-foam-anti-bacterior-pop-up-100-g/is--GAA-60023-05245-00001), yang menampilkan BPOM NA18191200283. [AEON Malaysia—Men's Double Scrub Acne Solution](https://myaeon2go.com/product/42896/men's-double-scrub-acne-solution) dipertahankan sebagai pembanding.  
**Status:** `VALIDATED_ID_FULL_INCI`; formula Indonesia sama secara material dengan kandidat yang sebelumnya diberikan.

**INCI pendukung:** Water, Glycerin, Palmitic Acid, Stearic Acid, Laureth-6 Carboxylic Acid, Propylene Glycol, Myristic Acid, Potassium Hydroxide, Sorbitol, Lauric Acid, PEG-150, Cellulose, Zea Mays (Corn) Starch, Fragrance, Decyl Glucoside, Disodium EDTA, Menthol, Magnesium Potassium Fluorosilicate, Sodium MA/Vinyl Alcohol Copolymer, PEG-45M, Magnesium/Potassium/Silicon/Fluoride/Hydroxide/Oxide, Melaleuca Alternifolia (Tea Tree) Leaf Oil, Camellia Sinensis Leaf Extract, BHT, Caprylic/Capric Triglyceride, Arginine, Bentonite, Betaine, o-Cymen-5-ol, CI 19140, CI 42090, CI 77491.

**Catatan audit:** sumber Malaysia memakai nama **Acne Solution**, sedangkan katalog Indonesia dan retailer Farmaku memakai **Acne Bacterior**. Untuk dataset Indonesia, gunakan nama Acne Bacterior dan BPOM NA18191200283.

## 8. Apa yang masih diperlukan

### 8.1 Status setelah pencarian web

- Seluruh 37 record telah dikonfirmasi; M02-M04 ditambahkan sebagai lini MS Glow For Men terbaru dan M01 dipertahankan sebagai riwayat nonaktif.
- Seluruh 37 record memiliki full INCI dari sumber Indonesia. B01 dan B03N diselesaikan melalui foto belakang kemasan Indonesia; M02-M04 memakai transkripsi retailer Indonesia yang dicocokkan dengan identitas dan BPOM resmi.
- B03 tetap dikunci sebagai formula Acne Skincare lama berdasarkan BPOM `NA18211200323`; B03N dikunci sebagai Acne Bright Care formula baru berdasarkan label BPOM `NA18241202965`.
- Audit Watsons tetap disimpan sebagai riwayat pembanding, bukan satu-satunya dasar validasi.
- K01 dan K02 harus dipisahkan menurut ukuran karena formula 50 ml dan 100 ml yang tampil di Watsons tidak sama. Katalog kini memuat 37 record produk dan sekurang-kurangnya 40 record SKU/formula.
- G01 memiliki dua kandidat formula; formula Watsons/Blibli Indonesia menjadi kandidat utama, sedangkan formula INCIdecoder disimpan sebagai versi lintas pasar.
- M01, B04, B07, dan B08 tidak lagi berstatus “menunggu label” karena full INCI Indonesia telah ditemukan.
- B05 dan B06 memiliki full INCI Indonesia, tetapi harus disimpan sebagai formula berversi karena nama/klaim resminya tidak sepenuhnya konsisten dengan daftar retailer.

### 8.2 Foto: fungsi dan kebutuhan sebenarnya

Peneliti **tidak perlu memotret ingredients seluruh 37 record produk**. Untuk tahap penyusunan basis data, audit web di dokumen ini sudah cukup. Foto produk mempunyai dua fungsi berbeda:

1. **Foto depan produk** dipakai untuk tampilan alternatif pada aplikasi/kuesioner agar responden mengenali varian. Foto ini tidak menambah validitas formula.
2. **Foto label ingredients** hanya menjadi bukti primer untuk menyelesaikan formula yang belum lengkap atau konflik versi.

Jika penelitian akan mengunci formula produk yang benar-benar dibeli, prioritas foto label tinggal lima varian:

- G01 AcnoFight Scrub in Foam, B05 Bright Energy, dan B06 Cool Oil Clear—karena masih ada konflik versi/nama/klaim.

Untuk kelimanya, satu rangkaian foto cukup memuat nama depan, ukuran, ingredients, barcode, nomor BPOM, dan batch. Jika varian tersebut tidak masuk alternatif final, foto label tidak perlu dicari sekarang. Untuk record lain, foto depan dapat diambil dari halaman resmi/official store sesuai izin penggunaan, sedangkan ingredients tidak perlu diketik ulang.

### 8.3 Data yang masih harus dicatat per SKU

| Data | Alasan |
|---|---|
| Nama persis pada kemasan | Membedakan Bright/White Energy dan Acne Bacterior/Acne Solution |
| Barcode/EAN dan ukuran | Menghubungkan formula dengan SKU yang tepat |
| Nomor notifikasi BPOM | Memastikan produk dan pemohon terdaftar |
| Produsen/distributor dan negara pasar | Menghindari pencampuran formula Indonesia–Malaysia |
| Batch, tanggal produksi, dan kedaluwarsa | Menentukan versi formula |
| Petunjuk pemakaian dan peringatan | Dibutuhkan untuk konteks keamanan dan frekuensi penggunaan |
| Tempat, URL, dan waktu pengambilan data | Menjaga jejak audit |
| Harga normal/promo pada tanggal observasi | Harga bersifat berubah, bukan atribut tetap produk |
| pH dan konsentrasi bahan kunci | Simpan `UNKNOWN` kecuali dinyatakan oleh sumber yang dapat diaudit |

### 8.4 Koreksi istilah “bahan aktif”

Daftar yang ditemukan adalah **daftar ingredients/INCI lengkap**, bukan seluruhnya bahan aktif. Untuk analisis, pisahkan setidaknya menjadi:

1. surfaktan/pembersih;
2. humektan dan emolien;
3. bahan unggulan/pendukung klaim, misalnya Niacinamide, Tea Tree Oil, Charcoal, atau ekstrak;
4. scrub dan absorben, misalnya Cellulose, Corn Starch, Kaolin, Bentonite, atau Hydrated Silica;
5. bahan yang relevan untuk sensitivitas, misalnya fragrance, menthol, essential oil, dan alcohol;
6. pengawet, chelator, pengatur pH, serta pewarna.

Urutan INCI tidak memberi konsentrasi eksak, dan keberadaan suatu bahan tidak otomatis membuktikan efektivitas klinis. Karena semua produk adalah **rinse-off**, lama kontak perlu dipertimbangkan secara konservatif dalam scoring.

## 9. Data harga

Harga tidak dimasukkan sebagai nilai tetap di katalog ini. Simpan sebagai observasi:

| Field | Contoh |
|---|---|
| `sku_id` | G07-100ML |
| `amount` | 45000 |
| `currency` | IDR |
| `seller` | Official store/retailer |
| `source_url` | URL produk |
| `observed_at` | 2026-09-14T14:00:00+07:00 |
| `promo` | true/false |
| `normal_price` | nullable |

Harga promo dan ongkir tidak boleh dibandingkan sebagai harga normal tanpa definisi yang konsisten.

## 10. Implikasi ke sistem

- Formula `VERIFIED_OFFICIAL_INCI` dan `VALIDATED_ID_FULL_INCI` dapat dipakai dalam pemetaan ingredients dengan URL, tanggal akses, dan versi sumber.
- Formula `VALIDATED_ID_VERSIONED` dapat dipakai hanya sebagai record versi yang jelas; jangan menggabungkan dua daftar formula.
- B01 dan B03N sudah boleh diberi skor ingredients final berdasarkan formula label Indonesia. B03 hanya digunakan sebagai Acne Skincare lama sesuai nomor BPOM dan tidak boleh dinamai Acne Bright Care.
- M01, B04, B07, dan B08 sudah memiliki full INCI dari sumber Indonesia dan tidak lagi harus dikeluarkan hanya karena tidak ditemukan di Watsons.
- Simpan formula yang berbeda sebagai record berversi; jangan menggabungkan dua daftar atau menimpa formula lama.
- Jangan memaksa lima merek semuanya muncul di hasil bila sebagian formula belum lengkap.
- Setelah label lengkap, satu varian boleh memiliki beberapa SKU ukuran tetapi satu formula aktif yang sama hanya jika daftar komposisinya terbukti sama.
- Setiap claim-to-score harus memiliki rule, konteks, alasan, sumber, dan versi.
- Kandungan menthol, fragrance, essential oil, scrub, banyak eksfolian, serta surfaktan perlu dinilai bersama sensitivitas/barrier; keberadaannya bukan vonis otomatis untuk semua pengguna.
