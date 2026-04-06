# Handoffs — FitPass Dakar

Ce dossier contient les fichiers de passation entre agents.
Chaque agent écrit son handoff ici avant de terminer.
Le PM lit ces fichiers pour orchestrer la suite.

## Convention de nommage
```
{sprint}-{source}-to-{cible}.md
ex: v2.2-dev-to-qa.md
    v2.2-qa-to-pm.md
    v2.2-pm-to-dev.md
```

## Cycle de vie complet

```
PM écrit   memory/handoffs/{sprint}-pm-to-dev.md
           ↓
DEV lit    {sprint}-pm-to-dev.md        → travaille
DEV écrit  {sprint}-dev-to-qa.md
           ↓
PM spawne QA en lui disant de lire {sprint}-dev-to-qa.md
QA lit     {sprint}-dev-to-qa.md        → écrit les tests
QA écrit   {sprint}-qa-to-pm.md
           ↓
PM lit     {sprint}-qa-to-pm.md         → vérifie VERT ✅
PM met à jour BACKLOG + commit
PM déplace tous les {sprint}-*.md → done/
```

## Handoffs actifs (sprint courant)
→ Lister les fichiers ici pour connaître l'état en cours

## Archive
→ `done/` contient les handoffs des sprints terminés
