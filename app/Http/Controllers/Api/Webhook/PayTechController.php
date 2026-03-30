<?php

namespace App\Http\Controllers\Api\Webhook;

use App\Http\Controllers\Controller;
use App\Services\Interfaces\PaymentServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PayTechController extends Controller
{
    public function __construct(private readonly PaymentServiceInterface $paymentService) {}

    public function store(Request $request): JsonResponse
    {
        $payload = $request->all();

        Log::info('[PayTech Webhook] Reçu', ['payload' => $payload]);

        // Note : la validation HMAC sera activée quand on branchera le vrai PayTech
        // Pour l'instant, le FakePaymentService traite directement le payload

        $processed = $this->paymentService->processWebhook($payload);

        if (! $processed) {
            return response()->json(['message' => 'Webhook ignoré (doublon ou inconnu).'], 200);
        }

        return response()->json(['message' => 'Webhook traité.'], 200);
    }
}
