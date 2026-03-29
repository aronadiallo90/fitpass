# Design System — [NOM_PROJET]
# Copier dans .claude/rules/design.md de chaque projet
# Remplir avec les vraies valeurs du projet

## Palette officielle
# Inspiré Yango — couleurs vives sur fond sombre, épuré, mobile-first
PRIMARY=#FF3B3B        # Rouge vif FitPass (énergie, sport)
SECONDARY=#FF8C00      # Orange accent (dynamisme)
BACKGROUND=#0A0A0F     # Fond noir profond
BACKGROUND_SOFT=#13131A # Fond cards
TEXT=#FFFFFF           # Texte principal
TEXT_MUTED=#8888A0     # Texte secondaire
DANGER=#EF4444         # Erreur
SUCCESS=#22C55E        # Succès / actif
WARNING=#F59E0B        # Avertissement / en attente

## Typographie
# Google Fonts — impact + modernité
FONT_HEADING=Barlow Condensed   # Titres — condensé, sportif
FONT_BODY=Inter                 # Corps — lisible, moderne

# Import obligatoire dans les layouts Blade
# <link href="https://fonts.googleapis.com/css2?family=..." rel="stylesheet">

## Config Tailwind (tailwind.config.js)
# ← Ajouter les couleurs custom dans extend.colors
# Exemple :
# colors: {
#   gold: { DEFAULT: '#C9A84C', light: '#E8C97A', dim: '#7A6328' },
#   black: { DEFAULT: '#06060A', soft: '#0F0F15' },
# }

## Composants Blade standards

### Bouton primaire
# class="bg-[PRIMARY] text-black px-8 py-3 uppercase tracking-widest
#        font-medium transition-all duration-300 hover:opacity-90"

### Bouton outline
# class="border border-[PRIMARY]/50 text-[PRIMARY] px-8 py-3
#        uppercase tracking-widest hover:border-[PRIMARY]"

### Card
# class="bg-[BACKGROUND_SOFT] border border-[PRIMARY]/10
#        hover:border-[PRIMARY]/25 transition-all duration-300 p-6"

### Badge statut
# class="text-xs uppercase tracking-widest px-2 py-1 border"
# Variantes par statut : active (vert), pending (amber), expired (rouge)

### Input formulaire
# class="bg-white/5 border border-[PRIMARY]/20 focus:border-[PRIMARY]
#        text-white placeholder-white/30 px-4 py-3 w-full"

## Règles design strictes

- Jamais de couleurs Tailwind génériques par défaut
- Toujours les variables CSS custom du projet
- Mobile-first : concevoir 375px en premier, puis agrandir
- Animations : transition-all duration-300 ease-out uniquement
- Espacements généreux : padding minimum p-6 pour les cards
- Bordures fines : border (1px) — jamais border-2 sauf accent
- Typographie : titres avec la font heading, corps avec body
- Hiérarchie visuelle claire : 3 niveaux max (h1, h2, texte)

## Inspiration visuelle
# Inspiré Yango — rouge vif sur fond sombre, typographie condensée sportive
# Énergie + modernité + mobile-first — proche de l'esthétique fitness apps

## Notes spécifiques
# ← À REMPLIR si règles particulières au projet
