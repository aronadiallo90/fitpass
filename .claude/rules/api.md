# Conventions API REST — [DEV]
# Fichier réutilisable — ne pas modifier entre projets

## Structure des URLs
- Préfixe obligatoire : `/api/v1/`
- Ressources au pluriel : `/api/v1/orders`, `/api/v1/products`
- Snake_case pour les paramètres query
- UUID pour tous les IDs exposés publiquement

## Verbes HTTP

| Verbe | Usage | Exemple |
|---|---|---|
| GET | Lecture (liste ou détail) | GET /api/v1/orders |
| POST | Création | POST /api/v1/orders |
| PUT | Remplacement complet | PUT /api/v1/orders/{id} |
| PATCH | Modification partielle | PATCH /api/v1/orders/{id} |
| DELETE | Suppression | DELETE /api/v1/orders/{id} |

## Codes HTTP

| Code | Signification | Quand |
|---|---|---|
| 200 | OK | Lecture ou modification réussie |
| 201 | Created | Création réussie |
| 204 | No Content | Suppression réussie |
| 400 | Bad Request | Erreur syntaxe requête |
| 401 | Unauthorized | Non authentifié (pas de token) |
| 403 | Forbidden | Authentifié mais pas autorisé |
| 404 | Not Found | Ressource inexistante |
| 422 | Unprocessable | Validation échouée |
| 429 | Too Many Requests | Rate limit atteint |
| 500 | Server Error | Erreur serveur (jamais de détails en prod) |

## Format des réponses

### Succès — objet unique
```json
{
  "data": {
    "id": "uuid",
    "status": "active",
    "total": 25000
  }
}
```

### Succès — liste paginée
```json
{
  "data": [ ... ],
  "links": {
    "first": "url?page=1",
    "last": "url?page=10",
    "prev": null,
    "next": "url?page=2"
  },
  "meta": {
    "current_page": 1,
    "last_page": 10,
    "per_page": 15,
    "total": 150
  }
}
```

### Erreur de validation (422)
```json
{
  "message": "Les données fournies sont invalides.",
  "errors": {
    "email": ["L'email est obligatoire.", "Le format de l'email est invalide."],
    "amount": ["Le montant doit être un entier positif."]
  }
}
```

### Erreur générique
```json
{
  "message": "Description lisible de l'erreur"
}
```

## Resources Laravel — obligatoire

```php
// ✅ Toujours passer par une Resource
return new OrderResource($order);
return OrderResource::collection($orders);
return OrderResource::collection($orders)->response()->setStatusCode(200);

// ❌ Jamais retourner le Model directement
return response()->json($order);
return $order->toArray();
```

## Form Requests — obligatoire

```php
// ✅ Toujours un Form Request dédié
public function store(StoreOrderRequest $request): JsonResponse

// StoreOrderRequest.php
public function rules(): array
{
    return [
        'customer_id' => ['required', 'uuid', 'exists:customers,id'],
        'items'       => ['required', 'array', 'min:1'],
        'items.*.product_id' => ['required', 'uuid', 'exists:products,id'],
        'items.*.quantity'   => ['required', 'integer', 'min:1'],
    ];
}
```

## Nommage des routes — toutes nommées

```php
Route::prefix('v1')->name('api.')->group(function () {
    Route::apiResource('orders', OrderController::class);
    // Génère : api.orders.index, api.orders.store, api.orders.show, etc.

    Route::patch('orders/{order}/cancel', [OrderController::class, 'cancel'])
         ->name('orders.cancel');
});
```

## Authentication — Sanctum

```php
// Header obligatoire sur toutes les routes protégées
Authorization: Bearer {sanctum_token}

// Route protégée
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('orders', OrderController::class);
});

// Route admin (auth + rôle)
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::apiResource('admin/users', AdminUserController::class);
});
```

## Pagination standard

```php
// Toujours paginer les listes
public function index(Request $request): AnonymousResourceCollection
{
    $orders = Order::with(['customer', 'items'])
        ->latest()
        ->paginate($request->get('per_page', 15));

    return OrderResource::collection($orders);
}
```

## Filtres et tri

```php
// Paramètres query standards
GET /api/v1/orders?status=active&sort=created_at&direction=desc&per_page=20

// Implémenter via Eloquent scope ou query builder
$query->when($request->status, fn($q, $status) => $q->where('status', $status));
$query->when($request->sort, fn($q, $sort) => $q->orderBy($sort, $request->direction ?? 'asc'));
```
