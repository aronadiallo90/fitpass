@extends('layouts.app')
@section('title', 'Mon Abonnement')

@section('content')

<div class="page-header">
    <h1 class="page-title">Abonnement</h1>
</div>

{{-- Abonnement actif --}}
@if($activeSubscription)
<div class="card-static" style="margin-bottom: 2rem; border-color: color-mix(in srgb, var(--color-success) 30%, transparent);">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
        <div>
            <div style="font-family: var(--font-heading); font-size: 1.5rem; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 0.25rem;">
                {{ $activeSubscription->plan->name }}
            </div>
            <div style="font-size: 0.875rem; color: var(--color-text-muted);">
                Ref : {{ $activeSubscription->reference }} · Expire le {{ $activeSubscription->expires_at->format('d M Y') }}
            </div>
        </div>
        <div style="display: flex; align-items: center; gap: 1rem;">
            <span style="font-family: var(--font-heading); font-size: 1.75rem; color: var(--color-primary);">
                {{ number_format($activeSubscription->plan->price, 0, '.', ' ') }} FCFA
            </span>
            <span class="badge badge-active">Actif</span>
        </div>
    </div>
    @if($activeSubscription->plan->type === 'decouverte')
    <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--color-border);">
        <div style="display: flex; justify-content: space-between; align-items: center; max-width: 280px;">
            <span class="kpi-label">Séances restantes</span>
            <span style="font-family: var(--font-heading); font-size: 1.5rem; color: var(--color-primary);">
                {{ $activeSubscription->checkins_remaining }} / {{ $activeSubscription->plan->checkins_limit }}
            </span>
        </div>
    </div>
    @endif
</div>
@endif

{{-- Plans disponibles --}}
<div style="margin-bottom: 1.5rem;">
    <h2 style="font-family: var(--font-heading); font-size: 1.25rem; text-transform: uppercase; letter-spacing: 0.04em; color: var(--color-text-muted); margin-bottom: 1.25rem;">
        {{ $activeSubscription ? 'Renouveler / Changer de plan' : 'Choisir un plan' }}
    </h2>
    <div style="display: grid; grid-template-columns: 1fr; gap: 1rem;">
        @foreach($plans as $plan)
        <div class="card {{ $activeSubscription?->plan_id === $plan->id ? 'border-primary' : '' }}"
             style="{{ $activeSubscription?->plan_id === $plan->id ? 'border-color: var(--color-primary);' : '' }}">
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <div style="font-family: var(--font-heading); font-size: 1.25rem; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 0.25rem;">
                        {{ $plan->name }}
                        @if($activeSubscription?->plan_id === $plan->id)
                            <span class="badge badge-active" style="margin-left: 0.5rem;">Actuel</span>
                        @endif
                    </div>
                    <div style="font-size: 0.8rem; color: var(--color-text-muted);">
                        @if($plan->type === 'decouverte')
                            {{ $plan->checkins_limit }} séances · Accès toutes salles
                        @else
                            {{ $plan->duration_days }} jours · Accès illimité
                        @endif
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <span style="font-family: var(--font-heading); font-size: 1.5rem; color: var(--color-primary); white-space: nowrap;">
                        {{ number_format($plan->price, 0, '.', ' ') }} FCFA
                    </span>
                    @if(!$activeSubscription || $activeSubscription->plan_id !== $plan->id)
                    <form method="POST" action="{{ route('member.subscriptions.store') }}">
                        @csrf
                        <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                        <input type="hidden" name="method" value="wave">
                        <button type="submit" class="btn-primary" style="white-space: nowrap;">
                            Choisir →
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- Historique --}}
@if($subscriptions->isNotEmpty())
<div class="card-static" style="padding: 0; overflow: hidden;">
    <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--color-border);">
        <span class="kpi-label">Historique des abonnements</span>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Référence</th>
                <th>Plan</th>
                <th>Période</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach($subscriptions as $sub)
            <tr>
                <td style="font-family: monospace; font-size: 0.8rem; color: var(--color-text-muted);">{{ $sub->reference }}</td>
                <td>{{ $sub->plan->name }}</td>
                <td style="color: var(--color-text-muted); font-size: 0.8rem;">
                    {{ $sub->starts_at->format('d M Y') }} → {{ $sub->expires_at->format('d M Y') }}
                </td>
                <td><span class="badge badge-{{ $sub->status }}">{{ ucfirst($sub->status) }}</span></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

@endsection
