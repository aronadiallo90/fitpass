@extends('layouts.gym')
@section('title', 'Ma Salle')

@push('styles')
<style>
    .tab-btn {
        padding: 8px 14px; border-radius: 8px; font-size: 0.8rem;
        font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em;
        cursor: pointer; border: none; transition: all 0.2s;
        background: transparent; color: var(--color-text-muted);
    }
    .tab-btn.active { background: rgba(255,59,59,0.15); color: #FF3B3B; }
    .tab-btn:hover:not(.active) { color: #fff; }

    .hours-row {
        display: grid;
        grid-template-columns: 90px 1fr 1fr 80px;
        gap: 8px;
        align-items: center;
        margin-bottom: 8px;
    }
    @media (max-width: 480px) {
        .hours-row { grid-template-columns: 1fr 1fr; row-gap: 4px; }
        .hours-row span:first-child { grid-column: 1 / -1; font-weight: 600; }
        .hours-row label:last-child { grid-column: 1 / -1; }
    }

    .program-card {
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,59,59,0.1);
        border-radius: 10px;
        padding: 1rem;
        margin-bottom: 0.75rem;
    }
</style>
@endpush

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">{{ $gym->name }}</h1>
        <p style="font-size:0.75rem; color: var(--color-text-muted); margin-top: 2px;">
            Gérez les informations de votre salle
        </p>
    </div>
</div>

@if(session('success'))
    <div style="background:rgba(34,197,94,0.1); border:1px solid rgba(34,197,94,0.3); border-radius:8px; padding:10px 16px; color:#22C55E; margin-bottom:1rem; font-size:0.9rem;">
        {{ session('success') }}
    </div>
@endif

<div x-data="{ tab: '{{ request('tab', 'infos') }}' }">

    {{-- Navigation tabs --}}
    <div style="display:flex; gap:4px; flex-wrap:wrap; margin-bottom:1.5rem; background:rgba(255,255,255,0.03); padding:4px; border-radius:10px; border:1px solid rgba(255,255,255,0.07);">
        <button class="tab-btn" :class="{ active: tab === 'infos' }"      @click="tab='infos'">Informations</button>
        <button class="tab-btn" :class="{ active: tab === 'horaires' }"   @click="tab='horaires'">Horaires</button>
        <button class="tab-btn" :class="{ active: tab === 'activites' }"  @click="tab='activites'">Activités</button>
        <button class="tab-btn" :class="{ active: tab === 'programmes' }" @click="tab='programmes'">Programmes ({{ $gym->programs->count() }})</button>
    </div>

    {{-- ══════════════════════════════════════════════ --}}
    {{-- TAB : INFORMATIONS --}}
    {{-- ══════════════════════════════════════════════ --}}
    <div x-show="tab === 'infos'" x-cloak>
        <form method="POST" action="{{ route('gym.profil.infos') }}" class="card">
            @csrf @method('PUT')

            <h2 style="font-size:1rem; font-weight:600; color:#fff; margin-bottom:1.25rem;">Informations générales</h2>

            <div class="mb-4">
                <label class="label">Nom de la salle</label>
                <input type="text" name="name" class="input" value="{{ old('name', $gym->name) }}" required>
                @error('name') <p style="color:#EF4444; font-size:0.75rem; margin-top:4px;">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="label">Adresse</label>
                <input type="text" name="address" class="input" value="{{ old('address', $gym->address) }}" required>
                @error('address') <p style="color:#EF4444; font-size:0.75rem; margin-top:4px;">{{ $message }}</p> @enderror
            </div>

            <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(min(180px,45%), 1fr)); gap:1rem;" class="mb-4">
                <div>
                    <label class="label">Téléphone</label>
                    <input type="text" name="phone" class="input" value="{{ old('phone', $gym->phone) }}" placeholder="+221 77 000 00 00">
                </div>
                <div>
                    <label class="label">WhatsApp</label>
                    <input type="text" name="phone_whatsapp" class="input" value="{{ old('phone_whatsapp', $gym->phone_whatsapp) }}" placeholder="+221 77 000 00 00">
                </div>
            </div>

            <div class="mb-6">
                <label class="label">Description</label>
                <textarea name="description" class="input" rows="5"
                          placeholder="Décrivez votre salle : équipements, ambiance, spécialités…">{{ old('description', $gym->description) }}</textarea>
                @error('description') <p style="color:#EF4444; font-size:0.75rem; margin-top:4px;">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="btn-primary" style="width:100%;">Enregistrer les informations</button>
        </form>
    </div>

    {{-- ══════════════════════════════════════════════ --}}
    {{-- TAB : HORAIRES --}}
    {{-- ══════════════════════════════════════════════ --}}
    <div x-show="tab === 'horaires'" x-cloak
         x-data="hoursEditor({{ json_encode($gym->opening_hours ?? []) }})">

        <form method="POST" action="{{ route('gym.profil.horaires') }}" class="card"
              @submit="prepareSubmit">
            @csrf @method('PUT')
            <input type="hidden" name="opening_hours" x-ref="hoursInput">

            <h2 style="font-size:1rem; font-weight:600; color:#fff; margin-bottom:0.25rem;">Horaires d'ouverture</h2>
            <p style="font-size:0.8rem; color: var(--color-text-muted); margin-bottom:1.25rem;">
                Définissez vos horaires jour par jour. Cochez "Fermé" pour les jours de repos.
            </p>

            <template x-for="day in days" :key="day.key">
                <div class="hours-row">
                    <span x-text="day.label" style="font-size:0.85rem; color:#ccc;"></span>
                    <input type="time"
                           :value="hours[day.key]?.open || '06:00'"
                           @change="hours[day.key] = { ...hours[day.key], open: $event.target.value }"
                           :disabled="hours[day.key]?.closed"
                           style="background:rgba(255,255,255,0.05); border:1px solid rgba(255,59,59,0.2); border-radius:6px; padding:5px 8px; color:#fff; font-size:0.85rem; width:100%;">
                    <input type="time"
                           :value="hours[day.key]?.close || '22:00'"
                           @change="hours[day.key] = { ...hours[day.key], close: $event.target.value }"
                           :disabled="hours[day.key]?.closed"
                           style="background:rgba(255,255,255,0.05); border:1px solid rgba(255,59,59,0.2); border-radius:6px; padding:5px 8px; color:#fff; font-size:0.85rem; width:100%;">
                    <label style="display:flex; align-items:center; gap:4px; font-size:0.8rem; color:#EF4444; cursor:pointer;">
                        <input type="checkbox"
                               :checked="hours[day.key]?.closed"
                               @change="hours[day.key] = { ...hours[day.key], closed: $event.target.checked }"
                               class="accent-red-500">
                        Fermé
                    </label>
                </div>
            </template>

            <div style="margin-top:1.5rem;">
                <button type="submit" class="btn-primary" style="width:100%;">Enregistrer les horaires</button>
            </div>
        </form>
    </div>

    {{-- ══════════════════════════════════════════════ --}}
    {{-- TAB : ACTIVITÉS --}}
    {{-- ══════════════════════════════════════════════ --}}
    <div x-show="tab === 'activites'" x-cloak>
        <form method="POST" action="{{ route('gym.profil.activites') }}" class="card">
            @csrf @method('PUT')

            <h2 style="font-size:1rem; font-weight:600; color:#fff; margin-bottom:0.25rem;">Activités proposées</h2>
            <p style="font-size:0.8rem; color: var(--color-text-muted); margin-bottom:1.25rem;">
                Ces activités aident les membres à trouver votre salle selon leurs préférences.
            </p>

            @if($allActivities->isEmpty())
                <p style="color: var(--color-text-muted); font-size:0.85rem;">Aucune activité disponible pour le moment.</p>
            @else
                <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(min(180px, 45%), 1fr)); gap:0.75rem; margin-bottom:1.5rem;">
                    @foreach($allActivities as $activity)
                        <label style="display:flex; align-items:center; gap:10px; cursor:pointer; padding:10px; background:rgba(255,255,255,0.03); border:1px solid rgba(255,59,59,0.1); border-radius:8px; transition:border-color 0.2s;"
                               onmouseover="this.style.borderColor='rgba(255,59,59,0.35)'"
                               onmouseout="this.style.borderColor='rgba(255,59,59,0.1)'">
                            <input type="checkbox"
                                   name="activity_ids[]"
                                   value="{{ $activity->id }}"
                                   class="accent-red-500"
                                   {{ $gym->gymActivities->contains($activity->id) ? 'checked' : '' }}>
                            <span style="font-size:1.1rem;">{{ $activity->icon }}</span>
                            <span style="font-size:0.85rem; color:#ccc;">{{ $activity->name }}</span>
                        </label>
                    @endforeach
                </div>
            @endif

            <button type="submit" class="btn-primary" style="width:100%;">Enregistrer les activités</button>
        </form>
    </div>

    {{-- ══════════════════════════════════════════════ --}}
    {{-- TAB : PROGRAMMES --}}
    {{-- ══════════════════════════════════════════════ --}}
    <div x-show="tab === 'programmes'" x-cloak>

        {{-- Formulaire ajout --}}
        <div class="card" style="margin-bottom:1.5rem;">
            <h2 style="font-size:1rem; font-weight:600; color:#fff; margin-bottom:1.25rem;">Ajouter un programme</h2>
            <form method="POST" action="{{ route('gym.profil.programmes.store') }}">
                @csrf

                <div class="mb-4">
                    <label class="label">Nom du programme</label>
                    <input type="text" name="name" class="input" placeholder="Yoga du matin, HIIT cardio…" required>
                    @error('name') <p style="color:#EF4444; font-size:0.75rem; margin-top:4px;">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="label">Description (optionnel)</label>
                    <textarea name="description" class="input" rows="3" placeholder="Décrivez brièvement le programme…"></textarea>
                </div>

                <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(min(140px,45%), 1fr)); gap:1rem;" class="mb-4">
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
                        <input type="checkbox" name="is_active" value="1" checked class="accent-red-500">
                        Actif immédiatement
                    </label>
                </div>

                <button type="submit" class="btn-primary" style="width:100%;">Ajouter le programme</button>
            </form>
        </div>

        {{-- Liste des programmes --}}
        <h2 style="font-size:0.85rem; font-weight:600; color: var(--color-text-muted); text-transform:uppercase; letter-spacing:0.08em; margin-bottom:0.75rem;">
            Programmes existants ({{ $gym->programs->count() }})
        </h2>

        @forelse($gym->programs as $program)
            <div class="program-card" x-data="{ editing: false }">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:0.5rem;">
                    <div style="flex:1; min-width:0;">
                        <div style="display:flex; align-items:center; gap:0.5rem; flex-wrap:wrap; margin-bottom:2px;">
                            <strong style="color:#fff; font-size:0.9rem;">{{ $program->name }}</strong>
                            @if($program->is_active)
                                <span style="font-size:0.65rem; color:#22C55E; border:1px solid rgba(34,197,94,0.3); padding:1px 6px; border-radius:999px;">Actif</span>
                            @else
                                <span style="font-size:0.65rem; color:#8888A0; border:1px solid rgba(255,255,255,0.1); padding:1px 6px; border-radius:999px;">Inactif</span>
                            @endif
                        </div>
                        <span style="font-size:0.75rem; color: var(--color-text-muted);">
                            {{ $program->duration_minutes }} min
                            @if($program->max_spots) · {{ $program->max_spots }} places @endif
                        </span>
                        @if($program->description)
                            <p style="font-size:0.78rem; color: var(--color-text-muted); margin-top:4px;">{{ $program->description }}</p>
                        @endif
                    </div>
                    <div style="display:flex; gap:4px; flex-shrink:0;">
                        <button @click="editing = !editing"
                                style="padding:4px 10px; font-size:0.75rem; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); border-radius:6px; color:#ccc; cursor:pointer;">
                            Modifier
                        </button>
                        <form method="POST" action="{{ route('gym.profil.programmes.destroy', $program) }}"
                              onsubmit="return confirm('Supprimer ce programme ?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    style="padding:4px 8px; font-size:0.75rem; background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.3); border-radius:6px; color:#EF4444; cursor:pointer;">
                                ✕
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Formulaire édition inline --}}
                <div x-show="editing" x-cloak
                     style="margin-top:0.75rem; padding-top:0.75rem; border-top:1px solid rgba(255,255,255,0.07);">
                    <form method="POST" action="{{ route('gym.profil.programmes.update', $program) }}">
                        @csrf @method('PUT')
                        <div class="mb-3">
                            <input type="text" name="name" class="input" value="{{ $program->name }}" required
                                   style="font-size:0.85rem; padding:7px 10px;">
                        </div>
                        <div class="mb-3">
                            <textarea name="description" class="input" rows="2"
                                      style="font-size:0.85rem; padding:7px 10px;">{{ $program->description }}</textarea>
                        </div>
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.5rem;" class="mb-3">
                            <input type="number" name="duration_minutes" class="input"
                                   value="{{ $program->duration_minutes }}" min="15" max="300" required
                                   style="font-size:0.85rem; padding:7px 10px;">
                            <input type="number" name="max_spots" class="input"
                                   value="{{ $program->max_spots }}" placeholder="Illimité" min="1" max="500"
                                   style="font-size:0.85rem; padding:7px 10px;">
                        </div>
                        <div class="mb-3">
                            <label style="display:flex; align-items:center; gap:8px; font-size:0.82rem; color:#ccc; cursor:pointer;">
                                <input type="checkbox" name="is_active" value="1"
                                       {{ $program->is_active ? 'checked' : '' }} class="accent-red-500">
                                Actif
                            </label>
                        </div>
                        <button type="submit" class="btn-primary" style="width:100%; padding:7px; font-size:0.82rem;">
                            Enregistrer
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="empty-state" style="padding:2rem 1rem;">
                <div class="empty-state-icon">📋</div>
                <p class="empty-state-text">Aucun programme pour l'instant</p>
                <p style="font-size:0.8rem; color: var(--color-text-muted); margin-top:0.5rem;">
                    Ajoutez un programme ci-dessus pour qu'il apparaisse sur le profil de votre salle.
                </p>
            </div>
        @endforelse

    </div>

</div>{{-- /x-data tabs --}}

@endsection

@push('scripts')
<script>
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
