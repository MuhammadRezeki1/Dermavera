<?php

namespace Tests\Feature;

use App\Livewire\ConsultationWizard;
use Livewire\Livewire;
use Tests\TestCase;

class ConsultationValidationTest extends TestCase
{
    public function test_age_boundaries_and_required_profile_fields(): void
    {
        $this->withSession(['consultation_draft' => ['consent' => true]]);
        Livewire::test(ConsultationWizard::class, ['step' => 'profile'])->set('age', 17)->set('primaryComplaint', 'berminyak')->set('skinType', 'normal')->call('saveProfile')->assertHasErrors(['age']);
        Livewire::test(ConsultationWizard::class, ['step' => 'profile'])->set('age', 31)->set('primaryComplaint', 'berminyak')->set('skinType', 'normal')->call('saveProfile')->assertHasErrors(['age']);
    }

    public function test_all_safety_questions_are_required(): void
    {
        $this->withSession(['consultation_draft' => ['consent' => true]]);
        Livewire::test(ConsultationWizard::class, ['step' => 'safety'])->call('saveSafety')->assertHasErrors(['sensitive', 'barrierImpaired', 'acneTherapy']);
    }
}
