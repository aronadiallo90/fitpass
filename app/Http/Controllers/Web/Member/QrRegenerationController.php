<?php

namespace App\Http\Controllers\Web\Member;

use App\Exceptions\QrRegenerationNoActiveSubscriptionException;
use App\Exceptions\QrRegenerationTooSoonException;
use App\Http\Controllers\Controller;
use App\Services\Interfaces\QrRegenerationServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class QrRegenerationController extends Controller
{
    public function __construct(
        private readonly QrRegenerationServiceInterface $qrRegenerationService
    ) {}

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        try {
            $this->qrRegenerationService->regenerate($user);

            return redirect()
                ->route('member.qrcode')
                ->with('success', 'QR code régénéré avec succès.');

        } catch (QrRegenerationTooSoonException $e) {
            $nextAt = $e->getNextAllowedAt()->format('d/m/Y à H:i');

            return back()->with(
                'error',
                "Régénération impossible. Prochain QR disponible le {$nextAt}."
            );

        } catch (QrRegenerationNoActiveSubscriptionException $e) {
            return back()->with(
                'error',
                'Impossible de régénérer le QR code : vous n\'avez pas d\'abonnement actif.'
            );
        }
    }
}
