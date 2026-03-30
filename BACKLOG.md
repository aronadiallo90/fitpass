# BACKLOG — FitPass Dakar
Généré le : 2026-03-29 | Mis à jour : 2026-03-30
Sprint actuel : 6A — Recherche salles enrichie
Stack : Laravel 13 + MySQL 8 + Redis + Blade + Tailwind CSS v4 + Alpine.js

## État des sprints
| Sprint | Statut | Commit |
|--------|--------|--------|
| Sprint 0 — Kick-off | ✅ Terminé | — |
| Sprint 1 — Fondations | ✅ Terminé | `9c1e45d` |
| Sprint 2 — Core Métier | ✅ Terminé | `df78bb2` (52 tests) |
| Sprint 3 — Interfaces | ✅ Terminé | `f484be4` (102 tests) |
| Sprint 4 — Marketing | ✅ Terminé | `041bc9a` (118 tests) |
| Sprint 5 — Livraison | ✅ Terminé | `99d2030` (118 tests) |
| Sprint 6A — Recherche salles enrichie | 🔄 En cours | — |

---

## SPRINT 6A — Recherche salles enrichie (semaine 9)

### Contexte
Extension majeure du module Gym. Objectif : transformer la page salles en une vraie
expérience de découverte (recherche live, filtres, itinéraire, profil riche).

### Existant à réutiliser
- `gyms` table : `slug`, `activities` (JSON), `opening_hours` (JSON), `latitude/longitude` ✅
- Carte Leaflet déjà intégrée sur la landing et la page membre ✅
- GeoJSON endpoint `/api/v1/gyms/geojson` mis en cache Redis ✅
- `AdminGymController` CRUD existant ✅

### Nouvelles tables (migrations)
| Table | Description |
|-------|-------------|
| `gym_activities` | Tags activités normalisés (Muscu, Cardio, Yoga, etc.) |
| `gym_activity` | Pivot many-to-many gyms ↔ gym_activities |
| `gym_programs` | Cours collectifs : nom, horaire, durée, places_max |
| `gym_photos` | Galerie photos : url Cloudinary, ordre, is_cover |

### Nouveaux champs sur `gyms`
| Champ | Type | Description |
|-------|------|-------------|
| `phone_whatsapp` | string nullable | Numéro WhatsApp salle |
| `zone` | enum nullable | Plateau, Almadies, Mermoz, Parcelles, Guédiawaye, Thiès, Autre |
| `opening_hours` | JSON enrichi | `{lundi: {open:"06:00", close:"22:00", closed:false}, ...}` |

---

### Tâches séquentielles (ordre obligatoire)

| ID | User Story | Agent | Estim. | Dépend de |
|----|-----------|-------|--------|-----------|
| S6A-T1 | Migrations : gym_activities, gym_activity, gym_programs, gym_photos + alter gyms (zone, phone_whatsapp) | DEV | M | — |
| S6A-T2 | Models + Relations : Activity, GymProgram, GymPhoto + update Gym | DEV | M | S6A-T1 |
| S6A-T3 | GymSearchService : filtres nom/activité/zone + tri distance (Haversine) | DEV | L | S6A-T2 |
| S6A-T4 | API endpoints : GET /gyms/search + GET /gyms/{slug}/profile + GET /gyms/{slug}/programs | DEV | M | S6A-T3 |
| S6A-T5 | Page membre /gyms : barre recherche live + filtres activité/zone + "Près de moi" + carte mise à jour | DESIGNER+DEV | L | S6A-T4 |
| S6A-T6 | Page publique /gyms/{slug} : galerie, horaires, cours, tags, mini-carte, boutons WhatsApp/Maps | DESIGNER+DEV | L | S6A-T4 |
| S6A-T7 | Leaflet Routing Machine : itinéraire depuis position membre vers salle | DEV | M | S6A-T5, S6A-T6 |
| S6A-T8 | Admin /admin/gyms/{id}/edit enrichi : horaires, programmes, photos Cloudinary, tags activités | DESIGNER+DEV | L | S6A-T2 |
| S6A-T9 | Tests Feature + Unit : GymSearchService, API search/profil, filtres, admin | QA | M | S6A-T7, S6A-T8 |

### Tâches parallèles (peuvent avancer en même temps)

| ID | User Story | Agent | Estim. |
|----|-----------|-------|--------|
| S6A-P1 | Factory GymActivity + GymProgram + GymPhoto + seeders activités Dakar | DEV | S |
| S6A-P2 | Config Cloudinary : package + .env + GymPhotoService | DEV | S |
| S6A-P3 | Resource API enrichie : GymProfileResource (activités, programmes, photos, horaires) | DEV | M |

