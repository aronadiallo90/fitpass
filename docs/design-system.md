# Design System — FitPass Dakar
Version : 1.1 (Sprint 3)
Dernière mise à jour : 2026-03-30
Fichier source : `resources/css/app.css`

---

## Couleurs

| Variable CSS | Hex | Usage |
|-------------|-----|-------|
| `--color-primary` | `#FF3B3B` | Rouge FitPass — CTAs, accents, logo |
| `--color-primary-dark` | `#CC2222` | Hover bouton primaire |
| `--color-primary-light` | `#FF6B6B` | Accents légers |
| `--color-secondary` | `#FF8C00` | Orange — dynamisme, secondaire |
| `--color-bg` | `#0A0A0F` | Fond principal de toutes les pages |
| `--color-bg-soft` | `#13131A` | Fond navbar, sidebar, sections |
| `--color-bg-card` | `#1A1A24` | Fond cards, tableaux, inputs |
| `--color-text` | `#FFFFFF` | Texte principal |
| `--color-text-muted` | `#8888A0` | Labels, sous-titres, placeholders |
| `--color-border` | `#2A2A38` | Bordures par défaut |
| `--color-success` | `#22C55E` | Statut actif, succès, scan valide |
| `--color-warning` | `#F59E0B` | Statut en attente, avertissement |
| `--color-danger` | `#EF4444` | Erreur, statut expiré, scan refusé |

**Règle absolue :** Jamais de hex hardcodé dans les templates Blade — toujours `var(--color-*)`.

---

## Typographie

| Variable | Font | Usage |
|----------|------|-------|
| `--font-heading` | Barlow Condensed (700) | Tous les titres `h1`–`h4`, `.section-title`, `.page-title`, `.kpi-value` |
| `--font-sans` | Inter (400/500/600) | Corps de texte, labels, boutons |

**Import Google Fonts (dans chaque layout) :**
```html
<link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
```

---

## Composants — référence complète

### Boutons

```html
<!-- Primaire (CTA principal) -->
<button class="btn-primary">S'abonner</button>
<button class="btn-primary" disabled>Chargement...</button>

<!-- Outline (action secondaire) -->
<button class="btn-outline">Voir les plans</button>

<!-- Ghost (action tertiaire, nav) -->
<button class="btn-ghost">Déconnexion</button>
```

### Cards

```html
<!-- Card interactive (hover effect) -->
<div class="card">Contenu avec hover border rouge</div>

<!-- Card statique (pas de hover) -->
<div class="card-static">Formulaire, recap, info fixe</div>

<!-- KPI card (dashboards) -->
<div class="kpi-card">
    <div class="kpi-value">1 247</div>
    <div class="kpi-label">Membres actifs</div>
    <div class="kpi-trend kpi-trend-up">+12% ce mois</div>
</div>
```

### Stat card (dashboard)

```html
<div class="stat-card">
    <div class="stat-value">25 000 FCFA</div>
    <div class="stat-label">Revenus du mois</div>
</div>
```

### Formulaires

```html
<div style="margin-bottom: 1.25rem;">
    <label class="label">Téléphone</label>
    <input type="tel" class="input" placeholder="+221 77 123 45 67">
</div>
```

### Badges de statut

```html
<span class="badge badge-active">Actif</span>
<span class="badge badge-pending">En attente</span>
<span class="badge badge-expired">Expiré</span>
<span class="badge badge-cancelled">Annulé</span>
<span class="badge badge-valid">Valide</span>
<span class="badge badge-invalid">Invalide</span>
<span class="badge badge-failed">Échoué</span>
```

### Alertes

```html
<div class="alert-success">Abonnement activé avec succès.</div>
<div class="alert-error">Paiement échoué. Réessayez.</div>
<div class="alert-warning">Votre abonnement expire dans 7 jours.</div>
<div class="alert-info">QR code mis à jour.</div>
```

### Navigation

```html
<a href="/dashboard" class="nav-link">Dashboard</a>
<a href="/qrcode" class="nav-link-active">Mon QR Code</a>
```

### En-tête de page

```html
<div class="page-header">
    <h1 class="page-title">Dashboard</h1>
    <button class="btn-primary">Nouvelle action</button>
</div>
```

### Tableau de données

```html
<div class="card-static" style="padding: 0; overflow: hidden;">
    <table class="data-table">
        <thead>
            <tr>
                <th>Membre</th>
                <th>Plan</th>
                <th>Statut</th>
                <th>Expiration</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Moussa Diop</td>
                <td>Mensuel</td>
                <td><span class="badge badge-active">Actif</span></td>
                <td style="color: var(--color-text-muted);">15 avril 2026</td>
            </tr>
        </tbody>
    </table>
</div>
```

### État vide

```html
<div class="empty-state">
    <div class="empty-state-icon">🏋️</div>
    <p class="empty-state-text">Aucune entrée aujourd'hui</p>
</div>
```

