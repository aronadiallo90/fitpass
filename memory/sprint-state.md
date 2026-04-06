# Sprint State — FitPass Dakar
État courant du projet. Mettre à jour à chaque fin de sprint.
Dernière mise à jour : 2026-04-06 (Sprint V2.1 clos)

---

## Sprint courant : V2.2 — À définir

**Agents actifs :** DEV
**Commit de référence V2.1 :** `79e42fe` (163 tests)

---

## Ce qui est fait (Sprints 1 → V2.1)

### Sprint 1 ✅ (commit `9c1e45d`)
- Laravel 13, migrations DB (7 tables), models, factories, seeders
- Auth Sanctum multi-rôles, middleware CheckRole, rate limiting, 2FA admin
- Design system Tailwind v4, layouts Blade (app/admin/gym), auth pages

### Sprint 2 ✅ (commit `df78bb2`, 52 tests)
- Services : SubscriptionService, FakePaymentService, CheckinService, FakeSmsService
- Jobs async : SendActivationSms, SendReminderSms, SendCheckinSms
- Cron : ExpireSubscriptions (J-7, J-1, expiration)
- API : endpoints abonnements, checkins, gyms, webhook PayTech
- Resources : User, SubscriptionPlan, Subscription, Gym, Checkin, Payment
- 52 tests Unit + Feature — 100% passants

### Sprint 3 ✅ (commit `f484be4`, 102 tests)
- 13 vues Blade (member, gym, admin) — toutes opérationnelles
- 12 Web Controllers + routes complètes
- Scanner QR web (session auth)
- Simulation paiement local (FakePaymentController DEV)
- Fix expires_at, fix CSS Tailwind v4
- 50 nouveaux Feature tests Web — 102 tests total

### Sprint 4 ✅ (commit `041bc9a`, 118 tests)
- Landing page fitpass.sn, SEO, plan de lancement marketing

### Sprint 5 ✅ (commit `99d2030`, 118 tests)
- Audit sécurité OWASP, CI/CD GitHub Actions, config prod VPS

### Sprint 6A ✅ (commit `d5f8a62`, 157 tests)
- Recherche salles enrichie : GymSearchService, filtres, carte Leaflet
- Page /gyms membre + /gyms/{slug} profil complet
- Admin enrichi : horaires, programmes, photos, tags activités
- Leaflet Routing Machine (itinéraire)

### Sprint 6B ✅ (commit `0019a81`)
- PWA installable : manifest.json, service worker, meta tags
- Profil gym owner enrichi
- Fixes mobile

### V2.1 ✅ (commit `79e42fe`, 163 tests)

| ID | Tâche | Statut |
|----|-------|--------|
| V2.1-T7 | Anti-partage QR : photo membre affichée au scan + composant Avatar | ✅ |
| V2.1-T8 | UI upload/suppression photo profil sur dashboard membre (Alpine.js) | ✅ |

---

## 🎉 PROJET LIVRÉ — En attente déploiement VPS

### Actions restantes (côté client — Mamadou)
| Action | Priorité |
|--------|---------|
| Commander Droplet DigitalOcean (Ubuntu 24, 2vCPU/4GB, ~$24/mois) | 🔴 Critique |
| Enregistrer fitpass.sn | 🔴 Critique |
| Créer compte Twilio + renseigner credentials | 🔴 Critique |
| Contacter PayTech Sénégal pour clés API | 🔴 Critique |
| Créer Google Business Profile FitPass Dakar | 🟡 Important |
| Signer 10 salles partenaires minimum | 🟡 Important |

## Bloquants actuels

| Bloquant | Impact | Action requise |
|----------|--------|----------------|
| Clé API PayTech sandbox manquante | FakePaymentService actif | Mamadou contacte PayTech |
| Credentials Twilio manquants | FakeSmsService actif — SMS en log | Mamadou crée compte Twilio |
| Partenariats salles (0 signé ?) | Carte vide au lancement | Action commerciale parallèle |
| fitpass.sn enregistré ? | Landing page sans domaine | Mamadou vérifie le registrar |
