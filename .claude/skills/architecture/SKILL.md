---
name: architecture
description: >
  Valide et documente les choix d'architecture technique avant tout développement.
  Déclencher quand le DEV démarre un nouveau module important : système de paiement,
  auth multi-rôles, notifications, QR codes, géolocalisation, ou quand l'utilisateur
  dit "comment structurer X", "architecture de Y", "AGENT: DEV — architecture".
  Toujours invoquer avant d'écrire la première ligne de code d'un module complexe.
---

# Architecture Decision — [DEV]

Force une réflexion structurée avant de coder. Évite les refactorings coûteux
en validant les choix techniques en amont.

## Processus

### 1. Lire les contraintes projet
Lire `.claude/rules/laravel.md` pour :
- Architecture imposée (Controllers → Services → Repositories)
- Conventions de nommage
- Stack technique exacte

### 2. Analyser le module à construire

Répondre à ces questions avant de coder :

**Données**
- Quelles tables sont nécessaires ?
- Quelles relations (1:1, 1:N, N:N) ?
- Quels index sur quelles colonnes ?
- Soft delete nécessaire ?

**Logique métier**
- Quels Services créer ?
- Quelle interface pour chaque Service ?
- Quelles règles métier critiques à protéger ?
- Quels événements déclencher ?

**API**
- Quels endpoints exposer ?
- Quelles routes protéger (Sanctum) ?
- Quels rôles peuvent accéder à quoi ?
- Format de réponse (Resources) ?

**Async**
- Quelles tâches passer en Queue (Redis) ?
- Notifications → toujours async
- Imports/exports lourds → toujours async

**Sécurité**
- Rate limiting sur quelles routes ?
- Validation HMAC si webhook ?
- 2FA si admin ?

### 3. Produire l'ADR (Architecture Decision Record)

```markdown
# ADR — [Nom du module]
Date : [date]
Statut : Décidé

## Contexte
[Pourquoi ce module est nécessaire]

## Décision
[Ce qu'on va construire et comment]

## Structure

### Migrations
- [table_name] : [colonnes principales + index]

### Models
- [ModelName] : fillable, casts, relations

### Services
- [ServiceName] : méthodes principales
  - create(array $data): Model
  - update(Model $model, array $data): Model

### Controllers
- [ControllerName] : max 7 méthodes
  - Injecte [ServiceName]

### API Endpoints
- GET /api/v1/[resource] → index
- POST /api/v1/[resource] → store
- [autres endpoints]

### Jobs async
- [JobName] → Queue 'notifications'

## Alternatives considérées
[Ce qu'on a envisagé et pourquoi on ne l'a pas choisi]

## Conséquences
[Impact sur le reste du système]
```

### 4. Valider avant de continuer

Checklist avant de commencer à coder :
- [ ] ADR rédigé et cohérent avec laravel.md
- [ ] Pas de logique dans les Controllers planifiée
- [ ] Toutes les notifs prévues en async
- [ ] Rate limiting identifié sur les routes sensibles
- [ ] Tests unitaires planifiés pour chaque Service

## Chaînage après ce skill

→ Coder les migrations + models
→ Invoquer `code-review` avant chaque merge
→ Invoquer `deploy-checklist` avant mise en prod
