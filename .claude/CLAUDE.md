# CLAUDE.md — FitPass Dakar
# Chargé automatiquement à chaque session — règles toujours actives

---

## 🧠 DÉBUT DE SESSION — LIRE DANS CET ORDRE EXACT

**À chaque nouvelle conversation, Claude lit ces fichiers avant toute action :**

```
1. memory/sprint-state.md     → Quel sprint ? Quoi de fait ? Quoi reste-t-il ?
2. memory/preferences.md      → Comment travailler ? Comportements interdits ?
3. memory/decisions.md        → Décisions techniques figées — ne pas contredire
4. memory/technical.md        → Pièges connus — ne pas reproduire les erreurs
5. .claude/agents.md          → Workflow des agents, chainage, HANDOFF
6. BACKLOG.md                 → Tâches du sprint courant
7. ROADMAP.md                 → Timeline globale, sprints terminés, décisions ouvertes
```

**Après avoir lu ces fichiers, spawner l'agent PM pour résumer la situation :**
```
Agent PM → lit sprint-state + BACKLOG → résume en 3 lignes + identifie prochaine tâche
```

---

## ⚠️ RÈGLES ABSOLUES — s'appliquent en permanence, même après /compact

### Chainage agents réels — OBLIGATOIRE, aucune exception

**Les agents sont définis dans `.claude/agents/` et DOIVENT être utilisés via l'outil Agent.**
Jamais de travail direct sans passer par l'agent approprié.

#### Flux standard pour toute tâche

```
Utilisateur → PM (orchestrateur)
              ↓
              PM spawne DEV pour le code
              ↓
              DEV passe HANDOFF → QA
              ↓
              QA valide → HANDOFF → PM
              ↓
              PM met à jour BACKLOG + commit docs
```

#### Flux feature avec vues Blade

```
PM → DESIGNER (vues Blade) → HANDOFF → DEV (logique) → HANDOFF → QA → HANDOFF → PM
```

#### Flux sprint sécurité/déploiement

```
PM → SECURITY (audit) → HANDOFF → CICD (pipeline) → HANDOFF → PM
```

#### Agents disponibles (`.claude/agents/`)

| Fichier | Rôle | Quand spawner |
|---------|------|---------------|
| `pm.md` | Orchestrateur | Début de toute session, fin de sprint, planning |
| `dev.md` | Code Laravel | Feature, migration, Service, Controller, bug fix |
| `qa.md` | Tests PHPUnit | Après chaque livraison DEV |
| `designer.md` | Vues Blade + CSS | Dashboard, composant, page |
| `security.md` | Audit sécurité | Avant déploiement, audit OWASP |
| `cicd.md` | Pipeline CI/CD | Déploiement, GitHub Actions |
| `marketing.md` | Contenu + SEO | Landing page, campagne, copy |

#### Règle de séparation stricte
- DEV ne rédige PAS les tests → QA
- DESIGNER ne code PAS la logique → DEV
- QA ne code PAS les features → DEV
- PM ne code PAS → il orchestre uniquement

#### Format HANDOFF obligatoire entre agents
```
--- HANDOFF [SOURCE → CIBLE] ---
Sprint    : [N — nom]
Complété  : [liste des fichiers livrés]
Tests     : [résultat php artisan test ou "à faire par QA"]
À noter   : [pièges, cas limites, points d'attention]
Prêt pour : [agent cible + tâche exacte]
```

---

### Git — commit obligatoire après chaque feature
Après toute feature ou fix terminé et testé (php artisan test = vert) :
```bash
git add <fichiers concernés>
git commit -m "type(scope): description courte"
```
NE PAS attendre que l'utilisateur le demande. NE PAS grouper plusieurs features.

### Stack réelle du projet (ne pas confondre avec les templates)
- Laravel **13** (pas 11)
- Tailwind CSS **v4** — utilise `@import 'tailwindcss'` + `@theme {}` dans app.css, PAS de tailwind.config.js
- PHPUnit **12** — utilise `#[Test]` (attribut PHP 8), PAS `/** @test */`
- `HasUuids` trait (Eloquent built-in) pour les UUIDs — PAS de `boot()` manuel
- PHP via Herd : `/c/Users/Arona/.config/herd-lite/bin/php.exe`
- Composer via Herd : `/c/Users/Arona/.config/herd-lite/bin/composer.bat`
- Vite — lancer `npm run dev` ou `npm run build` avant de consulter les pages dans un navigateur

