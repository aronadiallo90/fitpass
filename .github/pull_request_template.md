## Description
<!-- Résumé des changements en 2-3 lignes -->

## Type de changement
- [ ] `feat` — Nouvelle fonctionnalité
- [ ] `fix` — Correction de bug
- [ ] `refactor` — Refactoring sans changement fonctionnel
- [ ] `test` — Ajout / modification de tests
- [ ] `chore` — Maintenance, dépendances
- [ ] `docs` — Documentation

## Checklist avant merge
- [ ] `php artisan test` → 157/157 (ou plus) tests passent
- [ ] Pas de logique métier dans les Controllers
- [ ] Prix stockés en integer FCFA (pas de float)
- [ ] Toutes les routes nommées
- [ ] Formulaires avec Form Request dédié
- [ ] Eager loading sur les relations utilisées
- [ ] Pas de `$request->all()` — utiliser `$request->validated()`

## Tests couverts
<!-- Lister les tests ajoutés ou modifiés -->

## Screenshots / démo
<!-- Si changement UI — captures mobile 375px + desktop -->
