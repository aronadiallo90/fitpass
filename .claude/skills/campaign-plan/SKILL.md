---
name: campaign-plan
description: >
  Planifie une campagne de lancement multi-canal complète pour un projet.
  Déclencher en Sprint 4 lors du lancement d'un nouveau projet, quand l'utilisateur
  dit "plan de lancement", "campaign plan", "AGENT: MARKETING — lancement", ou
  "comment on lance ce projet". Adapté au marché sénégalais : WhatsApp-first,
  mobile-first, paiement mobile (Wave/Orange Money).
---

# Campaign Plan — [MARKETING]

Planifie le lancement marketing d'un projet sur le marché sénégalais.
WhatsApp-first, mobile-first, conversion-focused.

## Processus

### 1. Définir l'objectif
- Objectif principal : inscriptions / ventes / notoriété
- Cible : qui exactement (âge, zone Dakar, comportement)
- Budget estimé (si disponible)
- Timeline : date de lancement + durée campagne

### 2. Analyse du marché sénégalais

Canaux par efficacité pour le marché dakarois :
```
1. WhatsApp (groupes + broadcast) → taux engagement très élevé
2. Facebook / Instagram          → reach large, surtout 25-45 ans
3. TikTok                        → 18-30 ans, viral possible
4. Bouche à oreille              → très puissant au Sénégal
5. SMS                           → pour les non-smartphones
6. Google Ads                    → intention d'achat forte
```

### 3. Construire le plan par canal

#### WhatsApp (canal principal)
```
Stratégie broadcast :
- Liste de contacts qualifiés à constituer
- Messages : 1x/semaine max (ne pas spammer)
- Format : texte court + image/vidéo + CTA
- Heure optimale : 18h-21h en semaine

Stratégie groupes :
- Identifier 5-10 groupes WhatsApp pertinents Dakar
- Demander permission avant de partager
- Contenu utile, pas juste promotionnel

Script de lancement WhatsApp :
"Bonsoir [prénom] ! 👋
[Nom projet] vient de lancer à Dakar.
[Bénéfice principal en 1 phrase].
Découvrir → [lien]
Questions ? Répondez ici 😊"
```

#### Facebook / Instagram
```
Semaine -2 avant lancement : teasing (sans révéler)
Semaine -1 : annonce officielle + countdown
Jour J : post de lancement + stories
Semaine +1 : témoignages premiers clients
Post fréquence : 3-4x/semaine
Stories : quotidien si possible
```

#### Partenariats locaux
```
Identifier :
- Influenceurs Dakar pertinents (micro-influenceurs 5k-50k)
- Associations et groupements professionnels
- Points de vente physiques partenaires
- Medias locaux (Seneweb, Dakaractu, etc.)
```

### 4. Timeline de lancement

```
J-14 : Teasing sur tous les canaux
J-7  : Annonce officielle + landing page live
J-3  : Email/WhatsApp aux early adopters
J-1  : Reminder + preview
J    : LANCEMENT — post tous les canaux en même temps
J+1  : Follow-up + premiers retours
J+7  : Bilan semaine + ajustements
J+30 : Rapport performance complet
```

### 5. KPIs à suivre

```
Acquisition :
- Visites landing page
- Taux de conversion visiteur → inscrit
- Source des inscriptions (WhatsApp / FB / organique)

Engagement :
- Taux d'ouverture WhatsApp
- Reach et engagement social media
- Partages et mentions

Conversion :
- Inscriptions / ventes
- Coût par acquisition (si ads)
- Revenu généré J+30
```

## Output — Plan de campagne

```markdown
# Plan de Campagne — [Projet]
Lancement : [date]
Objectif : [objectif principal]
Cible : [description cible]

## Timeline
[tableau semaine par semaine]

## Canaux et contenus
[par canal : fréquence, format, messages clés]

## Messages clés
Proposition de valeur principale : [1 phrase]
Tagline : [courte accroche mémorable]
CTA principal : [action attendue]

## Budget recommandé
[si applicable]

## KPIs et objectifs
[tableau objectifs vs métriques]
```

## Chaînage après ce skill

→ Invoquer `email-sequence` pour créer les séquences WhatsApp/email
→ Invoquer `content-creation` pour produire les contenus par canal
→ Invoquer `seo-audit` pour optimiser la landing page
→ Après lancement : invoquer `performance-report` pour mesurer les résultats
