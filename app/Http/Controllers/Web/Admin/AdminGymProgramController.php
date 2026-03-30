<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gym;
use App\Models\GymProgram;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminGymProgramController extends Controller
{
    private array $rules = [
        'name'             => ['required', 'string', 'max:120'],
        'description'      => ['nullable', 'string', 'max:500'],
        'schedule'         => ['nullable', 'array'],
        'duration_minutes' => ['required', 'integer', 'min:15', 'max:300'],
        'max_spots'        => ['nullable', 'integer', 'min:1', 'max:500'],
        'is_active'        => ['nullable', 'boolean'],
    ];

    public function store(Request $request, Gym $gym): RedirectResponse
    {
        $data = $request->validate($this->rules);
        $data['is_active'] = (bool) ($data['is_active'] ?? true);

        $gym->programs()->create($data);

        return back()->with('success', 'Programme ajouté.');
    }

    public function update(Request $request, Gym $gym, GymProgram $program): RedirectResponse
    {
        abort_unless($program->gym_id === $gym->id, 403);

        $data = $request->validate($this->rules);
        // Checkbox HTML : absente du POST si décochée — on lit explicitement la valeur
        $data['is_active'] = $request->boolean('is_active');

        $program->update($data);

        return back()->with('success', 'Programme mis à jour.');
    }

    public function destroy(Gym $gym, GymProgram $program): RedirectResponse
    {
        abort_unless($program->gym_id === $gym->id, 403);

        $program->delete();

        return back()->with('success', 'Programme supprimé.');
    }
}
