@extends('layouts.admin')
@section('title', 'Salles Partenaires')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
@endpush

@section('content')

<div class="page-header">
    <h1 class="page-title">Salles Partenaires</h1>
    <a href="{{ route('admin.gyms.create') }}" class="btn-primary">+ Ajouter une salle</a>
</div>

{{-- Carte --}}
<div id="gyms-map" class="map-container" style="margin-bottom: 1.5rem;"></div>

{{-- Liste --}}
<div class="card-static" style="padding: 0; overflow: hidden;">
    <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--color-border);">
        <span class="kpi-label">{{ $gyms->count() }} salle(s) enregistrée(s)</span>
    </div>
    @if($gyms->isEmpty())
        <div class="empty-state">
            <div class="empty-state-icon">🏢</div>
            <p class="empty-state-text">Aucune salle partenaire</p>
            <a href="{{ route('admin.gyms.create') }}" class="btn-primary" style="margin-top: 1rem;">Ajouter la première salle</a>
        </div>
    @else
    <div style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
    <table class="data-table">
        <thead>
            <tr>
                <th>Salle</th>
                <th>Adresse</th>
                <th>Activités</th>
                <th>Checkins 30j</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($gyms as $gym)
            <tr>
                <td>
                    <div style="font-weight: 500;">{{ $gym->name }}</div>
                    <div style="font-size: 0.7rem; color: var(--color-text-muted); font-family: monospace;">{{ Str::limit($gym->api_token, 12) }}...</div>
                </td>
                <td style="color: var(--color-text-muted); font-size: 0.8rem; max-width: 160px;">{{ $gym->address }}</td>
                <td style="font-size: 0.75rem; color: var(--color-text-muted);">
                    {{ implode(', ', $gym->activities ?? []) ?: '—' }}
                </td>
                <td style="font-family: var(--font-heading); font-size: 1.125rem;">{{ $gym->checkins_count }}</td>
                <td>
                    @if($gym->is_active)
                        <span class="badge badge-active">Active</span>
                    @else
                        <span class="badge badge-expired">Inactive</span>
                    @endif
                </td>
                <td>
                    <div style="display: flex; gap: 0.5rem;">
                        <a href="{{ route('admin.gyms.edit', $gym) }}" class="btn-ghost" style="padding: 0.25rem 0.75rem; font-size: 0.75rem;">Modifier</a>
                        <form method="POST" action="{{ route('admin.gyms.toggle', $gym) }}" style="display: inline;">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn-ghost" style="padding: 0.25rem 0.75rem; font-size: 0.75rem;">
                                {{ $gym->is_active ? 'Désactiver' : 'Activer' }}
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const map = L.map('gyms-map').setView([14.7167, -17.4677], 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
}).addTo(map);

const icon = L.divIcon({
    html: '<div style="background:#FF3B3B;width:14px;height:14px;border-radius:50%;border:2px solid white;box-shadow:0 2px 6px rgba(0,0,0,.4);"></div>',
    iconSize: [14, 14], iconAnchor: [7, 7],
});

fetch('/api/v1/gyms/geojson')
    .then(r => r.json())
    .then(data => {
        L.geoJSON(data, {
            pointToLayer: (f, latlng) => L.marker(latlng, { icon }),
            onEachFeature: (f, layer) => {
                const p = f.properties;
                layer.bindPopup(`
                    <strong style="font-family:sans-serif">${p.name}</strong><br>
                    <span style="font-size:0.8rem;color:#666">${p.address}</span>
                `);
            }
        }).addTo(map);
    });
</script>
@endpush
