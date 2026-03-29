---
name: debug
description: >
  Session de debugging structurée pour résoudre tout type de bug. Déclencher
  quand le DEV ou QA rencontre un bug, quand l'utilisateur dit "debug",
  "ça marche pas", "j'ai une erreur", "AGENT: DEV — debug", ou quand un test
  échoue. Suit une méthodologie rigoureuse : reproduire → isoler → corriger → tester.
---

# Debug — [DEV + QA]

Session de debugging méthodique. Ne jamais deviner — toujours reproduire et isoler.

## Méthodologie : 4 étapes

### Étape 1 — Reproduire le bug

```
Questions à répondre :
1. Que s'est-il passé exactement ? (comportement observé)
2. Que devrait-il se passer ? (comportement attendu)
3. Comment reproduire ? (étapes précises)
4. Toujours reproductible ou intermittent ?
5. Depuis quand ? (dernier commit fonctionnel ?)
```

Outils de reproduction :
```bash
# Logs Laravel
tail -f storage/logs/laravel.log

# Mode debug en local
APP_DEBUG=true

# Telescope (si installé)
php artisan telescope:install

# Debugbar
composer require barryvdh/laravel-debugbar --dev
```

### Étape 2 — Isoler la cause

**Bug PHP/Laravel**
```bash
# Lire l'erreur complète (stack trace)
# Identifier le fichier + numéro de ligne
# Vérifier les logs

# Tests pour isoler
php artisan test --filter=NomDuTest
php artisan tinker  # tester le code isolément
```

**Bug SQL/Eloquent**
```php
// Logger les requêtes
DB::enableQueryLog();
// ... code ...
dd(DB::getQueryLog());

// Vérifier N+1
// Installer telescope ou debugbar
```

**Bug Frontend (Alpine.js)**
```javascript
// Console du navigateur
// Vérifier x-data, x-bind, x-on
// Inspecter l'état Alpine : $el._x_dataStack
Alpine.store('nomStore') // vérifier le store
```

**Bug API**
```bash
# Tester avec curl
curl -X POST http://localhost/api/v1/endpoint \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"key":"value"}'

# Vérifier les routes
php artisan route:list | grep endpoint
```

**Bug Queue/Jobs**
```bash
# Vérifier les jobs échoués
php artisan queue:failed

# Relancer un job échoué
php artisan queue:retry ID

# Vider la queue
php artisan queue:clear
```

### Étape 3 — Corriger

Règles de correction :
- Corriger la cause racine, pas le symptôme
- Ne pas introduire de dette technique pour aller vite
- Respecter les conventions (laravel.md)
- Commenter le pourquoi si la correction n'est pas évidente

```php
// Bon commentaire de fix
// Fix: Le stock ne se décrémentait pas en cas de paiement Wave
// car le webhook arrivait avant la confirmation. On vérifie maintenant
// le statut PayTech avant de décrémenter.
```

### Étape 4 — Tester le fix

```bash
# Relancer les tests
php artisan test

# Tester manuellement le cas de reproduction
# Tester les cas adjacents (régression)

# Commiter avec message clair
git add .
git commit -m "fix(scope): description courte du bug corrigé"
git push origin develop
```

## Bugs fréquents Laravel — solutions rapides

```bash
# Vider tous les caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Régénérer autoload
composer dump-autoload

# Migrations en conflit
php artisan migrate:rollback --step=1
php artisan migrate

# Permissions storage
chmod -R 775 storage bootstrap/cache
```

## Format rapport de bug

```markdown
# Bug Report — [Description courte]
Date : [date]
Sévérité : Critique | Haute | Moyenne | Basse
Statut : Identifié | En cours | Corrigé

## Symptôme
[Ce qui se passe]

## Cause racine
[Pourquoi ça se passe]

## Correction appliquée
Fichier : [chemin/fichier.php]
Changement : [description]

## Tests
- [ ] Bug ne se reproduit plus
- [ ] Tests unitaires passants
- [ ] Tests de régression passants

## Commit
[hash du commit de fix]
```

## Chaînage après ce skill

→ Fix validé → `code-review` avant merge
→ Bug critique prod → invoquer `incident-response`
→ Tests mis à jour → continuer le sprint normalement
