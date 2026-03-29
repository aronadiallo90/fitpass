# CLAUDE.md — FitPass Dakar
# Chargé automatiquement à chaque session — règles toujours actives

## ⚠️ RÈGLES ABSOLUES — s'appliquent en permanence, même après /compact

### Git — commit obligatoire après chaque feature
Après toute feature ou fix terminé et testé (php artisan test = vert) :
```bash
git add <fichiers concernés>
git commit -m "type(scope): description courte"
```
NE PAS attendre que l'utilisateur le demande. NE PAS grouper plusieurs features.

### Stack réelle du projet (ne pas confondre avec les templates)
- Laravel **13** (pas 11)
- Tailwind CSS **v4** — utilise `@import 'tailwindcss'` + `@theme {}` dans app.css, PAS de tailwind.config.js
- PHPUnit **12** — utilise `#[Test]` (attribut PHP 8), PAS `/** @test */`
- `HasUuids` trait (Eloquent built-in) pour les UUIDs — PAS de `boot()` manuel
- PHP via Herd : `/c/Users/Arona/.config/herd-lite/bin/php.exe`
- Composer via Herd : `/c/Users/Arona/.config/herd-lite/bin/composer.bat`
- Vite — lancer `npm run dev` ou `npm run build` avant de consulter les pages dans un navigateur

---

## Chargement automatique
@.claude/agents.md
@.claude/rules/business.md
@.claude/rules/design.md
@.claude/rules/laravel.md
@.claude/rules/security.md
@.claude/rules/testing.md
@.claude/rules/api.md
@.claude/rules/git.md

---

## Projet
NOM=FitPass Dakar
DESCRIPTION=Abonnement universel donnant accès à toutes les salles de sport partenaires à Dakar
URL=fitpass.sn
STACK=Laravel 13 + MySQL 8 + Redis + Blade + Tailwind CSS v4 + Alpine.js

## Environnement
OS=Windows PowerShell
TOOLS=Laravel global, Git, Claude Code installés

## Services externes
PAIEMENT=PayTech Senegal (Wave + Orange Money)
SMS=Twilio
CARTES=Leaflet.js + OpenStreetMap
QR_CODE=simplesoftwareio/simple-qrcode
HEBERGEMENT=VPS DigitalOcean Ubuntu 24
CICD=GitHub Actions

---

## Modèles de données
- users (membres + admins)
- subscriptions (abonnements membres)
- subscription_plans (formules : mensuel, trimestriel, annuel)
- gyms (salles de sport partenaires)
- gym_checkins (entrées validées par QR code)
- payments (transactions PayTech)
- sms_logs (historique SMS Twilio)

## Rôles utilisateurs
member, gym_owner, admin, super_admin

## Statuts métier
Abonnement : pending -> active -> expired | cancelled
Paiement : pending -> completed | failed | refunded
Checkin : valid | invalid | expired

---

## Règles métier critiques
- Prix en FCFA entier, jamais float
- Paiement PayTech requis AVANT activation abonnement
- 1 QR code unique par membre, renouvelé à chaque recharge
- QR code valide uniquement si abonnement active
- Max 1 checkin par salle par jour par membre
- SMS Twilio à chaque changement de statut abonnement
- Gym owner ne voit que ses propres checkins

## Zones / Config spécifique
- Zone : Dakar uniquement (phase 1)
- Devise : FCFA exclusivement
- Fuseau : Africa/Dakar (UTC+0)
- Format ID abonnements : FIT-{YYYY}-{00001}

---

## Fonctionnalités à développer
- Authentification multi-rôles (member, gym_owner, admin, super_admin)
- Gestion abonnements (plans, souscription, renouvellement)
- Paiement PayTech (Wave + Orange Money)
- Génération et scan QR code (accès salle)
- Carte interactive Leaflet — salles partenaires
- Dashboard membre (abonnement, historique entrées)
- Dashboard gym owner (validations QR, stats fréquentation)
- Dashboard admin (membres, revenus, salles)
- Notifications SMS Twilio (activation, expiration, rappel)
- Landing page marketing fitpass.sn
- API publique pour bornes de scan en salle
- CI/CD GitHub Actions + déploiement VPS

---

## Démarrer le projet

Taper cette commande dans Claude Code :

```
SPRINT 0
```

Le PM lira ce fichier et produira automatiquement :
- Backlog complet avec user stories
- Estimations S/M/L par tâche
- Dépendances entre tâches
- Tâches séquentielles vs parallélisables
- Definition of Done par sprint
- Plan d'exécution sprint par sprint

---

## Notes projet
# Ajouter ici les décisions techniques importantes au fil du projet
# Exemples : choix de librairie, compromis acceptés, dette technique
## Démarrage
Si CLAUDE.md n'est pas encore rempli, demander à l'utilisateur :
1. Nom et URL du projet
2. Description en 1 phrase
3. Services externes (paiement, WhatsApp, SMS, images)
4. Palette de couleurs (hex) + typographie souhaitée
5. Inspiration visuelle (référence ou style)

Ensuite générer automatiquement les 3 fichiers et attendre validation
avant de lancer le competitive-brief.
