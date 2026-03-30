<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gym;
use App\Models\GymActivity;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminGymController extends Controller
{
    public function index(): View
    {
        $gyms = Gym::withCount([
                'checkins as checkins_count' => fn($q) =>
                    $q->where('created_at', '>=', now()->subDays(30))
            ])
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->get();

        return view('admin.gyms', compact('gyms'));
    }

    public function create(): View
    {
        $owners = User::where('role', 'gym_owner')->orderBy('name')->get();
        return view('admin.gyms-form', compact('owners'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'owner_id'    => ['required', 'uuid', 'exists:users,id'],
            'name'        => ['required', 'string', 'max:255'],
            'address'     => ['required', 'string', 'max:500'],
            'latitude'    => ['required', 'numeric', 'between:-90,90'],
            'longitude'   => ['required', 'numeric', 'between:-180,180'],
            'phone'       => ['nullable', 'string', 'max:30'],
            'activities'  => ['nullable', 'array'],
            'activities.*'=> ['string', 'max:50'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        Gym::create($data);

        return redirect()->route('admin.gyms')
            ->with('success', 'Salle ajoutée avec succès.');
    }

    public function edit(Gym $gym): View
    {
        $gym->load(['gymActivities', 'programs', 'photos' => fn($q) => $q->orderBy('display_order')]);

        $owners       = User::where('role', 'gym_owner')->orderBy('name')->get();
        $allActivities = GymActivity::orderBy('name')->get();
        $zones        = ['Plateau', 'Almadies', 'Mermoz', 'Parcelles', 'Guédiawaye', 'Thiès', 'Autre'];

        return view('admin.gym-edit', compact('gym', 'owners', 'allActivities', 'zones'));
    }

    public function update(Request $request, Gym $gym): RedirectResponse
    {
        $data = $request->validate([
            'owner_id'        => ['required', 'uuid', 'exists:users,id'],
            'name'            => ['required', 'string', 'max:255'],
            'address'         => ['required', 'string', 'max:500'],
            'zone'            => ['nullable', 'string', 'max:50'],
            'latitude'        => ['required', 'numeric', 'between:-90,90'],
            'longitude'       => ['required', 'numeric', 'between:-180,180'],
            'phone'           => ['nullable', 'string', 'max:30'],
            'phone_whatsapp'  => ['nullable', 'string', 'max:30'],
            'description'     => ['nullable', 'string', 'max:2000'],
            'opening_hours'   => ['nullable', 'string'], // JSON encodé
            'activity_ids'    => ['nullable', 'array'],
            'activity_ids.*'  => ['uuid', 'exists:gym_activities,id'],
        ]);

        // Décode opening_hours JSON
        $openingHours = null;
        if (! empty($data['opening_hours'])) {
            $openingHours = json_decode($data['opening_hours'], true);
        }

        $gym->update([
            'owner_id'       => $data['owner_id'],
            'name'           => $data['name'],
            'address'        => $data['address'],
            'zone'           => $data['zone'] ?? null,
            'latitude'       => $data['latitude'],
            'longitude'      => $data['longitude'],
            'phone'          => $data['phone'] ?? null,
            'phone_whatsapp' => $data['phone_whatsapp'] ?? null,
            'description'    => $data['description'] ?? null,
            'opening_hours'  => $openingHours,
        ]);

        // Sync activités many-to-many
        $gym->gymActivities()->sync($data['activity_ids'] ?? []);

        return redirect()->route('admin.gyms.edit', $gym)
            ->with('success', 'Salle mise à jour avec succès.');
    }

    public function toggle(Gym $gym): RedirectResponse
    {
        $gym->update(['is_active' => !$gym->is_active]);

        $action = $gym->is_active ? 'activée' : 'désactivée';

        return back()->with('success', "Salle {$gym->name} {$action}.");
    }
}
