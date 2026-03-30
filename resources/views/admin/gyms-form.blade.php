@extends('layouts.admin')

@section('title', isset($gym) ? 'Modifier la salle' : 'Nouvelle salle')

@section('content')
<div class="page-header">
    <h1 class="page-title">{{ isset($gym) ? 'Modifier : '.$gym->name : 'Nouvelle salle' }}</h1>
    <a href="{{ route('admin.gyms') }}" class="btn-outline text-sm px-4 py-2">
        ← Retour
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Formulaire --}}
    <div class="card">
        <h2 class="text-lg font-semibold text-white mb-6">Informations</h2>

        <form method="POST"
              action="{{ isset($gym) ? route('admin.gyms.update', $gym) : route('admin.gyms.store') }}">
            @csrf
            @if(isset($gym))
                @method('PUT')
            @endif

            {{-- Propriétaire --}}
            <div class="mb-4">
                <label for="owner_id" class="label">Propriétaire (gym_owner)</label>
                <select name="owner_id" id="owner_id" class="input" required>
                    <option value="">-- Sélectionner un propriétaire --</option>
                    @foreach($owners as $owner)
                        <option value="{{ $owner->id }}"
                            {{ old('owner_id', $gym->owner_id ?? '') === $owner->id ? 'selected' : '' }}>
                            {{ $owner->name }} — {{ $owner->email }}
                        </option>
                    @endforeach
                </select>
                @error('owner_id')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Nom --}}
            <div class="mb-4">
                <label for="name" class="label">Nom de la salle</label>
                <input type="text"
                       name="name"
                       id="name"
                       class="input"
                       value="{{ old('name', $gym->name ?? '') }}"
                       placeholder="FitZone Dakar"
                       required>
                @error('name')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Adresse --}}
            <div class="mb-4">
                <label for="address" class="label">Adresse</label>
                <input type="text"
                       name="address"
                       id="address"
                       class="input"
                       value="{{ old('address', $gym->address ?? '') }}"
                       placeholder="Route des Almadies, Dakar"
                       required>
                @error('address')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Coordonnées GPS --}}
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="latitude" class="label">Latitude</label>
                    <input type="number"
                           name="latitude"
                           id="latitude"
                           class="input"
                           step="any"
                           value="{{ old('latitude', $gym->latitude ?? '') }}"
                           placeholder="14.7200"
                           required>
                    @error('latitude')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="longitude" class="label">Longitude</label>
                    <input type="number"
                           name="longitude"
                           id="longitude"
                           class="input"
                           step="any"
                           value="{{ old('longitude', $gym->longitude ?? '') }}"
                           placeholder="-17.4700"
                           required>
                    @error('longitude')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Activités --}}
            <div class="mb-6">
                <label class="label">Activités proposées</label>
                <div class="grid grid-cols-2 gap-2 mt-2">
                    @foreach(['musculation', 'cardio', 'yoga', 'crossfit', 'boxe', 'natation', 'danse', 'arts-martiaux'] as $activity)
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox"
                                   name="activities[]"
                                   value="{{ $activity }}"
                                   class="accent-[--color-primary]"
                                   {{ in_array($activity, old('activities', $gym->activities ?? [])) ? 'checked' : '' }}>
                            <span class="text-sm text-[--color-text-muted] capitalize">{{ $activity }}</span>
                        </label>
                    @endforeach
                </div>
                @error('activities')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="btn-primary w-full">
                {{ isset($gym) ? 'Enregistrer les modifications' : 'Créer la salle' }}
            </button>
        </form>
    </div>

    {{-- Carte de sélection des coordonnées --}}
    <div class="card" x-data="coordPicker()">
        <h2 class="text-lg font-semibold text-white mb-4">Sélectionner sur la carte</h2>
        <p class="text-sm text-[--color-text-muted] mb-4">
            Cliquez sur la carte pour remplir automatiquement les coordonnées GPS.
        </p>

        <div id="picker-map" class="map-container rounded-lg mb-4" style="height: 380px;"></div>

        <div class="grid grid-cols-2 gap-4 text-sm">
            <div class="bg-white/5 rounded px-3 py-2">
                <span class="text-[--color-text-muted]">Lat :</span>
                <span x-text="lat || '—'" class="text-white ml-1"></span>
            </div>
            <div class="bg-white/5 rounded px-3 py-2">
                <span class="text-[--color-text-muted]">Lng :</span>
                <span x-text="lng || '—'" class="text-white ml-1"></span>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Leaflet --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
function coordPicker() {
    return {
        lat: document.getElementById('latitude').value || null,
        lng: document.getElementById('longitude').value || null,

        init() {
            const startLat = parseFloat(this.lat) || 14.7167;
            const startLng = parseFloat(this.lng) || -17.4677;

            const map = L.map('picker-map').setView([startLat, startLng], 12);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap'
            }).addTo(map);

            const markerIcon = L.divIcon({
                html: '<div style="width:16px;height:16px;background:#FF3B3B;border-radius:50%;border:2px solid white;"></div>',
                iconSize: [16, 16],
                iconAnchor: [8, 8],
                className: ''
            });

            let marker = null;

            if (this.lat && this.lng) {
                marker = L.marker([startLat, startLng], { icon: markerIcon }).addTo(map);
            }

            map.on('click', (e) => {
                const { lat, lng } = e.latlng;

                this.lat = lat.toFixed(6);
                this.lng = lng.toFixed(6);

                document.getElementById('latitude').value  = this.lat;
                document.getElementById('longitude').value = this.lng;

                if (marker) {
                    marker.setLatLng([lat, lng]);
                } else {
                    marker = L.marker([lat, lng], { icon: markerIcon }).addTo(map);
                }
            });
        }
    }
}
</script>
@endpush
