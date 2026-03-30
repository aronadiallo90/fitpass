<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gym;
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
        $owners = User::where('role', 'gym_owner')->orderBy('name')->get();
        return view('admin.gyms-form', compact('gym', 'owners'));
    }

    public function update(Request $request, Gym $gym): RedirectResponse
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

        $gym->update($data);

        return redirect()->route('admin.gyms')
            ->with('success', 'Salle mise à jour.');
    }

    public function toggle(Gym $gym): RedirectResponse
    {
        $gym->update(['is_active' => !$gym->is_active]);

        $action = $gym->is_active ? 'activée' : 'désactivée';

        return back()->with('success', "Salle {$gym->name} {$action}.");
    }
}
