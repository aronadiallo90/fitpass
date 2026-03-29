---
name: sprint-planning
description: >
  Planifie un sprint Agile complet pour un projet de l'agence. Déclencher quand
  l'utilisateur dit "planifie le sprint", "on démarre le sprint N", "SPRINT 0",
  "SPRINT 1", ou quand un nouveau projet démarre et qu'il faut organiser le travail.
  Génère automatiquement : user stories, estimations, dépendances, tâches parallèles,
  Definition of Done. Toujours invoquer avant de commencer à coder quoi que ce soit.
---

# Sprint Planning — [PM]

Planifie un sprint complet avant tout développement. Le PM structure le travail
pour que les agents DEV, DESIGNER, SECURITY et QA sachent exactement quoi faire.

## Processus

### 1. Lire le contexte projet
Lire `.claude/CLAUDE.md` du projet pour connaître :
- Stack technique, services externes
- Modèles de données, rôles utilisateurs
- Règles métier critiques
- Fonctionnalités à développer

### 2. Identifier le sprint

**Sprint 0 — Kick-off** (toujours en premier sur un nouveau projet)
- Analyser le brief complet
- Invoquer `competitive-brief` → analyser le marché
- Invoquer `write-spec` → rédiger les specs
- Générer BACKLOG.md complet

**Sprint 1 — Fondations**
- DEV : migrations DB + models + auth
- DESIGNER : design system + layouts
- SECURITY : rate limiting + 2FA

**Sprint 2 — Core métier**
- DEV : Services + API endpoints
- QA : unit tests + feature tests

**Sprint 3 — Interfaces**
- DESIGNER : dashboards + handoff
- DEV : intégrations tierces
- QA : recette mobile

**Sprint 4 — Marketing**
- MARKETING : landing page + SEO + campaign

**Sprint 5 — Livraison**
- QA + SECURITY + CICD + rapport client

### 3. Générer les user stories

Format obligatoire :
```
En tant que [rôle], je veux [action], afin de [bénéfice].
Critères d'acceptation :
  - [ ] critère 1
  - [ ] critère 2
Estimation : S (<2h) | M (2-4h) | L (>4h)
Agent : DEV | DESIGNER | SECURITY | QA | CICD | MARKETING
```

### 4. Identifier les dépendances

Toujours poser la question : "Quoi doit être fait AVANT quoi ?"
- Auth → tout le reste
- DB migrations → Services
- Services → API endpoints
- API → Frontend
- Frontend → Tests E2E

### 5. Séparer séquentiel et parallèle

**Séquentiel** (ne peut pas se faire en même temps) :
- Auth → Commandes → Paiement

**Parallèle** (peut se faire en même temps) :
- DEV backend + DESIGNER composants
- Tests unitaires + Design handoff

### 6. Définir la Definition of Done

Pour chaque sprint, lister les critères obligatoires :
```
Sprint N est terminé quand :
- [ ] php artisan test → 0 erreur
- [ ] [critère spécifique au sprint]
- [ ] Handoff produit vers agent suivant
```

## Output — BACKLOG.md

Générer un fichier structuré :

```markdown
# BACKLOG — [NOM_PROJET]
Généré le : [date]
Sprint actuel : [N]

## Sprint [N] — [Nom]

### Tâches séquentielles
| ID | User Story | Agent | Estimation | Dépend de |
|----|-----------|-------|-----------|-----------|
| T1 | ... | DEV | M | — |
| T2 | ... | DEV | L | T1 |

### Tâches parallèles
| ID | User Story | Agent | Estimation |
|----|-----------|-------|-----------|
| T3 | ... | DESIGNER | M |
| T4 | ... | SECURITY | S |

### Definition of Done Sprint [N]
- [ ] critère 1
- [ ] critère 2

## Backlog futur (sprints suivants)
[liste des features non encore planifiées]
```

## Chaînage après ce skill

Selon le sprint :
- Sprint 0 → invoquer `competitive-brief` puis `write-spec`
- Sprint 1 → activer DEV (`architecture`) + DESIGNER (`design-system`)
- Sprint 5 → invoquer `deploy-checklist` puis `stakeholder-update`
