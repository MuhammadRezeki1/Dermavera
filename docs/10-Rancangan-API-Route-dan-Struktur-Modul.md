# Rancangan API, Route, dan Struktur Modul

## 1. Web routes

```php
Route::get('/', HomePage::class)->name('home');
Route::get('/metode', MethodPage::class)->name('method');
Route::get('/katalog', CatalogPage::class)->name('catalog');
Route::get('/produk/{variant:slug}', ProductDetailPage::class)->name('products.show');

Route::prefix('konsultasi')->name('consultation.')->middleware('throttle:consultation')->group(function () {
    Route::get('/mulai', ConsultationWizard::class)->defaults('step', 'consent')->name('consent');
    Route::get('/profil', ConsultationWizard::class)->defaults('step', 'profile')->name('profile');
    Route::get('/keamanan', ConsultationWizard::class)->defaults('step', 'safety')->name('safety');
    Route::get('/preferensi', ConsultationWizard::class)->defaults('step', 'preference')->name('preference');
});

Route::get('/hasil/{consultation:uuid}', ResultPage::class)->name('results.show');
Route::get('/hasil/{consultation:uuid}/simpan', [ConsultationClaimController::class, 'start'])->name('results.claim.start');
Route::middleware('auth')->get('/hasil/{consultation:uuid}/simpan/selesai', [ConsultationClaimController::class, 'claim'])->name('results.claim.complete');
Route::get('/hasil/{consultation:uuid}/ekspor.csv', [ResultExportController::class, 'csv'])->name('results.csv');
Route::get('/hasil/{consultation:uuid}/ekspor.pdf', [ResultExportController::class, 'pdf'])->name('results.pdf');
```

Route konsultasi memakai throttle. `/dashboard` dan `/riwayat` hanya tersedia bagi pengguna terautentikasi; halaman hasil dan ekspor menggunakan UUID konsultasi sesuai policy/aturan akses yang diterapkan aplikasi. Route claim memverifikasi UUID terhadap session guest sebelum mengaitkan hasil ke akun.

Panel Filament gunakan prefix `/admin` dan middleware autentikasi + authorization.

## 2. API opsional

| Method | Endpoint | Fungsi |
|---|---|---|
| GET | `/api/v1/catalog` | Katalog produk aktif |
| GET | `/api/v1/catalog/{slug}` | Detail varian dan evidence |
| POST | `/api/v1/consultations` | Membuat konsultasi |
| POST | `/api/v1/consultations/{uuid}/evaluate` | Menjalankan safety + SAW satu kali |
| GET | `/api/v1/consultations/{uuid}/result` | Membaca hasil dengan signed/token access |

API tidak wajib untuk Livewire MVP. Buat bila ada aplikasi klien lain atau kebutuhan pengujian terpisah.

## 3. Struktur kode

```text
app/
  Domain/Recommendation/
    DTO/
    Enums/
    Rules/
    ValueObjects/
  Services/
    SafetyGateService.php
    EligibilityService.php
    CriterionScoringService.php
    SawCalculator.php
    RecommendationService.php
    ExplanationService.php
    DatasetVersionService.php
  Http/Controllers/
    ConsultationClaimController.php
  Livewire/
    Consultation/
    Catalog/
    Results/
  Filament/Resources/
  Models/
  Policies/
  Jobs/
  Actions/
resources/
  js/
    app.js
    animations/
      boot.js
      home.js
      consultation.js
      results.js
      catalog.js
      feedback.js
      motion-preferences.js
database/
  migrations/
  seeders/
tests/
  Unit/
  Feature/
```

## 4. Kontrak service

```php
interface SawCalculatorContract
{
    public function calculate(
        DecisionMatrix $matrix,
        WeightSet $weights
    ): SawResult;
}

interface SafetyGateContract
{
    public function assess(UserProfile $profile): SafetyDecision;
}
```

## 5. Contoh respons hasil

