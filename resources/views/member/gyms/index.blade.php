@extends('layouts.app')
@section('title', 'Trouver une salle')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
    #gym-map { height: 40vh; min-height: 220px; }
    @media (min-width: 640px) { #gym-map { height: 45vh; } }
    @media (min-width: 768px) { #gym-map { height: 55vh; } }

    /* Grille filtres responsive */
    .filter-row { display: flex; gap: 0.5rem; flex-wrap: wrap; }
    .filter-row select { flex: 1 1 130px; min-width: 0; }

    /* Cards grille — s'adapte à 375px */
    .gyms-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(min(280px, 100%), 1fr));
        gap: 1rem;
    }

    /* Pagination responsive */
    .pagination-row { display: flex; justify-content: center; align-items: center; gap: 0.5rem; flex-wrap: wrap; margin-top: 1.5rem; }
    .pagination-row button { padding: 8px 12px; font-size: 0.8rem; }
    @media (max-width: 400px) {
        .pagination-row button { padding: 6px 10px; font-size: 0.75rem; }
    }

    .gym-card {
        background: var(--color-bg-soft, #13131A);
        border: 1px solid rgba(255,59,59,0.12);
        border-radius: 12px;
        transition: border-color 0.2s, transform 0.2s;
        cursor: pointer;
    }
    .gym-card:hover,
    .gym-card.is-highlighted {
        border-color: rgba(255,59,59,0.5);
        transform: translateY(-2px);
    }
    .activity-badge {
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        padding: 2px 8px;
        border-radius: 999px;
        border: 1px solid rgba(255,59,59,0.3);
        color: #FF3B3B;
    }
    .zone-badge {
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        padding: 2px 8px;
        border-radius: 999px;
        background: rgba(255,140,0,0.15);
        color: #FF8C00;
    }
    .open-badge  { color: #22C55E; font-size: 0.7rem; }
    .closed-badge{ color: #EF4444; font-size: 0.7rem; }

    /* Leaflet popup custom */
    .leaflet-popup-content-wrapper {
        background: #13131A;
        color: #fff;
        border: 1px solid rgba(255,59,59,0.3);
        border-radius: 8px;
    }
    .leaflet-popup-tip { background: #13131A; }
</style>
@endpush

@section('content')

<div class="page-header" style="margin-bottom: 1.5rem;">
    <h1 class="page-title">Trouver une salle</h1>
    <p style="color: var(--color-text-muted, #8888A0); font-size: 0.9rem;">
        {{ count($activities) }} types d'activités • Toutes zones Dakar
    </p>
</div>

{{-- Composant Alpine.js principal --}}
<div x-data="gymSearch()" x-init="init()" class="gym-search-wrapper">

    {{-- ── Barre de recherche + filtres ── --}}
    <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1.25rem;">

        {{-- Champ texte --}}
        <div style="position: relative;">
            <svg style="position:absolute;left:12px;top:50%;transform:translateY(-50%);width:16px;height:16px;color:#8888A0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input
                type="text"
                placeholder="Rechercher une salle..."
                x-model.debounce.400ms="filters.q"
                @input="search()"
                style="width:100%; padding: 10px 12px 10px 38px; background:rgba(255,255,255,0.05); border:1px solid rgba(255,59,59,0.2); border-radius:8px; color:#fff; font-size:0.9rem;"
            >
        </div>

        {{-- Filtres zone + activité --}}
        <div class="filter-row">
            <select
                x-model="filters.zone"
                @change="search()"
                style="padding:8px 10px; background:rgba(255,255,255,0.05); border:1px solid rgba(255,59,59,0.2); border-radius:8px; color:#fff; font-size:0.85rem;"
            >
                <option value="">Toutes les zones</option>
                @foreach ($zones as $zone)
                    <option value="{{ $zone }}">{{ $zone }}</option>
                @endforeach
            </select>

            <select
                x-model="filters.activity"
                @change="search()"
                style="padding:8px 10px; background:rgba(255,255,255,0.05); border:1px solid rgba(255,59,59,0.2); border-radius:8px; color:#fff; font-size:0.85rem;"
            >
                <option value="">Toutes les activités</option>
                @foreach ($activities as $activity)
                    <option value="{{ $activity->slug }}">{{ $activity->icon }} {{ $activity->name }}</option>
                @endforeach
            </select>

            {{-- Bouton ma position --}}
            <button
                @click="locateMe()"
                :title="useLocation ? 'Désactiver ma position' : 'Trier par distance'"
                :style="useLocation ? 'border-color:rgba(34,197,94,0.6);color:#22C55E;' : ''"
                style="padding:8px 12px; background:transparent; border:1px solid rgba(255,59,59,0.2); border-radius:8px; color:#8888A0; cursor:pointer; transition:all 0.2s;"
            >
                <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </button>
        </div>

        {{-- Compteur résultats --}}
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <span style="font-size:0.8rem; color:#8888A0;">
                <span x-text="total"></span> salle<span x-show="total !== 1">s</span> trouvée<span x-show="total !== 1">s</span>
                <span x-show="useLocation"> • triées par distance</span>
            </span>
            <button
                x-show="filters.q || filters.zone || filters.activity"
                @click="resetFilters()"
                style="font-size:0.75rem; color:#FF3B3B; background:none; border:none; cursor:pointer; text-decoration:underline;"
            >
                Effacer les filtres
            </button>
        </div>
    </div>

    {{-- ── Carte Leaflet ── --}}
    <div id="gym-map" style="border-radius:12px; overflow:hidden; margin-bottom:1.25rem; border:1px solid rgba(255,59,59,0.15);"></div>

    {{-- ── État loading ── --}}
    <div x-show="loading" style="text-align:center; padding:2rem; color:#8888A0;">
        <svg style="width:24px;height:24px;animation:spin 1s linear infinite;margin:0 auto 0.5rem;" fill="none" viewBox="0 0 24 24">
            <circle style="opacity:.25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path style="opacity:.75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>
        Recherche en cours…
    </div>

    {{-- ── Liste des salles ── --}}
    <div x-show="!loading">
        <div x-show="gyms.length === 0 && !loading" style="text-align:center; padding:3rem 1rem; color:#8888A0;">
            <p style="font-size:1.1rem; margin-bottom:0.5rem;">Aucune salle trouvée</p>
            <p style="font-size:0.85rem;">Essayez d'élargir vos critères de recherche</p>
        </div>

        <div class="gyms-grid">
            <template x-for="gym in gyms" :key="gym.id">
                <a
                    :href="'/dashboard/gyms/' + gym.slug"
                    class="gym-card"
                    :class="{ 'is-highlighted': hoveredGymId === gym.id }"
                    @mouseenter="highlightMarker(gym.id)"
                    @mouseleave="hoveredGymId = null"
                    style="display:block; padding:1rem; text-decoration:none; color:inherit;"
                >
                    {{-- Photo couverture --}}
                    <div x-show="gym.photos && gym.photos.length > 0" style="margin:-1rem -1rem 0.75rem; height:140px; overflow:hidden; border-radius:12px 12px 0 0;">
                        <img
                            :src="gym.photos && gym.photos[0] ? gym.photos[0].url : ''"
                            :alt="gym.name"
                            style="width:100%; height:100%; object-fit:cover;"
                        >
                    </div>

                    {{-- Nom + badges --}}
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:0.5rem; margin-bottom:0.5rem;">
                        <h3 x-text="gym.name" style="font-size:0.95rem; font-weight:600; color:#fff; margin:0; line-height:1.3;"></h3>
                        <span x-show="gym.is_open_now" class="open-badge">● Ouvert</span>
                        <span x-show="!gym.is_open_now" class="closed-badge">● Fermé</span>
                    </div>

                    {{-- Zone + distance --}}
                    <div style="display:flex; gap:0.4rem; align-items:center; margin-bottom:0.6rem;">
                        <span x-show="gym.zone" x-text="gym.zone" class="zone-badge"></span>
                        <span x-show="gym.distance_km !== undefined && gym.distance_km !== null"
                              style="font-size:0.7rem; color:#8888A0;">
                            📍 <span x-text="gym.distance_km"></span> km
                        </span>
                    </div>

                    {{-- Adresse --}}
                    <p x-text="gym.address" style="font-size:0.8rem; color:#8888A0; margin:0 0 0.6rem; line-height:1.4;"></p>

                    {{-- Activités --}}
                    <div x-show="gym.activities && gym.activities.length > 0" style="display:flex; flex-wrap:wrap; gap:4px;">
                        <template x-for="act in (gym.activities || []).slice(0, 4)" :key="act.id">
                            <span class="activity-badge">
                                <span x-text="act.icon"></span>
                                <span x-text="act.name"></span>
                            </span>
                        </template>
                        <span x-show="(gym.activities || []).length > 4" class="activity-badge">
                            +<span x-text="gym.activities.length - 4"></span>
                        </span>
                    </div>
                </a>
            </template>
        </div>

        {{-- Pagination --}}
        <div x-show="lastPage > 1" class="pagination-row">
            <button
                @click="changePage(currentPage - 1)"
                :disabled="currentPage <= 1"
                style="background:rgba(255,255,255,0.05); border:1px solid rgba(255,59,59,0.2); border-radius:8px; color:#fff; cursor:pointer;"
            >← Précédent</button>

            <span style="padding:8px 12px; color:#8888A0; font-size:0.85rem;">
                Page <span x-text="currentPage"></span> / <span x-text="lastPage"></span>
            </span>

            <button
                @click="changePage(currentPage + 1)"
                :disabled="currentPage >= lastPage"
                style="background:rgba(255,255,255,0.05); border:1px solid rgba(255,59,59,0.2); border-radius:8px; color:#fff; cursor:pointer;"
            >Suivant →</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<style>
    @keyframes spin { to { transform: rotate(360deg); } }
</style>
<script>
function gymSearch() {
    return {
        gyms:        [],
        total:       0,
        currentPage: 1,
        lastPage:    1,
        loading:     false,
        useLocation: false,
        hoveredGymId: null,
        map:         null,
        markers:     {},
        userMarker:  null,

        filters: {
            q:        '',
            zone:     '',
            activity: '',
            lat:      null,
            lng:      null,
            per_page: 12,
        },

        init() {
            this.initMap();
            this.search();
        },

        initMap() {
            this.map = L.map('gym-map', { zoomControl: true }).setView([14.692, -17.447], 12);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 18,
            }).addTo(this.map);
        },

        async search(resetPage = true) {
            if (resetPage) this.currentPage = 1;
            this.loading = true;

            const params = new URLSearchParams();
            if (this.filters.q)        params.set('q',        this.filters.q);
            if (this.filters.zone)     params.set('zone',     this.filters.zone);
            if (this.filters.activity) params.set('activity', this.filters.activity);
            if (this.filters.lat)      params.set('lat',      this.filters.lat);
            if (this.filters.lng)      params.set('lng',      this.filters.lng);
            params.set('per_page', this.filters.per_page);
            params.set('page',     this.currentPage);

            try {
                const res  = await fetch('/api/v1/gyms/search?' + params.toString());
                const json = await res.json();

                this.gyms        = json.data        || [];
                this.total       = json.meta?.total  ?? 0;
                this.currentPage = json.meta?.current_page ?? 1;
                this.lastPage    = json.meta?.last_page    ?? 1;

                this.updateMarkers();
            } catch (e) {
                console.error('Gym search error:', e);
            } finally {
                this.loading = false;
            }
        },

        updateMarkers() {
            // Supprimer anciens marqueurs
            Object.values(this.markers).forEach(m => m.remove());
            this.markers = {};

            const bounds = [];

            this.gyms.forEach(gym => {
                if (!gym.latitude || !gym.longitude) return;

                const icon = L.divIcon({
                    className: '',
                    html: `<div style="
                        width:32px;height:32px;background:#FF3B3B;border-radius:50% 50% 50% 0;
                        transform:rotate(-45deg);border:2px solid #fff;
                        display:flex;align-items:center;justify-content:center;
                        box-shadow:0 2px 8px rgba(0,0,0,0.4);
                    "></div>`,
                    iconSize:   [32, 32],
                    iconAnchor: [16, 32],
                    popupAnchor:[0, -34],
                });

                const openBadge = gym.is_open_now
                    ? '<span style="color:#22C55E;font-size:0.7rem;">● Ouvert</span>'
                    : '<span style="color:#EF4444;font-size:0.7rem;">● Fermé</span>';

                const popup = L.popup({ maxWidth: 220 }).setContent(`
                    <div style="padding:4px;">
                        <strong style="font-size:0.9rem;">${gym.name}</strong><br>
                        <span style="font-size:0.75rem;color:#aaa;">${gym.address || ''}</span><br>
                        ${openBadge}
                        <br><br>
                        <a href="/dashboard/gyms/${gym.slug}"
                           style="display:inline-block;padding:4px 12px;background:#FF3B3B;color:#fff;border-radius:6px;font-size:0.75rem;text-decoration:none;">
                            Voir le profil →
                        </a>
                    </div>
                `);

                const marker = L.marker([gym.latitude, gym.longitude], { icon })
                    .addTo(this.map)
                    .bindPopup(popup);

                marker.on('mouseover', () => { this.hoveredGymId = gym.id; });
                marker.on('mouseout',  () => { this.hoveredGymId = null; });

                this.markers[gym.id] = marker;
                bounds.push([gym.latitude, gym.longitude]);
            });

            if (bounds.length > 0) {
                this.map.fitBounds(bounds, { padding: [40, 40], maxZoom: 14 });
            }
        },

        highlightMarker(gymId) {
            this.hoveredGymId = gymId;
            const marker = this.markers[gymId];
            if (marker) {
                marker.openPopup();
                this.map.panTo(marker.getLatLng(), { animate: true, duration: 0.3 });
            }
        },

        locateMe() {
            if (this.useLocation) {
                this.useLocation  = false;
                this.filters.lat  = null;
                this.filters.lng  = null;
                if (this.userMarker) { this.userMarker.remove(); this.userMarker = null; }
                this.search();
                return;
            }

            if (!navigator.geolocation) return;

            navigator.geolocation.getCurrentPosition(pos => {
                this.filters.lat  = pos.coords.latitude;
                this.filters.lng  = pos.coords.longitude;
                this.useLocation  = true;

                if (this.userMarker) this.userMarker.remove();
                this.userMarker = L.circleMarker(
                    [pos.coords.latitude, pos.coords.longitude],
                    { radius: 8, color: '#22C55E', fillColor: '#22C55E', fillOpacity: 0.7 }
                ).addTo(this.map).bindPopup('Ma position');

                this.search();
            }, () => {
                alert('Impossible d\'obtenir votre position.');
            });
        },

        resetFilters() {
            this.filters.q        = '';
            this.filters.zone     = '';
            this.filters.activity = '';
            this.useLocation      = false;
            this.filters.lat      = null;
            this.filters.lng      = null;
            if (this.userMarker) { this.userMarker.remove(); this.userMarker = null; }
            this.search();
        },

        changePage(page) {
            if (page < 1 || page > this.lastPage) return;
            this.currentPage = page;
            this.search(false);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
    };
}
</script>
@endpush
