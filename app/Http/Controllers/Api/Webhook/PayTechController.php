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
        // Validation HMAC — active uniquement si PAYTECH_SECRET est configuré (production)
        $secret = config('services.paytech.secret');

        if ($secret && ! $this->isValidHmac($request, $secret)) {
            Log::warning('[PayTech Webhook] Signature HMAC invalide', [
                'ip' => $request->ip(),
            ]);
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        $payload = $request->all();

        Log::info('[PayTech Webhook] Reçu', [
            'ref_command'   => $payload['ref_command'] ?? null,
            'response_text' => $payload['response_text'] ?? null,
        ]);

        $processed = $this->paymentService->processWebhook($payload);

        if (! $processed) {
            return response()->json(['message' => 'Webhook ignoré (doublon ou inconnu).'], 200);
        }

        return response()->json(['message' => 'Webhook traité.'], 200);
    }

    private function isValidHmac(Request $request, string $secret): bool
    {
        $signature = $request->header('X-PayTech-Signature', '');
        $expected  = hash_hmac('sha256', $request->getContent(), $secret);

        return hash_equals($expected, $signature);
    }
}
