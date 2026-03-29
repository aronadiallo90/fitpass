---
name: design-system
description: >
  Documente, audite ou étend le design system d'un projet. Déclencher en début
  de projet pour créer le système de design, quand le DESIGNER crée les premiers
  composants, quand l'utilisateur dit "design system", "palette du projet",
  "AGENT: DESIGNER — system", ou quand la cohérence visuelle doit être vérifiée.
  Toujours invoquer avant de créer le premier composant Blade d'un projet.
---

# Design System — [DESIGNER]

Crée et maintient le design system du projet. Garantit la cohérence visuelle
entre tous les composants et pages livrés par le DESIGNER et le DEV.

## Processus

### 1. Lire la config projet
Lire `.claude/rules/design.md` pour extraire :
- Palette hex officielle
- Typographie Google Fonts
- Classes Tailwind custom configurées

### 2. Documenter les fondations

#### Palette de couleurs
```css
/* Variables CSS — à placer dans resources/css/app.css */
:root {
  --color-primary:    [hex];   /* Couleur principale */
  --color-secondary:  [hex];   /* Couleur secondaire */
  --color-accent:     [hex];   /* Accent/CTA */
  --color-bg:         [hex];   /* Fond principal */
  --color-bg-soft:    [hex];   /* Fond doux / cards */
  --color-text:       [hex];   /* Texte principal */
  --color-text-muted: [hex];   /* Texte secondaire */
  --color-border:     [hex];   /* Bordures */
  --color-success:    #22C55E;
  --color-warning:    #F59E0B;
  --color-danger:     #EF4444;
}
```

#### Typographie
```css
/* Import Google Fonts — dans layouts/app.blade.php */
<link href="https://fonts.googleapis.com/css2?family=[HEADING]:wght@300;400;600&family=[BODY]:wght@300;400;500&display=swap" rel="stylesheet">

/* Usage */
.heading { font-family: '[HEADING]', serif; }
.body    { font-family: '[BODY]', sans-serif; }
```

#### Config Tailwind
```js
// tailwind.config.js
module.exports = {
  theme: {
    extend: {
      colors: {
        primary:   '[hex]',
        secondary: '[hex]',
        accent:    '[hex]',
        // etc.
      },
      fontFamily: {
        heading: ['[HEADING]', 'serif'],
        body:    ['[BODY]', 'sans-serif'],
      }
    }
  }
}
```

### 3. Cataloguer les composants

Pour chaque composant existant ou à créer :

#### Boutons
```html
<!-- Primaire -->
<button class="bg-[primary] text-[text-on-primary] px-8 py-3 
               uppercase tracking-widest font-medium 
               transition-all duration-300 hover:opacity-90
               focus:ring-2 focus:ring-[primary] focus:ring-offset-2">
  Action
</button>

<!-- Outline -->
<button class="border border-[accent]/50 text-[accent] px-8 py-3
               uppercase tracking-widest
               hover:border-[accent] hover:bg-[accent]/10
               transition-all duration-300">
  Action
</button>
```

#### Cards
```html
<div class="bg-[bg-soft] border border-[border]/10 
            hover:border-[border]/25 transition-all duration-300 
            p-6 rounded-xl">
  <!-- contenu -->
</div>
```

#### Inputs
```html
<input class="bg-white/5 border border-[accent]/20 
              focus:border-[accent] focus:ring-0
              text-[text] placeholder-[text-muted]
              px-4 py-3 w-full rounded-lg
              transition-all duration-300">
```

#### Badges de statut
```html
<!-- Active -->
<span class="text-xs uppercase tracking-widest px-2 py-1 
             border border-green-500/30 text-green-400 rounded">
  Actif
</span>
<!-- En attente -->
<span class="text-xs uppercase tracking-widest px-2 py-1
             border border-amber-500/30 text-amber-400 rounded">
  En attente
</span>
```

### 4. Règles d'usage

```markdown
## À TOUJOURS faire
- Utiliser les CSS variables, pas les hex hardcodés dans Tailwind
- Hover state sur TOUS les éléments interactifs
- Focus visible pour accessibilité clavier
- Mobile-first : concevoir 375px en premier
- Transition 300ms ease-out sur tous les changements d'état

## À NE JAMAIS faire
- Couleurs Tailwind génériques (gray-500, blue-600)
- Font Inter, Roboto, Arial
- Cards sans hover state
- Inputs sans focus ring
- Gradient purple/blue générique
```

### 5. Output — design-system.md

Générer un fichier de référence pour le projet :

```markdown
# Design System — [Projet]
Version : 1.0
Dernière mise à jour : [date]

## Couleurs
[tableau avec hex + nom + usage]

## Typographie
[specs heading + body]

## Composants
[liste avec code de référence]

## Règles
[dos and don'ts]
```

## Chaînage après ce skill

→ DESIGNER peut commencer à créer les composants Blade
→ Après chaque composant : invoquer `design-handoff`
→ Avant livraison : vérifier cohérence avec ce design system
