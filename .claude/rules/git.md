# Git Workflow — [DEV + CICD]
# Fichier réutilisable — ne pas modifier entre projets

## Branches

| Branche | Usage | Règle |
|---|---|---|
| `main` | Production | Protégée — merge via PR uniquement |
| `develop` | Intégration dev | Base de toutes les features |
| `feature/nom-court` | Nouvelle fonctionnalité | Depuis develop |
| `fix/description` | Correction de bug | Depuis develop |
| `hotfix/description` | Correction urgente prod | Depuis main |
| `release/v1.x.x` | Préparation release | Depuis develop |

## Conventional Commits

Format : `type(scope): description courte en minuscules`

| Type | Usage |
|---|---|
| `feat` | Nouvelle fonctionnalité |
| `fix` | Correction de bug |
| `refactor` | Refactoring sans changement fonctionnel |
| `docs` | Documentation uniquement |
| `test` | Ajout ou modification de tests |
| `chore` | Maintenance, dépendances, config |
| `style` | Formatage, espaces (pas de logique) |
| `perf` | Amélioration performance |

Exemples corrects :
```
feat(orders): ajouter système annulation commande
fix(payment): corriger validation signature HMAC
refactor(services): extraire logique notification
test(api): ajouter feature tests endpoint orders
chore(deps): mettre à jour Laravel 11.x
docs(readme): ajouter instructions installation
```

## Commit automatique — règle absolue

Après chaque feature ou fix terminé et testé :

```bash
git add .
git commit -m "type(scope): description courte"
git push origin develop
```

**Ne jamais attendre qu'on demande le commit.**
Si php artisan test échoue → corriger d'abord, commiter ensuite.

## Ce qu'on ne commit JAMAIS

```bash
# Toujours dans .gitignore :
.env
.env.*
!.env.example
/vendor/
/node_modules/
/storage/framework/cache/
/storage/logs/
/public/hot
/public/storage
*.key
.phpunit.result.cache
```

## .gitignore Laravel standard

```
/node_modules
/public/hot
/public/storage
/storage/*.key
/vendor
.env
.env.backup
.env.production
.phpunit.result.cache
docker-compose.override.yml
/.idea
/.vscode
Thumbs.db
.DS_Store
```

## Merge strategy

```bash
# feature → develop : squash merge
git checkout develop
git merge --squash feature/mon-feature
git commit -m "feat(scope): résumé de la feature"

# develop → main : merge commit (préserver historique)
git checkout main
git merge --no-ff develop -m "release: v1.2.0"

# hotfix → main ET develop
git checkout main && git merge --no-ff hotfix/description
git checkout develop && git merge --no-ff hotfix/description
```

## Tags de version (SemVer)

```bash
# MAJEUR.MINEUR.PATCH
# MAJEUR : changement breaking
# MINEUR : nouvelle fonctionnalité rétrocompatible
# PATCH  : correction de bug

git tag -a v1.0.0 -m "Release v1.0.0 — première mise en production"
git push origin v1.0.0
```

## Vérification avant push

```bash
# Toujours dans cet ordre :
php artisan test          # 0 erreur obligatoire
php artisan pint          # formatage PSR-12
git status                # vérifier les fichiers
git diff --staged         # relire les changements
git add .
git commit -m "type: description"
git push origin develop
```
