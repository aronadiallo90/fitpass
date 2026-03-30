@extends('layouts.app')
@section('title', 'Profil salle')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
    #gym-detail-map { height: 200px; border-radius: 12px; overflow: hidden; }
    @media (min-width: 640px) { #gym-detail-map { height: 250px; } }

    .activity-pill {
        display: inline-flex; align-items: center; gap: 4px;
        font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.06em;
        padding: 4px 10px; border-radius: 999px;
        border: 1px solid rgba(255,59,59,0.3); color: #FF3B3B;
    }
    .program-card {
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,59,59,0.1);
        border-radius: 10px; padding: 1rem;
    }
    .hours-row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid rgba(255,255,255,0.05); font-size: 0.85rem; }
    .hours-row:last-child { border-bottom: none; }
    .open-badge  { color: #22C55E; font-weight: 600; }
    .closed-badge{ color: #EF4444; }

    /* Galerie photos — responsive 375px */
    .photo-gallery { display: grid; grid-template-columns: repeat(auto-fill, minmax(min(140px, 45%), 1fr)); gap: 8px; }
    .photo-gallery img { width: 100%; height: 100px; object-fit: cover; border-radius: 8px; cursor: pointer; transition: opacity 0.2s; }
    .photo-gallery img:hover { opacity: 0.85; }
    @media (min-width: 640px) { .photo-gallery img { height: 120px; } }

    /* En-tête photo */
    .gym-hero { position:relative; margin: -1rem -1rem 1.5rem; height:200px; background:#13131A; overflow:hidden; border-radius: 0 0 16px 16px; }
    @media (min-width: 640px) { .gym-hero { height: 260px; } }

    /* Boutons action responsive */
    .action-buttons { display:flex; gap:0.75rem; flex-wrap:wrap; margin-bottom:1.5rem; }
    .action-buttons a { flex:1; min-width:120px; }

    /* Lightbox simple */
    .lightbox { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.9); z-index: 9999; align-items: center; justify-content: center; }
    .lightbox.open { display: flex; }
    .lightbox img { max-width: 92vw; max-height: 85vh; border-radius: 8px; }
    .lightbox-close { position: absolute; top: 0.75rem; right: 1rem; font-size: 2rem; color: #fff; cursor: pointer; min-width: 44px; min-height: 44px; display:flex; align-items:center; justify-content:center; }
</style>
@endpush

@section('content')

<div x-data="gymProfile('{{ $slug }}')" x-init="load()">

    {{-- Loading --}}
    <div x-show="loading" style="text-align:center; padding:4rem; color:#8888A0;">
        Chargement…
    </div>

    {{-- Erreur --}}
    <div x-show="!loading && !gym" style="text-align:center; padding:4rem; color:#EF4444;">
        Salle introuvable ou inactive.
        <br>
        <a href="{{ route('member.gyms') }}" style="color:#FF3B3B; text-decoration:underline; margin-top:0.5rem; display:inline-block;">
            ← Retour à la liste
        </a>
    </div>

    {{-- Contenu --}}
    <div x-show="gym && !loading">

        {{-- ── En-tête : photo cover + nom ── --}}
        <div class="gym-hero">
            <template x-if="coverPhoto">
                <img :src="coverPhoto" :alt="gym?.name" style="width:100%; height:100%; object-fit:cover; opacity:0.7;">
            </template>
            <div style="position:absolute; inset:0; display:flex; flex-direction:column; justify-content:flex-end; padding:1.25rem; background:linear-gradient(transparent, rgba(0,0,0,0.8));">
                <div style="display:flex; justify-content:space-between; align-items:flex-end; gap:0.5rem;">
                    <div>
                        <h1 x-text="gym?.name" style="font-size:1.4rem; font-weight:700; color:#fff; margin:0 0 0.3rem;"></h1>
                        <div style="display:flex; gap:0.5rem; align-items:center; flex-wrap:wrap;">
                            <span x-show="gym?.zone" x-text="gym?.zone"
                                  style="font-size:0.7rem; padding:2px 8px; border-radius:999px; background:rgba(255,140,0,0.2); color:#FF8C00;"></span>
                            <span x-show="gym?.is_open_now" class="open-badge" style="font-size:0.75rem;">● Ouvert maintenant</span>
                            <span x-show="gym && !gym.is_open_now" class="closed-badge" style="font-size:0.75rem;">● Fermé</span>
                        </div>
                    </div>
                    <a href="{{ route('member.gyms') }}"
                       style="padding:6px 12px; background:rgba(255,255,255,0.1); border-radius:8px; color:#fff; font-size:0.8rem; text-decoration:none; white-space:nowrap;">
                        ← Retour
                    </a>
                </div>
            </div>
        </div>

        {{-- ── Boutons d'action ── --}}
        <div class="action-buttons">
            <a
                :href="'https://wa.me/' + (gym?.phone_whatsapp || gym?.phone)"
                x-show="gym?.phone_whatsapp || gym?.phone"
                target="_blank"
                style="flex:1; min-width:140px; display:flex; align-items:center; justify-content:center; gap:6px;
                       padding:10px 16px; background:#22C55E; color:#fff; border-radius:10px; font-size:0.85rem; font-weight:600; text-decoration:none;"
            >
                <svg style="width:16px;height:16px;" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347"/></svg>
                WhatsApp
            </a>

            <a
                :href="'https://www.google.com/maps/dir/?api=1&destination=' + gym?.latitude + ',' + gym?.longitude"
                x-show="gym?.latitude && gym?.longitude"
                target="_blank"
                style="flex:1; min-width:140px; display:flex; align-items:center; justify-content:center; gap:6px;
                       padding:10px 16px; background:rgba(255,59,59,0.15); border:1px solid rgba(255,59,59,0.3); color:#FF3B3B; border-radius:10px; font-size:0.85rem; font-weight:600; text-decoration:none;"
            >
                <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                </svg>
                Y aller
            </a>
        </div>

        {{-- ── Activités ── --}}
        <div x-show="gym?.activities?.length > 0" style="margin-bottom:1.5rem;">
            <h2 style="font-size:0.85rem; text-transform:uppercase; letter-spacing:0.1em; color:#8888A0; margin-bottom:0.75rem;">Activités</h2>
            <div style="display:flex; flex-wrap:wrap; gap:6px;">
                <template x-for="act in gym?.activities || []" :key="act.id">
                    <span class="activity-pill">
                        <span x-text="act.icon"></span>
                        <span x-text="act.name"></span>
                    </span>
                </template>
            </div>
        </div>

        {{-- ── Description ── --}}
        <div x-show="gym?.description" style="margin-bottom:1.5rem;">
            <h2 style="font-size:0.85rem; text-transform:uppercase; letter-spacing:0.1em; color:#8888A0; margin-bottom:0.5rem;">À propos</h2>
            <p x-text="gym?.description" style="font-size:0.9rem; color:#ccc; line-height:1.6; margin:0;"></p>
        </div>

        {{-- ── Photos ── --}}
        <div x-show="gym?.photos?.length > 1" style="margin-bottom:1.5rem;">
            <h2 style="font-size:0.85rem; text-transform:uppercase; letter-spacing:0.1em; color:#8888A0; margin-bottom:0.75rem;">Photos</h2>
            <div class="photo-gallery">
                <template x-for="(photo, idx) in gym?.photos || []" :key="photo.id">
                    <img :src="photo.url" :alt="gym?.name + ' photo ' + (idx+1)" @click="openLightbox(photo.url)">
                </template>
            </div>
        </div>

        {{-- ── Programmes ── --}}
        <div x-show="gym?.programs?.length > 0" style="margin-bottom:1.5rem;">
            <h2 style="font-size:0.85rem; text-transform:uppercase; letter-spacing:0.1em; color:#8888A0; margin-bottom:0.75rem;">Programmes</h2>
            <div style="display:grid; gap:0.75rem;">
                <template x-for="prog in gym?.programs || []" :key="prog.id">
                    <div class="program-card">
                        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:0.4rem;">
                            <strong x-text="prog.name" style="font-size:0.9rem; color:#fff;"></strong>
                            <span x-text="prog.duration_minutes + ' min'" style="font-size:0.75rem; color:#8888A0;"></span>
                        </div>
                        <p x-text="prog.description" x-show="prog.description" style="font-size:0.8rem; color:#aaa; margin:0 0 0.4rem; line-height:1.5;"></p>
                        <p x-show="prog.max_spots" style="font-size:0.75rem; color:#FF8C00; margin:0;">
                            Max <span x-text="prog.max_spots"></span> places
                        </p>
                    </div>
                </template>
            </div>
        </div>

        {{-- ── Horaires ── --}}
        <div x-show="gym?.opening_hours && Object.keys(gym.opening_hours).length > 0" style="margin-bottom:1.5rem;">
            <h2 style="font-size:0.85rem; text-transform:uppercase; letter-spacing:0.1em; color:#8888A0; margin-bottom:0.75rem;">Horaires</h2>
            <div style="background:rgba(255,255,255,0.03); border-radius:10px; padding:0.75rem 1rem; border:1px solid rgba(255,255,255,0.07);">
                <template x-for="[key, day] in Object.entries(gym?.opening_hours || {})" :key="key">
                    <div class="hours-row">
                        <span style="color:#ccc; text-transform:capitalize;" x-text="key"></span>
                        <span x-show="!day.closed" x-text="(day.open || '') + ' – ' + (day.close || '')" style="color:#fff;"></span>
                        <span x-show="day.closed" class="closed-badge">Fermé</span>
                    </div>
                </template>
            </div>
        </div>

        {{-- ── Mini-carte ── --}}
        <div x-show="gym?.latitude && gym?.longitude" style="margin-bottom:1.5rem;">
            <h2 style="font-size:0.85rem; text-transform:uppercase; letter-spacing:0.1em; color:#8888A0; margin-bottom:0.75rem;">Localisation</h2>
            <div id="gym-detail-map"></div>
            <p x-text="gym?.address" style="font-size:0.8rem; color:#8888A0; margin-top:0.5rem;"></p>
        </div>

    </div>

    {{-- Lightbox --}}
    <div class="lightbox" :class="{ open: lightboxSrc }" @click="lightboxSrc = null">
        <span class="lightbox-close" @click.stop="lightboxSrc = null">&times;</span>
        <img :src="lightboxSrc" @click.stop alt="Photo agrandie">
    </div>

</div>

@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
function gymProfile(slug) {
    return {
        slug,
        gym:       null,
        loading:   true,
        lightboxSrc: null,
        detailMap: null,

        get coverPhoto() {
            return this.gym?.photos?.find(p => p.is_cover)?.url
                || this.gym?.photos?.[0]?.url
                || null;
        },

        async load() {
            try {
                const res  = await fetch('/api/v1/gyms/' + this.slug + '/profile');
                if (!res.ok) { this.gym = null; return; }
                const json = await res.json();
                this.gym   = json.data;
                this.$nextTick(() => this.initMap());
            } catch (e) {
                this.gym = null;
            } finally {
                this.loading = false;
            }
        },

        initMap() {
            if (!this.gym?.latitude || !this.gym?.longitude) return;
            if (this.detailMap) return;

            this.detailMap = L.map('gym-detail-map', { zoomControl: false, dragging: false, scrollWheelZoom: false })
                              .setView([this.gym.latitude, this.gym.longitude], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap',
                maxZoom: 18,
            }).addTo(this.detailMap);

            const icon = L.divIcon({
                className: '',
                html: '<div style="width:24px;height:24px;background:#FF3B3B;border-radius:50%;border:3px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,0.5);"></div>',
                iconSize: [24,24], iconAnchor:[12,12],
            });

            L.marker([this.gym.latitude, this.gym.longitude], { icon })
             .addTo(this.detailMap)
             .bindPopup(`<strong>${this.gym.name}</strong>`)
             .openPopup();
        },

        openLightbox(src) {
            this.lightboxSrc = src;
        },
    };
}
</script>
@endpush
