@extends('layouts.app')
@section('title', 'Mon QR Code')

@section('content')

<div class="page-header">
    <h1 class="page-title">Mon QR Code</h1>
    <a href="{{ route('member.dashboard') }}" class="btn-ghost">← Retour</a>
</div>

<div style="max-width: 400px; margin: 0 auto; text-align: center;">

    @if($activeSubscription)

        {{-- Statut badge --}}
        <div style="margin-bottom: 1.5rem;">
            <span class="badge badge-active" style="font-size: 0.75rem; padding: 0.375rem 1rem;">
                Abonnement actif — {{ $activeSubscription->plan->name }}
            </span>
        </div>

        {{-- QR Code grand format --}}
        <div style="position: relative; display: inline-block; width: 100%;">
            <div class="qr-wrapper-lg">
                {!! $qrCode !!}
            </div>
        </div>

        {{-- Nom membre --}}
        <div style="margin-top: 1.5rem;">
            <div style="font-family: var(--font-heading); font-size: 1.5rem; text-transform: uppercase; letter-spacing: 0.04em;">
                {{ auth()->user()->name }}
            </div>
            <div style="font-size: 0.75rem; color: var(--color-text-muted); margin-top: 0.25rem; text-transform: uppercase; letter-spacing: 0.1em;">
                Expire le {{ $activeSubscription->expires_at->format('d M Y') }}
            </div>
        </div>

        {{-- Instruction --}}
        <div class="card-static" style="margin-top: 2rem; text-align: center;">
            <p style="font-size: 0.8rem; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.08em; line-height: 1.6;">
                Présentez ce code au scanner à l'entrée de la salle.<br>
                Restez à distance normale — ne pas zoomer.
            </p>
        </div>

        @if($activeSubscription->plan->type === 'decouverte')
        <div class="alert-info" style="margin-top: 1rem;">
            {{ $activeSubscription->checkins_remaining }} séance(s) restante(s) sur {{ $activeSubscription->plan->checkins_limit }}
        </div>
        @endif

        {{-- Régénération QR code --}}
        @php
            $regeneratedAt = auth()->user()->qr_token_regenerated_at;
            $canRegenerate = $regeneratedAt === null || $regeneratedAt->addHours(24)->isPast();
            $nextRegenerationAt = $canRegenerate ? null : $regeneratedAt->addHours(24);
        @endphp

        <div style="margin-top: 2rem;" x-data="{ confirming: false }">

            @if(session('success'))
                <div class="alert-success" style="margin-bottom: 1rem;">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert-danger" style="margin-bottom: 1rem;">
                    {{ session('error') }}
                </div>
            @endif

            @if($canRegenerate)
                {{-- Bouton de régénération avec confirmation Alpine.js --}}
                <div x-show="!confirming">
                    <button
                        type="button"
                        @click="confirming = true"
                        class="btn-ghost"
                        style="font-size: 0.8rem; color: var(--color-text-muted);"
                    >
                        Régénérer mon QR code
                    </button>
                </div>

                <div x-show="confirming" x-cloak style="border: 1px solid var(--color-warning); border-radius: 0.5rem; padding: 1rem; background: rgba(245,158,11,0.08);">
                    <p style="font-size: 0.85rem; color: var(--color-warning); margin-bottom: 1rem;">
                        Votre QR code actuel sera immédiatement invalide.<br>
                        Cette action est irréversible. Continuer ?
                    </p>
                    <div style="display: flex; gap: 0.75rem; justify-content: center;">
                        <form method="POST" action="{{ route('member.qrcode.regenerate') }}">
                            @csrf
                            <button type="submit" class="btn-primary" style="font-size: 0.8rem; padding: 0.5rem 1.25rem;">
                                Oui, régénérer
                            </button>
                        </form>
                        <button
                            type="button"
                            @click="confirming = false"
                            class="btn-ghost"
                            style="font-size: 0.8rem; padding: 0.5rem 1.25rem;"
                        >
                            Annuler
                        </button>
                    </div>
                </div>
            @else
                {{-- Cooldown actif — afficher heure de disponibilité --}}
                <p style="font-size: 0.75rem; color: var(--color-text-muted); text-align: center;">
                    Prochain QR disponible le
                    <strong style="color: var(--color-warning);">
                        {{ $nextRegenerationAt->format('d/m/Y à H:i') }}
                    </strong>
                </p>
            @endif
        </div>

    @else

        {{-- Pas d'abonnement --}}
        <div style="position: relative; display: inline-block; width: 100%;">
            <div class="qr-wrapper-lg" style="filter: blur(6px); pointer-events: none;">
                <div style="width: 240px; height: 240px; background: var(--color-border); border-radius: 0.5rem;"></div>
            </div>
            <div class="qr-expired-overlay">
                <span style="font-size: 3rem;">🔒</span>
                <p style="font-family: var(--font-heading); font-size: 1.25rem; text-transform: uppercase; color: white; text-align: center; padding: 0 1rem;">
                    Aucun abonnement actif
                </p>
                <a href="{{ route('member.subscriptions') }}" class="btn-primary">Voir les plans</a>
            </div>
        </div>

    @endif

</div>

@endsection
