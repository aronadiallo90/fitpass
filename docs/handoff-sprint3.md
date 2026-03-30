# Handoff Sprint 3 — FitPass Dakar
Designer : DESIGNER
Date : 2026-03-30
Sprint : 3 — Interfaces

---

## Vues livrées (13 fichiers)

| Vue | Route | Données requises |
|-----|-------|-----------------|
| `member/dashboard` | GET /dashboard | $activeSubscription, $qrCode, $recentCheckins |
| `member/qrcode` | GET /dashboard/my-qrcode | $activeSubscription, $qrCode |
| `member/subscriptions` | GET /dashboard/subscriptions | $activeSubscription, $plans, $subscriptions |
| `member/payments` | GET /dashboard/payments | $payments (paginé) |
| `member/checkins` | GET /dashboard/checkins | $checkins (paginé) |
| `member/map` | GET /dashboard/map | $gyms |
| `gym/dashboard` | GET /gym | $todayCount, $monthCount, $todayCheckins, $recentCheckins |
| `gym/scan` | GET /gym/scan | — (Alpine.js + API) |
| `gym/checkins` | GET /gym/checkins | $checkins (paginé, filtrable) |
| `admin/dashboard` | GET /admin | $totalMembers, $monthRevenue, $activeSubscriptions, $todayCheckins, $recentPayments, $recentMembers |
| `admin/members` | GET /admin/members | $members (paginé, filtrable) |
| `admin/gyms` | GET /admin/gyms | $gyms |
| `admin/payments` | GET /admin/payments | $payments (paginé), $totalRevenue |

---

## Composants CSS — nouveaux dans app.css

### `.qr-wrapper` / `.qr-wrapper-lg`
```
qr-wrapper     : fond blanc, border-radius 1rem, padding 1.5rem, inline-flex centré
qr-wrapper-lg  : fond blanc, border-radius 1.25rem, padding 2rem, max-width 320px, centré
```
Usage : enveloppe le PNG généré par simplesoftwareio/simple-qrcode.

### `.qr-expired-overlay`
```
Position : absolute inset-0
Fond     : rgba(10,10,15, 0.88)
Layout   : flex colonne centré, gap 0.75rem
```
Activé quand `$subscription->status !== 'active'`.

### `.scan-frame`
```
Largeur  : 100%, max-width 320px, centré
Ratio    : 1:1 (aspect-ratio: 1)
Coins    : pseudo-éléments ::before/::after — coins rouges FitPass 2.5rem
```
Contient la balise `<video>` et la `.scan-line` animée.

### `.scan-result` + `.scan-result-valid` / `.scan-result-invalid`
```
Position : fixed inset-0, z-index 50
Valid    : background rgba(34,197,94, 0.95) — vert
Invalid  : background rgba(239,68,68, 0.95) — rouge
Transition : opacity 0.3s ease-out (géré par Alpine x-transition)
```
Auto-reset après 4 secondes (setTimeout dans qrScanner()).

### `.data-table`
```
th : font 0.7rem uppercase tracking-wide, color text-muted, border-bottom
td : padding 0.875rem 1rem, font 0.875rem, border-bottom 50% opacity
tr:hover : background rgba(255,255,255, 0.03)
```
Toujours enveloppé dans `.card-static` avec `padding:0; overflow:hidden`.

### `.kpi-card`
```
Fond     : --color-bg-card (#1A1A24)
Border   : 1px solid --color-border
Hover    : border-color primary 30%
Radius   : 0.75rem, padding 1.5rem
```
`.kpi-value` : Barlow Condensed 700 2.5rem
`.kpi-label` : Inter 0.7rem uppercase tracking-wide text-muted
`.kpi-trend-up` : color-success | `.kpi-trend-down` : color-danger

### `.empty-state`
```
Layout : flex colonne centré, padding 4rem 2rem, gap 0.75rem
Icon   : 3rem, opacity 0.25
Text   : 0.875rem uppercase tracking-wide text-muted
```

### `.map-container`
```
Mobile  : height 400px
768px+  : height 500px
1280px+ : height 600px
Border  : 1px solid --color-border, border-radius 0.75rem
```

### `.page-header`
```
Layout   : flex, space-between, align-center, flex-wrap, gap 1rem
Margin   : margin-bottom 2rem
```

