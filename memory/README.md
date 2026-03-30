# Memory — FitPass Dakar
Système de mémoire persistante du projet.
**Lire en début de chaque session** (automatiquement chargé via CLAUDE.md).

## Index des fichiers

| Fichier | Contenu | Lire quand |
|---------|---------|-----------|
| `sprint-state.md` | Sprint courant, tâches restantes, bloquants | **En premier** — donne le contexte immédiat |
| `decisions.md` | Décisions techniques et règles métier figées | Avant toute décision technique |
| `preferences.md` | Comment travailler avec Mamadou, comportements interdits, erreurs passées | Avant de commencer à coder |
| `technical.md` | Gotchas Laravel 13, pièges ENUM, syntaxes obligatoires | Avant toute modification technique |
| `people.md` | Qui est qui, chemins Herd, profil utilisateur | Si besoin de contexte humain |

## Fichiers racine aussi chargés au début de session

| Fichier | Contenu |
|---------|---------|
| `ROADMAP.md` | Timeline 5 sprints, statut chaque livrable, décisions ouvertes |
| `BACKLOG.md` | Tâches détaillées + Definition of Done par sprint |

## Règle de mise à jour

- **Fin de sprint :** Mettre à jour `sprint-state.md` (PM)
- **Nouvelle décision technique :** Ajouter dans `decisions.md` (DEV)
- **Erreur répétée trouvée :** Ajouter dans `technical.md` (DEV ou QA)
- **Correction de comportement :** Ajouter dans `preferences.md` (PM)