---

### Détail des user stories

**S6A-T1 — Migrations**
```
En tant que DEV, je veux 4 nouvelles migrations + 1 alter,
afin de stocker activités normalisées, programmes, photos et zone géo.

Critères d'acceptation :
- [ ] Table gym_activities : id, name (unique), slug, icon, timestamps
- [ ] Table gym_activity (pivot) : gym_id, gym_activity_id
- [ ] Table gym_programs : id, gym_id, name, description, schedule (JSON), duration_minutes, max_spots, is_active
- [ ] Table gym_photos : id, gym_id, cloudinary_url, cloudinary_public_id, display_order, is_cover
- [ ] ALTER gyms : +zone (enum), +phone_whatsapp
- [ ] php artisan migrate:rollback sans erreur
Estimation : M | Agent : DEV
```

**S6A-T3 — GymSearchService**
```
En tant que membre, je veux filtrer les salles par nom/activité/zone/distance,
afin de trouver rapidement la salle qui me convient.

Critères d'acceptation :
- [ ] Recherche par nom (LIKE, insensible casse)
- [ ] Filtre par activité (many-to-many via gym_activities)
- [ ] Filtre par zone (enum sur gyms.zone)
- [ ] Tri par distance si lat/lng fournis (formule Haversine en SQL)
- [ ] Résultats paginés (15/page)
- [ ] Cache Redis 5 min sur requêtes fréquentes
- [ ] Interface GymSearchServiceInterface respectée
Estimation : L | Agent : DEV
```

**S6A-T5 — Page /gyms membre**
```
En tant que membre authentifié, je veux une page de recherche des salles,
afin de trouver et explorer toutes les salles FitPass à Dakar.

Critères d'acceptation :
- [ ] Barre recherche live Alpine.js (debounce 300ms)
- [ ] Filtres activité : Muscu, Cardio, Yoga, Spinning, CrossFit, Natation, Boxe
- [ ] Filtres zone : Plateau, Almadies, Mermoz, Parcelles, Guédiawaye, Thiès, Autre
- [ ] Bouton "Près de moi" → Geolocation API browser → tri par distance
- [ ] Carte Leaflet synchronisée avec les filtres actifs
- [ ] Clic marqueur → popup avec nom + bouton "Voir la fiche"
- [ ] Liste résultats sous la carte (cards salles)
- [ ] Design cohérent avec le reste de l'app (dark theme, rouge FitPass)
Estimation : L | Agent : DESIGNER+DEV
```

**S6A-T6 — Page /gyms/{slug}**
```
En tant que membre, je veux une fiche complète de chaque salle,
afin d'évaluer la salle avant de m'y rendre.

Critères d'acceptation :
- [ ] Galerie photos : cover en header + carousel si plusieurs photos
- [ ] Badge Ouvert/Fermé calculé en temps réel (heure locale Africa/Dakar)
- [ ] Horaires d'ouverture par jour avec highlight du jour courant
- [ ] Liste cours collectifs : nom, horaire semaine, durée, places
- [ ] Tags activités cliquables (filtre sur /gyms)
- [ ] Mini-carte Leaflet centrée sur la salle (pas de routing ici)
- [ ] Bouton "Itinéraire" → Leaflet Routing depuis ma position
- [ ] Bouton "Google Maps" → https://maps.google.com/?q=LAT,LNG
- [ ] Bouton "WhatsApp" → https://wa.me/221XXXXXXX (si phone_whatsapp renseigné)
Estimation : L | Agent : DESIGNER+DEV
```

**S6A-T7 — Leaflet Routing Machine**
```
En tant que membre, je veux voir l'itinéraire pied/voiture vers une salle,
afin de savoir comment m'y rendre depuis ma position.

Critères d'acceptation :
- [ ] Leaflet Routing Machine chargé via CDN (OSRM public — gratuit)
- [ ] Bouton "Itinéraire" demande la géolocalisation browser
- [ ] Trace le chemin depuis position actuelle → salle sur la carte
- [ ] Affiche la distance et le temps estimé
- [ ] Fallback : si géolocalisation refusée → message clair
Estimation : M | Agent : DEV
```

