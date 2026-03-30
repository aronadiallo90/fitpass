# Sprint State — FitPass Dakar
État courant du projet. Mettre à jour à chaque fin de sprint.
Dernière mise à jour : 2026-03-30 (Sprint 3 terminé)

---

## Sprint courant : 3 — Interfaces

**Semaine :** 5-6
**Agents actifs :** DESIGNER → DEV → QA
**Commit de référence Sprint 2 :** `df78bb2` (52 tests)

## Ce qui est fait (Sprints 1 + 2)

### Sprint 1 ✅ (commit `9c1e45d`)
- Laravel 13 configuré, migrations DB (7 tables), models, factories, seeders
- Auth Sanctum multi-rôles, middleware CheckRole, rate limiting, 2FA admin
- Design system Tailwind v4, layouts Blade (app/admin/gym), auth pages

### Sprint 2 ✅ (commit `df78bb2`, 52 tests)
- Services : SubscriptionService, FakePaymentService, CheckinService, FakeSmsService
- Jobs async : SendActivationSms, SendReminderSms, SendCheckinSms
- Cron : ExpireSubscriptions (J-7, J-1, expiration)
- API : endpoints abonnements, checkins, gyms, webhook PayTech
- Resources : User, SubscriptionPlan, Subscription, Gym, Checkin, Payment
- 52 tests Unit + Feature — 100% passants

## Fait Sprint 3 (en cours)

### DESIGNER ✅ (commit `7de79b3`)
- Design system vérifié + 8 composants CSS ajoutés (kpi-card, data-table, scan-result, qr-wrapper, map-container, empty-state...)
- 13 vues Blade créées (member, gym, admin)
- docs/design-system.md + docs/handoff-sprint3.md

### DEV ✅ (commit `7de79b3`)
- 12 Web Controllers créés
- Routes Web complètes (member, gym, admin CRUD)
- Accessors price/type/amount sur models
- Migration is_active users
- 52 tests — 100% ✅

### QA ✅ (commit `ed00184`, 102 tests)
- 50 nouveaux tests Web Feature (AdminWebTest, GymWebTest, MemberWebTest)
- Fix SubscriptionPlanFactory slug unique (bug hardcodé 'mensuel')
- Création vue manquante admin/gyms-form.blade.php
- Fix User model is_active cast boolean
- **102 tests — 100% passants**

### Sprint 3 TERMINÉ ✅

### Tâches Sprint 3 (BACKLOG.md)
| ID | Tâche | Agent |
|----|-------|-------|
| S3-T1 | Dashboard membre (abonnement actif, QR code, historique) | DESIGNER+DEV |
| S3-T2 | Page QR code grand format | DESIGNER+DEV |
| S3-T3 | Interface scan gym_owner (camera Alpine.js) | DESIGNER+DEV |
| S3-T4 | Carte Leaflet salles + filtres | DESIGNER+DEV |
| S3-T5 | API GeoJSON salles (Redis cache 1h) | DEV |
| S3-T6 | Dashboard admin | DESIGNER+DEV |
| S3-T7 | CRUD admin salles + sélecteur carte | DESIGNER+DEV |
| S3-T8 | Dashboard gym_owner | DESIGNER+DEV |
| S3-P1 | Tests E2E mobile 375px | QA |
| S3-P2 | Tests scan QR valide/refusé | QA |
| S3-P3 | Performance PageSpeed > 90 | QA |

## Bloquants actuels

| Bloquant | Impact | Action requise |
|----------|--------|----------------|
| Clé API PayTech sandbox manquante | FakePaymentService actif — pas de vrai paiement | Mamadou contacte PayTech |
| Credentials Twilio manquants | FakeSmsService actif — SMS écrits en log | Mamadou crée compte Twilio |
| Partenariats salles (0 signé ?) | Carte vide au lancement | Action commerciale parallèle |

## Sprints à venir

| Sprint | Statut |
|--------|--------|
| Sprint 4 — Marketing | ⏳ Semaine 7 |
| Sprint 5 — Livraison | ⏳ Semaine 8 |
