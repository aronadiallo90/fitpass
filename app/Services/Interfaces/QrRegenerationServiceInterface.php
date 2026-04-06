<?php

namespace App\Services\Interfaces;

use App\Models\User;
use Carbon\Carbon;

interface QrRegenerationServiceInterface
{
    /**
     * Régénère le QR token du membre.
     * Lance QrRegenerationTooSoonException si cooldown actif.
     * Lance QrRegenerationNoActiveSubscriptionException si pas d'abonnement actif.
     *
     * @return array{token: string, regenerated_at: Carbon}
     */
    public function regenerate(User $user): array;

    /**
     * Vérifie si le membre peut régénérer son QR code (cooldown 24h).
     */
    public function canRegenerate(User $user): bool;

    /**
     * Retourne la prochaine date à laquelle la régénération sera possible.
     * Retourne null si la régénération est déjà possible.
     */
    public function getNextRegenerationAt(User $user): ?Carbon;
}
