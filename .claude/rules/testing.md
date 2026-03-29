# Stratégie Tests — [QA]
# Fichier réutilisable — ne pas modifier entre projets

## Philosophie
Tester le comportement, pas l'implémentation.
Un test qui passe malgré un bug est pire que pas de test.
php artisan test doit passer à 100% avant TOUT commit.

## Structure

```
tests/
  Unit/
    Services/
      OrderServiceTest.php
      PaymentServiceTest.php
      NotificationServiceTest.php
      [UnService]Test.php       ← un fichier par Service
  Feature/
    Api/
      AuthTest.php
      OrderTest.php
      [UneRessource]Test.php    ← un fichier par ressource API
  E2E/                          ← Cypress (optionnel sprint final)
```

## Unit Tests — Services

```php
<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\OrderService;
use App\Models\Order;
use App\Models\Customer;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    private OrderService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(OrderService::class);
    }

    /** @test */
    public function it_creates_an_order_with_correct_status(): void
    {
        // Arrange
        $customer = Customer::factory()->create();
        $data = ['customer_id' => $customer->id, 'total' => 25000];

        // Act
        $order = $this->service->create($data);

        // Assert
        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals('nouvelle', $order->status);
        $this->assertDatabaseHas('orders', ['id' => $order->id]);
    }

    /** @test */
    public function it_throws_exception_when_cancelling_shipped_order(): void
    {
        // Arrange
        $order = Order::factory()->create(['status' => 'expediee']);

        // Act & Assert
        $this->expectException(\App\Exceptions\OrderCannotBeCancelledException::class);
        $this->service->cancel($order);
    }
}
```

Cas à couvrir systématiquement :
- ✅ Happy path (cas nominal)
- ✅ Cas limites (valeurs zéro, vides, maximales)
- ✅ Cas d'erreur (exception attendue)
- ✅ Effets de bord (stock décrémenté, notification envoyée, log créé)

## Feature Tests — API Endpoints

```php
<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function authenticated_user_can_create_order(): void
    {
        // Arrange
        Sanctum::actingAs(User::factory()->create());
        $payload = [...]; // données valides

        // Act
        $response = $this->postJson('/api/v1/orders', $payload);

        // Assert
        $response->assertStatus(201)
                 ->assertJsonStructure(['data' => ['id', 'status', 'total']]);
        $this->assertDatabaseHas('orders', ['status' => 'nouvelle']);
    }

    /** @test */
    public function unauthenticated_user_cannot_create_order(): void
    {
        $response = $this->postJson('/api/v1/orders', []);
        $response->assertStatus(401);
    }

    /** @test */
    public function order_creation_fails_with_invalid_data(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $response = $this->postJson('/api/v1/orders', ['total' => -1]);
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['total']);
    }

    /** @test */
    public function non_admin_cannot_access_admin_endpoint(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'user']));
        $response = $this->getJson('/api/v1/admin/orders');
        $response->assertStatus(403);
    }
}
```

Vérifier systématiquement :
- ✅ Code HTTP correct (200, 201, 204, 401, 403, 404, 422, 429)
- ✅ Structure JSON de la réponse
- ✅ 401 sans token d'authentification
- ✅ 403 avec mauvais rôle
- ✅ 422 avec données invalides + messages d'erreur
- ✅ Données en base après création

## Règles QA strictes

```php
// Toujours RefreshDatabase — jamais de pollution entre tests
use Illuminate\Foundation\Testing\RefreshDatabase;

// Toujours factories — jamais de données en dur
$user = User::factory()->create(['role' => 'admin']);
$orders = Order::factory()->count(5)->for($user)->create();

// Jamais ça
$user = User::find(1); // peut ne pas exister
```

## Avant chaque commit

```bash
php artisan test
# Si un seul test échoue → bloquer le commit, corriger d'abord
```

## Checklist recette finale

### Fonctionnel
- [ ] Tous les parcours utilisateur testés manuellement
- [ ] Paiements testés en mode sandbox
- [ ] Notifications envoyées correctement
- [ ] Statuts métier respectés

### Performance
- [ ] Aucune requête N+1 (Laravel Debugbar en dev)
- [ ] Temps de réponse API < 300ms
- [ ] PageSpeed mobile > 90

### Responsive
- [ ] Mobile 375px (iPhone SE)
- [ ] Tablette 768px
- [ ] Desktop 1280px+
- [ ] Tous les formulaires utilisables au doigt

### Sécurité (avec SECURITY)
- [ ] Rate limiting actif
- [ ] 2FA admin fonctionnel
- [ ] Pas de stack trace visible en production
- [ ] Signatures webhooks validées
