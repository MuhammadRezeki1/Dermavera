<?php

use App\Http\Controllers\ResultExportController;
use App\Http\Controllers\ConsultationClaimController;
use App\Livewire\AboutPage;
use App\Livewire\CatalogPage;
use App\Livewire\ConsultationWizard;
use App\Livewire\HistoryPage;
use App\Livewire\HomePage;
use App\Livewire\MethodPage;
use App\Livewire\ProductDetailPage;
use App\Livewire\ResultPage;
use Illuminate\Support\Facades\Route;

Route::get('/', HomePage::class)->name('home');
Route::get('/metode', MethodPage::class)->name('method');
Route::get('/tentang', AboutPage::class)->name('about');
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

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', HistoryPage::class)->name('dashboard');
    Route::get('/riwayat', HistoryPage::class)->name('history');
});

require __DIR__.'/settings.php';