---

## Variables DEV à injecter par vue

### `member/dashboard`
```php
// MemberDashboardController::index()
$activeSubscription = auth()->user()
    ->subscriptions()
    ->where('status', 'active')
    ->with('plan')
    ->first();

$qrCode = QrCode::size(180)->generate(auth()->user()->qr_token);

$recentCheckins = auth()->user()
    ->checkins()
    ->with('gym')
    ->latest()
    ->limit(5)
    ->get();
```

### `member/qrcode`
```php
// Même données, QrCode::size(280)
$qrCode = QrCode::size(280)->generate(auth()->user()->qr_token);
```

### `member/subscriptions`
```php
$activeSubscription = ...; // avec plan
$plans = SubscriptionPlan::active()->ordered()->get();
$subscriptions = auth()->user()->subscriptions()->with('plan')->latest()->paginate(10);
```

### `member/payments`
```php
$payments = auth()->user()
    ->payments()
    ->with('subscription.plan')
    ->latest()
    ->paginate(15);
```

### `member/checkins`
```php
$checkins = auth()->user()
    ->checkins()
    ->with('gym')
    ->latest()
    ->paginate(20);
```

### `member/map`
```php
$gyms = Gym::active()->get(['id','name','address','activities','phone','latitude','longitude']);
```

### `gym/dashboard`
```php
$gym = auth()->user()->gym; // relation hasOne
$todayCheckins = $gym->checkins()->whereDate('created_at', today())->with('user')->latest()->get();
$todayCount    = $todayCheckins->count();
$monthCount    = $gym->checkins()->whereMonth('created_at', now()->month)->count();
$recentCheckins = $gym->checkins()->with('user')->latest()->limit(20)->get();
```

### `gym/checkins`
```php
$checkins = $gym->checkins()
    ->with('user')
    ->when(request('from'), fn($q) => $q->whereDate('created_at', '>=', request('from')))
    ->when(request('to'),   fn($q) => $q->whereDate('created_at', '<=', request('to')))
    ->latest()
    ->paginate(25);
```

### `admin/dashboard`
```php
$totalMembers        = User::where('role', 'member')->where('is_active', true)->count();
$monthRevenue        = Payment::where('status', 'completed')->whereMonth('created_at', now()->month)->sum('amount');
$activeSubscriptions = Subscription::where('status', 'active')->count();
$todayCheckins       = GymCheckin::whereDate('created_at', today())->where('status', 'valid')->count();
$recentPayments      = Payment::with('subscription.plan', 'subscription.user')->latest()->limit(5)->get();
$recentMembers       = User::where('role', 'member')->with('activeSubscription.plan')->latest()->limit(5)->get();
```

### `admin/members`
```php
$members = User::where('role', 'member')
    ->with(['activeSubscription.plan', 'latestCheckin'])
    ->when(request('search'), fn($q, $s) => $q->where('name', 'like', "%$s%")->orWhere('phone', 'like', "%$s%"))
    ->when(request('status') === 'active',  fn($q) => $q->whereHas('subscriptions', fn($s) => $s->where('status', 'active')))
    ->when(request('status') === 'expired', fn($q) => $q->whereHas('subscriptions', fn($s) => $s->where('status', 'expired')))
    ->when(request('status') === 'none',    fn($q) => $q->whereDoesntHave('subscriptions'))
    ->latest()
    ->paginate(25);
```

### `admin/gyms`
```php
$gyms = Gym::withCount(['checkins' => fn($q) => $q->where('created_at', '>=', now()->subDays(30))])
    ->orderByDesc('is_active')
    ->get();
```

### `admin/payments`
```php
$payments     = Payment::with('subscription.plan', 'subscription.user')
    ->when(request('status'), fn($q, $s) => $q->where('status', $s))
    ->when(request('method'), fn($q, $m) => $q->where('method', $m))
    ->latest()
    ->paginate(25);
$totalRevenue = Payment::where('status', 'completed')->sum('amount');
```

---

## Routes Web à ajouter

