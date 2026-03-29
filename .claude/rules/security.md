# Règles Sécurité — [SECURITY]
# Fichier réutilisable — ne pas modifier entre projets

## Auth & Accès

- Sanctum pour toutes les routes API
- Token dans le header : `Authorization: Bearer {token}`
- 2FA obligatoire : tous les comptes admin et super_admin
- Sessions : `secure`, `httponly`, `samesite=strict` en production
- Passwords : bcrypt min 12 caractères, vérifier HaveIBeenPwned si possible

## Rate Limiting

```php
// Routes publiques
RateLimiter::for('api', fn($request) => Limit::perMinute(60));

// Routes auth (login, register, forgot-password)
RateLimiter::for('auth', fn($request) => Limit::perMinute(10));

// Routes admin sensibles
RateLimiter::for('admin', fn($request) => Limit::perMinute(30));
```

## Webhooks externes (PayTech, Twilio, etc.)

```php
// Toujours valider la signature HMAC avant traitement
private function validateHmacSignature(Request $request, string $secret): bool
{
    $signature = $request->header('X-Webhook-Signature');
    $expected = hash_hmac('sha256', $request->getContent(), $secret);
    return hash_equals($expected, $signature);
}

// Early return si signature invalide
if (!$this->validateHmacSignature($request, config('services.paytech.secret'))) {
    return response()->json(['message' => 'Unauthorized'], 401);
}
```

## Production — règles absolues

```php
// config/app.php
'debug' => env('APP_DEBUG', false), // JAMAIS true en production

// Handler d'exceptions — jamais de stack trace exposée
public function render($request, Throwable $e): Response
{
    if (app()->isProduction()) {
        Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
        return response()->json(['message' => 'Une erreur est survenue'], 500);
    }
    return parent::render($request, $e);
}
```

## Logs — données sensibles interdites

```php
// ❌ Jamais logger des données sensibles
Log::info('Paiement', ['card' => $cardNumber, 'cvv' => $cvv]);

// ✅ Logger uniquement les métadonnées
Log::info('Paiement initié', ['order_id' => $order->id, 'amount' => $amount]);
```

## Variables d'environnement

```bash
# Jamais committer .env ou backup .env
# Toujours dans .gitignore :
.env
.env.*
!.env.example
```

## Headers de sécurité (Middleware)

```php
// Ajouter via middleware sur toutes les routes
'X-Content-Type-Options'  => 'nosniff',
'X-Frame-Options'          => 'SAMEORIGIN',
'X-XSS-Protection'         => '1; mode=block',
'Referrer-Policy'          => 'strict-origin-when-cross-origin',
'Permissions-Policy'       => 'geolocation=(self)',
```

## Validation des inputs

```php
// Toujours valider et typer strictement
'email'   => ['required', 'email:rfc,dns', 'max:255'],
'phone'   => ['required', 'regex:/^(\+221|00221)?[7][0-9]{8}$/'], // format SN
'amount'  => ['required', 'integer', 'min:1', 'max:10000000'],     // FCFA
'status'  => ['required', Rule::in(['active', 'suspended', 'expired'])],
```

## Checklist audit avant livraison

### Authentification
- [ ] 2FA activé sur tous les comptes admin
- [ ] Sessions expirées correctement (timeout configuré)
- [ ] Brute force protection sur /login (rate limit)

### API
- [ ] Tous les endpoints nécessitant auth retournent 401 sans token
- [ ] Tous les endpoints admin retournent 403 pour un user normal
- [ ] Rate limiting actif et testé

### Webhooks
- [ ] Signature HMAC validée sur tous les webhooks
- [ ] Webhook idempotent (doublon ignoré silencieusement)

### Production
- [ ] APP_DEBUG=false
- [ ] Pas de stack trace exposée (tester intentionnellement une erreur)
- [ ] composer audit → 0 vulnérabilité critique
- [ ] npm audit → 0 vulnérabilité critique
- [ ] .env non commité dans git

### OWASP Top 10
- [ ] Injection SQL → Eloquent uniquement, pas de raw queries non bindées
- [ ] XSS → Blade {{ }} obligatoire (pas de {!! !!} sans raison)
- [ ] CSRF → token Blade sur tous les formulaires
- [ ] Données sensibles → HTTPS forcé, cookies httponly
- [ ] Accès non autorisé → policies Laravel sur toutes les ressources