```json
{
  "consultation_id": "uuid",
  "dataset_version": "1.0.0",
  "algorithm_version": "saw-1.1.0",
  "safety": {"outcome": "eligible", "warnings": []},
  "ranking": [
    {
      "rank": 1,
      "variant_id": 10,
      "score": "0.874359",
      "criteria": {"C1": 5, "C2": 4, "C3": 4, "C4": 45000, "C5": 4, "C6": 5},
      "explanation_codes": ["matches_primary_complaint", "formula_verified"]
    }
  ]
}
```

## 6. Idempotensi dan konsistensi

- Evaluasi final memiliki idempotency key.
- Setelah selesai, hasil tidak dihitung ulang dengan dataset baru.
- Re-evaluasi membuat `recommendation_run` baru.
- Gunakan `numeric`, bukan binary float, pada database dan kalkulator.

## 7. Filament resources

`BrandResource`, `ProductVariantResource`, `FormulaVersionResource`, `IngredientResource`, `SourceResource`, `PriceObservationResource`, `RuleResource`, `WeightSetResource`, `DatasetVersionResource`, `ConsultationResource` (read-only sensitif), dan `AuditLogResource` (read-only).

## 8. Kontrak event antarmuka

| Event | Payload minimum | Pemakai |
|---|---|---|
| `consultation-step-changed` | `from`, `to`, `progress` | Transisi pertanyaan dan progress bar |
| `consultation-validation-failed` | `questionId` | Shake ringan dan fokus ke error |
| `recommendation-processing` | `stage` | Status loading faktual |
| `recommendation-ready` | `resultId`, `scores` | Kartu ranking, counter, dan criterion bar |
| `catalog-updated` | `visibleIds` | Transisi layout katalog |

Event tidak boleh membawa bobot rahasia atau meminta browser menghitung ulang ranking. Payload skor berasal dari hasil yang telah dipersistenkan.

## 9. Integrasi lifecycle Livewire

- Inisialisasi animasi setelah elemen komponen tersedia.
- Simpan scope per root komponen, bukan sebagai selector global.
- Jalankan cleanup/revert sebelum komponen diganti atau halaman dinavigasikan.
- Jangan mendaftarkan listener berulang pada setiap render.
- Setelah animasi selesai, hapus style sementara yang tidak diperlukan agar state Tailwind/Flux tetap menjadi baseline.

## 10. Kontrak hasil v3.3

Respons hasil/persisted snapshot harus mendukung bentuk berikut:

```json
{
  "candidate_summary": {
    "eligible": 1,
    "excluded": 32,
    "insufficient_evidence": 5
  },
  "score_mode": "relative_single_candidate",
  "results": {
    "ranked": [],
    "non_ranked": [
      {"status": "excluded", "reason_codes": ["HC-02"], "image": "..."},
      {"status": "insufficient_evidence", "reason_codes": ["stale_price"], "image": "..."}
    ]
  }
}
```

Contoh jumlah di atas adalah contoh satu run, bukan jumlah tetap untuk semua input. UI mengambil jumlah aktual dari run yang sedang dibaca. `relative_single_candidate` hanya dipakai ketika hasil eligible berjumlah satu; untuk minimal dua kandidat gunakan `comparative_saw`.

## 11. Modul admin dan data freshness

`PriceObservationResource` menerima sumber, tanggal observasi, `is_available`, dan `ranking_eligible`. Admin tidak menghapus observasi lama untuk mengganti harga. Setelah perubahan yang memengaruhi evaluasi, `DatasetVersionResource` dipakai untuk membuat atau mengaktifkan snapshot baru. `DatasetVersionService` menolak evaluasi jika hash dataset aktif tidak lagi cocok dengan data saat ini.

Detail pengujian route, ekspor, snapshot, dan status non-ranking terdapat pada `docs/19-UAT-Sistem-Rekomendasi-Facial-Wash.md`.
