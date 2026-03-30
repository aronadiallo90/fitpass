@extends('layouts.gym')
@section('title', 'Dashboard Salle')

@section('content')

<div class="page-header">
    <h1 class="page-title">{{ auth()->user()->gym?->name ?? 'Ma Salle' }}</h1>
    <a href="{{ route('gym.scan') }}" class="btn-primary">Scanner un QR Code</a>
</div>

{{-- KPIs du jour --}}
<div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 2rem;">
    <div class="kpi-card">
        <div class="kpi-value">{{ $todayCount }}</div>
        <div class="kpi-label">Entrées aujourd'hui</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-value">{{ $monthCount }}</div>
        <div class="kpi-label">Ce mois</div>
    </div>
</div>

{{-- Entrées du jour --}}
<div class="card-static" style="padding: 0; overflow: hidden; margin-bottom: 1.5rem;">
    <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--color-border); display: flex; justify-content: space-between; align-items: center;">
        <span class="kpi-label">Entrées aujourd'hui</span>
        <span style="font-size: 0.75rem; color: var(--color-text-muted);">{{ now()->format('d M Y') }}</span>
    </div>
    @if($todayCheckins->isEmpty())
        <div class="empty-state">
            <div class="empty-state-icon">🏋️</div>
            <p class="empty-state-text">Aucune entrée pour l'instant</p>
        </div>
    @else
    <table class="data-table">
        <thead>
            <tr>
                <th>Membre</th>
                <th>Heure</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach($todayCheckins as $checkin)
            <tr>
                <td>{{ $checkin->user?->name ?? '—' }}</td>
                <td style="color: var(--color-text-muted);">{{ $checkin->created_at->format('H:i') }}</td>
                <td><span class="badge badge-{{ $checkin->status }}">{{ ucfirst($checkin->status) }}</span></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>

{{-- Historique 30 derniers jours --}}
<div class="card-static" style="padding: 0; overflow: hidden;">
    <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--color-border); display: flex; justify-content: space-between; align-items: center;">
        <span class="kpi-label">Historique récent</span>
        <a href="{{ route('gym.checkins') }}" style="font-size: 0.75rem; color: var(--color-primary); text-decoration: none; text-transform: uppercase; letter-spacing: 0.08em;">Tout voir →</a>
    </div>
    @if($recentCheckins->isEmpty())
        <div class="empty-state">
            <div class="empty-state-icon">📊</div>
            <p class="empty-state-text">Aucune donnée disponible</p>
        </div>
    @else
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
            @foreach($recentCheckins as $checkin)
            <tr>
                <td>{{ $checkin->user?->name ?? '—' }}</td>
                <td style="color: var(--color-text-muted);">{{ $checkin->created_at->format('d M') }}</td>
                <td style="color: var(--color-text-muted);">{{ $checkin->created_at->format('H:i') }}</td>
                <td><span class="badge badge-{{ $checkin->status }}">{{ ucfirst($checkin->status) }}</span></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>

@endsection
