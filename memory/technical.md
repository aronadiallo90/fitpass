# Technical — FitPass Dakar
Gotchas techniques, solutions trouvées, pièges à éviter.
Lire avant toute modification technique pour éviter de répéter les mêmes erreurs.

---

## Pièges Laravel 13 sur ce projet

### QUEUE_CONNECTION en test
```xml
<!-- phpunit.xml — TOUJOURS null, jamais sync ni array -->
<env name="QUEUE_CONNECTION" value="null"/>
```
- `sync` → crash `InvokeDeferredCallbacks` (jobs s'exécutent pendant le teardown DB)
- `array` → `The [array] queue connection has not been configured.` en Laravel 13
- `null` → jobs silencieusement ignorés ✅

### Rate limiting — ne pas utiliser throttleWithRedis()
```php
// ❌ bootstrap/app.php — SUPPRIMÉ, cause RedisException en test
$middleware->throttleWithRedis();

// ✅ AppServiceProvider::boot() — cache-based, fonctionne sans Redis
RateLimiter::for('api', fn($request) => Limit::perMinute(60)->by($request->ip()));
RateLimiter::for('auth', fn($request) => Limit::perMinute(10)->by($request->ip()));
RateLimiter::for('admin', fn($request) => Limit::perMinute(30)->by($request->ip()));
RateLimiter::for('checkins', fn($request) => Limit::perMinute(60)->by($request->ip()));

// ✅ Dans routes — toujours named limiter (pas throttle:60,1)
Route::post('/checkins/validate', ...)->middleware('throttle:checkins');
```

### GymCheckin — ENUM status strict
```php
// Migration ENUM — seulement ces 3 valeurs autorisées
$table->enum('status', ['valid', 'invalid', 'expired']);

// ❌ Interdit — viole la contrainte ENUM
GymCheckin::create(['status' => 'invalid_qr']);      // CRASH
GymCheckin::create(['status' => 'no_subscription']); // CRASH

// ✅ Correct — status='invalid' + failure_reason pour le détail
GymCheckin::create([
    'status' => 'invalid',
    'failure_reason' => 'QR code inconnu',
]);
```

### Nullable FKs dans gym_checkins
Les colonnes `user_id` et `subscription_id` sont **nullable** (migration ajoutée Sprint 2).
Nécessaire pour enregistrer les tentatives d'accès invalides (QR inconnu → user_id null).
```
Migration : 2026_03_30_000001_make_gym_checkins_user_subscription_nullable.php
```

### HasUuids — ne jamais utiliser boot() manuel
```php
// ✅ Correct — trait built-in Laravel
use Illuminate\Database\Eloquent\Concerns\HasUuids;
class Subscription extends Model { use HasFactory, HasUuids; }

// ❌ Obsolète — ne plus faire
protected static function boot(): void {
    parent::boot();
    static::creating(fn($m) => $m->id = (string) Str::uuid());
}
```

## Structure des services

### Interfaces obligatoires
```
app/Services/Interfaces/
  SubscriptionServiceInterface.php
  PaymentServiceInterface.php        → FakePaymentService (actif)
  CheckinServiceInterface.php
  SmsServiceInterface.php            → FakeSmsService (actif)
```

### Bindings AppServiceProvider
```php
// app/Providers/AppServiceProvider.php
$this->app->bind(SubscriptionServiceInterface::class, SubscriptionService::class);
$this->app->bind(PaymentServiceInterface::class, FakePaymentService::class);   // ← remplacer quand PayTech dispo
$this->app->bind(CheckinServiceInterface::class, CheckinService::class);
$this->app->bind(SmsServiceInterface::class, FakeSmsService::class);           // ← remplacer quand Twilio dispo
```

## Tailwind v4 — syntaxe obligatoire

```css
/* ✅ resources/css/app.css */
@import 'tailwindcss';

@theme {
    --color-primary: #FF3B3B;
    --font-heading: 'Barlow Condensed', sans-serif;
}

@layer components {
    .btn-primary { ... }
}
```
- Pas de `tailwind.config.js`
- Pas de directives `@tailwind base/components/utilities`
- Variables dans `@theme {}`, composants dans `@layer components {}`

## Commandes Herd (Windows)

```bash
/c/Users/Arona/.config/herd-lite/bin/php.exe artisan test
/c/Users/Arona/.config/herd-lite/bin/php.exe artisan migrate
/c/Users/Arona/.config/herd-lite/bin/composer.bat install
```

## Architecture Controllers

```
app/Http/Controllers/
  Api/
    Auth/AuthController.php
    SubscriptionController.php
    GymController.php
    CheckinController.php
    Webhook/PayTechController.php
  Web/
    Auth/
      LoginController.php
      RegisterController.php
      TwoFactorController.php
```

## Tests — conventions

```php
// PHPUnit 12 — attribut #[Test] OBLIGATOIRE
use PHPUnit\Framework\Attributes\Test;

#[Test]
public function it_creates_a_subscription(): void { ... }

// Toujours RefreshDatabase + factories
use Illuminate\Foundation\Testing\RefreshDatabase;

// Sanctum pour auth dans Feature tests
use Laravel\Sanctum\Sanctum;
Sanctum::actingAs(User::factory()->create(['role' => 'member']));

// Queue::fake() — uniquement si assertPushed() nécessaire (Unit tests)
// Ne PAS utiliser dans Feature tests (pas nécessaire avec QUEUE=null)
```
