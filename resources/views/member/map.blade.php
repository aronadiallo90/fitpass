@extends('layouts.app')
@section('title', 'Salles Partenaires')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
    #gym-map { height: 55vh; }
    @media (min-width: 768px) { #gym-map { height: 65vh; } }
</style>
@endpush

@section('content')

<div class="page-header">
    <h1 class="page-title">Salles Partenaires</h1>
</div>

{{-- Filtres activités --}}
<div style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 1rem;" x-data="{ active: '' }">
    @foreach(['Musculation','Cardio','Yoga','Natation','Arts Martiaux','CrossFit'] as $activity)
    <button
        class="badge"
        :class="active === '{{ $activity }}' ? 'badge-active' : 'badge-expired'"
        @click="active = (active === '{{ $activity }}') ? '' : '{{ $activity }}'; filterMap(active)"
        style="cursor: pointer; padding: 0.375rem 0.875rem; font-size: 0.75rem;">
        {{ $activity }}
    </button>
    @endforeach
</div>

{{-- Carte --}}
<div id="gym-map" class="map-container" style="margin-bottom: 1.5rem;"></div>

{{-- Liste des salles --}}
<div style="display: grid; grid-template-columns: 1fr; gap: 1rem;" id="gym-list">
    @forelse($gyms as $gym)
    <div class="card" data-activities="{{ implode(',', $gym->activities ?? []) }}">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; flex-wrap: wrap;">
            <div>
                <div style="font-family: var(--font-heading); font-size: 1.125rem; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 0.25rem;">
                    {{ $gym->name }}
                </div>
                <div style="font-size: 0.8rem; color: var(--color-text-muted); margin-bottom: 0.5rem;">
                    {{ $gym->address }}
                </div>
                <div style="display: flex; gap: 0.375rem; flex-wrap: wrap;">
                    @foreach($gym->activities ?? [] as $act)
                        <span class="badge badge-valid" style="font-size: 0.65rem;">{{ $act }}</span>
                    @endforeach
                </div>
            </div>
            @if($gym->phone)
            <a href="tel:{{ $gym->phone }}" class="btn-outline" style="white-space: nowrap; font-size: 0.8rem; padding: 0.5rem 1rem;">
                Appeler
            </a>
            @endif
        </div>
    </div>
    @empty
    <div class="card">
        <div class="empty-state">
            <div class="empty-state-icon">🏢</div>
            <p class="empty-state-text">Aucune salle disponible pour l'instant</p>
        </div>
    </div>
    @endforelse
</div>

@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const map = L.map('gym-map').setView([14.7167, -17.4677], 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
}).addTo(map);

const fitpassIcon = L.divIcon({
    html: '<div style="background:#FF3B3B;width:16px;height:16px;border-radius:50%;border:2px solid white;box-shadow:0 2px 8px rgba(0,0,0,.5);"></div>',
    iconSize: [16, 16], iconAnchor: [8, 8], className: ''
});

let allMarkers = [];

fetch('/api/v1/gyms/geojson')
    .then(r => r.json())
    .then(data => {
        allMarkers = data.features.map(f => {
            const p = f.properties;
            const m = L.marker([f.geometry.coordinates[1], f.geometry.coordinates[0]], { icon: fitpassIcon });
            m.bindPopup(`
                <div style="font-family:sans-serif;min-width:160px;">
                    <strong style="font-size:0.9rem;">${p.name}</strong><br>
                    <span style="font-size:0.78rem;color:#666;">${p.address}</span><br>
                    <span style="font-size:0.72rem;color:#999;margin-top:4px;display:block">${(p.activities || []).join(' · ')}</span>
                </div>
            `);
            m.activities = (p.activities || []).map(a => a.toLowerCase());
            m.addTo(map);
            return m;
        });
    });

function filterMap(activity) {
    const q = (activity || '').toLowerCase();
    allMarkers.forEach(m => {
        if (!q || m.activities.includes(q)) {
            m.addTo(map);
        } else {
            m.remove();
        }
    });
    // Filtrer la liste aussi
    document.querySelectorAll('#gym-list .card[data-activities]').forEach(card => {
        const acts = card.dataset.activities.toLowerCase();
        card.style.display = (!q || acts.includes(q)) ? '' : 'none';
    });
}
</script>
@endpush
