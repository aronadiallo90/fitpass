@extends('layouts.admin')
@section('title', 'Membres')

@section('content')

<div class="page-header">
    <h1 class="page-title">Membres</h1>
    <span style="font-size: 0.875rem; color: var(--color-text-muted);">{{ $members->total() }} membres</span>
</div>

{{-- Filtres --}}
<form method="GET" style="margin-bottom: 1.5rem; display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: flex-end;">
    <div style="flex: 1; min-width: 200px;">
        <label class="label">Recherche</label>
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Nom ou téléphone..." class="input">
    </div>
    <div>
        <label class="label">Statut abonnement</label>
        <select name="status" class="input" style="width: auto;">
            <option value="">Tous</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Actif</option>
            <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expiré</option>
            <option value="none" {{ request('status') === 'none' ? 'selected' : '' }}>Aucun</option>
        </select>
    </div>
    <button type="submit" class="btn-outline">Filtrer</button>
    @if(request('search') || request('status'))
        <a href="{{ route('admin.members') }}" class="btn-ghost">Réinitialiser</a>
    @endif
</form>

<div class="card-static" style="padding: 0; overflow: hidden;">
    @if($members->isEmpty())
        <div class="empty-state">
            <div class="empty-state-icon">👥</div>
            <p class="empty-state-text">Aucun membre trouvé</p>
        </div>
    @else
    <table class="data-table">
        <thead>
            <tr>
                <th>Membre</th>
                <th>Téléphone</th>
                <th>Abonnement</th>
                <th>Expiration</th>
                <th>Dernière entrée</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($members as $member)
            <tr>
                <td>
                    <div style="font-weight: 500;">{{ $member->name }}</div>
                    <div style="font-size: 0.7rem; color: var(--color-text-muted);">Inscrit {{ $member->created_at->format('d M Y') }}</div>
                </td>
                <td style="color: var(--color-text-muted); font-size: 0.8rem;">{{ $member->phone }}</td>
                <td>
                    @if($member->activeSubscription)
                        <span class="badge badge-active">{{ $member->activeSubscription->plan->name }}</span>
                    @else
                        <span class="badge badge-expired">Aucun</span>
                    @endif
                </td>
                <td style="color: var(--color-text-muted); font-size: 0.8rem;">
                    {{ $member->activeSubscription?->expires_at->format('d M Y') ?? '—' }}
                </td>
                <td style="color: var(--color-text-muted); font-size: 0.8rem;">
                    {{ $member->latestCheckin?->created_at->format('d M · H:i') ?? '—' }}
                </td>
                <td>
                    <form method="POST" action="{{ route('admin.members.toggle', $member) }}"
                          onsubmit="return confirm('Confirmer cette action ?')"
                          style="display: inline;">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn-ghost" style="padding: 0.25rem 0.75rem; font-size: 0.75rem;">
                            {{ $member->is_active ? 'Désactiver' : 'Activer' }}
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--color-border);">
        {{ $members->withQueryString()->links() }}
    </div>
    @endif
</div>

@endsection
