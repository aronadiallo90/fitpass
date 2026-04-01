<?php

namespace App\Http\Controllers\Web\Gym;

use App\Http\Controllers\Controller;
use App\Models\GymProgram;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class GymProgramOwnerController extends Controller
{
    private array $rules = [
        'name'             => ['required', 'string', 'max:120'],
        'description'      => ['nullable', 'string', 'max:500'],
        'duration_minutes' => ['required', 'integer', 'min:15', 'max:300'],
        'max_spots'        => ['nullable', 'integer', 'min:1', 'max:500'],
        'is_active'        => ['nullable', 'boolean'],
    ];

    private function ownerGymId(): string
    {
        $gym = auth()->user()->gym;
        abort_if(is_null($gym), 403, 'Aucune salle associée à votre compte.');
        return $gym->id;
    }

    public function store(Request $request): RedirectResponse
    {
        $gymId = $this->ownerGymId();

        $data = $request->validate($this->rules);
        $data['is_active'] = (bool) ($data['is_active'] ?? true);
        $data['gym_id']    = $gymId;

        GymProgram::create($data);

        return redirect()->route('gym.profil', ['tab' => 'programmes'])
            ->with('success', 'Programme ajouté.');
    }

    public function update(Request $request, GymProgram $program): RedirectResponse
    {
        abort_unless($program->gym_id === $this->ownerGymId(), 403);

        $data = $request->validate($this->rules);
        $data['is_active'] = $request->boolean('is_active');

        $program->update($data);

        return redirect()->route('gym.profil', ['tab' => 'programmes'])
            ->with('success', 'Programme mis à jour.');
    }

    public function destroy(GymProgram $program): RedirectResponse
    {
        abort_unless($program->gym_id === $this->ownerGymId(), 403);

        $program->delete();

        return redirect()->route('gym.profil', ['tab' => 'programmes'])
            ->with('success', 'Programme supprimé.');
    }
}
