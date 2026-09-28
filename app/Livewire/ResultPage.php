<?php

namespace App\Livewire;

use App\Models\Consultation;
use App\Models\User;
use App\Services\ExplanationService;
use Illuminate\View\View;
use Livewire\Component;

class ResultPage extends Component
{
    public Consultation $consultation;

    public function mount(Consultation $consultation): void
    {
        $this->authorizeAccess($consultation);
        $this->consultation = $consultation;
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

    public function render(ExplanationService $explanations): View
    {
        $this->consultation->load(['safetyAssessment', 'datasetVersion', 'runs.results' => fn ($q) => $q->orderByRaw('rank is null, rank'), 'runs.results.variant.brand', 'runs.results.sku', 'runs.results.price.source', 'runs.results.formula.source']);
        $run = $this->consultation->runs->last();
        // Keep every product that passed the safety gate available to the view.
        // The first three are still the editorial "Top 3", but they should not
        // hide other eligible candidates from the user.
        $eligible = $run?->results->where('eligibility_status', 'eligible')->values() ?? collect();
        $eligible = $eligible->map(function ($result) use ($eligible) {
            $result->setAttribute('tie_count', $eligible->filter(fn ($candidate) => abs((float) $candidate->final_score - (float) $result->final_score) <= 0.0001)->count());

            return $result;
        });
        $top = $eligible->take(3);

        return view('livewire.result-page', ['run' => $run, 'top' => $top, 'eligible' => $eligible, 'explanations' => $explanations])->layout('layouts.public', ['title' => 'Hasil Rekomendasi']);
    }
}