**S6A-T8 — Admin enrichi**
```
En tant qu'admin, je veux gérer complètement le profil d'une salle,
afin que les membres aient des informations riches et à jour.

Critères d'acceptation :
- [ ] Horaires : toggle Ouvert/Fermé par jour + plages horaires (Alpine.js)
- [ ] CRUD programmes/cours : ajouter, modifier, supprimer, activer/désactiver
- [ ] Upload photos : Cloudinary, drag-and-drop, définir la cover, réordonner
- [ ] Tags activités : sélecteur multi-choix depuis gym_activities
- [ ] Champ WhatsApp avec formatage +221XXXXXXXXX
- [ ] Zone Dakar : sélecteur dropdown
Estimation : L | Agent : DESIGNER+DEV
```

---

### Definition of Done Sprint 6A

- [ ] `php artisan migrate` sans erreur + rollback OK
- [ ] `php artisan test` → 0 erreur (cible : 140+ tests)
- [ ] Page /gyms : recherche live fonctionnelle + filtres + carte synchronisée
- [ ] Page /gyms/{slug} : badge Ouvert/Fermé correct + galerie + itinéraire
- [ ] Leaflet Routing Machine : itinéraire tracé depuis géolocalisation
- [ ] Admin : horaires + programmes + photos Cloudinary opérationnels
- [ ] Cache Redis actif sur GeoJSON et search
- [ ] Design cohérent dark theme + responsive mobile 375px
- [ ] `git commit` après chaque tâche terminée

---

### Stack spécifique Sprint 6A

| Outil | Usage | Notes |
|-------|-------|-------|
| Leaflet Routing Machine | Itinéraire OSRM | CDN : `leaflet-routing-machine.js` |
| Cloudinary SDK PHP | Upload photos | `cloudinary/cloudinary_php` |
| Alpine.js | Recherche live + filtres | Déjà installé |
| Redis | Cache search + GeoJSON | Déjà configuré |
| Haversine SQL | Distance membre → salle | Implémenté dans GymSearchService |

---

## Résumé exécutif Sprint 0

**Opportunité :** Marché vierge — aucun agrégateur multi-salles n'existe à Dakar.
ClassPass et Urban Sports Club sont absents du Sénégal. Les salles locales
n'ont pas d'infrastructure digitale. FitPass arrive en pionnier.

**Différenciateur N°1 :** Paiement Wave/Orange Money natif (0 concurrent global ne supporte).
**Différenciateur N°2 :** QR code entrée salle simple et instantané.
**Différenciateur N°3 :** Prix FCFA accessible (vs USD/EUR inaccessibles localement).

**Risque principal :** Lancer avec < 10 salles partenaires — invalide la proposition de valeur.
**Action immédiate :** Signer les partenariats salles EN PARALLÈLE du développement.

---

## SPRINT 1 — Fondations (semaine 1-2)

### Tâches séquentielles (ordre obligatoire)

| ID | User Story | Agent | Estim. | Dépend de |
|----|-----------|-------|--------|-----------|
| S1-T1 | ~~Créer le projet Laravel 11 + config `.env` + Git init~~ | DEV | S | — | ✅ |
| S1-T2 | ~~Migrations DB : users, subscription_plans, subscriptions, payments, gyms, gym_checkins, sms_logs~~ | DEV | M | S1-T1 | ✅ |
| S1-T3 | ~~Models Eloquent + relations + casts (prix integer FCFA)~~ | DEV | M | S1-T2 | ✅ |
| S1-T4 | ~~Factories + Seeders (plans tarifaires, salles test, users)~~ | DEV | M | S1-T3 | ✅ |
| S1-T5 | ~~Auth multi-rôles : register/login/logout (Sanctum API + sessions Web)~~ | DEV | L | S1-T3 | ✅ |
| S1-T6 | ~~Middleware rôles : `role:member`, `role:gym_owner`, `role:admin`~~ | SECURITY | S | S1-T5 | ✅ |
| S1-T7 | ~~Rate limiting : api (60/min), auth (10/min), admin (30/min)~~ | SECURITY | S | S1-T5 | ✅ |
| S1-T8 | ~~2FA obligatoire pour admin + super_admin~~ | SECURITY | M | S1-T5 | ✅ |

### Tâches parallèles (peuvent se faire en même temps)

