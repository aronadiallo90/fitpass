<?php

namespace App\Services;

use App\Exceptions\QrRegenerationNoActiveSubscriptionException;
use App\Exceptions\QrRegenerationTooSoonException;
use App\Models\SmsLog;
use App\Models\User;
use App\Services\Interfaces\QrRegenerationServiceInterface;
use Carbon\Carbon;
use Illuminate\Support\Str;

class QrRegenerationService implements QrRegenerationServiceInterface
{
    private const COOLDOWN_HOURS = 24;

    public function canRegenerate(User $user): bool
    {
        // Pas de régénération précédente → autorisé
        if ($user->qr_token_regenerated_at === null) {
            return true;
        }

        return $user->qr_token_regenerated_at->addHours(self::COOLDOWN_HOURS)->isPast();
    }

    public function getNextRegenerationAt(User $user): ?Carbon
    {
        if ($this->canRegenerate($user)) {
            return null;
        }

        return $user->qr_token_regenerated_at->addHours(self::COOLDOWN_HOURS);
    }

    /**
     * @return array{token: string, regenerated_at: Carbon}
     *
     * @throws QrRegenerationTooSoonException
     * @throws QrRegenerationNoActiveSubscriptionException
     */
    public function regenerate(User $user): array
    {
        // 1. Vérifier le cooldown 24h
        if (! $this->canRegenerate($user)) {
            throw new QrRegenerationTooSoonException(
                $this->getNextRegenerationAt($user)
            );
        }

        // 2. Vérifier l'abonnement actif
        $hasActiveSubscription = $user->subscriptions()
            ->where('status', 'active')
            ->whereDate('expires_at', '>=', today())
            ->exists();

        if (! $hasActiveSubscription) {
            throw new QrRegenerationNoActiveSubscriptionException();
        }

        // 3. Générer le nouveau token et persister
        $newToken      = Str::random(64);
        $regeneratedAt = now();

        $user->update([
            'qr_token'                => $newToken,
            'qr_token_regenerated_at' => $regeneratedAt,
        ]);

        // 4. Logger l'opération dans sms_logs (réutilisation table existante)
        SmsLog::create([
            'user_id'    => $user->id,
            'phone'      => $user->phone,
            'message'    => 'QR code régénéré par le membre.',
            'status'     => 'sent',
            'event_type' => 'qr_regeneration',
            'sent_at'    => $regeneratedAt,
        ]);

        return [
            'token'          => $newToken,
            'regenerated_at' => $regeneratedAt,
        ];
    }
}
