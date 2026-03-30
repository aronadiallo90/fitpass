# Decisions — FitPass Dakar
Décisions techniques et produit prises au fil du projet.
Toujours lire avant de proposer une alternative technique.

---

## Architecture

| Décision | Choix retenu | Raison | Date |
|----------|-------------|--------|------|
| Framework | Laravel 13 (pas 11) | Version actuelle stable | Sprint 1 |
| CSS | Tailwind v4 — `@theme {}` dans app.css, **pas de tailwind.config.js** | Nouvelle API v4 | Sprint 1 |
| Tests | PHPUnit 12 — attribut `#[Test]`, **jamais `/** @test */`** | Syntaxe PHP 8 moderne | Sprint 1 |
| UUID | Trait `HasUuids` built-in Laravel — **jamais de `boot()` manuel** | Plus simple, officiel | Sprint 1 |
| Auth | Laravel Sanctum (API tokens + sessions Web) | Multi-rôles, stateful + stateless | Sprint 1 |
| Queue driver tests | `QUEUE_CONNECTION=null` dans phpunit.xml | `sync` crash InvokeDeferredCallbacks, `array` invalide en Laravel 13 | Sprint 2 |
| Rate limiting | `RateLimiter::for()` dans AppServiceProvider + named limiters dans routes | `throttleWithRedis()` force Redis — crash si Redis absent en test | Sprint 2 |
| Paiement | FakePaymentService (implémente PaymentServiceInterface) | Clé API PayTech sandbox non disponible | Sprint 2 |
| SMS | FakeSmsService (implémente SmsServiceInterface) | Credentials Twilio non disponibles | Sprint 2 |
| QR Code | `simplesoftwareio/simple-qrcode` server-side PNG | 0 dépendance client | Sprint 1 |
| Carte | Leaflet.js + OpenStreetMap | 0 coût, 0 clé API | Sprint 1 |
| Deploy | VPS DigitalOcean Ubuntu 24 + GitHub Actions | Contrôle total | Sprint 0 |

## Règles métier figées

| Règle | Détail |
|-------|--------|
| Prix | Integer FCFA, **jamais float** |
| QR Code | Valide seulement si `subscription.status = active` ET `expires_at >= today` |
| Checkin | Max 1 par salle par jour par membre |
| Abonnement | Max 1 actif simultanément par membre |
| SMS | Envoyé à chaque changement de statut abonnement |
| Gym owner | Ne voit QUE les checkins de ses propres salles |
| Paiement | PayTech requis AVANT activation abonnement |
| Référence | Format `FIT-{YYYY}-{00001}` |

## Décisions ouvertes (à trancher)

- [ ] Bibliothèque scan QR côté client : `html5-qrcode` ou `jsQR` + Alpine.js ?
- [ ] Graphiques dashboard : `Chart.js` ou SVG Blade ?
- [ ] Upload photos salles : stockage local ou DigitalOcean Spaces ?
- [ ] Clé API PayTech sandbox → remplacer FakePaymentService par PaymentService réel
- [ ] Credentials Twilio → remplacer FakeSmsService par SmsService réel
- [ ] Plan Découverte : 4 séances valables 30j ou illimité dans le temps ?
- [ ] Langue : français uniquement ou français + wolof ?
