<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class ConsultationClaimController extends Controller
{
    public function start(Consultation $consultation): RedirectResponse
    {
        abort_unless($this->isGuestConsultation($consultation), 403);

        if (auth()->user() instanceof User) {
            return $this->claim($consultation);
        }

        session(['consultation_claim_uuid' => $consultation->uuid]);

        return redirect()->guest(route('login'));
    }

    public function claim(Consultation $consultation): RedirectResponse
    {
        $user = auth()->user();
        abort_unless($user instanceof User, 403);
        abort_unless(session('consultation_claim_uuid') === $consultation->uuid, 403);
        abort_if($consultation->user_id && $consultation->user_id !== $user->id, 403);

        if (! $consultation->user_id) {
            $consultation->update(['user_id' => $user->id, 'expires_at' => null]);
            $guestConsultations = collect(session('guest_consultations', []))
                ->reject(fn ($uuid) => $uuid === $consultation->uuid)
                ->values()
                ->all();
            session(['guest_consultations' => $guestConsultations]);
        }

        session()->forget('consultation_claim_uuid');

        return redirect()->route('results.show', $consultation);
    }

    private function isGuestConsultation(Consultation $consultation): bool
    {
        return ! $consultation->user_id
            && in_array($consultation->uuid, session('guest_consultations', []), true);
    }
}
