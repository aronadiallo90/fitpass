<?php

namespace App\Exceptions;

use RuntimeException;

class SubscriptionException extends RuntimeException
{
    public static function alreadyActive(): self
    {
        return new self('Ce membre a déjà un abonnement actif.');
    }

    public static function notActive(): self
    {
        return new self('Cet abonnement n\'est pas actif.');
    }

    public static function cannotCancel(string $status): self
    {
        return new self("Impossible d'annuler un abonnement avec le statut : {$status}.");
    }
}
