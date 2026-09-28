<?php

namespace App\Livewire;

use App\Models\Criterion;
use App\Models\DatasetVersion;
use App\Models\WeightSet;
use App\Services\CriterionScoringService;
use Illuminate\View\View;
use Livewire\Component;

class MethodPage extends Component
{
    public function render(): View
    {
        return view('livewire.method-page', [
            'criteria' => Criterion::orderBy('code')->get(),
            'weightSet' => WeightSet::with('weights.criterion')->where('is_active', true)->first(),
            'dataset' => DatasetVersion::where('status', 'active')->first(),
            'algorithmVersion' => (string) config('dermavera.algorithm_version'),
            'scoringVersion' => CriterionScoringService::VERSION,
        ])->layout('layouts.public', ['title' => 'Metode Safety Gate dan SAW']);
    }
}
