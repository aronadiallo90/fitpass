# BACKLOG — FitPass Dakar
Généré le : 2026-03-29
Sprint actuel : 0 (Kick-off)
Stack : Laravel 11 + MySQL 8 + Redis + Blade + Tailwind CSS + Alpine.js

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
| S1-T1 | Créer le projet Laravel 11 + config `.env` + Git init | DEV | S | — |
| S1-T2 | Migrations DB : users, subscription_plans, subscriptions, payments, gyms, gym_checkins, sms_logs | DEV | M | S1-T1 |
| S1-T3 | Models Eloquent + relations + casts (prix integer FCFA) | DEV | M | S1-T2 |
| S1-T4 | Factories + Seeders (plans tarifaires, salles test, users) | DEV | M | S1-T3 |
| S1-T5 | Auth multi-rôles : register/login/logout (Sanctum API + sessions Web) | DEV | L | S1-T3 |
| S1-T6 | Middleware rôles : `role:member`, `role:gym_owner`, `role:admin` | SECURITY | S | S1-T5 |
| S1-T7 | Rate limiting : api (60/min), auth (10/min), admin (30/min) | SECURITY | S | S1-T5 |
| S1-T8 | 2FA obligatoire pour admin + super_admin | SECURITY | M | S1-T5 |

### Tâches parallèles (peuvent se faire en même temps)

| ID | User Story | Agent | Estim. |
|----|-----------|-------|--------|
| S1-P1 | Config Tailwind : couleurs FitPass (rouge #FF3B3B, fond #0A0A0F) + Google Fonts | DESIGNER | S |
| S1-P2 | Layout Blade : `app.blade.php` (member), `admin.blade.php`, `gym.blade.php` | DESIGNER | M |
| S1-P3 | Composants Blade : bouton primaire, card, badge statut, input formulaire | DESIGNER | M |
| S1-P4 | Headers sécurité HTTP (Middleware SecurityHeaders) | SECURITY | S |
| S1-P5 | Config `.gitignore` + `.env.example` + README install | DEV | S |

### Definition of Done Sprint 1
- [ ] `php artisan test` → 0 erreur
- [ ] Migrations rollback/re-run sans erreur
- [ ] Auth : register, login, logout fonctionnels en Feature test
- [ ] Rôles : 401 sans token, 403 mauvais rôle (tests écrits)
- [ ] 2FA admin opérationnel
- [ ] Design system Tailwind appliqué (pas de couleurs Tailwind génériques)
- [ ] `git commit` avec message conventionnel

---

## SPRINT 2 — Core Métier (semaine 3-4)

### Tâches séquentielles

| ID | User Story | Agent | Estim. | Dépend de |
|----|-----------|-------|--------|-----------|
| S2-T1 | `SubscriptionPlanRepository` + `SubscriptionPlanService` | DEV | S | S1-T3 |
| S2-T2 | `SubscriptionService` : create, renew, expire, cancel | DEV | L | S2-T1 |
| S2-T3 | `PaymentService` : initiate (PayTech), process webhook, validate HMAC | DEV | L | S2-T2 |
| S2-T4 | Webhook PayTech : POST `/api/v1/webhooks/paytech` + job Redis async | DEV | M | S2-T3 |
| S2-T5 | `CheckinService` : validate (QR + abonnement + anti-doublon) | DEV | L | S2-T2 |
| S2-T6 | API endpoints abonnements + paiements + checkins (Resources + Form Requests) | DEV | L | S2-T3, S2-T5 |
| S2-T7 | `SmsService` (Twilio) : send, queue async, log dans `sms_logs` | DEV | M | S1-T3 |
| S2-T8 | Cron jobs : expirer abonnements + rappels SMS J-7/J-1 | DEV | M | S2-T7 |

### Tâches parallèles

| ID | User Story | Agent | Estim. |
|----|-----------|-------|--------|
| S2-P1 | Unit tests : SubscriptionService (happy path, erreurs, transitions statuts) | QA | M |
| S2-P2 | Unit tests : CheckinService (valide, expiré, doublon, plan découverte) | QA | M |
| S2-P3 | Feature tests API : abonnements (201, 401, 403, 422) | QA | M |
| S2-P4 | Feature tests API : webhook PayTech (HMAC valide/invalide, idempotence) | QA | M |
| S2-P5 | Interfaces Blade : page plans, récapitulatif paiement, retour PayTech | DESIGNER | M |

### Definition of Done Sprint 2
- [ ] `php artisan test` → 0 erreur
- [ ] Souscription end-to-end testée en sandbox PayTech
- [ ] Webhook PayTech : HMAC validé, idempotence OK, activation abonnement OK
- [ ] SMS Twilio envoyé à l'activation (testé en sandbox)
- [ ] Checkin : valide / expiré / doublon gérés correctement
- [ ] Plan Découverte : décompte séances fonctionnel
- [ ] Cron expiration abonnements testé manuellement
- [ ] 0 requête N+1 (vérification Laravel Debugbar)

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
- [ ] Parcours membre complet (s'abonner → QR code → entrer en salle) fonctionnel sur mobile 375px
- [ ] Scanner QR : retour visuel < 1 seconde
- [ ] Carte Leaflet : toutes les salles visibles, filtres opérationnels
- [ ] Dashboards admin + gym_owner opérationnels
- [ ] PageSpeed mobile > 90
- [ ] Aucun bug visuel sur 375px / 768px / 1280px

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
