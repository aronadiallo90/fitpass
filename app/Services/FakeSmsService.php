<?php

namespace App\Services;

use App\Models\SmsLog;
use App\Models\Subscription;
use App\Models\User;
use App\Services\Interfaces\SmsServiceInterface;
use Illuminate\Support\Facades\Log;

/**
 * Implémentation de test — stocke les SMS en base sans appel Twilio.
 * À remplacer par TwilioSmsService quand les credentials sont disponibles.
 */
class FakeSmsService implements SmsServiceInterface
{
    public function sendWelcome(User $user): void
    {
        $message = "Bienvenue sur FitPass Dakar ! "
            . "Abonnez-vous sur fitpass.sn pour accéder à toutes les salles partenaires. "
            . "Paiement Wave & Orange Money.";

        $this->log($user, $message, 'welcome');
    }

    public function sendActivation(User $user, Subscription $subscription): void
    {
        $planName  = $subscription->plan->name ?? 'FitPass';
        $expiresAt = $subscription->expires_at?->format('d/m/Y') ?? 'N/A';

        $message = "FitPass : Votre abonnement {$planName} est activé ! "
            . "Valable jusqu'au {$expiresAt}. "
            . "Ref : {$subscription->reference}";

        $this->log($user, $message, 'activation');
    }

    public function sendReminder(User $user, Subscription $subscription, int $daysLeft): void
    {
        $message = "FitPass : Votre abonnement expire dans {$daysLeft} jour(s). "
            . "Renouvelez sur fitpass.sn pour continuer à accéder à vos salles.";

        $this->log($user, $message, 'reminder');
    }

    public function sendExpiration(User $user, Subscription $subscription): void
    {
        $message = "FitPass : Votre abonnement a expiré. "
            . "Renouvelez sur fitpass.sn pour retrouver l'accès à toutes vos salles.";

        $this->log($user, $message, 'expiration');
    }

    public function sendCheckinConfirmation(User $user, string $gymName): void
    {
        $message = "FitPass : Entrée validée à {$gymName}. Bonne séance !";

        $this->log($user, $message, 'checkin');
    }

    private function log(User $user, string $message, string $eventType): void
    {
        SmsLog::create([
            'user_id'    => $user->id,
            'phone'      => $user->phone,
            'message'    => $message,
            'twilio_sid' => 'FAKE-' . strtoupper(uniqid()),
            'status'     => 'sent',
            'event_type' => $eventType,
            'sent_at'    => now(),
        ]);

        Log::info("[FakeSMS] {$eventType} → {$user->phone}: {$message}");
    }
}
