# Sprint State — FitPass Dakar
État courant du projet. Mettre à jour à chaque fin de sprint.
Dernière mise à jour : 2026-03-30 (Sprint 4 démarré)

---

## Sprint courant : 6B — PWA Installable

**Semaine :** 10
**Agents actifs :** DEV → QA
**Commit de référence Sprint 6A :** `d5f8a62` (157 tests)

## Ce qui est fait (Sprints 1 → 3)

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
- Scanner QR web (session auth, pas d'api_token requis)
- Simulation paiement local (FakePaymentController DEV)
- Fix expires_at : whereDate vs datetime comparison
- Fix CSS Tailwind v4 : accolade orpheline @layer components
- Alpine.js ajouté au layout gym
- 50 nouveaux Feature tests Web — 102 tests total, 100% passants

## Sprint 4 — ✅ Terminé (commit `041bc9a`, 118 tests)

## Sprint 5 — ✅ Terminé (commit `99d2030`, 118 tests)

### Tâches

| ID | Tâche | Agent | Statut |
|----|-------|-------|--------|
| S5-T1 | Audit sécurité OWASP | SECURITY | ✅ |
| S5-T2 | composer audit + npm audit → 0 vulnérabilité | SECURITY | ✅ |
| S5-T3 | GitHub Actions CI/CD | CICD | ✅ |
| S5-T4 | Config prod + guide VPS | CICD | ✅ |
| S5-T5 | Recette finale | QA | ⏳ En attente VPS |
| S5-T6 | Rapport client stakeholder | PM | ✅ (`docs/rapport-sprint5-cloture.md`) |

## Sprint 6A — ✅ Terminé (commit `d5f8a62`, 157 tests)

Toutes les tâches S6A-T1 à S6A-T9 + parallèles complétées.
Détail dans BACKLOG.md section Sprint 6A.

## Sprint 6B — En cours (semaine 10)

### Tâches

| ID | Tâche | Agent | Statut |
|----|-------|-------|--------|
| S6B-T1 | Icônes FitPass 192/512/180px + manifest.json | DEV | ⏳ |
| S6B-T2 | Service Worker sw.js (cache-first assets + network-first pages) | DEV | ⏳ |
| S6B-T3 | Meta tags PWA dans 3 layouts Blade + enregistrement SW + page /offline | DEV | ⏳ |
| S6B-T4 | QA : Lighthouse PWA ≥ 90 + test Android + test iPhone | QA | ⏳ |

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

## Sprint 4 — À faire (archivé)

### Tâches

| ID | Tâche | Agent | Statut |
|----|-------|-------|--------|
| S4-T1 | Landing page fitpass.sn (hero, plans, carte, témoignages, CTA) | DESIGNER+DEV | ✅ |
| S4-T2 | SEO : meta tags, OG, sitemap.xml, robots.txt | DEV | ✅ |
| S4-T3 | Plan de lancement Instagram + TikTok + WhatsApp | MARKETING | ✅ |
| S4-T4 | Séquence SMS onboarding (bienvenue + rappels) | MARKETING+DEV | ✅ |
| S4-T5 | Audit SEO + content gaps | MARKETING | ✅ |

## Bloquants actuels

| Bloquant | Impact | Action requise |
|----------|--------|----------------|
| Clé API PayTech sandbox manquante | FakePaymentService actif — pas de vrai paiement | Mamadou contacte PayTech |
| Credentials Twilio manquants | FakeSmsService actif — SMS écrits en log | Mamadou crée compte Twilio |
| Partenariats salles (0 signé ?) | Carte vide au lancement | Action commerciale parallèle |
| fitpass.sn enregistré ? | Landing page sans domaine | Mamadou vérifie le registrar |

## Sprints à venir

| Sprint | Statut |
|--------|--------|
| Sprint 4 — Marketing | 🔄 En cours — Semaine 7 |
| Sprint 5 — Livraison | ⏳ Semaine 8 |
