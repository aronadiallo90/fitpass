# QA — Sprint 6B : PWA Installable FitPass
Date : 2026-03-31
Agent : QA
Statut : ✅ Code validé — tests manuels à compléter sur appareil réel

---

## Audit statique — Critères Lighthouse PWA

### Installabilité (Installable)

| Critère | Fichier | Statut |
|---------|---------|--------|
| `manifest.json` accessible | `public/manifest.json` | ✅ |
| `name` présent | `"name": "FitPass Dakar"` | ✅ |
| `short_name` présent | `"short_name": "FitPass"` | ✅ |
| `start_url` défini | `"/dashboard"` | ✅ |
| `display: standalone` | ✅ | ✅ |
| Icône 192×192 avec purpose `any` | `icon-192x192.png` | ✅ |
| Icône 512×512 avec purpose `maskable` | `icon-512x512.png` | ✅ |
| Service Worker enregistré | `app.js` → `navigator.serviceWorker.register('/sw.js')` | ✅ |
| SW intercepte les fetch | `sw.js` addEventListener fetch | ✅ |
| SW gère l'offline | fallback → `/offline` pré-caché | ✅ |

### PWA Optimisée (PWA Optimized)

| Critère | Layout | Statut |
|---------|--------|--------|
| `<meta name="viewport">` | app / admin / gym / landing | ✅ |
| `<title>` présent | app / admin / gym / landing | ✅ |
| `<meta name="description">` | app / admin / gym / landing | ✅ (fix appliqué) |
| `<meta name="theme-color">` `#FF3B3B` | app / admin / gym / landing | ✅ |
| `<link rel="manifest">` | app / admin / gym / landing | ✅ |
| `<link rel="apple-touch-icon">` | app / admin / gym / landing | ✅ |
| `apple-mobile-web-app-capable` | app / admin / gym / landing | ✅ |
| `apple-mobile-web-app-status-bar-style` | black-translucent | ✅ |
| `apple-mobile-web-app-title` | FitPass | ✅ |
| `<html lang="fr">` | app / admin / gym / landing | ✅ |
| Pages admin non-indexées | `noindex, nofollow` | ✅ (fix appliqué) |

### Service Worker — stratégies cache

| Route | Stratégie | Comportement offline |
|-------|-----------|----------------------|
| `/build/*` (Vite JS/CSS) | Cache-first | ✅ Disponible offline |
| `fonts.googleapis.com` | Cache-first | ✅ Après 1ère visite |
| `/icons/*`, `/manifest.json` | Cache-first | ✅ Pré-caché à l'install |
| Pages HTML Laravel | Network-first | ✅ Cache si déjà visité |
| `/offline` | Pré-caché install | ✅ Toujours disponible |
| `/admin/*` | Ignoré par SW | ✅ Jamais caché (sécurité) |
| POST/PUT/PATCH/DELETE | Ignoré par SW | ✅ Toujours réseau |

---

## Problèmes corrigés pendant l'audit

| # | Problème | Fix |
|---|---------|-----|
| 1 | `admin.blade.php` sans `<meta name="description">` | Ajouté + `noindex` |
| 2 | `gym.blade.php` sans `<meta name="description">` | Ajouté + `noindex` |

---

## Tests manuels à effectuer (sur appareil réel)

### Prérequis
- Serveur accessible en HTTPS (PWA requiert HTTPS — en local utiliser `php artisan serve` + ngrok ou Valet/Herd avec HTTPS)
- Chrome DevTools → Application → Manifest : vérifier que le manifest est lu sans erreur
- Chrome DevTools → Application → Service Workers : vérifier que sw.js est installé et activé

### Android Chrome

- [ ] Ouvrir `https://fitpass.sn` (ou URL HTTPS locale)
- [ ] Menu Chrome → "Ajouter à l'écran d'accueil" apparaît
- [ ] Icône FitPass visible sur l'écran d'accueil
- [ ] Lancement depuis l'icône : pas de barre d'adresse Chrome
- [ ] `theme_color` `#FF3B3B` visible dans la barre de statut
- [ ] Splash screen : fond `#0A0A0F` + icône 512×512
- [ ] Mode avion activé → page `/offline` s'affiche
- [ ] Retour réseau → bouton "Réessayer" recharge correctement

### iPhone Safari (iOS 16.4+)

- [ ] Ouvrir dans Safari (pas Chrome iOS)
- [ ] Icône Partager → "Sur l'écran d'accueil"
- [ ] Titre affiché : "FitPass" (short_name)
- [ ] Icône `apple-touch-icon.png` (180×180) visible
- [ ] Lancement : barre Safari masquée (standalone)
- [ ] `status-bar-style: black-translucent` → barre de statut transparente
- [ ] Mode avion → page `/offline` s'affiche

### Lighthouse (Chrome DevTools)

```
DevTools → Lighthouse → Mode: Navigation → Catégorie: Progressive Web App → Analyze
```

Cible : **score PWA ≥ 90**

Éléments Lighthouse attendus au vert :
- Installable : Has a `<link rel="manifest">` ✅
- Installable : Registers a service worker that controls page and start_url ✅
- PWA Optimized : Configured for a custom splash screen ✅
- PWA Optimized : Sets a theme color for address bar ✅
- PWA Optimized : Content is sized correctly for the viewport ✅
- PWA Optimized : Has a `<meta name="viewport">` ✅
- PWA Optimized : Provides a valid `apple-touch-icon` ✅
- PWA Optimized : Manifest has a maskable icon ✅

### Points d'attention Lighthouse connus

| Point | Explication |
|-------|-------------|
| `start_url` redirige vers `/login` | Normal — l'utilisateur non connecté est redirigé. Lighthouse peut signaler un avertissement non bloquant. |
| Google Fonts non cachées au premier audit | Normal — cache-first se construit à la première visite. |
| Score < 100 possible | PWA score 90+ est la cible. 100 requiert HTTPS + pas de redirects sur start_url. |

---

## Résultat

**Code :** 100% conforme aux critères Lighthouse PWA (audit statique)
**Tests manuels :** à effectuer dès HTTPS disponible (VPS ou ngrok)
**Régression :** 0 — 157/157 tests passants

### Definition of Done Sprint 6B

- [x] 157 tests — aucune régression
- [x] manifest.json valide (name, icons, standalone, theme_color)
- [x] Service Worker : cache-first assets + network-first pages + fallback /offline
- [x] Meta tags PWA dans 4 layouts (app, admin, gym, landing)
- [x] Page /offline branded + route GET /offline
- [x] `noindex` sur admin et gym (sécurité SEO)
- [ ] Lighthouse PWA ≥ 90 — **à valider sur HTTPS**
- [ ] Installable Android Chrome — **à valider sur appareil réel**
- [ ] Installable iPhone Safari — **à valider sur appareil réel**
