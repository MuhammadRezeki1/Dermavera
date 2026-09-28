<?php

namespace App\Livewire;

use App\Services\RecommendationService;
use Illuminate\View\View;
use Livewire\Component;

class ConsultationWizard extends Component
{
    public string $step = 'consent';

    public bool $consent = false;

    public ?int $age = null;

    public string $primaryComplaint = '';

    /** @var list<string> */
    public array $secondaryConcerns = [];

    public string $skinType = '';

    public ?bool $sensitive = null;

    public ?bool $barrierImpaired = null;

    public ?bool $acneTherapy = null;

    public string $allergiesText = '';

    /** @var list<string> */
    public array $redFlags = [];

    public ?int $minPrice = null;

    public ?int $maxPrice = null;

    public string $packagingPreference = '';

    public function mount(string $step = 'consent'): void
    {
        $this->step = $step;
        $draft = session('consultation_draft', []);
        foreach (['consent', 'age', 'secondaryConcerns', 'sensitive', 'barrierImpaired', 'acneTherapy', 'redFlags', 'minPrice', 'maxPrice'] as $property) {
            if (array_key_exists($property, $draft)) {
                $this->{$property} = $draft[$property];
            }
        }
        foreach (['primaryComplaint', 'skinType', 'allergiesText', 'packagingPreference'] as $property) {
            if (isset($draft[$property])) {
                $this->{$property} = $draft[$property];
            }
        }
        if ($step !== 'consent' && ! ($draft['consent'] ?? false)) {
            $this->redirectRoute('consultation.consent');
        }
    }

    public function accept(): void
    {
        $this->validate(['consent' => ['accepted']]);
        $this->persist(['consent' => true, 'consent_at' => now()->toIso8601String()]);
        $this->redirectRoute('consultation.profile', navigate: true);
    }

    public function saveProfile(): void
    {
        $this->validate(['age' => ['required', 'integer', 'between:18,30'], 'primaryComplaint' => ['required', 'in:berminyak,komedo,jerawat_ringan,kusam,kering'], 'skinType' => ['required', 'in:normal,berminyak,kering,kombinasi'], 'secondaryConcerns' => ['array']]);
        $this->persist(['age' => $this->age, 'primaryComplaint' => $this->primaryComplaint, 'secondaryConcerns' => $this->secondaryConcerns, 'skinType' => $this->skinType]);
        $this->redirectRoute('consultation.safety', navigate: true);
    }

    public function saveSafety(): void
    {
        $this->validate(['sensitive' => ['required', 'boolean'], 'barrierImpaired' => ['required', 'boolean'], 'acneTherapy' => ['required', 'boolean'], 'allergiesText' => ['nullable', 'string', 'max:1000'], 'redFlags' => ['array'], 'redFlags.*' => ['in:nodul_kistik,jerawat_berat_memburuk,jaringan_parut,luka_infeksi_luas,bengkak_alergi_berat,nyeri_terbakar_menetap,penyakit_kulit_dalam_pengobatan']]);
        $this->persist(['sensitive' => (bool) $this->sensitive, 'barrierImpaired' => (bool) $this->barrierImpaired, 'acneTherapy' => (bool) $this->acneTherapy, 'allergiesText' => $this->allergiesText, 'redFlags' => $this->redFlags]);
        $this->redirectRoute('consultation.preference', navigate: true);
    }

    public function finish(RecommendationService $service): void
    {
        $this->validate(['minPrice' => ['nullable', 'integer', 'min:0'], 'maxPrice' => ['nullable', 'integer', 'min:0', 'gte:minPrice'], 'packagingPreference' => ['nullable', 'in:tube,pump,bottle']]);
        $this->persist(['minPrice' => $this->minPrice, 'maxPrice' => $this->maxPrice, 'packagingPreference' => $this->packagingPreference]);
        $draft = session('consultation_draft');
        $profile = ['age' => $draft['age'], 'primary_complaint' => $draft['primaryComplaint'], 'secondary_concerns' => $draft['secondaryConcerns'], 'skin_type' => $draft['skinType'], 'sensitive' => (bool) $draft['sensitive'], 'barrier_impaired' => (bool) $draft['barrierImpaired'], 'acne_therapy' => (bool) $draft['acneTherapy'], 'allergies' => array_values(array_filter(array_map('trim', preg_split('/[,;\n]+/', $draft['allergiesText'] ?? '') ?: []))), 'red_flags' => $draft['redFlags'], 'min_price' => $draft['minPrice'], 'max_price' => $draft['maxPrice'], 'packaging_preference' => $draft['packagingPreference'], 'consent_at' => $draft['consent_at']];
        $consultation = $service->evaluate($profile, auth()->user()?->id);
        if (! $consultation->user_id) {
            session()->push('guest_consultations', $consultation->uuid);
        }
        session()->forget('consultation_draft');
        $this->redirectRoute('results.show', $consultation, navigate: true);
    }

    /** @param array<string, mixed> $values */
    private function persist(array $values): void
    {
        session(['consultation_draft' => array_merge(session('consultation_draft', []), $values)]);
    }

    public function render(): View
    {
        return view('livewire.consultation-wizard')->layout('layouts.public', ['title' => 'Konsultasi — '.ucfirst($this->step)]);
    }
}
