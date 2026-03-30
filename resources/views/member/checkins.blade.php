@extends('layouts.app')
@section('title', 'Mes Entrées')

@section('content')

<div class="page-header">
    <h1 class="page-title">Historique des entrées</h1>
</div>

@if($checkins->isEmpty())
    <div class="card">
        <div class="empty-state">
            <div class="empty-state-icon">📍</div>
            <p class="empty-state-text">Aucune entrée enregistrée</p>
            <a href="{{ route('member.qrcode') }}" class="btn-primary" style="margin-top: 1rem;">Voir mon QR Code</a>
        </div>
    </div>
@else
<div class="card-static" style="padding: 0; overflow: hidden;">
    <table class="data-table">
        <thead>
            <tr>
                <th>Salle</th>
                <th>Date</th>
                <th>Heure</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach($checkins as $checkin)
            <tr>
                <td>
                    <div style="font-weight: 500;">{{ $checkin->gym->name }}</div>
                    <div style="font-size: 0.75rem; color: var(--color-text-muted);">{{ $checkin->gym->address }}</div>
                </td>
                <td style="color: var(--color-text-muted); white-space: nowrap;">
                    {{ $checkin->created_at->format('d M Y') }}
                </td>
                <td style="color: var(--color-text-muted);">
                    {{ $checkin->created_at->format('H:i') }}
                </td>
                <td>
                    <span class="badge badge-{{ $checkin->status }}">{{ ucfirst($checkin->status) }}</span>
                    @if($checkin->failure_reason)
                    <div style="font-size: 0.7rem; color: var(--color-danger); margin-top: 0.25rem;">
                        {{ $checkin->failure_reason }}
                    </div>
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
