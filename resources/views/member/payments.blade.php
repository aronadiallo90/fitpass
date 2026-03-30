@extends('layouts.app')
@section('title', 'Mes Paiements')

@section('content')

<div class="page-header">
    <h1 class="page-title">Paiements</h1>
</div>

@if($payments->isEmpty())
    <div class="card">
        <div class="empty-state">
            <div class="empty-state-icon">💳</div>
            <p class="empty-state-text">Aucun paiement enregistré</p>
        </div>
    </div>
@else
<div class="card-static" style="padding: 0; overflow: hidden;">
    <table class="data-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Plan</th>
                <th>Montant</th>
                <th>Méthode</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $payment)
            <tr>
                <td style="color: var(--color-text-muted); font-size: 0.8rem; white-space: nowrap;">
                    {{ $payment->created_at->format('d M Y · H:i') }}
                </td>
                <td>{{ $payment->subscription->plan->name }}</td>
                <td style="font-family: var(--font-heading); font-size: 1rem; color: var(--color-primary);">
                    {{ number_format($payment->amount, 0, '.', ' ') }} FCFA
                </td>
                <td>
                    <span style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.06em; color: var(--color-text-muted);">
                        {{ $payment->method === 'wave' ? '🌊 Wave' : '🟠 Orange Money' }}
                    </span>
                </td>
                <td><span class="badge badge-{{ $payment->status }}">{{ ucfirst($payment->status) }}</span></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @if($payments->hasPages())
    <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--color-border);">
        {{ $payments->links() }}
    </div>
    @endif
</div>
@endif

@endsection
