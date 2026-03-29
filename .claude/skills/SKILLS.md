# Skills Agence — Index & Chaînage
# Ce fichier est lu par agents.md pour orchestrer les skills

## Localisation des skills
Tous les skills sont dans `.claude/skills/[nom-skill]/SKILL.md`

## Catalogue complet — 7 skills locaux

| Skill | Agent | Invoquer quand |
|---|---|---|
| `sprint-planning` | PM | Nouveau sprint, SPRINT 0/1/N |
| `competitive-brief` | PM + MARKETING | Nouveau projet, analyse marché |
| `architecture` | DEV | Avant tout nouveau module |
| `deploy-checklist` | CICD + QA | Avant tout déploiement |
| `design-handoff` | DESIGNER | Après chaque composant/page |
| `seo-audit` | MARKETING | Avant mise en ligne |
| `campaign-plan` | MARKETING | Lancement projet |
| `stakeholder-update` | PM | Fin de sprint, rapport client |

---

## Chaînage par sprint

### Sprint 0 (PM)
```
sprint-planning
    └── competitive-brief    (analyser le marché)
    └── write-spec           (specs fonctionnelles)
    └── Générer BACKLOG.md
```

### Sprint 1 (DEV + DESIGNER + SECURITY)
```
architecture                 (DEV — avant de coder)
    └── Migrations + Models
    └── Auth multi-rôles
    └── design-system        (DESIGNER)
```

### Sprint 2 (DEV + QA)
```
DEV code les Services + API
    └── code-review          (avant chaque merge)
    └── Tests unitaires + feature tests
```

### Sprint 3 (DESIGNER + DEV + QA)
```
DESIGNER livre les dashboards
    └── design-handoff       (specs pour DEV)
    └── accessibility-review (WCAG audit)
DEV intègre les APIs tierces
QA recette mobile
```

### Sprint 4 (MARKETING)
```
competitive-brief            (positionnement final)
    └── campaign-plan        (plan de lancement)
    └── email-sequence       (WhatsApp/email nurturing)
    └── seo-audit            (avant mise en ligne)
    └── content-creation     (contenus par canal)
```

### Sprint 5 — LIVRAISON (tous)
```
deploy-checklist             (CICD — obligatoire)
    └── Si OK → déployer
    └── Si incident → incident-response
stakeholder-update           (PM — rapport final client)
    └── performance-report   (métriques de lancement)
```

---

## Règle d'or du chaînage

Chaque skill produit un OUTPUT qui devient l'INPUT du skill suivant.

```
competitive-brief OUTPUT
    → "Nos concurrents sont faibles sur X"
    → INPUT de sprint-planning
        → "Prioriser la feature X dans le backlog"
        → INPUT de architecture
            → "Voici comment construire X"
            → INPUT de design-handoff
                → "Voici les specs de X pour le DEV"
                → INPUT de deploy-checklist
                    → "X est prêt à déployer"
                    → INPUT de stakeholder-update
                        → "X a été livré au client"
```
