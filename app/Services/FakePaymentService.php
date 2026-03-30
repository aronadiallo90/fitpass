<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Subscription;
use App\Services\Interfaces\PaymentServiceInterface;
use App\Services\Interfaces\SubscriptionServiceInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Implémentation de test — simule PayTech sans appel API externe.
 * À remplacer par PayTechService quand les clés API sont disponibles.
 */
class FakePaymentService implements PaymentServiceInterface
{
    public function __construct(
        private readonly SubscriptionServiceInterface $subscriptionService
    ) {}

    public function initiate(Subscription $subscription, string $method): Payment
    {
        return Payment::create([
            'subscription_id' => $subscription->id,
            'user_id'         => $subscription->user_id,
            'paytech_ref'     => 'FAKE-' . strtoupper(Str::random(12)),
            'paytech_token'   => Str::random(32),
            'method'          => $method,
            'status'          => 'pending',
            'amount_fcfa'     => $subscription->amount_fcfa,
        ]);
    }

    public function processWebhook(array $payload): bool
    {
        $paytechRef = $payload['ref_command'] ?? null;

        if ($paytechRef === null) {
            Log::warning('[FakePayment] Webhook sans ref_command ignoré.');
            return false;
        }

        $payment = Payment::where('paytech_ref', $paytechRef)->first();

        if ($payment === null) {
            Log::warning('[FakePayment] Paiement introuvable', ['ref' => $paytechRef]);
            return false;
        }

        // Idempotence — ignorer si déjà traité
        if ($payment->status === 'completed') {
            Log::info('[FakePayment] Webhook doublon ignoré', ['ref' => $paytechRef]);
            return false;
        }

        $success = ($payload['response_text'] ?? '') === 'SUCCESS';

        if ($success) {
            $payment->update([
                'status'          => 'completed',
                'paytech_payload' => $payload,
                'paid_at'         => now(),
            ]);

            $this->subscriptionService->activate($payment->subscription);

            Log::info('[FakePayment] Paiement complété', ['ref' => $paytechRef]);
        } else {
            $payment->update([
                'status'          => 'failed',
                'paytech_payload' => $payload,
            ]);

            Log::info('[FakePayment] Paiement échoué', ['ref' => $paytechRef]);
        }

        return true;
    }
}
