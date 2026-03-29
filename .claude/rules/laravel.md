# Conventions Laravel — [DEV]
# Fichier réutilisable — ne pas modifier entre projets

## Stack de référence
Laravel 11 + MySQL 8 + Redis + Blade + Tailwind CSS + Alpine.js

## Architecture imposée

```
app/
  Http/
    Controllers/
      Admin/          → Protégé auth + 2FA
      Api/            → Sanctum — retourne Resources
      Web/            → Pages publiques — retourne views
    Requests/         → OBLIGATOIRE pour toute écriture
    Resources/        → OBLIGATOIRE pour toute réponse API
    Middleware/
  Services/           → Logique métier UNIQUEMENT ICI
  Repositories/       → Accès données (interface + implémentation)
  Models/             → Eloquent uniquement
  Events/             → Événements domaine métier
  Listeners/          → Handlers async via Queue
  Jobs/               → Tâches async Redis
database/
  migrations/
  seeders/
  factories/
tests/
  Feature/Api/        → Tests endpoints
  Unit/Services/      → Tests logique métier
```

## Controllers — règles strictes

- Maximum 7 méthodes : index, create, store, show, edit, update, destroy
- Injecter Services via constructeur uniquement
- Jamais de logique métier dans un Controller
- Jamais d'appel Eloquent direct dans un Controller
- Toujours retourner via Resource (API) ou view() (Web)

```php
// ✅ Correct
public function __construct(private OrderServiceInterface $orderService) {}

public function store(StoreOrderRequest $request): JsonResponse
{
    $order = $this->orderService->create($request->validated());
    return new OrderResource($order);
}

// ❌ Interdit
public function store(Request $request): JsonResponse
{
    $order = Order::create($request->all());
    return response()->json($order);
}
```

## Services — règles strictes

- Interface obligatoire pour chaque Service
- Responsabilité unique : 1 Service = 1 domaine métier
- Typer tous les paramètres et retours
- Injecter Repositories, jamais les Models directement

```php
// Interface obligatoire
interface OrderServiceInterface
{
    public function create(array $data): Order;
    public function cancel(Order $order): bool;
    public function updateStatus(Order $order, string $status): Order;
}

// Binding dans AppServiceProvider
$this->app->bind(OrderServiceInterface::class, OrderService::class);
```

## Models Eloquent

```php
// ✅ Toujours définir explicitement
protected $fillable = ['name', 'price', 'status'];

protected $casts = [
    'price'      => 'integer',  // FCFA — JAMAIS float
    'is_active'  => 'boolean',
    'metadata'   => 'array',
    'created_at' => 'datetime',
];

// UUID pour clés exposées en API
public $incrementing = false;
protected $keyType = 'string';

protected static function boot(): void
{
    parent::boot();
    static::creating(fn($model) => $model->id = Str::uuid());
}
```

## Migrations

```php
// Index sur toutes les colonnes de recherche fréquente
$table->index('status');
$table->index('customer_id');
$table->index(['status', 'created_at']); // index composé si filtrage multi-colonnes

// Foreign keys explicites
$table->foreign('category_id')
    ->references('id')
    ->on('categories')
    ->onDelete('restrict');
```

## Queues & Jobs (async obligatoire)

- Toutes les notifications (email, SMS, WhatsApp) → Queue async
- Driver : Redis
- Retry : 3 tentatives avec backoff exponentiel
- Failed jobs → table `failed_jobs` pour monitoring

```php
// Dispatch en async — jamais en sync pour les notifs
SendWhatsAppNotification::dispatch($order)->onQueue('notifications');
```

## Eager Loading — obligatoire

```php
// ✅ Toujours eager load les relations utilisées
Order::with(['customer', 'items.product', 'payment'])->get();

// ❌ Jamais de N+1
$orders = Order::all();
foreach ($orders as $order) {
    echo $order->customer->name; // N+1 interdit
}
```

## Form Requests — obligatoire

```php
// ✅ Toujours utiliser un Form Request
public function store(StoreOrderRequest $request): JsonResponse

// ❌ Jamais valider dans le Controller
public function store(Request $request): JsonResponse
{
    $request->validate([...]); // interdit
}
```

## Conventions générales

- Code en anglais, commentaires en français
- PSR-12 strict — 4 espaces PHP, 2 espaces JS/Blade
- Early returns — éviter imbrication > 2 niveaux
- Typer tous les paramètres et retours PHP 8+
- Prix : integer FCFA dans la DB — JAMAIS float
- Routes : toutes nommées — `Route::get(...)->name('...')`
- Préfixe API : `/api/v1/`

## Ce qu'on ne fait JAMAIS

```
❌ jQuery                → Alpine.js uniquement
❌ Bootstrap             → Tailwind CSS uniquement
❌ Logique dans Controller → Services uniquement
❌ SQL dans les vues     → Eloquent dans Services
❌ $request->all()       → $request->validated() après Form Request
❌ response()->json($model) → toujours une Resource
❌ Float pour les prix   → integer FCFA uniquement
❌ $guarded = []         → $fillable explicite obligatoire
```