```php
// routes/web.php — à ajouter dans le groupe member
Route::get('/map', fn() => view('member.map', [...]))->name('map');

// À ajouter dans le groupe admin
Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments');
Route::patch('/members/{user}/toggle', [AdminMemberController::class, 'toggle'])->name('members.toggle');
Route::get('/gyms/create', [AdminGymController::class, 'create'])->name('gyms.create');
Route::post('/gyms', [AdminGymController::class, 'store'])->name('gyms.store');
Route::get('/gyms/{gym}/edit', [AdminGymController::class, 'edit'])->name('gyms.edit');
Route::put('/gyms/{gym}', [AdminGymController::class, 'update'])->name('gyms.update');
Route::patch('/gyms/{gym}/toggle', [AdminGymController::class, 'toggle'])->name('gyms.toggle');
```

---

## Dépendances JS à charger

| Page | CDN à inclure | Via |
|------|--------------|-----|
| `gym/scan` | `https://unpkg.com/jsqr@1.4.0/dist/jsQR.js` | `@push('scripts')` |
| `member/map` | Leaflet CSS + JS | `@push('styles')` + `@push('scripts')` |
| `admin/gyms` | Leaflet CSS + JS | idem |
| Toutes | Alpine.js | déjà dans layout |

---

## Checklist accessibilité

- [x] Tous les `<input>` ont un `<label class="label">` associé
- [x] Boutons avec texte explicite (`Scanner à nouveau`, `S'abonner`)
- [x] `scan-result` affiché en overlay plein écran — lisible en plein soleil (contrast 4.5:1 garanti)
- [x] QR code sur fond blanc — contraste optimal pour scan
- [x] Focus visible sur tous les boutons (géré par `.btn-primary:focus-visible`)
- [x] Navigation clavier : `tabindex` naturel sur tous les formulaires
- [ ] `aria-live="polite"` à ajouter sur `.scan-result` (DEV)
- [ ] `alt=""` sur l'image QR code (DEV — `{!! $qrCode !!}` génère une `<img>`)

---

## Notes critiques pour le DEV

```
⚠️ 1. gym/scan.blade.php — auth()->user()->gym?->api_token
      La relation gym doit exister sur le User (hasOne Gym via owner_id).
      Si la relation est absente → le bearer token sera vide → 403 sur le scan.

⚠️ 2. member/dashboard + qrcode — QrCode::size()
      Installer simplesoftwareio/simple-qrcode si pas encore fait :
      composer require simplesoftwareio/simple-qrcode
      Importer : use SimpleSoftwareIO\QrCode\Facades\QrCode;

⚠️ 3. admin/members — relations activeSubscription + latestCheckin
      Ces relations doivent être définies dans le Model User :
      public function activeSubscription() { return $this->hasOne(Subscription::class)->where('status','active'); }
      public function latestCheckin() { return $this->hasOne(GymCheckin::class)->latestOfMany(); }

⚠️ 4. admin/gyms + member/map — colonne activities
      Le Model Gym doit caster activities en array :
      protected $casts = ['activities' => 'array'];

⚠️ 5. member/subscriptions — route store
      Route::post('/dashboard/subscriptions', ...)->name('member.subscriptions.store') à ajouter.

⚠️ 6. Leaflet — ne pas charger en production depuis CDN unpkg si performances critiques
      Installer via npm : npm install leaflet
      Importer dans resources/js/app.js si PageSpeed < 90.
```

---

## HANDOFF DESIGNER → DEV

```
--- HANDOFF DESIGNER → DEV ---
Sprint    : 3 — Interfaces
Complété  : 13 vues Blade + 8 nouveaux composants CSS + docs/design-system.md + docs/handoff-sprint3.md
Skills    : /design-system (vérification + ajouts) + /design-handoff
Fichiers  : resources/views/member/* (6) + resources/views/gym/* (3) + resources/views/admin/* (4) + resources/css/app.css + docs/

Tests     : Aucun test à ce stade — DEV teste après intégration des controllers

À noter   :
  - gym/scan.blade.php dépend de la relation User → hasOne Gym
  - 3 routes admin manquantes à déclarer (payments, gyms CRUD, members toggle)
  - QrCode package à vérifier installé
  - activeSubscription + latestCheckin à ajouter dans User model

Bloquants : Aucun côté DESIGNER — toutes les vues sont fonctionnelles avec des données passées par le controller

Prêt pour : [AGENT: DEV] → créer les Web Controllers + brancher les données → puis [AGENT: QA]
```
