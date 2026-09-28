<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ResultExportController extends Controller
{
    public function csv(Consultation $consultation): StreamedResponse
    {
        $this->authorizeAccess($consultation);
        $run = $consultation->runs()->with(['results.variant.brand', 'results.sku', 'results.price.source'])->latest('id')->first();
        $dataset = $consultation->datasetVersion()->first();

        return response()->streamDownload(function () use ($consultation, $run, $dataset) {
            $out = fopen('php://output', 'w');
            if (! is_resource($out)) {
                throw new RuntimeException('Tidak dapat membuka stream ekspor.');
            }
            fputcsv($out, ['consultation_uuid', 'dataset_version', 'algorithm_version', 'rank', 'brand', 'variant', 'size', 'observed_price', 'observed_at', 'price_source', 'C1', 'C2', 'C3', 'C4_per_100', 'C5', 'C6', 'final_score']);
            foreach ($run?->results->where('eligibility_status', 'eligible') ?? [] as $r) {
                fputcsv($out, [$consultation->uuid, $dataset?->version, $consultation->algorithm_version, $r->rank, $r->variant->brand->name, $r->variant->name, $r->sku?->size_value.' '.$r->sku?->size_unit, $r->price?->amount, $r->price?->observed_at?->toIso8601String(), $r->price?->source?->url, ...array_values($r->raw_scores), $r->final_score]);
            }
            fclose($out);
        }, "dermavera-{$consultation->uuid}.csv", ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function pdf(Consultation $consultation): Response
    {
        $this->authorizeAccess($consultation);
        $consultation->load(['safetyAssessment', 'datasetVersion', 'runs.results.variant.brand', 'runs.results.sku', 'runs.results.price.source']);

        return Pdf::loadView('exports.result-pdf', ['consultation' => $consultation, 'run' => $consultation->runs->last()])->download("dermavera-{$consultation->uuid}.pdf");
    }

    private function authorizeAccess(Consultation $consultation): void
    {
        if ($consultation->user_id) {
            $user = auth()->user();
            abort_unless($user instanceof User && ($user->id === $consultation->user_id || $user->is_admin), 403);
        } else {
            abort_unless(in_array($consultation->uuid, session('guest_consultations', []), true), 403);
        }
    }
}
