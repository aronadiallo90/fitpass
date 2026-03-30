@extends('layouts.gym')
@section('title', 'Historique Entrées')

@section('content')

<div class="page-header">
    <h1 class="page-title">Historique des entrées</h1>
    <a href="{{ route('gym.scan') }}" class="btn-primary">Scanner</a>
</div>

{{-- Filtre date --}}
<form method="GET" style="margin-bottom: 1.5rem; display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: flex-end;">
    <div>
        <label class="label">Date début</label>
        <input type="date" name="from" value="{{ request('from') }}" class="input" style="width: auto;">
    </div>
    <div>
        <label class="label">Date fin</label>
        <input type="date" name="to" value="{{ request('to') }}" class="input" style="width: auto;">
    </div>
    <button type="submit" class="btn-outline">Filtrer</button>
    @if(request('from') || request('to'))
        <a href="{{ route('gym.checkins') }}" class="btn-ghost">Réinitialiser</a>
    @endif
</form>

@if($checkins->isEmpty())
    <div class="card">
        <div class="empty-state">
            <div class="empty-state-icon">📍</div>
            <p class="empty-state-text">Aucune entrée pour cette période</p>
        </div>
    </div>
@else
<div class="card-static" style="padding: 0; overflow: hidden;">
    <table class="data-table">
        <thead>
            <tr>
                <th>Membre</th>
                <th>Date</th>
                <th>Heure</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach($checkins as $checkin)
            <tr>
                <td>{{ $checkin->user?->name ?? '—' }}</td>
                <td style="color: var(--color-text-muted); white-space: nowrap;">
                    {{ $checkin->created_at->format('d M Y') }}
                </td>
                <td style="color: var(--color-text-muted);">
                    {{ $checkin->created_at->format('H:i') }}
                </td>
                <td>
                    <span class="badge badge-{{ $checkin->status }}">{{ ucfirst($checkin->status) }}</span>
                    @if($checkin->failure_reason)
                        <div style="font-size: 0.7rem; color: var(--color-danger); margin-top: 0.2rem;">{{ $checkin->failure_reason }}</div>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @if($checkins->hasPages())
    <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--color-border);">
        {{ $checkins->links() }}
    </div>
    @endif
</div>
@endif

@endsection
