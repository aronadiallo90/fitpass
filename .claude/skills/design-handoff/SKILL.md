---
name: design-handoff
description: >
  Génère les specs pixel-perfect pour le DEV après chaque composant ou page terminée.
  Déclencher quand le DESIGNER a fini un composant, une page ou un dashboard,
  quand l'utilisateur dit "AGENT: DESIGNER — handoff", "specs pour le dev",
  "prépare le handoff", ou après chaque livraison DESIGNER dans un sprint.
  Toujours invoquer avant que le DEV commence l'intégration frontend.
---

# Design Handoff — [DESIGNER → DEV]

Produit les specs complètes pour que le DEV intègre sans ambiguïté.
Zéro aller-retour, zéro réunion de clarification.

## Processus

### 1. Lire le design system projet
Lire `.claude/rules/design.md` pour :
- Palette hex exacte
- Typographie Google Fonts
- Classes Tailwind custom
- Composants standards

### 2. Documenter chaque composant

Pour chaque composant livré :

#### Dimensions & espacement
```
Composant : [nom]
Largeur : [valeur ou "100% du parent"]
Hauteur : [valeur ou "auto"]
Padding : [top right bottom left en px]
Margin : [si applicable]
Border-radius : [valeur]
Border : [épaisseur couleur]
```

#### Typographie
```
Titre : [font-family] [size] [weight] [color hex]
Sous-titre : [font-family] [size] [weight] [color hex]
Corps : [font-family] [size] [weight] [color hex]
```

#### Couleurs utilisées
```
Background : [hex]
Texte principal : [hex]
Accent : [hex]
Border : [hex + opacité]
```

#### États interactifs
```
Default : [description]
Hover : [ce qui change — couleur, transform, shadow]
Focus : [ring color + offset]
Active/Pressed : [ce qui change]
Disabled : [opacity + cursor]
Loading : [skeleton ou spinner]
```

#### Breakpoints responsive
```
Mobile 375px : [layout description]
Tablette 768px : [ce qui change]
Desktop 1280px : [layout final]
```

#### Code Tailwind de référence
```html
<!-- Exemple d'implémentation Tailwind -->
<div class="...classes...">
  <!-- structure HTML -->
</div>
```

#### Alpine.js si interactions
```html
<div x-data="{ open: false, ... }">
  <!-- interactions -->
</div>
```

### 3. Checklist accessibilité (WCAG 2.1 AA)
- [ ] Contraste texte/fond ≥ 4.5:1
- [ ] Tous les inputs ont un label associé
- [ ] Boutons ont un texte ou aria-label
- [ ] Images ont un alt text
- [ ] Navigation au clavier possible (tabindex, focus visible)
- [ ] États focus visibles sur tous les éléments interactifs

### 4. Notes d'intégration pour le DEV
```
⚠️ Points d'attention :
- [point 1 : ex. "Le hover doit être transition 300ms"]
- [point 2 : ex. "Sur mobile, masquer cette colonne"]
- [point 3 : ex. "Ce composant reçoit les données via l'API /api/v1/..."]
```

## Format output — Handoff doc

```markdown
# Handoff — [Composant/Page]
Designer : [nom]
Date : [date]
Sprint : [N]

## Composants livrés
1. [Composant A]
2. [Composant B]

## Specs [Composant A]
[toutes les sections ci-dessus]

## Assets
- Palette : voir design.md
- Fonts : Importées via Google Fonts dans layouts/app.blade.php
- Icons : [librairie utilisée]

## Checklist accessibilité
[checklist avec résultats]

## Notes DEV
[points d'attention]
```

## Chaînage après ce skill

→ DEV peut démarrer l'intégration frontend immédiatement
→ QA peut préparer les scénarios de test responsive
→ Invoquer `design-system` si des nouveaux tokens ont été créés
