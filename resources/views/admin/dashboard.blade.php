@extends('layouts.admin')
@section('title', 'Dashboard Admin')

@section('content')

<div class="page-header">
    <h1 class="page-title">Dashboard</h1>
    <span style="font-size: 0.75rem; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.1em;">
        {{ now()->format('d M Y') }}
    </span>
</div>

{{-- KPIs --}}
<div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 2rem;">
    <div class="kpi-card">
        <div class="kpi-value">{{ $totalMembers }}</div>
        <div class="kpi-label">Membres actifs</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-value" style="color: var(--color-primary);">
            {{ number_format($monthRevenue, 0, '.', ' ') }}
        </div>
        <div class="kpi-label">Revenus ce mois (FCFA)</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-value">{{ $activeSubscriptions }}</div>
        <div class="kpi-label">Abonnements actifs</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-value">{{ $todayCheckins }}</div>
        <div class="kpi-label">Entrées aujourd'hui</div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem;">

    {{-- Derniers paiements --}}
    <div class="card-static" style="padding: 0; overflow: hidden;">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--color-border); display: flex; justify-content: space-between; align-items: center;">
            <span class="kpi-label">Derniers paiements</span>
            <a href="{{ route('admin.payments') }}" style="font-size: 0.75rem; color: var(--color-primary); text-decoration: none; text-transform: uppercase; letter-spacing: 0.08em;">Tout voir →</a>
        </div>
        @if($recentPayments->isEmpty())
            <div class="empty-state"><p class="empty-state-text">Aucun paiement</p></div>
        @else
        <table class="data-table">
            <thead>
                <tr>
                    <th>Membre</th>
                    <th>Plan</th>
                    <th>Montant</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentPayments as $payment)
                <tr>
                    <td>{{ $payment->subscription->user->name }}</td>
                    <td style="color: var(--color-text-muted);">{{ $payment->subscription->plan->name }}</td>
                    <td style="font-family: var(--font-heading); color: var(--color-primary);">
                        {{ number_format($payment->amount, 0, '.', ' ') }} FCFA
                    </td>
                    <td><span class="badge badge-{{ $payment->status }}">{{ ucfirst($payment->status) }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    {{-- Derniers membres --}}
    <div class="card-static" style="padding: 0; overflow: hidden;">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--color-border); display: flex; justify-content: space-between; align-items: center;">
            <span class="kpi-label">Derniers membres inscrits</span>
            <a href="{{ route('admin.members') }}" style="font-size: 0.75rem; color: var(--color-primary); text-decoration: none; text-transform: uppercase; letter-spacing: 0.08em;">Tout voir →</a>
        </div>
        @if($recentMembers->isEmpty())
            <div class="empty-state"><p class="empty-state-text">Aucun membre</p></div>
        @else
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Téléphone</th>
                    <th>Inscrit le</th>
                    <th>Abonnement</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentMembers as $member)
                <tr>
                    <td>{{ $member->name }}</td>
                    <td style="color: var(--color-text-muted); font-size: 0.8rem;">{{ $member->phone }}</td>
                    <td style="color: var(--color-text-muted); font-size: 0.8rem;">{{ $member->created_at->format('d M Y') }}</td>
                    <td>
                        @if($member->activeSubscription)
                            <span class="badge badge-active">{{ $member->activeSubscription->plan->name }}</span>
                        @else
                            <span class="badge badge-expired">Aucun</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

</div>

@endsection
