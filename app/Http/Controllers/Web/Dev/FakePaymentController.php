<?php

namespace App\Http\Controllers\Web\Dev;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\Interfaces\PaymentServiceInterface;
use Illuminate\Http\RedirectResponse;

/**
 * Simulation de paiement PayTech — LOCAL uniquement.
 * Ces routes n'existent qu'en APP_ENV=local (voir routes/web.php).
 */
class FakePaymentController extends Controller
{
    public function __construct(
        private readonly PaymentServiceInterface $paymentService
    ) {}

    public function confirm(Payment $payment): RedirectResponse
    {
        $this->paymentService->processWebhook([
            'ref_command'   => $payment->paytech_ref,
            'response_text' => 'SUCCESS',
        ]);

        return back()->with('success', '[DEV] Paiement simulé comme complété — abonnement activé.');
    }

    public function fail(Payment $payment): RedirectResponse
    {
        $this->paymentService->processWebhook([
            'ref_command'   => $payment->paytech_ref,
            'response_text' => 'FAILED',
        ]);

        return back()->with('error', '[DEV] Paiement simulé comme échoué.');
    }
}
