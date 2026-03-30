# Preferences — FitPass Dakar
Comment Claude doit se comporter sur ce projet. Lire impérativement au début de chaque session.

---

## Communication

- **Langue :** Français uniquement (code en anglais, commentaires en français)
- **Format :** Code d'abord, explication courte ensuite
- **Longueur :** Réponses courtes et directes — pas de longues introductions
- **Emojis :** Uniquement dans les tableaux markdown ou titres de sections — jamais dans le prose
- **Confirmation :** Ne pas demander confirmation pour les actions mineures (créer un fichier, écrire du code) — agir directement

## Comportements obligatoires

- Toujours annoncer `**[AGENT: NOM]**` avant chaque bloc de travail
- Lire `agents.md` avant de commencer un sprint
- Lire `BACKLOG.md` pour connaître le périmètre du sprint courant
- Lancer `php artisan test` avant tout commit — bloquer si un test échoue
- Faire le commit sans attendre que Mamadou le demande (après feature terminée + tests verts)
- Mettre à jour `BACKLOG.md` + `ROADMAP.md` en fin de sprint

## Comportements interdits

- **Ne jamais** mélanger les rôles agents (DEV écrit les tests → interdit, c'est QA)
- **Ne jamais** utiliser `/** @test */` (PHPUnit 12 → `#[Test]` uniquement)
- **Ne jamais** créer de `tailwind.config.js` (Tailwind v4 → `@theme {}` dans app.css)
- **Ne jamais** utiliser `$middleware->throttleWithRedis()` (Redis absent en test)
- **Ne jamais** créer une route Web sans que la vue Blade correspondante existe
- **Ne jamais** utiliser de couleurs Tailwind génériques (gray-500, blue-600 — toujours les variables FitPass)
- **Ne jamais** stocker un prix en float (FCFA integer uniquement)
- **Ne jamais** grouper plusieurs features dans un seul commit

## Stack — valeurs exactes à utiliser

```
Laravel         : 13 (pas 11, pas 12)
Tailwind CSS    : v4 — @import 'tailwindcss' + @theme {} (pas tailwind.config.js)
PHPUnit         : 12 — #[Test] attribut PHP 8 (pas /** @test */)
UUID            : HasUuids trait built-in (pas boot() manuel)
Queue (tests)   : null (pas sync, pas array)
Rate limiting   : RateLimiter::for() + named limiters (pas throttleWithRedis())
PHP path        : /c/Users/Arona/.config/herd-lite/bin/php.exe
Composer path   : /c/Users/Arona/.config/herd-lite/bin/composer.bat
```

## Design system — règles strictes

```
PRIMARY         : #FF3B3B   (rouge vif FitPass)
SECONDARY       : #FF8C00   (orange accent)
BACKGROUND      : #0A0A0F   (fond noir profond)
BACKGROUND_SOFT : #13131A   (fond cards)
BACKGROUND_CARD : #1A1A24
TEXT            : #FFFFFF
TEXT_MUTED      : #8888A0
BORDER          : #2A2A38
SUCCESS         : #22C55E
WARNING         : #F59E0B
DANGER          : #EF4444

FONT_HEADING    : Barlow Condensed (titres, uppercase)
FONT_BODY       : Inter (corps)
```

## Erreurs passées à ne pas reproduire

| Erreur | Ce qui s'est passé | Règle |
|--------|-------------------|-------|
| DEV a écrit les tests Sprint 2 | Violation chainage — QA écrit les tests | DEV code, QA teste — sans exception |
| `sync` dans phpunit.xml | Crash InvokeDeferredCallbacks Laravel 13 | Toujours `null` pour QUEUE en test |
| `throttleWithRedis()` dans bootstrap/app.php | Crash Redis en test | Named limiters via RateLimiter::for() |
| Routes Web sans vues | `member/subscriptions.blade.php` manquante | DESIGNER crée la vue avant que DEV déclare la route |
| S2-P5 non livré | Pages plans/paiement absentes | HANDOFF formel obligatoire entre agents |
