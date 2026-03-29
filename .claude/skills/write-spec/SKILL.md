---
name: write-spec
description: >
  Rédige les spécifications fonctionnelles d'une feature avant développement.
  Déclencher en Sprint 0 après competitive-brief, quand le PM doit documenter
  une feature, quand l'utilisateur dit "écris les specs de X", "write-spec",
  "spécification fonctionnelle", ou avant que le DEV commence une feature complexe.
  Toujours invoquer avant architecture pour les features importantes.
---

# Write Spec — [PM]

Rédige les specs fonctionnelles complètes avant tout développement.
Évite les malentendus et les refactorings coûteux.

## Processus

### 1. Capturer le contexte
- Lire CLAUDE.md du projet
- Identifier la feature à spécifier
- Comprendre l'utilisateur cible et son besoin

### 2. Structure de la spec

```markdown
# Spec — [Nom de la Feature]
Projet : [Nom]
Version : 1.0
Date : [date]
Agent : [DEV / DESIGNER / les deux]
Sprint : [N]

## Résumé
[1-2 phrases décrivant la feature et sa valeur]

## Utilisateurs concernés
- [Rôle 1] : [ce qu'il peut faire avec cette feature]
- [Rôle 2] : [...]

## User Stories

### Story 1 — [Titre]
En tant que [rôle],
Je veux [action],
Afin de [bénéfice].

**Critères d'acceptation :**
- [ ] Critère 1 (vérifiable, précis)
- [ ] Critère 2
- [ ] Critère 3

**Estimation :** S | M | L
**Priorité :** Critique | Haute | Moyenne | Basse

### Story 2 — [Titre]
[...]

## Règles métier
- Règle 1 : [description précise]
- Règle 2 : [description précise]
- Exception : [cas particulier]

## Flux utilisateur

### Flux principal (happy path)
1. L'utilisateur [action 1]
2. Le système [réaction]
3. L'utilisateur [action 2]
4. Le système [réaction]
5. Résultat : [état final]

### Flux alternatifs
- Si [condition] → [comportement alternatif]
- Si [erreur] → [message d'erreur + action possible]

## Interface (si applicable)
- Page/composant concerné : [nom]
- Données à afficher : [liste]
- Actions disponibles : [liste]
- États possibles : [liste]

## API (si applicable)
- Endpoint : [METHOD /api/v1/route]
- Input : [paramètres]
- Output : [format réponse]
- Erreurs possibles : [liste codes HTTP]

## Dépendances
- Dépend de : [feature/migration/service requis]
- Bloque : [ce qui ne peut pas être fait sans cette feature]

## Hors scope (ce qu'on ne fait PAS dans cette version)
- [limitation 1]
- [limitation 2]

## Questions ouvertes
- [ ] [Question à trancher avant dev]
- [ ] [Décision technique à prendre]

## Definition of Done
- [ ] User stories implémentées
- [ ] Tests unitaires écrits et passants
- [ ] Tests feature API écrits et passants
- [ ] Design handoff produit
- [ ] Code review effectué
- [ ] Déployé en staging et validé
```

## Chaînage après ce skill

→ Invoquer `architecture` pour le DEV (décisions techniques)
→ Invoquer `design-system` pour le DESIGNER (composants à créer)
→ QA prépare les scénarios de test à partir des critères d'acceptation
