---
name: code-review
description: >
  Effectue une revue de code complète avant tout merge. Déclencher quand le DEV
  ou SECURITY veut valider du code, quand l'utilisateur dit "review ce code",
  "code-review", "AGENT: DEV — review", ou avant chaque merge vers develop/main.
  Vérifie : architecture, sécurité, performance, conventions Laravel du projet.
---

# Code Review — [DEV + SECURITY]

Revue de code systématique avant tout merge. Un code non-reviewé ne merge pas.

## Checklist Architecture

### Controllers
- [ ] Maximum 7 méthodes
- [ ] Aucune logique métier (tout dans les Services)
- [ ] Injection via constructeur uniquement
- [ ] Retourne toujours via Resource (API) ou view() (Web)
- [ ] Utilise les Form Requests pour la validation

### Services
- [ ] Interface implémentée
- [ ] Responsabilité unique respectée
- [ ] Pas d'appel Eloquent direct (via Repository)
- [ ] Typage PHP complet sur paramètres et retours
- [ ] Early returns utilisés (pas d'imbrication > 2 niveaux)

### Models
- [ ] `$fillable` explicite (pas de `$guarded = []`)
- [ ] `$casts` définis pour tous les types non-string
- [ ] Eager loading utilisé (pas de N+1)
- [ ] Soft deletes si entité métier importante

### Migrations
- [ ] Index sur les colonnes de recherche fréquente
- [ ] Foreign keys avec `onDelete` défini
- [ ] Pas de `down()` vide

## Checklist Sécurité

- [ ] Pas de données sensibles dans les logs
- [ ] Pas de `$request->all()` sans validation
- [ ] Rate limiting sur les routes sensibles
- [ ] Signature HMAC validée sur les webhooks
- [ ] Pas de SQL brut non-bindé
- [ ] Pas de `{!! !!}` Blade sans raison (XSS)
- [ ] Policies Laravel sur les ressources protégées

## Checklist Qualité Code

- [ ] Conventions respectées : code anglais, commentaires français
- [ ] PSR-12 : 4 espaces PHP, 2 espaces JS/Blade
- [ ] Prix en FCFA integer (jamais float)
- [ ] UUID pour les clés primaires exposées en API
- [ ] Pas de `dd()`, `dump()`, `var_dump()` oubliés
- [ ] Pas de `console.log()` oublié
- [ ] Alpine.js utilisé (pas jQuery)
- [ ] Tailwind CSS utilisé (pas Bootstrap)

## Checklist Performance

- [ ] Eager loading sur toutes les relations utilisées
- [ ] Pas de requête dans une boucle (N+1)
- [ ] Cache Redis utilisé si données statiques
- [ ] Jobs async pour opérations lentes (notifs, emails)

## Checklist Tests

- [ ] Tests unitaires pour tout nouveau Service
- [ ] Tests feature pour tout nouvel endpoint API
- [ ] `php artisan test` → 100% passant
- [ ] Cas d'erreur testés (pas seulement happy path)

## Format du rapport de review

```markdown
# Code Review — [Feature/PR]
Date : [date]
Reviewer : DEV + SECURITY
Fichiers reviewés : [liste]

## Résultat global
✅ Approuvé | ⚠️ Approuvé avec réserves | ❌ À corriger

## Points positifs
- [ce qui est bien fait]

## Corrections obligatoires (bloquantes)
- [ ] [fichier:ligne] — [problème] → [correction requise]

## Suggestions (non-bloquantes)
- [suggestion d'amélioration]

## Sécurité
✅ RAS | ⚠️ [point à surveiller]

## Tests
✅ Couverts | ❌ [ce qui manque]
```

## Chaînage après ce skill

→ Si approuvé → merge + `git push` automatique
→ Si corrections → DEV corrige + relance la review
→ Avant déploiement → invoquer `deploy-checklist`
