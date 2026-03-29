@extends('layouts.app')
@section('title', 'Mon Dashboard')

@section('content')
<div>
    <h1 class="section-title" style="margin-bottom: 2rem;">Bonjour, {{ auth()->user()->name }}</h1>

    <!-- TODO Sprint 2 : abonnement actif + QR code + checkins récents -->
    <div class="card" style="text-align: center; padding: 3rem;">
        <p style="color: var(--color-text-muted); font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.1em;">Dashboard membre — Sprint 2</p>
    </div>
</div>
@endsection
