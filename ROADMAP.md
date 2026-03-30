# ROADMAP — FitPass Dakar
Créé le : 2026-03-29 | Mis à jour : 2026-03-30
Version : 1.0 — V1 Lancement Dakar

---

## Vision produit

FitPass est le premier agrégateur multi-salles de sport à Dakar.
Un abonnement unique, toutes les salles partenaires, paiement Wave/Orange Money.

**OKR lancement V1 :**
- 10+ salles partenaires actives à J+0
- 100 membres actifs à M+1
- Taux de renouvellement abonnements > 60% à M+3

---

## Timeline globale — 8 semaines

```
Semaine 1-2   Sprint 1 — Fondations          ✅ Terminé  (commit 9c1e45d)
Semaine 3-4   Sprint 2 — Core Métier          ✅ Terminé  (commit df78bb2, 52 tests)
Semaine 5-6   Sprint 3 — Interfaces           🔄 En cours
Semaine 7     Sprint 4 — Marketing            ⏳ À venir
Semaine 8     Sprint 5 — Livraison            ⏳ À venir
```

---

## Sprint 1 — Fondations ✅

**Objectif :** Base technique solide avant tout code métier.

| Livrable | Statut |
|----------|--------|
| Projet Laravel 13 configuré | ✅ |
| Migrations DB (7 tables) | ✅ |
| Models Eloquent + relations + casts | ✅ |
| Factories + Seeders | ✅ |
| Auth multi-rôles Sanctum | ✅ |
| Middleware rôles (member, gym_owner, admin) | ✅ |
| Rate limiting (api 60/min, auth 10/min, admin 30/min) | ✅ |
| 2FA admin | ✅ |
| Design system Tailwind v4 (rouge #FF3B3B, fond #0A0A0F) | ✅ |
| Layouts Blade (app, admin, gym) | ✅ |
| Composants Blade (bouton, card, badge, input) | ✅ |
| Headers sécurité HTTP | ✅ |

---

## Sprint 2 — Core Métier ✅

**Objectif :** Moteur abonnement + paiement + QR code fonctionnel.

| Livrable | Statut |
|----------|--------|
| SubscriptionService (create, activate, expire, cancel) | ✅ |
| PaymentService / FakePaymentService (sans PayTech réel) | ✅ |
| CheckinService (validate QR, anti-doublon, plan découverte) | ✅ |
| SmsService / FakeSmsService (sans Twilio réel) | ✅ |
| API endpoints abonnements + paiements + checkins | ✅ |
| Webhook PayTech (HMAC + idempotence) | ✅ |
| Cron expiration abonnements + rappels SMS J-7/J-1 | ✅ |
| 52 tests (Unit + Feature) — 100% passants | ✅ |

**Note :** FakePaymentService et FakeSmsService sont utilisés en attendant les clés API réelles. Remplacer dans Sprint 3 (S3-T5 DEV).

---

## Sprint 3 — Interfaces 🔄

**Objectif :** Interfaces utilisateur complètes — mobile-first, opérationnelles.

**Semaine 5-6** | Agent lead : DESIGNER → DEV → QA

| ID | Livrable | Agent | Statut |
|----|----------|-------|--------|
| S3-T1 | Dashboard membre (abonnement actif, QR code, historique) | DESIGNER+DEV | ⏳ |
| S3-T2 | Page QR code grand format (valide/expiré) | DESIGNER+DEV | ⏳ |
| S3-T3 | Interface scan gym_owner (camera, résultat vert/rouge) | DESIGNER+DEV | ⏳ |
| S3-T4 | Carte Leaflet salles partenaires + filtres | DESIGNER+DEV | ⏳ |
| S3-T5 | API GeoJSON salles (mis en cache Redis 1h) | DEV | ⏳ |
| S3-T6 | Dashboard admin (membres, revenus, abonnements, salles) | DESIGNER+DEV | ⏳ |
| S3-T7 | CRUD admin salles (coordonnées GPS + sélecteur carte) | DESIGNER+DEV | ⏳ |
| S3-T8 | Dashboard gym_owner (checkins du jour, stats) | DESIGNER+DEV | ⏳ |
| S3-P1 | Tests E2E recette mobile 375px (parcours souscription) | QA | ⏳ |
| S3-P2 | Tests E2E recette mobile (scan QR autorisé + refusé) | QA | ⏳ |
| S3-P3 | Performance : PageSpeed mobile > 90, API < 300ms | QA | ⏳ |
| S3-P4 | API publique bornes scan POST /api/v1/checkins/validate | DEV | ✅ (Sprint 2) |

**Definition of Done Sprint 3 :**
- [ ] Parcours membre complet sur mobile 375px
- [ ] Scanner QR : retour visuel < 1 seconde
- [ ] Carte Leaflet : toutes les salles visibles, filtres OK
- [ ] Dashboards admin + gym_owner opérationnels
- [ ] PageSpeed mobile > 90
- [ ] 0 bug visuel sur 375px / 768px / 1280px

---

## Sprint 4 — Marketing ⏳

**Objectif :** Landing page + plan de lancement.

**Semaine 7** | Agent lead : MARKETING + DESIGNER

| ID | Livrable | Agent | Statut |
|----|----------|-------|--------|
| S4-T1 | Landing page fitpass.sn (hero, plans, carte, CTA) | DESIGNER+DEV | ⏳ |
| S4-T2 | SEO : meta tags, OG, sitemap.xml, robots.txt | DEV | ⏳ |
| S4-T3 | Plan de lancement Instagram + TikTok + WhatsApp | MARKETING | ⏳ |
| S4-T4 | Séquence SMS onboarding Twilio (bienvenue + rappels) | MARKETING+DEV | ⏳ |
| S4-T5 | Audit SEO + content gaps | MARKETING | ⏳ |

---

## Sprint 5 — Livraison ⏳

**Objectif :** Recette finale + déploiement production.

**Semaine 8** | Agent lead : QA + SECURITY + CICD + PM

| ID | Livrable | Agent | Statut |
|----|----------|-------|--------|
| S5-T1 | Audit sécurité OWASP complet | SECURITY | ⏳ |
| S5-T2 | composer audit + npm audit → 0 vulnérabilité critique | SECURITY | ⏳ |
| S5-T3 | GitHub Actions CI/CD : test + deploy VPS DigitalOcean | CICD | ⏳ |
| S5-T4 | Config prod : APP_DEBUG=false, HTTPS, cookies sécurisés | CICD | ⏳ |
| S5-T5 | Recette finale complète (tous les parcours) | QA | ⏳ |
| S5-T6 | Rapport client stakeholder | PM | ⏳ |

---

## Post-V1 — Roadmap future

| Feature | Priorité | Justification |
|---------|----------|---------------|
| Module B2B corporate | Haute | Grandes entreprises Dakar |
| Notifications WhatsApp (WATI API) | Haute | Canal principal Sénégal |
| App mobile native (React Native) | Haute | Post-V1 si traction confirmée |
| Extension CEDEAO (Abidjan, Bamako) | Haute | Après traction Dakar |
| Géolocalisation "salles près de moi" | Moyenne | UX mobile améliorée |
| Régénération QR code membre | Moyenne | Sécurité si QR compromis |
| Réservation créneaux horaires | Moyenne | Salles avec cours collectifs |
| QR code dynamique (change à chaque scan) | Basse | Anti-partage avancé |
| Notation et avis membres | Basse | Social proof |
| Cours on-demand vidéo | Basse | Différenciation ClassPass-like |

---

## Décisions architecture prises

| Décision | Choix | Raison |
|----------|-------|--------|
| Auth | Sanctum (API + sessions Web) | Multi-rôles, stateful + stateless |
| Queue | Redis | Webhooks PayTech + SMS async |
| QR Code | simplesoftwareio/simple-qrcode | Serveur-side, PNG, 0 dépendance client |
| Carte | Leaflet.js + OpenStreetMap | 0 coût, 0 clé API |
| Paiement | PayTech Sénégal | Wave + Orange Money natif |
| SMS | Twilio | Canal principal, WhatsApp WATI post-V1 |
| Deploy | VPS DigitalOcean Ubuntu 24 + GitHub Actions | Contrôle total, coût maîtrisé |
| Cache | Redis (GeoJSON, sessions, rate limiting) | Performance + cohérence |
| Tests | PHPUnit 12, QUEUE=null, DB SQLite en mémoire | Vitesse + isolation |

---

## Questions ouvertes

- [ ] Prix définitifs des 4 plans (Découverte / Mensuel / Trimestriel / Annuel) ?
- [ ] Clé API PayTech sandbox disponible ? (bloque remplacement FakePaymentService)
- [ ] Credentials Twilio disponibles ? (bloque remplacement FakeSmsService)
- [ ] Nombre de salles partenaires signées au lancement (objectif : 10+) ?
- [ ] Plan Découverte : 4 séances valables combien de jours (30j ? illimité) ?
- [ ] Langue interface : français uniquement ou français + wolof ?
- [ ] Nom de domaine `fitpass.sn` : déjà enregistré ?
