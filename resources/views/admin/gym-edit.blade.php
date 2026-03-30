@extends('layouts.admin')
@section('title', 'Modifier : ' . $gym->name)

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
    .tab-btn {
        padding: 8px 16px; border-radius: 8px; font-size: 0.82rem;
        font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em;
        cursor: pointer; border: none; transition: all 0.2s;
        background: transparent; color: var(--color-text-muted, #8888A0);
    }
    .tab-btn.active { background: rgba(255,59,59,0.15); color: #FF3B3B; }
    .tab-btn:hover:not(.active) { color: #fff; }

    .hours-row { display: grid; grid-template-columns: 90px 1fr 1fr 80px; gap: 8px; align-items: center; margin-bottom: 6px; }
    #picker-map { height: 300px; border-radius: 8px; }

    .photo-thumb { position: relative; width: 100%; padding-top: 66%; border-radius: 8px; overflow: hidden; }
    .photo-thumb img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
    .photo-thumb .photo-actions { position: absolute; inset: 0; background: rgba(0,0,0,0.6); display: flex; align-items: center; justify-content: center; gap: 8px; opacity: 0; transition: opacity 0.2s; }
    .photo-thumb:hover .photo-actions { opacity: 1; }

    .program-card { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,59,59,0.1); border-radius: 10px; padding: 1rem; margin-bottom: 0.75rem; }
</style>
@endpush

@section('content')

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <h1 class="page-title">{{ $gym->name }}</h1>
        <p style="font-size:0.8rem; color:#8888A0; margin-top:2px;">
            ID : {{ $gym->id }}
            · Créée le {{ $gym->created_at->format('d/m/Y') }}
        </p>
    </div>
    <div style="display:flex; gap:0.5rem;">
        <a href="{{ route('admin.gyms') }}" class="btn-outline text-sm px-4 py-2">← Retour</a>
        <a href="{{ route('member.gyms.show', $gym->slug) }}" target="_blank"
           style="padding:8px 16px; font-size:0.82rem; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); border-radius:8px; color:#ccc; text-decoration:none;">
            Voir le profil ↗
        </a>
    </div>
</div>

@if(session('success'))
    <div style="background:rgba(34,197,94,0.1); border:1px solid rgba(34,197,94,0.3); border-radius:8px; padding:10px 16px; color:#22C55E; margin-bottom:1rem; font-size:0.9rem;">
        {{ session('success') }}
    </div>
@endif

{{-- ── Tabs ── --}}
<div x-data="{ tab: 'infos' }">

    {{-- Navigation tabs --}}
    <div style="display:flex; gap:4px; flex-wrap:wrap; margin-bottom:1.5rem; background:rgba(255,255,255,0.03); padding:4px; border-radius:10px; border:1px solid rgba(255,255,255,0.07);">
        <button class="tab-btn" :class="{ active: tab === 'infos' }"    @click="tab='infos'">Informations</button>
        <button class="tab-btn" :class="{ active: tab === 'hours' }"    @click="tab='hours'">Horaires</button>
        <button class="tab-btn" :class="{ active: tab === 'activities'}" @click="tab='activities'">Activités</button>
        <button class="tab-btn" :class="{ active: tab === 'programs' }" @click="tab='programs'">Programmes ({{ $gym->programs->count() }})</button>
        <button class="tab-btn" :class="{ active: tab === 'photos' }"   @click="tab='photos'">Photos ({{ $gym->photos->count() }})</button>
    </div>

    {{-- ═══════════════════════════════════════════════════ --}}
    {{-- TAB : INFORMATIONS --}}
    {{-- ═══════════════════════════════════════════════════ --}}
    <div x-show="tab === 'infos'" x-cloak>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem;">

            <form method="POST" action="{{ route('admin.gyms.update', $gym) }}" class="card">
                @csrf @method('PUT')

                <h2 style="font-size:1rem; font-weight:600; color:#fff; margin-bottom:1.25rem;">Infos générales</h2>

                {{-- Propriétaire --}}
                <div class="mb-4">
                    <label class="label">Propriétaire</label>
                    <select name="owner_id" class="input" required>
                        @foreach($owners as $owner)
                            <option value="{{ $owner->id }}" {{ $gym->owner_id === $owner->id ? 'selected' : '' }}>
                                {{ $owner->name }} — {{ $owner->email }}
                            </option>
                        @endforeach
                    </select>
                    @error('owner_id') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Nom --}}
                <div class="mb-4">
                    <label class="label">Nom de la salle</label>
                    <input type="text" name="name" class="input" value="{{ old('name', $gym->name) }}" required>
                    @error('name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Zone --}}
                <div class="mb-4">
                    <label class="label">Zone Dakar</label>
                    <select name="zone" class="input">
                        <option value="">-- Sélectionner une zone --</option>
                        @foreach($zones as $zone)
                            <option value="{{ $zone }}" {{ old('zone', $gym->zone) === $zone ? 'selected' : '' }}>{{ $zone }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Adresse --}}
                <div class="mb-4">
                    <label class="label">Adresse</label>
                    <input type="text" name="address" class="input" value="{{ old('address', $gym->address) }}" required>
                    @error('address') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- GPS --}}
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;" class="mb-4">
                    <div>
                        <label class="label">Latitude</label>
                        <input type="number" name="latitude" id="latitude" class="input" step="any" value="{{ old('latitude', $gym->latitude) }}" required>
                    </div>
                    <div>
                        <label class="label">Longitude</label>
                        <input type="number" name="longitude" id="longitude" class="input" step="any" value="{{ old('longitude', $gym->longitude) }}" required>
                    </div>
                </div>

                {{-- Téléphones --}}
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;" class="mb-4">
                    <div>
                        <label class="label">Téléphone</label>
                        <input type="text" name="phone" class="input" value="{{ old('phone', $gym->phone) }}" placeholder="+221 77 000 00 00">
                    </div>
                    <div>
                        <label class="label">WhatsApp</label>
                        <input type="text" name="phone_whatsapp" class="input" value="{{ old('phone_whatsapp', $gym->phone_whatsapp) }}" placeholder="+221 77 000 00 00">
                    </div>
                </div>

                {{-- Description --}}
                <div class="mb-6">
                    <label class="label">Description</label>
                    <textarea name="description" class="input" rows="4" placeholder="Décrivez la salle…">{{ old('description', $gym->description) }}</textarea>
                </div>

                {{-- Champs horaires + activités cachés (soumis via les autres tabs) --}}
                <input type="hidden" name="opening_hours" id="opening_hours_input">

                <button type="submit" class="btn-primary w-full">Enregistrer les informations</button>
            </form>

            {{-- Carte GPS --}}
            <div class="card" x-data="coordPicker()">
                <h2 style="font-size:1rem; font-weight:600; color:#fff; margin-bottom:0.75rem;">Position GPS</h2>
                <p style="font-size:0.8rem; color:#8888A0; margin-bottom:0.75rem;">Cliquez sur la carte pour modifier les coordonnées.</p>
                <div id="picker-map" style="margin-bottom:0.75rem;"></div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.5rem; font-size:0.82rem;">
                    <div style="background:rgba(255,255,255,0.05); border-radius:6px; padding:6px 10px;">
                        <span style="color:#8888A0;">Lat :</span>
                        <span x-text="lat || '—'" style="color:#fff; margin-left:4px;"></span>
                    </div>
                    <div style="background:rgba(255,255,255,0.05); border-radius:6px; padding:6px 10px;">
                        <span style="color:#8888A0;">Lng :</span>
                        <span x-text="lng || '—'" style="color:#fff; margin-left:4px;"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════ --}}
    {{-- TAB : HORAIRES --}}
    {{-- ═══════════════════════════════════════════════════ --}}
    <div x-show="tab === 'hours'" x-cloak x-data="hoursEditor({{ json_encode($gym->opening_hours ?? []) }})">

        <form method="POST" action="{{ route('admin.gyms.update', $gym) }}" class="card" @submit="prepareSubmit">
            @csrf @method('PUT')
            {{-- Champs requis cachés --}}
            <input type="hidden" name="owner_id"  value="{{ $gym->owner_id }}">
            <input type="hidden" name="name"      value="{{ $gym->name }}">
            <input type="hidden" name="address"   value="{{ $gym->address }}">
            <input type="hidden" name="latitude"  value="{{ $gym->latitude }}">
            <input type="hidden" name="longitude" value="{{ $gym->longitude }}">
            <input type="hidden" name="opening_hours" x-ref="hoursInput">

            <h2 style="font-size:1rem; font-weight:600; color:#fff; margin-bottom:1.25rem;">Horaires d'ouverture</h2>

            <template x-for="day in days" :key="day.key">
                <div class="hours-row">
                    <span x-text="day.label" style="font-size:0.85rem; color:#ccc; text-transform:capitalize;"></span>
                    <input type="time" :value="hours[day.key]?.open || '06:00'"
                           @change="hours[day.key] = { ...hours[day.key], open: $event.target.value }"
                           :disabled="hours[day.key]?.closed"
                           style="background:rgba(255,255,255,0.05); border:1px solid rgba(255,59,59,0.2); border-radius:6px; padding:5px 8px; color:#fff; font-size:0.85rem;">
                    <input type="time" :value="hours[day.key]?.close || '22:00'"
                           @change="hours[day.key] = { ...hours[day.key], close: $event.target.value }"
                           :disabled="hours[day.key]?.closed"
                           style="background:rgba(255,255,255,0.05); border:1px solid rgba(255,59,59,0.2); border-radius:6px; padding:5px 8px; color:#fff; font-size:0.85rem;">
                    <label style="display:flex; align-items:center; gap:4px; font-size:0.8rem; color:#EF4444; cursor:pointer;">
                        <input type="checkbox"
                               :checked="hours[day.key]?.closed"
                               @change="hours[day.key] = { ...hours[day.key], closed: $event.target.checked }"
                               class="accent-red-500">
                        Fermé
                    </label>
                </div>
            </template>

            <div style="margin-top:1.25rem;">
                <button type="submit" class="btn-primary w-full">Enregistrer les horaires</button>
            </div>
        </form>
    </div>

    {{-- ═══════════════════════════════════════════════════ --}}
    {{-- TAB : ACTIVITÉS --}}
    {{-- ═══════════════════════════════════════════════════ --}}
    <div x-show="tab === 'activities'" x-cloak>

        <form method="POST" action="{{ route('admin.gyms.update', $gym) }}" class="card">
            @csrf @method('PUT')
            {{-- Champs requis cachés --}}
            <input type="hidden" name="owner_id"  value="{{ $gym->owner_id }}">
            <input type="hidden" name="name"      value="{{ $gym->name }}">
            <input type="hidden" name="address"   value="{{ $gym->address }}">
            <input type="hidden" name="latitude"  value="{{ $gym->latitude }}">
            <input type="hidden" name="longitude" value="{{ $gym->longitude }}">

            <h2 style="font-size:1rem; font-weight:600; color:#fff; margin-bottom:1.25rem;">Activités proposées</h2>
            <p style="font-size:0.82rem; color:#8888A0; margin-bottom:1rem;">
                Ces activités permettent aux membres de filtrer les salles par type d'entraînement.
            </p>

            <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(180px, 1fr)); gap:0.75rem; margin-bottom:1.5rem;">
                @foreach($allActivities as $activity)
                    <label style="display:flex; align-items:center; gap:10px; cursor:pointer; padding:10px; background:rgba(255,255,255,0.03); border:1px solid rgba(255,59,59,0.1); border-radius:8px; transition:border-color 0.2s;"
                           :style="''"
                           onmouseover="this.style.borderColor='rgba(255,59,59,0.35)'"
                           onmouseout="this.style.borderColor='rgba(255,59,59,0.1)'">
                        <input type="checkbox"
                               name="activity_ids[]"
                               value="{{ $activity->id }}"
                               class="accent-[--color-primary]"
                               {{ $gym->gymActivities->contains($activity->id) ? 'checked' : '' }}>
                        <span style="font-size:1.1rem;">{{ $activity->icon }}</span>
                        <span style="font-size:0.85rem; color:#ccc;">{{ $activity->name }}</span>
                    </label>
                @endforeach
            </div>

            @if($allActivities->isEmpty())
                <p style="color:#8888A0; font-size:0.85rem;">Aucune activité configurée. Créez-en via le seeder.</p>
            @endif

            <button type="submit" class="btn-primary w-full">Enregistrer les activités</button>
        </form>
    </div>

    {{-- ═══════════════════════════════════════════════════ --}}
    {{-- TAB : PROGRAMMES --}}
    {{-- ═══════════════════════════════════════════════════ --}}
    <div x-show="tab === 'programs'" x-cloak>
        <div style="display:grid; grid-template-columns:1fr 1.2fr; gap:1.5rem; align-items:start;">

            {{-- Formulaire ajout --}}
            <div class="card">
                <h2 style="font-size:1rem; font-weight:600; color:#fff; margin-bottom:1.25rem;">Ajouter un programme</h2>
                <form method="POST" action="{{ route('admin.gyms.programs.store', $gym) }}">
                    @csrf
                    <div class="mb-4">
                        <label class="label">Nom du programme</label>
                        <input type="text" name="name" class="input" placeholder="Yoga du matin" required>
                        @error('name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="mb-4">
                        <label class="label">Description (optionnel)</label>
                        <textarea name="description" class="input" rows="3" placeholder="Détails du programme…"></textarea>
                    </div>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;" class="mb-4">
                        <div>
                            <label class="label">Durée (min)</label>
                            <input type="number" name="duration_minutes" class="input" value="60" min="15" max="300" required>
                        </div>
                        <div>
                            <label class="label">Places max</label>
                            <input type="number" name="max_spots" class="input" placeholder="Illimité" min="1" max="500">
                        </div>
                    </div>
                    <div class="mb-6">
                        <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-size:0.85rem; color:#ccc;">
                            <input type="checkbox" name="is_active" value="1" checked class="accent-[--color-primary]">
                            Actif immédiatement
                        </label>
                    </div>
                    <button type="submit" class="btn-primary w-full">Ajouter le programme</button>
                </form>
            </div>

            {{-- Liste des programmes --}}
            <div>
                <h2 style="font-size:1rem; font-weight:600; color:#fff; margin-bottom:1rem;">
                    Programmes existants
                </h2>

                @forelse($gym->programs->sortBy('name') as $program)
                    <div class="program-card" x-data="{ editing: false }">
                        <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:0.5rem;">
                            <div style="flex:1;">
                                <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:2px;">
                                    <strong style="color:#fff; font-size:0.9rem;">{{ $program->name }}</strong>
                                    @if($program->is_active)
                                        <span style="font-size:0.65rem; color:#22C55E; border:1px solid rgba(34,197,94,0.3); padding:1px 6px; border-radius:999px;">Actif</span>
                                    @else
                                        <span style="font-size:0.65rem; color:#8888A0; border:1px solid rgba(255,255,255,0.1); padding:1px 6px; border-radius:999px;">Inactif</span>
                                    @endif
                                </div>
                                <span style="font-size:0.75rem; color:#8888A0;">
                                    {{ $program->duration_minutes }} min
                                    @if($program->max_spots) · {{ $program->max_spots }} places @endif
                                </span>
                            </div>
                            <div style="display:flex; gap:4px;">
                                <button @click="editing = !editing"
                                        style="padding:4px 10px; font-size:0.75rem; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); border-radius:6px; color:#ccc; cursor:pointer;">
                                    Modifier
                                </button>
                                <form method="POST" action="{{ route('admin.gyms.programs.destroy', [$gym, $program]) }}"
                                      onsubmit="return confirm('Supprimer ce programme ?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" style="padding:4px 8px; font-size:0.75rem; background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.3); border-radius:6px; color:#EF4444; cursor:pointer;">✕</button>
                                </form>
                            </div>
                        </div>

                        {{-- Formulaire édition inline --}}
                        <div x-show="editing" x-cloak style="margin-top:0.75rem; padding-top:0.75rem; border-top:1px solid rgba(255,255,255,0.07);">
                            <form method="POST" action="{{ route('admin.gyms.programs.update', [$gym, $program]) }}">
                                @csrf @method('PUT')
                                <div class="mb-3">
                                    <input type="text" name="name" class="input" value="{{ $program->name }}" required style="font-size:0.85rem; padding:7px 10px;">
                                </div>
                                <div class="mb-3">
                                    <textarea name="description" class="input" rows="2" style="font-size:0.85rem; padding:7px 10px;">{{ $program->description }}</textarea>
                                </div>
                                <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.5rem;" class="mb-3">
                                    <input type="number" name="duration_minutes" class="input" value="{{ $program->duration_minutes }}" min="15" max="300" required style="font-size:0.85rem; padding:7px 10px;">
                                    <input type="number" name="max_spots" class="input" value="{{ $program->max_spots }}" placeholder="Illimité" style="font-size:0.85rem; padding:7px 10px;">
                                </div>
                                <div class="mb-3">
                                    <label style="display:flex; align-items:center; gap:8px; font-size:0.82rem; color:#ccc; cursor:pointer;">
                                        <input type="checkbox" name="is_active" value="1" {{ $program->is_active ? 'checked' : '' }} class="accent-[--color-primary]">
                                        Actif
                                    </label>
                                </div>
                                <button type="submit" style="width:100%; padding:6px; font-size:0.82rem;" class="btn-primary">Enregistrer</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p style="color:#8888A0; font-size:0.85rem; padding:1rem 0;">Aucun programme pour cette salle.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════ --}}
    {{-- TAB : PHOTOS --}}
    {{-- ═══════════════════════════════════════════════════ --}}
    <div x-show="tab === 'photos'" x-cloak>
        <div style="display:grid; grid-template-columns:1fr 1.5fr; gap:1.5rem; align-items:start;">

            {{-- Upload --}}
            <div class="card">
                <h2 style="font-size:1rem; font-weight:600; color:#fff; margin-bottom:1.25rem;">Ajouter une photo</h2>
                <form method="POST"
                      action="{{ route('admin.gyms.photos.store', $gym) }}"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label class="label">Fichier image (JPEG, PNG, WebP — max 4 Mo)</label>
                        <input type="file" name="photo" accept="image/jpeg,image/png,image/webp"
                               class="input" style="padding:8px;" required>
                        @error('photo') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="mb-6">
                        <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-size:0.85rem; color:#ccc;">
                            <input type="checkbox" name="is_cover" value="1" class="accent-[--color-primary]">
                            Définir comme photo de couverture
                        </label>
                    </div>
                    <button type="submit" class="btn-primary w-full">Uploader la photo</button>
                </form>
            </div>

            {{-- Galerie --}}
            <div>
                <h2 style="font-size:1rem; font-weight:600; color:#fff; margin-bottom:1rem;">
                    Photos ({{ $gym->photos->count() }})
                </h2>

                @if($gym->photos->isEmpty())
                    <p style="color:#8888A0; font-size:0.85rem;">Aucune photo pour cette salle.</p>
                @else
                    <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(150px, 1fr)); gap:0.75rem;">
                        @foreach($gym->photos as $photo)
                            <div style="position:relative;">
                                <div class="photo-thumb">
                                    <img src="{{ $photo->photo_url }}" alt="Photo">
                                    <div class="photo-actions">
                                        {{-- Définir couverture --}}
                                        @unless($photo->is_cover)
                                            <form method="POST" action="{{ route('admin.gyms.photos.cover', [$gym, $photo]) }}">
                                                @csrf @method('PATCH')
                                                <button type="submit" title="Définir comme couverture"
                                                        style="width:32px;height:32px;background:#FF8C00;border:none;border-radius:50%;color:#fff;cursor:pointer;font-size:0.9rem;">★</button>
                                            </form>
                                        @endunless
                                        {{-- Supprimer --}}
                                        <form method="POST" action="{{ route('admin.gyms.photos.destroy', [$gym, $photo]) }}"
                                              onsubmit="return confirm('Supprimer cette photo ?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" title="Supprimer"
                                                    style="width:32px;height:32px;background:#EF4444;border:none;border-radius:50%;color:#fff;cursor:pointer;font-size:0.9rem;">✕</button>
                                        </form>
                                    </div>
                                </div>
                                @if($photo->is_cover)
                                    <span style="display:block; margin-top:4px; text-align:center; font-size:0.65rem; color:#FF8C00; text-transform:uppercase; letter-spacing:0.06em;">★ Couverture</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>{{-- /x-data tabs --}}

