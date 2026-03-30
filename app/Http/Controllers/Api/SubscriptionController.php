<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\SubscriptionException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreSubscriptionRequest;
use App\Http\Resources\SubscriptionResource;
use App\Models\SubscriptionPlan;
use App\Services\Interfaces\PaymentServiceInterface;
use App\Services\Interfaces\SubscriptionServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SubscriptionController extends Controller
{
    public function __construct(
        private readonly SubscriptionServiceInterface $subscriptionService,
        private readonly PaymentServiceInterface $paymentService
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $subscriptions = $request->user()
            ->subscriptions()
            ->with('plan')
            ->latest()
            ->paginate(10);

        return SubscriptionResource::collection($subscriptions);
    }

    public function store(StoreSubscriptionRequest $request): JsonResponse
    {
        $user = $request->user();
        $plan = SubscriptionPlan::where('id', $request->plan_id)
            ->where('is_active', true)
            ->firstOrFail();

        // Crée l'abonnement en statut pending
        try {
            $subscription = $this->subscriptionService->create($user, $plan);
        } catch (SubscriptionException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        // Initie le paiement (fake ou PayTech selon l'implémentation bindée)
        $payment = $this->paymentService->initiate($subscription, $request->method);

        return response()->json([
            'data'    => new SubscriptionResource($subscription->load('plan')),
            'payment' => [
                'id'          => $payment->id,
                'paytech_ref' => $payment->paytech_ref,
                'method'      => $payment->method,
                'amount_fcfa' => $payment->amount_fcfa,
                // PayTech : ici on retournerait le lien de paiement
                'redirect_url' => null,
            ],
        ], 201);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $subscription = $request->user()
            ->subscriptions()
            ->with('plan')
            ->findOrFail($id);

        return response()->json(['data' => new SubscriptionResource($subscription)]);
    }
}
