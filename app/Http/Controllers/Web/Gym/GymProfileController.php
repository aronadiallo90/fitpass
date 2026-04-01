<?php

namespace App\Http\Controllers\Web\Gym;

use App\Http\Controllers\Controller;
use App\Models\GymActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GymProfileController extends Controller
{
    private function ownerGym(): \App\Models\Gym
    {
        $gym = auth()->user()->gym;
        abort_if(is_null($gym), 403, 'Aucune salle associée à votre compte.');
        return $gym;
    }

    public function edit(): View
    {
        $gym = $this->ownerGym();
        $gym->load(['gymActivities', 'programs' => fn($q) => $q->orderBy('name')]);

        $allActivities = GymActivity::orderBy('name')->get();

        return view('gym.profile', compact('gym', 'allActivities'));
    }

    public function updateInfo(Request $request): RedirectResponse
    {
        $gym = $this->ownerGym();

        $data = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'address'        => ['required', 'string', 'max:500'],
            'phone'          => ['nullable', 'string', 'max:30'],
            'phone_whatsapp' => ['nullable', 'string', 'max:30'],
            'description'    => ['nullable', 'string', 'max:2000'],
        ]);

        $gym->update($data);

        return redirect()->route('gym.profil', ['tab' => 'infos'])
            ->with('success', 'Informations mises à jour.');
    }

    public function updateHours(Request $request): RedirectResponse
    {
        $gym = $this->ownerGym();

        $request->validate([
            'opening_hours' => ['nullable', 'string'],
        ]);

        $openingHours = null;
        if ($request->filled('opening_hours')) {
            $decoded = json_decode($request->input('opening_hours'), true);
            $openingHours = is_array($decoded) ? $decoded : null;
        }

        $gym->update(['opening_hours' => $openingHours]);

        return redirect()->route('gym.profil', ['tab' => 'horaires'])
            ->with('success', 'Horaires mis à jour.');
    }

    public function updateActivities(Request $request): RedirectResponse
    {
        $gym = $this->ownerGym();

        $request->validate([
            'activity_ids'   => ['nullable', 'array'],
            'activity_ids.*' => ['uuid', 'exists:gym_activities,id'],
        ]);

        $gym->gymActivities()->sync($request->input('activity_ids', []));

        return redirect()->route('gym.profil', ['tab' => 'activites'])
            ->with('success', 'Activités mises à jour.');
    }
}