@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
function coordPicker() {
    return {
        lat: document.getElementById('latitude')?.value || null,
        lng: document.getElementById('longitude')?.value || null,

        init() {
            const startLat = parseFloat(this.lat) || 14.7167;
            const startLng = parseFloat(this.lng) || -17.4677;

            const map = L.map('picker-map').setView([startLat, startLng], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap' }).addTo(map);

            const icon = L.divIcon({
                html: '<div style="width:16px;height:16px;background:#FF3B3B;border-radius:50%;border:2px solid #fff;box-shadow:0 2px 4px rgba(0,0,0,0.5);"></div>',
                iconSize:[16,16], iconAnchor:[8,8], className:''
            });

            let marker = null;
            if (this.lat && this.lng) {
                marker = L.marker([startLat, startLng], { icon }).addTo(map);
            }

            map.on('click', (e) => {
                this.lat = e.latlng.lat.toFixed(6);
                this.lng = e.latlng.lng.toFixed(6);
                document.getElementById('latitude').value  = this.lat;
                document.getElementById('longitude').value = this.lng;
                if (marker) marker.setLatLng(e.latlng);
                else marker = L.marker(e.latlng, { icon }).addTo(map);
            });
        }
    };
}

function hoursEditor(existing) {
    const days = [
        { key: 'lundi',    label: 'Lundi' },
        { key: 'mardi',    label: 'Mardi' },
        { key: 'mercredi', label: 'Mercredi' },
        { key: 'jeudi',    label: 'Jeudi' },
        { key: 'vendredi', label: 'Vendredi' },
        { key: 'samedi',   label: 'Samedi' },
        { key: 'dimanche', label: 'Dimanche' },
    ];

    const defaults = {};
    days.forEach(d => {
        defaults[d.key] = existing[d.key] || { open: '06:00', close: '22:00', closed: false };
    });

    return {
        days,
        hours: defaults,

        prepareSubmit() {
            this.$refs.hoursInput.value = JSON.stringify(this.hours);
        },
    };
}
</script>
@endpush
