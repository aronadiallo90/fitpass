<?php

namespace App\Services\Interfaces;

use App\Models\Payment;
use App\Models\Subscription;

interface PaymentServiceInterface
{
    /**
     * Initie un paiement pour un abonnement donné.
     * Retourne le Payment créé en statut pending.
     */
    public function initiate(Subscription $subscription, string $method): Payment;

    /**
     * Traite un webhook entrant (PayTech ou fake).
     * Retourne true si le paiement a été traité, false si ignoré (doublon).
     */
    public function processWebhook(array $payload): bool;
}
