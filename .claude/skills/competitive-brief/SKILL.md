---
name: competitive-brief
description: >
  Analyse la concurrence d'un projet ou d'un client. Déclencher quand l'utilisateur
  dit "analyse les concurrents", "competitive brief", "qui sont les concurrents de X",
  "SPRINT 0", ou avant tout démarrage de projet pour orienter la stratégie.
  Produit un brief complet : forces/faiblesses, positionnement, opportunités,
  implications stratégiques. Toujours invoquer avant write-spec et sprint-planning.
---

# Competitive Brief — [PM + MARKETING]

Produit une analyse concurrentielle complète et actionnable avant tout démarrage
de projet. Le résultat oriente directement le backlog et le positionnement marketing.

## Processus

### 1. Définir le scope
Identifier :
- Concurrents directs (même produit, même marché)
- Concurrents indirects (même besoin, approche différente)
- Le marché cible (ex: Dakar, Sénégal, CEDEAO)
- La décision que ce brief va informer

### 2. Rechercher (web search)
Pour chaque concurrent :
- Pages produit et liste de fonctionnalités
- Tarifs et packaging
- Avis clients (Google, Facebook, WhatsApp groups)
- Présence réseaux sociaux (Instagram, TikTok, Facebook)
- Derniers lancements et annonces

### 3. Analyser le marché sénégalais
Spécificités à toujours prendre en compte :
- Paiement dominant : Wave + Orange Money (pas carte bancaire)
- Communication : WhatsApp > email
- Mobile first : 90%+ du trafic sur mobile
- Langue : français + wolof selon la cible
- Livraison : challenge logistique Dakar

### 4. Produire le brief

#### Vue d'ensemble concurrents
Pour chaque concurrent :
```
Nom : [concurrent]
Positionnement : [comment il se décrit]
Cible : [qui il vise]
Forces : [ce qu'il fait bien]
Faiblesses : [ce qu'il fait mal]
Prix : [modèle tarifaire]
Canaux : [où il est présent]
```

#### Tableau comparatif fonctionnalités
| Feature | Notre projet | Concurrent A | Concurrent B |
|---------|-------------|-------------|-------------|
| [feature] | Fort | Faible | Absent |

Rating : Fort / Adéquat / Faible / Absent

#### Opportunités identifiées
- Gaps dans les offres concurrentes
- Ce que les clients demandent sans l'avoir
- Segments non couverts

#### Menaces
- Où les concurrents investissent
- Moves concurrentiels à surveiller

#### Implications stratégiques (le plus important)
- Quoi construire en priorité
- Où se différencier vs atteindre la parité
- Comment positionner le message marketing
- Quoi surveiller en continu

## Output format

```markdown
# Competitive Brief — [Projet]
Date : [date]

## Résumé exécutif
[3-4 phrases — la conclusion principale]

## Concurrents analysés
[sections par concurrent]

## Comparatif fonctionnalités
[tableau]

## Opportunités clés
[liste priorisée]

## Implications pour notre projet
[actions concrètes]
```

## Chaînage après ce skill

→ Invoquer `sprint-planning` pour intégrer les insights dans le backlog
→ Invoquer `write-spec` pour écrire les specs différenciantes
→ En Sprint 4 : invoquer `campaign-plan` avec ce brief comme input