| ID | User Story | Agent | Estim. | Statut |
|----|-----------|-------|--------|--------|
| S1-P1 | ~~Config Tailwind : couleurs FitPass (rouge #FF3B3B, fond #0A0A0F) + Google Fonts~~ | DESIGNER | S | ✅ |
| S1-P2 | ~~Layout Blade : `app.blade.php` (member), `admin.blade.php`, `gym.blade.php`~~ | DESIGNER | M | ✅ |
| S1-P3 | ~~Composants Blade : bouton primaire, card, badge statut, input formulaire~~ | DESIGNER | M | ✅ |
| S1-P4 | ~~Headers sécurité HTTP (Middleware SecurityHeaders)~~ | SECURITY | S | ✅ |
| S1-P5 | ~~Config `.gitignore` + `.env.example` + README install~~ | DEV | S | ✅ |

### Definition of Done Sprint 1
- [x] `php artisan test` → 0 erreur
- [x] Migrations rollback/re-run sans erreur
- [x] Auth : register, login, logout fonctionnels en Feature test
- [x] Rôles : 401 sans token, 403 mauvais rôle (tests écrits)
- [x] 2FA admin opérationnel
- [x] Design system Tailwind appliqué (pas de couleurs Tailwind génériques)
- [x] `git commit` avec message conventionnel (`9c1e45d`)

---

## SPRINT 2 — Core Métier (semaine 3-4)

### Tâches séquentielles

| ID | User Story | Agent | Estim. | Dépend de |
|----|-----------|-------|--------|-----------|
| S2-T1 | ~~`SubscriptionPlanRepository` + `SubscriptionPlanService`~~ | DEV | S | S1-T3 | ✅ |
| S2-T2 | ~~`SubscriptionService` : create, renew, expire, cancel~~ | DEV | L | S2-T1 | ✅ |
| S2-T3 | ~~`PaymentService` : initiate (PayTech), process webhook, validate HMAC~~ | DEV | L | S2-T2 | ✅ (FakePaymentService — sandbox PayTech requis) |
| S2-T4 | ~~Webhook PayTech : POST `/api/v1/webhooks/paytech` + job Redis async~~ | DEV | M | S2-T3 | ✅ |
| S2-T5 | ~~`CheckinService` : validate (QR + abonnement + anti-doublon)~~ | DEV | L | S2-T2 | ✅ |
| S2-T6 | ~~API endpoints abonnements + paiements + checkins (Resources + Form Requests)~~ | DEV | L | S2-T3, S2-T5 | ✅ |
| S2-T7 | ~~`SmsService` (Twilio) : send, queue async, log dans `sms_logs`~~ | DEV | M | S1-T3 | ✅ (FakeSmsService — credentials Twilio requis) |
| S2-T8 | ~~Cron jobs : expirer abonnements + rappels SMS J-7/J-1~~ | DEV | M | S2-T7 | ✅ |

### Tâches parallèles

| ID | User Story | Agent | Estim. | Statut |
|----|-----------|-------|--------|--------|
| S2-P1 | ~~Unit tests : SubscriptionService (happy path, erreurs, transitions statuts)~~ | QA | M | ✅ |
| S2-P2 | ~~Unit tests : CheckinService (valide, expiré, doublon, plan découverte)~~ | QA | M | ✅ |
| S2-P3 | ~~Feature tests API : abonnements (201, 401, 403, 422)~~ | QA | M | ✅ |
| S2-P4 | ~~Feature tests API : webhook PayTech (HMAC valide/invalide, idempotence)~~ | QA | M | ✅ |
| S2-P5 | Interfaces Blade : page plans, récapitulatif paiement, retour PayTech | DESIGNER | M | ⏳ (Sprint 3) |

### Definition of Done Sprint 2
- [x] `php artisan test` → 0 erreur (52 tests passants — commit `df78bb2`)
- [ ] Souscription end-to-end testée en sandbox PayTech *(bloqué : clé API manquante)*
- [x] Webhook PayTech : HMAC validé, idempotence OK, activation abonnement OK
- [ ] SMS Twilio envoyé à l'activation *(bloqué : credentials Twilio manquants)*
- [x] Checkin : valide / expiré / doublon gérés correctement
- [x] Plan Découverte : décompte séances fonctionnel
- [x] Cron expiration abonnements testé manuellement
- [ ] 0 requête N+1 *(à vérifier avec Laravel Debugbar en Sprint 3)*

---

## Bilan DESIGNER Sprint 1-2 (audit 2026-03-30)

### ✅ Livré correctement
- `resources/css/app.css` — design system complet (variables, composants Tailwind v4)
- `resources/views/layouts/` — app, admin, gym
- `resources/views/auth/` — login, register, 2FA setup/challenge

### ❌ Manquant (S2-P5 non livré + vues référencées dans web.php inexistantes)
Ces vues sont déclarées dans `routes/web.php` mais n'existent pas :

| Vue | Route | À créer Sprint 3 |
|-----|-------|-----------------|
| `member/subscriptions.blade.php` | GET /dashboard/subscriptions | S3 (S2-P5 reporté) |
| `member/payments.blade.php` | GET /dashboard/payments | S3 (S2-P5 reporté) |
| `member/qrcode.blade.php` | GET /dashboard/my-qrcode | S3-T2 |
| `member/checkins.blade.php` | GET /dashboard/checkins | S3 |
| `gym/scan.blade.php` | GET /gym/scan | S3-T3 |
| `gym/checkins.blade.php` | GET /gym/checkins | S3-T8 |
| `admin/members.blade.php` | GET /admin/members | S3-T6 |
| `admin/gyms.blade.php` | GET /admin/gyms | S3-T7 |

> Les dashboards (member, admin, gym) sont des stubs intentionnels — à remplir en Sprint 3.

---

## SPRINT 3 — Interfaces (semaine 5-6)

### Tâches séquentielles

| ID | User Story | Agent | Estim. | Dépend de |
|----|-----------|-------|--------|-----------|
| S3-T1 | Dashboard membre : abonnement actif, QR code, dernière entrée | DESIGNER+DEV | L | S2-T6 |
| S3-T2 | Page QR code membre : affichage grand format, état valide/expiré | DESIGNER+DEV | M | S2-T5 |
| S3-T3 | Interface scan gym_owner : camera, résultat vert/rouge | DESIGNER+DEV | L | S2-T5 |
| S3-T4 | Carte Leaflet : salles partenaires + popups + filtres activités | DESIGNER+DEV | L | S1-T3 |
| S3-T5 | API GeoJSON salles (mis en cache Redis 1h) | DEV | S | S3-T4 |
| S3-T6 | Dashboard admin : membres, revenus, abonnements, salles | DESIGNER+DEV | L | S2-T6 |
| S3-T7 | CRUD admin salles : coordonnées GPS + sélecteur carte | DESIGNER+DEV | M | S3-T4 |
| S3-T8 | Dashboard gym_owner : checkins du jour, stats fréquentation | DESIGNER+DEV | M | S2-T5 |

### Tâches parallèles

| ID | User Story | Agent | Estim. |
|----|-----------|-------|--------|
| S3-P1 | Tests E2E recette mobile (375px) : parcours souscription complet | QA | M |
| S3-P2 | Tests E2E recette mobile : scan QR code (accès autorisé + refusé) | QA | M |
| S3-P3 | Performance : PageSpeed mobile > 90, réponse API < 300ms | QA | M |
| S3-P4 | API publique bornes scan : POST `/api/v1/checkins/validate` + token statique | DEV | M |

### Definition of Done Sprint 3
- [x] Parcours membre complet (s'abonner → QR code → entrer en salle) fonctionnel
- [x] Scanner QR : retour visuel immédiat (valide/invalide)
- [x] Carte Leaflet : salles visibles, filtres activités opérationnels
- [x] Dashboards admin + gym_owner opérationnels
- [x] 102 tests — 100% passants (commit `f484be4`)
- [x] Simulation paiement local (boutons DEV ✓/✗)
- [x] Bug expires_at corrigé (whereDate vs datetime)

---

## SPRINT 4 — Marketing (semaine 7)

### Tâches

| ID | User Story | Agent | Estim. | Dépend de |
|----|-----------|-------|--------|-----------|
| S4-T1 | Landing page fitpass.sn : hero, plans, carte, témoignages, CTA | DESIGNER+DEV | L | S3-T4 |
| S4-T2 | SEO : meta tags, OG, sitemap.xml, robots.txt | DEV | S | S4-T1 |
| S4-T3 | Plan de lancement : campagne Instagram + TikTok + WhatsApp | MARKETING | M | — |
| S4-T4 | Séquence SMS onboarding Twilio (bienvenue + rappels) | MARKETING+DEV | M | S2-T7 |
| S4-T5 | Audit SEO + content gaps | MARKETING | M | S4-T1 |

### Definition of Done Sprint 4
- [ ] Landing page deployée sur fitpass.sn
- [ ] PageSpeed landing > 90 mobile
- [ ] SEO : title, meta description, OG sur toutes les pages clés
- [ ] Plan de lancement validé par le client

---

## SPRINT 5 — Livraison (semaine 8)

### Tâches séquentielles

| ID | User Story | Agent | Estim. | Dépend de |
|----|-----------|-------|--------|-----------|
| S5-T1 | Audit sécurité complet (checklist OWASP) | SECURITY | M | Tout |
| S5-T2 | `composer audit` + `npm audit` → 0 vulnérabilité critique | SECURITY | S | S5-T1 |
| S5-T3 | GitHub Actions CI/CD : test + deploy VPS DigitalOcean Ubuntu 24 | CICD | L | S5-T1 |
| S5-T4 | Config prod : APP_DEBUG=false, HTTPS, cookies sécurisés | CICD | S | S5-T3 |
| S5-T5 | Recette finale complète (tous les parcours) | QA | M | S5-T3 |
| S5-T6 | Rapport client stakeholder (métriques, livraison, prochaines étapes) | PM | M | S5-T5 |

### Definition of Done Sprint 5 (= Definition of Done Projet)
- [ ] `php artisan test` → 0 erreur en CI
- [ ] Deploy automatique sur push `main` → VPS
- [ ] APP_DEBUG=false vérifié en production
- [ ] Checklist sécurité OWASP complète (cf. `.claude/rules/security.md`)
- [ ] Recette QA : tous les parcours validés
- [ ] 10+ salles partenaires actives sur la carte
- [ ] Rapport client livré

---

## Backlog futur (post-V1)

| Feature | Priorité | Justification |
|---------|---------|---------------|
| Module B2B corporate (avantages salariés) | Haute | Segment inexploité, grandes entreprises Dakar |
| Notifications WhatsApp (WATI API) | Haute | Canal principal au Sénégal (> email) |
| Géolocalisation "salles près de moi" | Moyenne | UX mobile améliorée |
| Régénération QR code par le membre | Moyenne | Sécurité si QR compromis |
| QR code dynamique (change à chaque scan) | Basse | Anti-partage avancé |
| Réservation de créneaux horaires | Moyenne | Salles avec cours collectifs |
| Notation et avis membres | Basse | Social proof |
| App mobile native (React Native / Flutter) | Haute | Post-V1 si traction confirmée |
| Cours on-demand vidéo | Basse | Différenciation ClassPass-like |
| Extension CEDEAO (Abidjan, Bamako) | Haute | Post-V1, traction Dakar confirmée |

---

## Estimations globales

| Sprint | Durée | Points | Agents |
|--------|-------|--------|--------|
| Sprint 0 (Kick-off) | 1 jour | — | PM |
| Sprint 1 (Fondations) | 2 semaines | 13 tâches | DEV, DESIGNER, SECURITY |
| Sprint 2 (Core métier) | 2 semaines | 12 tâches | DEV, QA, DESIGNER |
| Sprint 3 (Interfaces) | 2 semaines | 12 tâches | DEV, DESIGNER, QA |
| Sprint 4 (Marketing) | 1 semaine | 5 tâches | DESIGNER, DEV, MARKETING |
| Sprint 5 (Livraison) | 1 semaine | 6 tâches | SECURITY, CICD, QA, PM |
| **TOTAL** | **~8 semaines** | **48 tâches** | |

---

## Questions ouvertes — À trancher AVANT Sprint 1

- [ ] Prix définitifs des 4 plans (Découverte / Mensuel / Trimestriel / Annuel) ?
- [ ] Clé API PayTech sandbox disponible ? (bloque Sprint 2-T3)
- [ ] Nombre de salles partenaires signées au lancement (objectif : 10+) ?
- [ ] Plan Découverte : 4 séances valables combien de jours (30j ? illimité) ?
- [ ] Gym_owner : accès web uniquement ou borne dédiée pour scanner ?
- [ ] Langue interface : français uniquement ou français + wolof ?
- [ ] Nom de domaine `fitpass.sn` : déjà enregistré ?

---

## Décisions architecture (à valider Sprint 1)

- **Auth :** Laravel Sanctum (API tokens) + sessions Web (dashboard Blade)
- **Queue :** Redis (webhooks PayTech, SMS Twilio en async)
- **QR Code :** `simplesoftwareio/simple-qrcode` (côté serveur, PNG)
- **Carte :** Leaflet.js + OpenStreetMap (0 coût, 0 clé API)
- **Paiement :** PayTech Sénégal (Wave + Orange Money)
- **SMS :** Twilio (canal principal, WhatsApp WATI post-V1)
- **Deploy :** VPS DigitalOcean Ubuntu 24 + GitHub Actions
- **Cache :** Redis (GeoJSON salles, sessions, rate limiting)
