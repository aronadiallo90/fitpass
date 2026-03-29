---
name: seo-audit
description: >
  Audit SEO complet et identification des content gaps avant mise en ligne.
  Déclencher avant chaque livraison de site, quand l'utilisateur dit "audit SEO",
  "optimise le SEO", "AGENT: MARKETING — seo", ou en Sprint 4 avant la landing page.
  Produit un rapport actionnable avec les corrections prioritaires à faire.
---

# SEO Audit — [MARKETING]

Audit complet du SEO technique et du contenu avant mise en ligne.
Chaque item manquant = trafic organique perdu.

## Processus

### 1. SEO Technique

#### Balises meta (chaque page)
```html
<!-- Vérifier la présence et qualité de : -->
<title>[Titre unique 50-60 chars max]</title>
<meta name="description" content="[Description 150-160 chars]">
<meta property="og:title" content="[Titre réseaux sociaux]">
<meta property="og:description" content="[Description RS]">
<meta property="og:image" content="[Image 1200x630px]">
<meta property="og:url" content="[URL canonique]">
<link rel="canonical" href="[URL canonique]">
```

#### Structure HTML
- [ ] Un seul `<h1>` par page
- [ ] Hiérarchie logique : h1 → h2 → h3
- [ ] Images avec attribut `alt` descriptif
- [ ] URLs propres et lisibles (pas de `?id=123`)
- [ ] Sitemap.xml présent et accessible
- [ ] robots.txt configuré correctement

#### Performance (Core Web Vitals)
- [ ] LCP (Largest Contentful Paint) < 2.5s
- [ ] FID (First Input Delay) < 100ms
- [ ] CLS (Cumulative Layout Shift) < 0.1
- [ ] Images optimisées (WebP, lazy loading)
- [ ] CSS/JS minifiés et compressés
- [ ] Cache configuré correctement

### 2. SEO Local (marché sénégalais)

#### Schema.org LocalBusiness
```json
{
  "@context": "https://schema.org",
  "@type": "LocalBusiness",
  "name": "[NOM_BOUTIQUE]",
  "description": "[DESCRIPTION]",
  "url": "[URL]",
  "telephone": "[TELEPHONE]",
  "address": {
    "@type": "PostalAddress",
    "streetAddress": "[ADRESSE]",
    "addressLocality": "Dakar",
    "addressCountry": "SN"
  },
  "openingHours": "Mo-Sa 09:00-20:00",
  "priceRange": "CFA"
}
```

#### Présence locale
- [ ] Google Business Profile créé et optimisé
- [ ] Adresse Dakar mentionnée dans le contenu
- [ ] Numéro WhatsApp visible (format +221...)
- [ ] Horaires d'ouverture à jour

### 3. Content Gap Analysis

Identifier les mots-clés manquants :

#### Recherches typiques marché sénégalais
- "[produit/service] Dakar"
- "[produit/service] Sénégal"
- "[produit/service] prix FCFA"
- "livraison [produit] Dakar"
- "acheter [produit] en ligne Sénégal"

#### Pages manquantes à créer
```
Page catégorie : [terme recherché] → [page à créer]
FAQ : [questions fréquentes] → [section à ajouter]
Blog : [sujet pertinent] → [article à rédiger]
```

### 4. Rapport d'audit

```markdown
# Audit SEO — [Projet]
Date : [date]
Score estimé : [X/100]

## Problèmes critiques (à corriger avant mise en ligne)
- [ ] [problème] → [correction]

## Problèmes importants (à corriger dans les 30 jours)
- [ ] [problème] → [correction]

## Améliorations recommandées
- [ ] [suggestion]

## Content gaps identifiés
| Mot-clé | Volume estimé | Page actuelle | Action |
|---------|--------------|---------------|--------|
| [kw] | [volume] | Manquante | Créer |

## Actions prioritaires
1. [action 1]
2. [action 2]
3. [action 3]

## Métriques à surveiller
- Position Google pour : [mots-clés cibles]
- Trafic organique mensuel
- Taux de conversion organique
```

## Chaînage après ce skill

→ DEV corrige les problèmes techniques identifiés
→ MARKETING crée les pages/contenus manquants (`content-creation`)
→ Invoquer `campaign-plan` avec les mots-clés identifiés comme base
