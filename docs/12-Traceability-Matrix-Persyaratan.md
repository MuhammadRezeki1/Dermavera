# Traceability Matrix Persyaratan

| Tujuan/argumen | Kebutuhan | Data/aturan | Implementasi | Pengujian | Output Bab IV/V |
|---|---|---|---|---|---|
| Membantu memilih facial wash | FR-03-FR-10 | Profil + produk + C1-C6 | Consultation + RecommendationService | UT-02-04, feature ranking | Desain proses dan hasil ranking |
| Scope pria 18-30 | FR-02 | Age group 18-24/25-30 | Validasi input + microcopy | Boundary 17,18,24,25,30,31 | Jelaskan scope, bukan kewajiban biologis |
| Keamanan tidak dikompensasi | FR-05-FR-07 | Red flag + hard constraint | SafetyGateService | UT-05-06 | Hasil safety gate dan kasus uji |
| Formula per varian | FR-12-FR-14 | Variant, SKU, formula_version, INCI | ERD dan Filament | CRUD/version test | Tabel alternatif yang benar |
| Banyak bahan bukan otomatis baik | FR-14 | Rule formulasi holistik | CriterionScoringService | Rule conflict tests | Analisis kontribusi C3 |
| pH tidak boleh ditebak | FR-13 | nullable pH + evidence | ProductEvidenceService | Missing pH test | Keterbatasan data |
| Konsentrasi beragam/tidak ada | FR-13 | concentration_status | FormulaIngredient | Declared/unknown test | Pembahasan konservatif |
| Harga dinamis | FR-16 | price_observation | UnitPriceService | Fresh/stale price test | Harga bertanggal |
| Validasi dokter | FR-15, FR-20 | expert validation summary | WeightSet + rules | Bobot/aturan test | Bobot 5,5,5,3,3,5 dan batasan |
| Transparansi | FR-10, FR-18 | X,R,W,V,evidence | Explanation + export | Snapshot test | Lampiran hitung dan sumber |
| Reproducibility | NFR-03 | dataset/version/hash | DatasetVersionService | Version immutability | Versi sistem/data |
| Privasi validator/pengguna | NFR-02 | consent, permission | Policy + private storage | IDOR/export test | Etika dan keterbatasan |
| Interaksi modern namun aksesibel | FR-21-FR-23, NFR-07, NFR-13-NFR-16 | State Livewire + motion tokens | Anime.js scope/timeline/layout | reduced motion, keyboard, cleanup, fallback | Rancangan UI dan bukti uji usability |

## Pembaruan traceability v3.3

| Tujuan/argumen | Kebutuhan | Data/aturan | Implementasi | Pengujian | Output Bab IV/V |
|---|---|---|---|---|---|
| Menghindari salah tafsir nilai 1,0000 | FR-24, FR-29 | `eligible_count`, `score_mode` | RecommendationService + result view | UAT-10, UAT-11, REG-01 | Interpretasi skor relatif |
| Tetap transparan terhadap kandidat yang tidak diranking | FR-25, FR-28 | status, reason codes, image | RecommendationResult + result page | UAT-13, UAT-14, REG-02, REG-04 | Tabel kandidat non-ranking |
| Menjaga harga tidak kedaluwarsa | FR-26, FR-27 | observed_at, source, ranking_eligible, max age | PriceObservationResource + DatasetVersionService | UAT-15, UAT-16, REG-03 | Audit pembaruan harga |
| Menilai keluhan kusam secara konsisten | FR-03, FR-27 | primary/secondary concern + ingredient evidence | CriterionScoringService | UAT-08, UAT-09, REG-05 | Pembahasan modifier keluhan |
| Menyediakan akses demo yang benar | FR-30, NFR-11 | HTTPS forwarded scheme | trusted proxy middleware + Docker/Cloudflare | UAT-21, REG-06 | Bukti deployment lokal/tunnel |
| Mengizinkan konsultasi tanpa friksi akun | FR-31, FR-32, NFR-17 | guest session, consent, UUID | ConsultationWizard + ResultPage | UAT-22, REG-07 | Alur guest-first |
| Menyimpan hasil guest ke akun | FR-33, NFR-18 | claim UUID, user_id, expires_at | ConsultationClaimController | UAT-23, REG-08 | Lifecycle akun dan riwayat |
| Menyajikan riwayat ringkas | FR-34 | result #1, brand, foto, price, input snapshot | HistoryPage + history card | UAT-24 | UI riwayat |

Status `secondary_concerns` harus dibuktikan lewat UAT-09; implementasi scoring sudah menyediakan modifier pada `criteria-1.3.0`. Status guest/claim juga harus diuji terhadap IDOR dan konsistensi snapshot.

## Checklist hubungan Bab I-Bab V

| Bab | Artefak pendukung |
|---|---|
| Bab I | masalah, scope 18-30, tujuan, batasan, risiko |
| Bab II | definisi SPK/SAW, facial wash, jenis kulit, formulasi, penelitian terkait |
| Bab III | sumber data, validator, instrumen, kriteria, langkah SAW, pengujian |
| Bab IV | kebutuhan, arsitektur, use case, ERD, UI, knowledge base, implementasi, contoh hitung |
| Bab V | hasil uji, interpretasi ranking, validasi ahli, keterbatasan, saran |

## Klaim yang tidak boleh ditulis

- “Usia di bawah 18 atau di atas 30 tidak perlu memakai facial wash.”
- “Produk peringkat pertama pasti cocok.”
- “Kandungan salicylic acid berarti pasti efektif untuk semua jerawat.”
- “pH produk adalah X” bila produsen/label tidak menyatakannya.
- “Konsentrasi tinggi” hanya dari urutan INCI.
- “Para dokter sepakat” bila hanya satu validator.
- “Sistem mendiagnosis jenis/penyakit kulit.”