---

## Composants Sprint 3 — nouveaux

### QR Code — affichage membre

```html
<!-- Petit format (dashboard) -->
<div class="qr-wrapper">
    {!! $qrCode !!}
</div>

<!-- Grand format (page dédiée) -->
<div style="position: relative;">
    <div class="qr-wrapper-lg">
        {!! $qrCode !!}
    </div>
    <!-- Overlay si expiré -->
    @if($subscription?->status !== 'active')
    <div class="qr-expired-overlay">
        <span style="font-size: 3rem;">🔒</span>
        <p style="color: white; font-family: var(--font-heading); font-size: 1.25rem; text-transform: uppercase;">Abonnement expiré</p>
        <a href="{{ route('member.subscriptions') }}" class="btn-primary">Renouveler</a>
    </div>
    @endif
</div>
```

### Scanner QR — gym owner

```html
<!-- Cadre de scan -->
<div class="scan-frame">
    <video id="qr-video" style="width: 100%; height: 100%; object-fit: cover;"></video>
</div>

<!-- Résultat valide (Alpine.js x-show) -->
<div class="scan-result scan-result-valid" x-show="result === 'valid'" x-transition>
    <div class="scan-result-icon">✓</div>
    <div class="scan-result-name" x-text="memberName"></div>
    <div class="scan-result-status">Entrée validée</div>
    <button class="btn-outline" @click="reset()" style="border-color: white; color: white;">Scanner à nouveau</button>
</div>

<!-- Résultat invalide -->
<div class="scan-result scan-result-invalid" x-show="result === 'invalid'" x-transition>
    <div class="scan-result-icon">✗</div>
    <div class="scan-result-name">Accès refusé</div>
    <div class="scan-result-status" x-text="failureReason"></div>
    <button class="btn-outline" @click="reset()" style="border-color: white; color: white;">Scanner à nouveau</button>
</div>
```

### Carte Leaflet

```html
<div id="gym-map" class="map-container"></div>

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const map = L.map('gym-map').setView([14.7167, -17.4677], 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
// Marqueurs depuis API GeoJSON : GET /api/v1/gyms/geojson
</script>
@endpush
```

---

## Page header — structure standard

Toutes les pages dashboard suivent cette structure :

```html
@extends('layouts.app') {{-- ou admin / gym --}}
@section('content')

<div class="page-header">
    <h1 class="page-title">Titre de la page</h1>
    {{-- Action optionnelle --}}
</div>

{{-- Grille KPIs (si dashboard) --}}
<div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 2rem;">
    <div class="kpi-card">...</div>
    <div class="kpi-card">...</div>
</div>

{{-- Contenu principal --}}
<div class="card-static" style="padding: 0; overflow: hidden;">
    <table class="data-table">...</table>
</div>

@endsection
```

---

## Responsive — breakpoints

| Breakpoint | Largeur | Usage |
|-----------|---------|-------|
| Mobile (défaut) | 375px | Priorité absolue — concevoir d'abord |
| Tablette | 768px | Adapter grilles 1col → 2col |
| Desktop | 1280px | Layout sidebar + content |

```html
<!-- Grille responsive standard -->
<div style="display: grid; grid-template-columns: 1fr; gap: 1rem;">
    <!-- Mobile : 1 colonne -->
</div>
<!-- Tablette+ via style inline ou @media dans app.css -->
```

---

## Règles strictes

### À TOUJOURS faire
- `var(--color-*)` — jamais de hex inline
- Hover state sur **tous** les éléments cliquables
- `transition-all duration-300 ease-out` — toutes les interactions
- Mobile-first : `max-width: 375px` en premier dans la tête
- `class="label"` + `class="input"` — tous les formulaires
- `class="badge badge-*"` — tous les statuts

### À NE JAMAIS faire
- `text-gray-500`, `bg-blue-600` — couleurs Tailwind génériques
- `font-family: Arial` ou `Roboto`
- Card sans hover state (utiliser `.card` pas `.card-static` si interactif)
- Input sans focus ring (`class="input"` gère ça automatiquement)
- `border-2` — jamais (sauf exception documentée)
- Inline styles pour les couleurs (`color: #FF3B3B` → `class="text-primary"`)

---

## Utilitaires disponibles

```html
<p class="text-primary">Rouge FitPass</p>
<p class="text-muted">Texte secondaire</p>
<p class="text-success">Vert succès</p>
<p class="text-warning">Ambre avertissement</p>
<p class="text-danger">Rouge erreur</p>

<div class="bg-bg">Fond principal</div>
<div class="bg-bg-soft">Fond doux</div>
<div class="bg-bg-card">Fond card</div>

<h1 class="font-heading">Titre condensé sportif</h1>

<p class="text-gradient">Texte dégradé rouge → orange</p>
```
