---
name: email-sequence
description: >
  Crée des séquences de messages WhatsApp ou email pour nurturing, onboarding,
  relance et fidélisation. Déclencher quand le MARKETING doit créer une séquence
  de communication, quand l'utilisateur dit "séquence WhatsApp", "email sequence",
  "messages de relance", "onboarding client", ou en Sprint 4 après campaign-plan.
  Adapté au marché sénégalais : WhatsApp-first, ton chaleureux, horaires locaux.
---

# Email / WhatsApp Sequence — [MARKETING]

Crée des séquences de communication automatisées pour le marché sénégalais.
WhatsApp est le canal principal — email en complément pour les professionnels.

## Processus

### 1. Identifier le type de séquence

**Onboarding** — Nouvel inscrit/client
- Objectif : activation et première utilisation
- Durée : 7 jours
- Fréquence : J0, J1, J3, J7

**Nurturing** — Prospect pas encore converti
- Objectif : construire la confiance, pousser à l'achat
- Durée : 14 jours
- Fréquence : J0, J3, J7, J10, J14

**Relance panier abandonné**
- Objectif : récupérer la vente
- Durée : 3 jours
- Fréquence : J0 (1h après), J1, J3

**Fidélisation** — Client existant
- Objectif : réachat et recommandation
- Durée : mensuel/trimestriel

### 2. Règles marché sénégalais

```
Horaires d'envoi optimaux :
- Matin : 8h-9h (avant le travail)
- Midi : 12h30-13h30 (pause déjeuner)
- Soir : 19h-21h (après le travail)
- Éviter : heures de prière du vendredi 13h-14h

Langue : français (+ wolof si cible populaire)
Longueur : court sur WhatsApp (< 150 mots), plus long email
Emoji : utilisés naturellement, pas excessivement
Ton : chaleureux, personnel, jamais corporate

Canal principal : WhatsApp
Canal secondaire : Email (surtout B2B)
```

### 3. Structure de chaque message

```
Message WhatsApp :
- Salutation personnalisée : "Bonjour [Prénom] ! 👋"
- Corps : 2-3 phrases max, bénéfice clair
- CTA unique et simple : "Cliquez ici →"
- Pas de liens longs (utiliser bit.ly ou lien court)

Email :
- Objet : < 50 caractères, curiosité ou bénéfice direct
- Préheader : complète l'objet
- Corps : 150-300 mots max
- 1 seul CTA clair
- Signature avec WhatsApp number
```

### 4. Templates par type

#### Séquence onboarding (7 jours)

```
J0 — Bienvenue (immédiat après inscription)
WhatsApp : "Bienvenue [Prénom] ! 🎉
Votre compte [Nom projet] est prêt.
[Bénéfice principal en 1 phrase].
Commencer maintenant → [lien]
Des questions ? Répondez ici 😊"

J1 — Première valeur
"Bonjour [Prénom] !
Savez-vous que vous pouvez [fonctionnalité clé] ?
[Explication simple en 2 phrases].
Essayer maintenant → [lien]"

J3 — Témoignage social
"[Prénom], voici ce que dit [prénom client] :
'[témoignage court et authentique]'
Rejoignez [X] clients satisfaits → [lien]"

J7 — Engagement ou aide
"[Prénom], avez-vous eu le temps d'essayer [Nom projet] ?
Si vous avez des questions, je suis là.
[Offre d'aide ou bonus si pas encore actif]"
```

#### Séquence relance panier abandonné

```
J0 — 1h après abandon
"[Prénom], vous avez oublié quelque chose ! 😊
Votre [produit/service] vous attend.
[Prix] FCFA — Stock limité ⚠️
Finaliser → [lien panier]"

J1 — Lever une objection
"[Prénom], une question avant votre commande ?
[Répondre à l'objection principale : prix/livraison/confiance]
Paiement Wave ou Orange Money acceptés ✅
Commander → [lien]"

J3 — Dernière chance
"[Prénom], dernière chance ! ⏰
Votre [produit] est toujours disponible.
[Offre spéciale si possible : livraison offerte, remise]
→ [lien]"
```

### 5. Output — Séquence complète

```markdown
# Séquence [Type] — [Projet]
Canal principal : WhatsApp
Durée : [X] jours
Nombre de messages : [N]

## Message 1 — [Nom] (J[N])
**Canal** : WhatsApp
**Horaire** : [heure optimale]
**Contenu** :
[texte complet du message]

## Message 2 — [Nom] (J[N])
[...]

## KPIs à mesurer
- Taux d'ouverture : objectif > [X]%
- Taux de clic : objectif > [X]%
- Taux de conversion : objectif > [X]%
```

## Chaînage après ce skill

→ DEV configure les jobs Laravel pour l'envoi automatique (Queue + Twilio/WATI)
→ Invoquer `performance-report` après 30 jours pour analyser les résultats
→ Ajuster les messages selon les taux constatés
