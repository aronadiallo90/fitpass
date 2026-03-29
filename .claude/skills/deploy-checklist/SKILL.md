---
name: deploy-checklist
description: >
  Checklist complète à exécuter obligatoirement avant tout déploiement en production.
  Déclencher quand le CICD ou le DEV s'apprête à déployer, quand l'utilisateur dit
  "on déploie", "deploy", "mise en prod", "AGENT: CICD — deploy", ou en fin de sprint.
  Zéro exception — même pour un hotfix urgent. Bloque le déploiement si un item échoue.
---

# Deploy Checklist — [CICD + QA + SECURITY]

Checklist obligatoire avant chaque déploiement. Un seul item qui échoue =
déploiement bloqué jusqu'à résolution.

## Checklist complète

### Code & Tests
- [ ] `php artisan test` → 0 erreur, 0 warning
- [ ] `composer audit` → 0 vulnérabilité critique
- [ ] `npm audit` → 0 vulnérabilité critique
- [ ] Pas de `dd()`, `dump()`, `var_dump()` oubliés dans le code
- [ ] Pas de `console.log()` oublié dans le JS
- [ ] Code review effectué (`code-review` invoqué)

### Base de données
- [ ] Toutes les migrations sont dans `database/migrations/`
- [ ] `php artisan migrate --pretend` → aucune erreur
- [ ] Seeders de production ne contiennent pas de données de test
- [ ] Backup de la DB de production fait avant déploiement
- [ ] Index créés sur toutes les colonnes de recherche fréquente

### Variables d'environnement
- [ ] `.env` de production configuré avec les vraies valeurs
- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false` ← critique
- [ ] `APP_KEY` défini
- [ ] Clés API (PayTech, WATI, Twilio, Cloudinary) configurées
- [ ] `DB_*` pointent vers la DB de production
- [ ] `REDIS_*` configurés
- [ ] `.env` non commité dans git (vérifier `.gitignore`)

### Cache & Performance
- [ ] `php artisan config:cache`
- [ ] `php artisan route:cache`
- [ ] `php artisan view:cache`
- [ ] `php artisan optimize`
- [ ] Assets compilés : `npm run build`

### Sécurité
- [ ] Rate limiting actif sur les routes sensibles
- [ ] 2FA admin configuré
- [ ] Signature HMAC webhook PayTech configurée
- [ ] HTTPS forcé (vérifier nginx config)
- [ ] Headers de sécurité en place (X-Frame-Options, etc.)
- [ ] Pas de stack traces exposées (tester intentionnellement une erreur 500)

### Infrastructure
- [ ] Docker image buildée sans erreur
- [ ] `docker-compose up` fonctionne en local
- [ ] Pipeline GitHub Actions → tous les steps au vert
- [ ] Rollback plan documenté (quelle commande si ça plante ?)
- [ ] Health check endpoint `/health` répond 200

### Post-déploiement
- [ ] `php artisan migrate --force` exécuté
- [ ] Site accessible sur le domaine de production
- [ ] Login admin fonctionnel
- [ ] Tester le parcours critique : inscription → commande → paiement
- [ ] Vérifier les logs : `tail -f storage/logs/laravel.log`
- [ ] Monitoring actif (uptime, erreurs)

## Format rapport de déploiement

```markdown
# Déploiement — [Projet] v[version]
Date : [date et heure]
Environnement : production
Déployé par : [nom]

## Résultats checklist
✅ Code & Tests — OK
✅ Base de données — OK
✅ Variables d'environnement — OK
✅ Cache & Performance — OK
✅ Sécurité — OK
✅ Infrastructure — OK

## Post-déploiement
✅ Site accessible
✅ Parcours critique testé
✅ Logs propres

## Version déployée
Commit : [hash]
Tag : v[x.y.z]

## Rollback si nécessaire
`git revert [hash] && git push && ssh prod 'cd /var/www && php artisan migrate:rollback'`
```

## Si un item échoue

Ne pas déployer. Corriger d'abord, recommencer la checklist depuis le début.
Documenter le problème et la solution dans le rapport.

## Chaînage après ce skill

→ Si tout OK : déployer + invoquer `stakeholder-update` pour informer le client
→ Si incident après déploiement : invoquer `incident-response` immédiatement
