# Git Workflow — FitPass Dakar

## Architecture des branches

```
origin/main          ← Production (VPS DigitalOcean)
  ↑ PR validée CI + review
origin/develop       ← Intégration (toujours stable, CI vert)
  ↑ PR feature validée CI
feature/sprint-X-*  ← Développement actif (local + origin)
fix/description     ← Corrections de bugs
hotfix/description  ← Corrections urgentes prod (depuis main)
```

## Règles par branche

| Branche | Protection | Qui merge | CI obligatoire | Deploy |
|---------|-----------|-----------|----------------|--------|
| `main` | Protégée | PR depuis develop uniquement | ✅ | ✅ Auto VPS |
| `develop` | Protégée | PR depuis feature/* | ✅ | ❌ |
| `feature/*` | Libre | Dev local → origin | ✅ | ❌ |
| `fix/*` | Libre | Dev local → origin | ✅ | ❌ |
| `hotfix/*` | Libre | → main ET develop | ✅ | ✅ Auto VPS |

## Nommage des branches

```bash
# Feature sprint — format obligatoire
feature/sprint-6a-gym-search
feature/sprint-6b-leaflet-routing
feature/sprint-7-sms-notifications
feature/sprint-8-paytech-webhook

# Fix bug — description courte
fix/cache-closure-serialization
fix/alpine-js-not-initialized
fix/checkin-date-comparison

# Hotfix production urgente
hotfix/paytech-signature-validation
hotfix/qrcode-expired-check

# Release
release/v1.0.0
release/v1.1.0
```

## Workflow quotidien

### 1. Démarrer une feature
```bash
git checkout develop
git pull origin develop
git checkout -b feature/sprint-7-sms-notifications
```

### 2. Développer + commiter
```bash
# Commiter après chaque feature atomique
git add app/Services/SmsService.php app/Jobs/SendWelcomeSms.php
git commit -m "feat(sms): service Twilio + job SMS bienvenue"

# Jamais : git add .  (risque d'inclure .env, logs, etc.)
```

### 3. Pousser et créer la PR
```bash
php artisan test          # 0 erreur obligatoire avant push
git push origin feature/sprint-7-sms-notifications

# Créer PR sur GitHub : feature/sprint-7... → develop
# Remplir le template de PR
```

### 4. Merge vers develop (après CI vert)
```bash
# Sur GitHub — Squash and merge (un seul commit propre dans develop)
# Message du commit : feat(sms): service Twilio complet + tests
```

### 5. Release vers production
```bash
# Sur GitHub — Merge commit (préserver l'historique)
# develop → main  (PR de release)
# CI doit être vert + review obligatoire
# Deploy automatique déclenché par push sur main
```

## Stratégie de merge

| Sens | Stratégie | Raison |
|------|-----------|--------|
| `feature/*` → `develop` | **Squash merge** | Un commit propre par feature |
| `develop` → `main` | **Merge commit** | Préserver l'historique de release |
| `hotfix/*` → `main` | **Merge commit** | Traçabilité urgence |
| `hotfix/*` → `develop` | **Cherry-pick** | Reporter le fix sans le merge de main |

## Conventional Commits — rappel

```
feat(scope): nouvelle fonctionnalité
fix(scope): correction de bug
refactor(scope): refactoring sans changement fonctionnel
test(scope): ajout / modification de tests
docs(scope): documentation uniquement
chore(scope): maintenance, dépendances, config
perf(scope): amélioration performance
style(scope): formatage PSR-12 uniquement

# Exemples FitPass
feat(sms): envoyer SMS bienvenue à l'activation
fix(checkin): utiliser whereDate au lieu de datetime comparison
refactor(gymsearch): extraire buildQuery dans méthode privée
test(api): couvrir endpoints search + profile gym
chore(deps): installer alpinejs via npm
```

## Scopes courants FitPass

```
auth        → Authentification + 2FA
member      → Dashboard et pages membre
gym         → Salles, programmes, horaires, photos
checkin     → Validation QR code + entrées
subscription → Abonnements et plans
payment     → PayTech Wave + Orange Money
sms         → Notifications Twilio
admin       → Dashboard et pages admin
api         → Endpoints REST
frontend    → Blade + Alpine.js + Vite
ci          → GitHub Actions
deploy      → VPS + scripts déploiement
```

## CI/CD — Déclencheurs

### ci.yml (Tests + Lint)
- Push sur `main` ou `develop`
- Pull Request vers `main` ou `develop`
- Toute feature branch poussée

### deploy.yml (Production)
- Push sur `main` uniquement
- Conditionnel : `vars.DEPLOY_ENABLED == 'true'`
- Activer dans GitHub → Settings → Variables → DEPLOY_ENABLED = true

## Protection des branches (à configurer sur GitHub)

### main
- Require PR before merging
- Require 1 approval (ou 0 si solo dev)
- Require status checks: `PHP Tests` (ci.yml)
- Do not allow force pushes
- Do not allow deletions

### develop
- Require PR before merging
- Require status checks: `PHP Tests` (ci.yml)
- Do not allow force pushes

## Commandes utiles

```bash
# Voir toutes les branches locales et distantes
git branch -a

# Supprimer une branch locale après merge
git branch -d feature/sprint-6a-gym-search

# Supprimer la branch distante après merge
git push origin --delete feature/sprint-6a-gym-search

# Mettre à jour develop depuis main (après hotfix)
git checkout develop
git merge --no-ff main -m "chore(merge): reporter hotfix paytech depuis main"

# Voir l'état des branches vs origin
git fetch --all && git branch -vv
```
