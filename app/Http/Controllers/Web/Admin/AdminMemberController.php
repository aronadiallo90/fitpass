<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminMemberController extends Controller
{
    public function index(Request $request): View
    {
        $members = User::where('role', 'member')
            ->with(['activeSubscription.plan', 'latestCheckin'])
            ->when($request->search, fn($q, $s) =>
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%")
            )
            ->when($request->status === 'active', fn($q) =>
                $q->whereHas('subscriptions', fn($s) => $s->where('status', 'active'))
            )
            ->when($request->status === 'expired', fn($q) =>
                $q->whereHas('subscriptions', fn($s) => $s->where('status', 'expired'))
            )
            ->when($request->status === 'none', fn($q) =>
                $q->whereDoesntHave('subscriptions')
            )
            ->latest()
            ->paginate(25);

        return view('admin.members', compact('members'));
    }

    public function toggle(Request $request, User $user): RedirectResponse
    {
        // Seul un admin peut activer/désactiver un membre
        $user->update(['is_active' => !$user->is_active]);

        $action = $user->is_active ? 'activé' : 'désactivé';

        return back()->with('success', "Membre {$user->name} {$action}.");
    }
}
