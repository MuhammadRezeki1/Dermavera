<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\View\View;
use Livewire\Component;

class HistoryPage extends Component
{
    public function render(): View
    {
        $user = auth()->user();
        abort_unless($user instanceof User, 403);

        $consultations = $user->consultations()
            ->with([
                'datasetVersion',
                'runs.results' => fn ($query) => $query->where('eligibility_status', 'eligible')->orderBy('rank'),
                'runs.results.variant.brand',
                'runs.results.sku',
                'runs.results.price',
            ])
            ->latest()
            ->paginate(10);

        return view('livewire.history-page', compact('consultations'))->layout('layouts.public', ['title' => 'Riwayat Rekomendasi']);
    }
}
