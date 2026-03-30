@extends('layouts.app')
@section('title', 'Mon Dashboard')

@section('content')

<div class="page-header">
    <h1 class="page-title">Bonjour, {{ auth()->user()->name }}</h1>
    @if(!$activeSubscription)
        <a href="{{ route('member.subscriptions') }}" class="btn-primary">S'abonner</a>
    @endif
</div>

{{-- Alerte expiration imminente --}}
@if($activeSubscription && $activeSubscription->expires_at->diffInDays(now()) <= 7)
    <div class="alert-warning" style="margin-bottom: 1.5rem;">
        Votre abonnement expire le {{ $activeSubscription->expires_at->format('d M Y') }} —
        <a href="{{ route('member.subscriptions') }}" style="color: inherit; font-weight: 600; text-decoration: underline;">Renouveler</a>
    </div>
@endif

<div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem;">

    {{-- Bloc abonnement + QR --}}
    <div style="display: grid; grid-template-columns: 1fr; gap: 1rem;">

        {{-- Abonnement actif --}}
        @if($activeSubscription)
        <div class="card">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                <span class="kpi-label">Abonnement actif</span>
                <span class="badge badge-active">Actif</span>
            </div>
            <div style="font-family: var(--font-heading); font-size: 1.5rem; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 0.25rem;">
                {{ $activeSubscription->plan->name }}
            </div>
            <div style="font-size: 0.875rem; color: var(--color-text-muted);">
                Expire le {{ $activeSubscription->expires_at->format('d M Y') }}
            </div>
            @if($activeSubscription->plan->type === 'decouverte')
            <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--color-border);">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--color-text-muted);">Séances restantes</span>
                    <span style="font-family: var(--font-heading); font-size: 1.5rem; color: var(--color-primary);">
                        {{ $activeSubscription->checkins_remaining }} / {{ $activeSubscription->plan->checkins_limit }}
                    </span>
                </div>
            </div>
            @endif
        </div>
        @else
        <div class="card" style="text-align: center;">
            <div class="empty-state">
                <div class="empty-state-icon">🏋️</div>
                <p class="empty-state-text">Aucun abonnement actif</p>
                <a href="{{ route('member.subscriptions') }}" class="btn-primary" style="margin-top: 1rem;">Voir les plans</a>
            </div>
        </div>
        @endif

        {{-- QR Code --}}
        @if($activeSubscription)
        <div class="card" style="text-align: center;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                <span class="kpi-label">Mon QR Code</span>
                <a href="{{ route('member.qrcode') }}" style="font-size: 0.75rem; color: var(--color-primary); text-decoration: none; text-transform: uppercase; letter-spacing: 0.08em;">Agrandir →</a>
            </div>
            <div class="qr-wrapper" style="margin: 0 auto;">
                {!! $qrCode !!}
            </div>
            <p style="font-size: 0.7rem; color: var(--color-text-muted); margin-top: 0.75rem; text-transform: uppercase; letter-spacing: 0.08em;">
                Présentez ce code à l'entrée de la salle
            </p>
        </div>
        @endif

    </div>

    {{-- Dernières entrées --}}
    <div class="card-static" style="padding: 0; overflow: hidden;">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--color-border);">
            <span class="kpi-label">Dernières entrées</span>
        </div>
        @if($recentCheckins->isEmpty())
            <div class="empty-state">
                <div class="empty-state-icon">📍</div>
                <p class="empty-state-text">Aucune entrée enregistrée</p>
            </div>
        @else
        <table class="data-table">
            <thead>
                <tr>
                    <th>Salle</th>
                    <th>Date</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentCheckins as $checkin)
                <tr>
                    <td>{{ $checkin->gym->name }}</td>
                    <td style="color: var(--color-text-muted);">
                        {{ $checkin->created_at->format('d M · H:i') }}
                    </td>
                    <td><span class="badge badge-{{ $checkin->status }}">{{ ucfirst($checkin->status) }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--color-border);">
            <a href="{{ route('member.checkins') }}" style="font-size: 0.75rem; color: var(--color-primary); text-decoration: none; text-transform: uppercase; letter-spacing: 0.08em;">Voir tout l'historique →</a>
        </div>
        @endif
    </div>

</div>

@endsection
