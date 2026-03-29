---
name: stakeholder-update
description: >
  Génère un rapport d'avancement clair pour le client ou les stakeholders.
  Déclencher en fin de sprint, quand l'utilisateur dit "rapport client",
  "AGENT: PM — rapport", "stakeholder update", "qu'est-ce qu'on livre au client",
  ou "LIVRAISON". Adapté pour des clients non-techniques à Dakar.
---

# Stakeholder Update — [PM]

Produit un rapport d'avancement clair, visuel et actionnable pour le client.
Le client comprend ce qui a été fait, ce qui reste, et les prochaines étapes.
Pas de jargon technique — langage business accessible.

## Processus

### 1. Collecter les informations du sprint

Lire :
- BACKLOG.md → tâches prévues vs réalisées
- Derniers commits git
- Résultats `php artisan test`
- Handoffs des agents

### 2. Structurer le rapport

#### En-tête
```
Projet : [Nom]
Rapport Sprint : [N]
Période : [du XX au XX]
Préparé par : Agence Digitale
```

#### Ce qui a été livré
Lister en langage client (pas technique) :
```
✅ [Feature 1] — ex: "Les clients peuvent maintenant créer un compte et se connecter"
✅ [Feature 2] — ex: "Le paiement Wave et Orange Money est opérationnel"
✅ [Feature 3] — ex: "Le tableau de bord admin affiche les commandes en temps réel"
```

#### Ce qui est en cours
```
🔄 [Feature X] — [% d'avancement]
🔄 [Feature Y] — [ce qui reste à faire]
```

#### Ce qui arrive au prochain sprint
```
📋 [Feature A]
📋 [Feature B]
```

#### Métriques techniques (si pertinent)
```
Tests : [X] tests — tous au vert ✅
Performance : temps de réponse moyen [X]ms
Sécurité : audit effectué — [X] points corrigés
```

#### Points d'attention / décisions requises
```
⚠️ [Décision 1] : [ex: "Confirmez-vous la zone de livraison Thiès ?"]
⚠️ [Question 2] : [ex: "Quel logo final utiliser pour la version mobile ?"]
```

#### Planning
```
Sprint [N+1] : [dates] → [objectif principal]
Livraison finale estimée : [date]
```

### 3. Adapter le ton au contexte sénégalais

- Chaleureux et direct (pas corporate froid)
- Bilan positif d'abord, puis les points à clarifier
- Si retard : expliquer clairement + nouveau planning
- Proposer un appel WhatsApp ou une réunion si complexe

## Format output

```markdown
# Rapport Sprint [N] — [Projet]
**[Nom client]**, voici le bilan de cette semaine.

## Ce qu'on a livré ✅
- [liste en langage client]

## En cours 🔄
- [liste]

## Prochaine étape 📋
[Sprint N+1 : objectif en 1 phrase]

## On a besoin de vous sur 💬
- [décision ou validation requise]

## Calendrier
- Sprint [N+1] : [dates]
- Livraison finale : [date]

---
Disponible pour en discuter : WhatsApp [numéro] ou réunion à votre convenance.
```

## Chaînage après ce skill

→ Fin de projet : invoquer `performance-report` pour les métriques finales
→ Si nouveau projet découvert pendant la discussion client : invoquer `sprint-planning`
→ Si client a des retours : mettre à jour le BACKLOG.md
