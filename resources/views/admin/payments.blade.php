@extends('layouts.admin')
@section('title', 'Paiements')

@section('content')

<div class="page-header">
    <h1 class="page-title">Paiements</h1>
    <div style="display: flex; gap: 1rem; align-items: center;">
        <span style="font-family: var(--font-heading); font-size: 1.25rem; color: var(--color-primary);">
            {{ number_format($totalRevenue, 0, '.', ' ') }} FCFA
        </span>
        <span class="kpi-label">total encaissé</span>
    </div>
</div>

{{-- Filtres --}}
<form method="GET" style="margin-bottom: 1.5rem; display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: flex-end;">
    <div>
        <label class="label">Statut</label>
        <select name="status" class="input" style="width: auto;">
            <option value="">Tous</option>
            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Complété</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>En attente</option>
            <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Échoué</option>
            <option value="refunded" {{ request('status') === 'refunded' ? 'selected' : '' }}>Remboursé</option>
        </select>
    </div>
    <div>
        <label class="label">Méthode</label>
        <select name="method" class="input" style="width: auto;">
            <option value="">Toutes</option>
            <option value="wave" {{ request('method') === 'wave' ? 'selected' : '' }}>Wave</option>
            <option value="orange_money" {{ request('method') === 'orange_money' ? 'selected' : '' }}>Orange Money</option>
        </select>
    </div>
    <button type="submit" class="btn-outline">Filtrer</button>
    @if(request('status') || request('method'))
        <a href="{{ route('admin.payments') }}" class="btn-ghost">Réinitialiser</a>
    @endif
</form>

<div class="card-static" style="padding: 0; overflow: hidden;">
    @if($payments->isEmpty())
        <div class="empty-state">
            <div class="empty-state-icon">💳</div>
            <p class="empty-state-text">Aucun paiement trouvé</p>
        </div>
    @else
    <table class="data-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Membre</th>
                <th>Plan</th>
                <th>Montant</th>
                <th>Méthode</th>
                <th>Réf. PayTech</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $payment)
            <tr>
                <td style="color: var(--color-text-muted); font-size: 0.8rem; white-space: nowrap;">
                    {{ $payment->created_at->format('d M Y · H:i') }}
                </td>
                <td>{{ $payment->subscription->user->name }}</td>
                <td style="color: var(--color-text-muted); font-size: 0.8rem;">{{ $payment->subscription->plan->name }}</td>
                <td style="font-family: var(--font-heading); font-size: 1rem; color: var(--color-primary);">
                    {{ number_format($payment->amount, 0, '.', ' ') }} FCFA
                </td>
                <td style="font-size: 0.8rem;">
                    {{ $payment->method === 'wave' ? '🌊 Wave' : '🟠 Orange Money' }}
                </td>
                <td style="font-family: monospace; font-size: 0.75rem; color: var(--color-text-muted);">
                    {{ $payment->paytech_ref ?? '—' }}
                </td>
                <td><span class="badge badge-{{ $payment->status }}">{{ ucfirst($payment->status) }}</span></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--color-border);">
        {{ $payments->withQueryString()->links() }}
    </div>
    @endif
</div>

@endsection
