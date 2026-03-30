# Audit SEO — FitPass Dakar
Date : 2026-03-30
Score estimé : **72/100** (avant corrections)
Cible post-corrections : **88/100**

---

## ✅ Points forts (déjà en place)

| Élément | Statut | Détail |
|---------|--------|--------|
| `<title>` | ✅ | "FitPass Dakar — 1 abonnement · Toutes les salles de sport" (55 chars) |
| `<meta description>` | ✅ | 150 chars, contient les mots-clés cibles |
| OG tags | ✅ | og:title, og:description, og:type, og:url, og:image, og:locale (fr_SN) |
| Twitter Card | ✅ | summary_large_image configuré |
| `<link rel="canonical">` | ✅ | Ajouté Sprint 4 |
| sitemap.xml | ✅ | Accessible sur `/sitemap.xml` |
| robots.txt | ✅ | Admin/dashboard/api bloqués |
| Structure H1 > H2 > H3 | ✅ | Un seul H1, hiérarchie cohérente |
| FAQ | ✅ | Section "Questions fréquentes" avec 5 Q&A pertinentes |
| Paiement Wave/OM | ✅ | Mentionné 6+ fois — mots-clés à fort intent |
| URL propres | ✅ | Pas de paramètres dans les URLs publiques |
| HTTPS | ✅ (en prod) | Forcé via config serveur |

---

## ❌ Problèmes critiques — Corriger avant mise en ligne

### 1. Schema.org LocalBusiness manquant
**Impact :** Absence de rich snippets Google — perte de visibilité locale
**Correction :** Ajouter JSON-LD dans `layouts/landing.blade.php`
→ **Corrigé dans ce sprint** (voir ci-dessous)

### 2. og:image physique manquante
**Impact :** Partages WhatsApp/Facebook sans aperçu visuel → taux de clic divisé par 3
**Correction :** Créer `/public/og-image.jpg` (1200×630px) avec le logo FitPass sur fond sombre
→ **Action designer requise**

---

## ⚠️ Problèmes importants — Corriger dans les 30 jours

### 3. Mot-clé "gym" absent du texte visible
La landing utilise "salle de sport" mais pas "gym Dakar" — terme recherché séparément.
**Correction :** Ajouter "gym" dans le sous-titre hero ou dans la section salles.

### 4. Mots-clés activités absents
"CrossFit Dakar", "yoga Dakar", "fitness Dakar", "musculation Dakar" = 0 occurrence.
Ces requêtes long-tail sont moins concurrentielles et convertissent mieux.
**Correction :** Ajouter une liste d'activités dans la section salles.

### 5. Pages légales inexistantes
Mentions légales, CGU, Confidentialité → pointent vers `#` (invalide).
Google pénalise les sites sans pages légales.
**Correction :** Créer 3 pages Blade minimalistes + routes dédiées.

### 6. Google Business Profile non créé
**Impact :** Invisible sur Google Maps — énorme pour recherches locales Dakar.
**Correction :** Créer le profil sur business.google.com (action externe).

---

## 📊 Content Gaps — Mots-clés manquants

| Mot-clé | Volume estimé | Difficulté | Page actuelle | Action |
|---------|--------------|------------|---------------|--------|
| salle de sport Dakar | Élevé | Faible (0 concurrent SEO) | Landing (partiel) | Renforcer |
| gym Dakar | Élevé | Faible | Absente | Ajouter au contenu |
| abonnement gym Dakar | Moyen | Très faible | Landing (partiel) | Renforcer H2 |
| fitness Dakar | Moyen | Faible | Absente | Ajouter section |
| payer Wave sport Dakar | Faible | Nulle | Landing ✅ | Déjà bien positionné |
| yoga Dakar | Moyen | Faible | Absente | Section activités |
| musculation Dakar | Moyen | Faible | Absente | Section activités |
| CrossFit Dakar | Faible | Nulle | Absente | À créer post-V1 |
| salle de sport pas cher Dakar | Faible | Nulle | Absente | Ajouter plan Découverte |
| coach sportif Dakar | Faible | Faible | Absente | Post-V1 |

---

## 🔧 Améliorations recommandées

### Performance (Core Web Vitals)
- Leaflet.js (160KB) chargé en synchrone → passer en `defer` ou charger conditionnellement
- Google Fonts : ajouter `&display=swap` ✅ déjà fait
- Images : aucune image bitmap → pas de problème lazy-loading (emojis utilisés)
- CSS/JS : Vite minifie automatiquement en `npm run build` ✅

### Contenu additionnel recommandé (post-V1)
- **Blog :** "Comment choisir sa salle de sport à Dakar" (intent informatif → conversion)
- **Page /salles :** liste SEO-friendly de toutes les salles avec descriptions
- **Page /activites :** yoga, musculation, CrossFit, arts martiaux à Dakar
- **Témoignages** : ajouter les vrais témoignages membres avec photos

---

## Actions prioritaires

1. ✅ **[DEV - immédiat]** Ajouter Schema.org JSON-LD dans le layout landing
2. 🎨 **[DESIGNER - avant lancement]** Créer og-image.jpg (1200×630px)
3. ✍️ **[DEV - avant lancement]** Ajouter "gym" + activités dans le texte landing
4. ⚖️ **[DEV - avant lancement]** Créer pages légales (Mentions légales, CGU, Confidentialité)
5. 📍 **[CLIENT - avant lancement]** Créer Google Business Profile FitPass Dakar
6. 📊 **[CLIENT - J+7]** Installer Google Analytics + Search Console

---

## Métriques à surveiller après lancement

| Métrique | Outil | Objectif J+30 |
|---------|-------|--------------|
| Position "salle de sport Dakar" | Search Console | Top 10 |
| Position "abonnement gym Dakar" | Search Console | Top 5 |
| Impressions organiques | Search Console | > 1 000/mois |
| Clics organiques | Search Console | > 80/mois |
| PageSpeed mobile | PageSpeed Insights | > 90 |
| LCP | Chrome DevTools | < 2.5s |