---

## Chargement automatique
@memory/sprint-state.md
@memory/preferences.md
@memory/decisions.md
@memory/technical.md
@ROADMAP.md
@.claude/agents.md
@.claude/rules/business.md
@.claude/rules/design.md
@.claude/rules/laravel.md
@.claude/rules/security.md
@.claude/rules/testing.md
@.claude/rules/api.md
@.claude/rules/git.md

---

## Projet
NOM=FitPass Dakar
DESCRIPTION=Abonnement universel donnant accès à toutes les salles de sport partenaires à Dakar
URL=fitpass.sn
STACK=Laravel 13 + MySQL 8 + Redis + Blade + Tailwind CSS v4 + Alpine.js

## Environnement
OS=Windows PowerShell
TOOLS=Laravel global, Git, Claude Code installés

## Services externes
PAIEMENT=PayTech Senegal (Wave + Orange Money)
SMS=Twilio
CARTES=Leaflet.js + OpenStreetMap
QR_CODE=simplesoftwareio/simple-qrcode
HEBERGEMENT=VPS DigitalOcean Ubuntu 24
CICD=GitHub Actions

---

## Modèles de données
- users (membres + admins)
- subscriptions (abonnements membres)
- subscription_plans (formules : mensuel, trimestriel, annuel)
- gyms (salles de sport partenaires)
- gym_checkins (entrées validées par QR code)
- payments (transactions PayTech)
- sms_logs (historique SMS Twilio)

## Rôles utilisateurs
member, gym_owner, admin, super_admin

## Statuts métier
Abonnement : pending -> active -> expired | cancelled
Paiement : pending -> completed | failed | refunded
Checkin : valid | invalid | expired

---

## Règles métier critiques
- Prix en FCFA entier, jamais float
- Paiement PayTech requis AVANT activation abonnement
- 1 QR code unique par membre, renouvelé à chaque recharge
- QR code valide uniquement si abonnement active
- Max 1 checkin par salle par jour par membre
- SMS Twilio à chaque changement de statut abonnement
- Gym owner ne voit que ses propres checkins

## Zones / Config spécifique
- Zone : Dakar uniquement (phase 1)
- Devise : FCFA exclusivement
- Fuseau : Africa/Dakar (UTC+0)
- Format ID abonnements : FIT-{YYYY}-{00001}

---

## Fonctionnalités à développer
- Authentification multi-rôles (member, gym_owner, admin, super_admin)
- Gestion abonnements (plans, souscription, renouvellement)
- Paiement PayTech (Wave + Orange Money)
- Génération et scan QR code (accès salle)
- Carte interactive Leaflet — salles partenaires
- Dashboard membre (abonnement, historique entrées)
- Dashboard gym owner (validations QR, stats fréquentation)
- Dashboard admin (membres, revenus, salles)
- Notifications SMS Twilio (activation, expiration, rappel)
- Landing page marketing fitpass.sn
- API publique pour bornes de scan en salle
- CI/CD GitHub Actions + déploiement VPS

---

## Démarrer le projet

Taper cette commande dans Claude Code :

```
SPRINT 0
```

Le PM lira ce fichier et produira automatiquement :
- Backlog complet avec user stories
- Estimations S/M/L par tâche
- Dépendances entre tâches
- Tâches séquentielles vs parallélisables
- Definition of Done par sprint
- Plan d'exécution sprint par sprint

---

## Notes projet
# Ajouter ici les décisions techniques importantes au fil du projet
# Exemples : choix de librairie, compromis acceptés, dette technique
## Démarrage
Si CLAUDE.md n'est pas encore rempli, demander à l'utilisateur :
1. Nom et URL du projet
2. Description en 1 phrase
3. Services externes (paiement, WhatsApp, SMS, images)
4. Palette de couleurs (hex) + typographie souhaitée
5. Inspiration visuelle (référence ou style)

Ensuite générer automatiquement les 3 fichiers et attendre validation
avant de lancer le competitive-brief.
